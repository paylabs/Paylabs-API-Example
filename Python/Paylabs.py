import requests
import json
from datetime import datetime
import random
import hashlib
import base64
from Crypto.Signature import pkcs1_15
from Crypto.Hash import SHA256
from Crypto.PublicKey import RSA
import pytz

class Paylabs:
    
    def __init__(self):
        # Tentukan zona waktu Asia/Jakarta
        jakarta_tz = pytz.timezone('Asia/Jakarta')
        now = datetime.now(jakarta_tz)
        
        self.server = "SIT"
        self.endpoint = "/payment/"
        self.url_prod = "https://pay.paylabs.co.id/payment/"
        self.url_sit = "https://sit-pay.paylabs.co.id/payment/"
        self.url_sitch = "https://sitch-pay.paylabs.co.id/payment/"
        self.private_key = None
        self.public_key = None
        self.date = now.strftime("%Y-%m-%dT%H:%M:%S.%f")[:-3] + "+07:00"
        self.id_request = str(now.strftime("%Y%m%d%H%M%S") + str(random.randint(11111, 99999)))
        self.merchant_trade_no = self.id_request
        self.notify_url = None
        self.signature = None
        self.path = None
        self.headers = None
        self.body = None

    def set_mid(self, mid):
        self.mid = mid

    def set_version(self, version):
        self.version = version

    def set_id_request(self, id):
        self.id_request = id

    def set_merc_trade_no(self, no):
        self.merchant_trade_no = no

    def set_notify_url(self, url):
        self.notify_url = url

    def set_server(self, server):
        self.server = server

    def set_private_key(self, private_key):
        self.private_key = private_key

    def set_public_key(self, public_key):
        self.public_key = public_key

    def get_url(self):
        if self.server == "PROD":
            return self.url_prod + self.version
        elif self.server == "SIT":
            return self.url_sit + self.version
        elif self.server == "SITCH":
            return self.url_sitch + self.version
        else:
            return self.url_sit + self.version

    def get_endpoint(self):
        return self.endpoint + self.version

    def set_headers(self):
        self.headers = {
            'X-TIMESTAMP': self.date,
            'X-SIGNATURE': self.signature,
            'X-PARTNER-ID': self.mid,
            'X-REQUEST-ID': self.id_request,
            'Content-Type': 'application/json;charset=utf-8'
        }

    def set_h5(self, amount, phone_number, product, redirect_url, payer="Testing", store_id=None, notify_url=None):
        self.path = "/h5/createLink"
        self.body = {
            'merchantId': self.mid,
            'merchantTradeNo': self.merchant_trade_no,
            'requestId': self.id_request,
            'amount': amount,
            'phoneNumber': phone_number,
            'productName': product,
            'redirectUrl': redirect_url,
            'payer': payer
        }

        if store_id is not None:
            self.body['storeId'] = str(store_id)
        if notify_url is not None:
            self.body['notifyUrl'] = notify_url
            
    def set_qris(self, amount, product, store_id=None, notify_url=None):
        self.path = "/qris/create"
        self.body = {
            'merchantId': self.mid,
            'merchantTradeNo': self.merchant_trade_no,
            'requestId': self.id_request,
            'paymentType': "QRIS",
            'amount': amount,
            'productName': product
        }

        if store_id is not None:
            self.body['storeId'] = str(store_id)
        if notify_url is not None:
            self.body['notifyUrl'] = notify_url

        return self.body

    def set_emoney(self, channel, amount, product, store_id=None, notify_url=None):
        self.path = "/ewallet/create"
        self.body = {
            'merchantId': self.mid,
            'merchantTradeNo': self.merchant_trade_no,
            'requestId': self.id_request,
            'paymentType': channel,
            'amount': amount,
            'productName': product
        }

        if store_id is not None:
            self.body['storeId'] = str(store_id)
        if notify_url is not None:
            self.body['notifyUrl'] = notify_url

        return self.body

    def set_va(self, channel, amount, product, payer, store_id=None, notify_url=None):
        self.path = "/va/create"
        self.body = {
            'merchantId': self.mid,
            'merchantTradeNo': self.merchant_trade_no,
            'requestId': self.id_request,
            'paymentType': channel,
            'amount': amount,
            'productName': product,
            'payer': payer
        }

        if store_id is not None:
            self.body['storeId'] = str(store_id)
        if notify_url is not None:
            self.body['notifyUrl'] = notify_url

        return self.body

    def set_cc(self, channel, amount, product, payer, store_id=None, notify_url=None):
        self.path = "/cc/create"
        self.body = {
            'merchantId': self.mid,
            'merchantTradeNo': self.merchant_trade_no,
            'requestId': self.id_request,
            'paymentType': channel,
            'amount': amount,
            'productName': product
        }

        if store_id is not None:
            self.body['storeId'] = str(store_id)
        if notify_url is not None:
            self.body['notifyUrl'] = notify_url

        return self.body

    def set_e_credit(self, channel, amount, product, store_id=None, notify_url=None):
        self.path = "/dd/create"
        self.body = {
            'merchantId': self.mid,
            'merchantTradeNo': self.merchant_trade_no,
            'requestId': self.id_request,
            'paymentType': channel,
            'amount': amount,
            'productName': product
        }

        if store_id is not None:
            self.body['storeId'] = str(store_id)
        if notify_url is not None:
            self.body['notifyUrl'] = notify_url

        return self.body

    def set_otc(self, channel, amount, product, payer, store_id=None, notify_url=None):
        self.path = "/store/create"
        self.body = {
            'merchantId': self.mid,
            'merchantTradeNo': self.merchant_trade_no,
            'requestId': self.id_request,
            'paymentType': channel,
            'amount': amount,
            'productName': product,
            'payer': payer,
        }

        if store_id is not None:
            self.body['storeId'] = str(store_id)
        if notify_url is not None:
            self.body['notifyUrl'] = notify_url

        return self.body
        
    def verify_sign(self, path, data_to_sign, sign, date_time):
        binary_signature = base64.b64decode(sign)
        sha_json = hashlib.sha256(data_to_sign.encode()).hexdigest()
        signature_after = f"POST:{path}:{sha_json}:{date_time}"

        public_key = RSA.import_key(self.public_key)
        h = SHA256.new(signature_after.encode())

        try:
            pkcs1_15.new(public_key).verify(h, binary_signature)
            return True
        except (ValueError, TypeError):
            return False

    def generate_sign(self):
        # Mengonversi body ke string JSON dan menghasilkan hash SHA-256
        sha_json = hashlib.sha256(json.dumps(self.body, separators=(',', ':'), ensure_ascii=False).encode()).hexdigest()
    
        # Membentuk pesan yang akan ditandatangani
        signature_before = f"POST:{self.get_endpoint()}{self.path}:{sha_json}:{self.date}"
    
        # Membuat objek kunci RSA dari kunci privat dalam format PEM
        private_key = RSA.import_key(self.private_key)
    
        # Membuat objek hash SHA-256
        h = SHA256.new(signature_before.encode())
    
        # Membuat tanda tangan digital menggunakan kunci privat
        signature = pkcs1_15.new(private_key).sign(h)
    
        # Mengonversi tanda tangan ke format base64
        sign = base64.b64encode(signature).decode()
    
        self.signature = sign
        return sign

    def request(self):
        self.generate_sign()
        self.set_headers()

        url = self.get_url() + self.path
        payload = json.dumps(self.body)

        headers = self.headers

        response = requests.post(url, headers=headers, data=payload)
        print(url)
        print(headers)
        print(payload)
        return response.json()