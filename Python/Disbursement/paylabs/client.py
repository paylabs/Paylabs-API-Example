import base64
import hashlib
from typing import Dict, Any, Optional

import requests
from cryptography.hazmat.primitives import hashes
from cryptography.hazmat.primitives.asymmetric import padding
from cryptography.hazmat.primitives import serialization

from .utils import compact_json, php_timestamp_with_B

class PaylabsRemitClient:
    """Python port of Paylabs Remit SNAP PHP client.
    - Base URL (SIT):  https://sit-remit-api.paylabs.co.id/api-pay/snap/
      Base URL (PROD): https://remit-api.paylabs.co.id/api-pay/snap/
    - getUrl()   -> base_url + {version}
    - getEndpoint() used in signature: "/api-pay/snap/" + {version}
    - StringToSign (request):  "POST:" + getEndpoint() + path + ":" + sha256(JSON(body)) + ":" + timestamp
    - Headers: X-TIMESTAMP, X-SIGNATURE, X-PARTNER-ID, X-EXTERNAL-ID, ORIGIN, Content-Type
    - verify_sign (callback):  "POST:" + path + ":" + sha256(raw_body) + ":" + timestamp
    """

    BASE_URL = {
        "SIT": "https://sit-remit-api.paylabs.co.id/api-pay/snap/",
        "PROD": "https://remit-api.paylabs.co.id/api-pay/snap/"
    }

    def __init__(self, mid: str, version: str = "v1.2", server: str = "SIT",
                 private_key: str = "", public_key: str = "", log: bool = False):
        self.server = (server or "SIT").upper()
        self.mid = mid
        self.version = version
        self.log = log

        # Timestamp & IDs mimicking PHP constructor
        self.date = php_timestamp_with_B()
        import random, datetime
        self.id_request = datetime.datetime.now().strftime("%Y%m%d%H%M%S") + str(random.randint(11111, 99999))
        self.merchant_trade_no = self.id_request

        # Load keys (accept base64 blocks without PEM lines or full PEMs)
        self._private_key = self._load_private_key(private_key) if private_key else None
        self._public_key = self._load_public_key(public_key) if public_key else None

    # ---------------- internal helpers ----------------
    def _get_url(self) -> str:
        base = self.BASE_URL.get(self.server, self.BASE_URL["SIT"])  # ends with /api-pay/snap/
        return f"{base}{self.version}"

    def _get_endpoint(self) -> str:
        # used by signature prefix
        return f"/api-pay/snap/{self.version}"

    def _load_private_key(self, val: str):
        val = val.strip()
        if "BEGIN" in val:
            pem = val.encode()
        else:
            pem = f"-----BEGIN RSA PRIVATE KEY-----\n{val}\n-----END RSA PRIVATE KEY-----".encode()
        try:
            return serialization.load_pem_private_key(pem, password=None)
        except Exception:
            # Try PKCS#8 if merchant provides that format instead
            if "BEGIN" not in val:
                pem = f"-----BEGIN PRIVATE KEY-----\n{val}\n-----END PRIVATE KEY-----".encode()
                return serialization.load_pem_private_key(pem, password=None)
            raise

    def _load_public_key(self, val: str):
        val = val.strip()
        if "BEGIN" in val:
            pem = val.encode()
        else:
            pem = f"-----BEGIN PUBLIC KEY-----\n{val}\n-----END PUBLIC KEY-----".encode()
        return serialization.load_pem_public_key(pem)

    def _generate_signature(self, method:str, path: str, body: Dict[str, Any]) -> str:
        body_json = compact_json(body)
        sha = hashlib.sha256(body_json.encode()).hexdigest().lower()
        s2s = f"{method}:{self._get_endpoint()}{path}:{sha}:{self.date}"
        if self.log:
            print("[Sign]", s2s)
        if not self._private_key:
            raise ValueError("private_key is not set for signing")
        sig = self._private_key.sign(s2s.encode(), padding.PKCS1v15(), hashes.SHA256())
        return base64.b64encode(sig).decode()

    def _headers(self, method:str, path: str, body: Dict[str, Any]) -> Dict[str, str]:
        return {
            "X-TIMESTAMP": self.date,
            "X-SIGNATURE": self._generate_signature(method, path, body),
            "X-PARTNER-ID": self.mid,
            "X-EXTERNAL-ID": self.id_request,
            "ORIGIN": "www.paylabs.co.id",
            "Content-Type": "application/json;charset=utf-8",
        }

    # --------------- transport ----------------
    def _post(self, path: str, body: Dict[str, Any]) -> Dict[str, Any]:
        url = self._get_url() + path
        method = "POST"
        headers = self._headers(method, path, body)
        if self.log:
            print("[POST]", url)
            print("[Body]", compact_json(body))
            print("[Hdrs]", headers)
        resp = requests.post(url, data=compact_json(body).encode("utf-8"), headers=headers, timeout=60)
        try:
            return resp.json()
        except Exception:
            return {"status_code": resp.status_code, "text": resp.text}

    def _get(self, path: str, body: Dict[str, Any]) -> Dict[str, Any]:
        """Mimic PHP GET which still sends JSON body via CURLOPT_POSTFIELDS."""
        url = self._get_url() + path
        method = "GET"
        headers = self._headers(method, path, body)
        if self.log:
            print("[GET]", url)
            print("[Body]", compact_json(body))
            print("[Hdrs]", headers)
        resp = requests.request("GET", url, data=compact_json(body).encode("utf-8"), headers=headers, timeout=60)
        try:
            return resp.json()
        except Exception:
            return {"status_code": resp.status_code, "text": resp.text}

    # --------------- public API methods (mirror PHP) ----------------
    def inquiry(self, bank_no: str, bank_code: str, remit_type: str, amount: int, notify_url: Optional[str] = None) -> Dict[str, Any]:
        path = "/account-inquiry-external"
        body = {
            "partnerReferenceNo": self.id_request,
            "beneficiaryAccountNo": bank_no,
            "beneficiaryBankCode": bank_code,
            "additionalInfo": {
                "remitType": remit_type,
                "amount": amount
            }
        }
        if notify_url is not None:
            body["additionalInfo"]["notifyUrl"] = notify_url
        return self._post(path, body)

    def transfer(self, customer_reference: str, bank_no: str, bank_name: str, bank_code: str,
                 remit_type: str, amount: int, notify_url: Optional[str] = None) -> Dict[str, Any]:
        path = "/transfer-interbank"
        body = {
            "partnerReferenceNo": self.id_request,
            "customerReference": customer_reference,
            "beneficiaryAccountNo": bank_no,
            "beneficiaryAccountName": bank_name,
            "beneficiaryBankCode": bank_code,
            "amount": {"value": amount, "currency": "Rp"},
            "additionalInfo": {"remitType": remit_type, "notifyUrl": notify_url}
        }
        return self._post(path, body)

    def status(self, partner_reference_no: str) -> Dict[str, Any]:
        path = "/transfer/status"
        body = {"originalPartnerReferenceNo": partner_reference_no}
        return self._post(path, body)

    def balance(self) -> Dict[str, Any]:
        path = "/balance-inquiry"
        body = {"partnerReferenceNo": self.id_request, "accountNo": self.mid}
        return self._post(path, body)

    def get_transfer_file(self, partner_reference_no: str, date: str) -> Dict[str, Any]:
        """GET with JSON body (as in PHP): date format e.g. '2025-04-13'"""
        path = "/transfer-file"
        body = {"partnerReferenceNo": partner_reference_no, "date": date}
        return self._get(path, body)

    # --------------- callback signature verification ----------------
    def verify_sign(self, path: str, raw_body: str, signature_b64: str, timestamp: str) -> bool:
        """Verify callback signature.
        StringToVerify = "POST:" + {path} + ":" + sha256(raw_body) + ":" + timestamp
        - 'path' must be the callback path provided by Paylabs (e.g., '/disbursement/callback.php')
        - 'raw_body' is the exact JSON string as received
        - 'timestamp' is the value of X-TIMESTAMP header
        """
        sha = hashlib.sha256(raw_body.encode()).hexdigest().lower()
        s2v = f"POST:{path}:{sha}:{timestamp}"
        if self.log:
            print("[Verify]", s2v)
        if not self._public_key:
            raise ValueError("public_key is not set for verification")
        try:
            self._public_key.verify(base64.b64decode(signature_b64), s2v.encode(), padding.PKCS1v15(), hashes.SHA256())
            return True
        except Exception:
            return False