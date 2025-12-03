import base64
import hashlib
from typing import Dict, Any
import requests

from cryptography.hazmat.primitives import hashes
from cryptography.hazmat.primitives.asymmetric import padding
from cryptography.hazmat.primitives import serialization

from .utils import iso8601_now_jakarta, compact_json

class PaylabsClient:
    BASE_URL = {
        "SIT": "https://sit-pay.paylabs.co.id/payment/",
        "PROD": "https://pay.paylabs.co.id/payment/"
    }

    def __init__(self, server: str, mid: str, version: str, private_key: str, public_key: str, log: bool=False):
        self.server = (server or "SIT").upper()
        self.mid = mid
        self.version = version
        self.private_key_raw = private_key.strip() if private_key else ""
        self.public_key_raw = public_key.strip() if public_key else ""
        self.log = log

        self.date = iso8601_now_jakarta()
        self.request_id = self._gen_request_id()
        self.merchant_trade_no = self.request_id

        self._private_key = self._load_private_key(self.private_key_raw)
        self._public_key = self._load_public_key(self.public_key_raw)

    def _gen_request_id(self) -> str:
        import random, datetime
        return datetime.datetime.now().strftime("%Y%m%d%H%M%S") + str(random.randint(11111, 99999))

    def _get_full_url(self, path: str) -> str:
        base = self.BASE_URL.get(self.server, self.BASE_URL["SIT"])
        return f"{base}{self.version}{path}"

    def _load_private_key(self, b64: str):
        if not b64:
            raise ValueError("private_key is required")
        pem_pkcs1 = f"-----BEGIN RSA PRIVATE KEY-----\n{b64}\n-----END RSA PRIVATE KEY-----".encode()
        try:
            return serialization.load_pem_private_key(pem_pkcs1, password=None)
        except Exception:
            # Fallback to PKCS#8 if provided in that format
            pem_pkcs8 = f"-----BEGIN PRIVATE KEY-----\n{b64}\n-----END PRIVATE KEY-----".encode()
            return serialization.load_pem_private_key(pem_pkcs8, password=None)

    def _load_public_key(self, b64: str):
        if not b64:
            raise ValueError("public_key is required")
        pem = f"-----BEGIN PUBLIC KEY-----\n{b64}\n-----END PUBLIC KEY-----".encode()
        return serialization.load_pem_public_key(pem)

    def _generate_signature(self, body: Dict[str, Any], path: str) -> str:
        body_json = compact_json(body)
        hashed_body = hashlib.sha256(body_json.encode()).hexdigest().lower()
        data_to_sign = f"POST:/payment/{self.version}{path}:{hashed_body}:{self.date}"
        signature = self._private_key.sign(
            data_to_sign.encode(),
            padding.PKCS1v15(),
            hashes.SHA256()
        )
        return base64.b64encode(signature).decode()

    def _build_headers(self, body: Dict[str, Any], path: str) -> Dict[str, str]:
        return {
            "X-TIMESTAMP": self.date,
            "X-SIGNATURE": self._generate_signature(body, path),
            "X-PARTNER-ID": self.mid,
            "X-REQUEST-ID": self.request_id,
            "Content-Type": "application/json;charset=utf-8"
        }

    def request(self, path: str, payload: Dict[str, Any]) -> Dict[str, Any]:
        default = {
            "merchantId": self.mid,
            "requestId": self.request_id,
            "merchantTradeNo": self.merchant_trade_no
        }
        body = {**default, **(payload or {})}
        url = self._get_full_url(path)
        headers = self._build_headers(body, path)

        if self.log:
            print("[Paylabs] POST", url)
            print("[Paylabs] Payload:", compact_json(body))
            print("[Paylabs] Headers:", headers)

        resp = requests.post(url, data=compact_json(body).encode("utf-8"), headers=headers, timeout=60)
        try:
            return resp.json()
        except Exception:
            return {"status_code": resp.status_code, "text": resp.text}

    def verify_signature(self, path: str, body_raw: str, signature_b64: str, timestamp: str) -> bool:
        hashed_body = hashlib.sha256(body_raw.encode()).hexdigest().lower()
        data = f"POST:{path}:{hashed_body}:{timestamp}"
        try:
            self._public_key.verify(
                base64.b64decode(signature_b64),
                data.encode(),
                padding.PKCS1v15(),
                hashes.SHA256()
            )
            return True
        except Exception:
            return False
