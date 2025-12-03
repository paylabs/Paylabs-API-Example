# Paylabs Remit SNAP Python Connector (Tanpa API Doc)
Port Python dari client PHP untuk `remit-api.paylabs.co.id/api-pay/snap`.

## Fitur
- Header & signature identik dengan PHP:
  - `X-TIMESTAMP`, `X-SIGNATURE`, `X-PARTNER-ID`, `X-EXTERNAL-ID`, `ORIGIN`
  - String-to-sign (request): `POST:/api-pay/snap/{version}{path}:{sha256(json)}:{timestamp}`
  - Verifikasi callback: `POST:{path}:{sha256(raw_body)}:{timestamp}`
- Endpoint yang dicontohkan:
  - `/account-inquiry-external` (POST)
  - `/transfer-interbank` (POST)
  - `/transfer/status` (POST)
  - `/balance-inquiry` (POST)
  - `/transfer-file` (GET, mengirim JSON body seperti PHP)

## Instalasi
```
python -m venv .venv
.venv\Scripts\activate   # Windows
pip install -r requirements.txt
```

## Konfigurasi
- `mid`, `version`, `server`: atur di `run.py`
- Kunci RSA bisa berupa blok base64 **tanpa** PEM, atau full PEM. Loader akan mencoba keduanya.

## Jalankan Contoh
```
python run.py
```

## Catatan Kompatibilitas
- Timestamp mengikuti PHP: `YYYY-MM-DDTHH:MM:SS.B+07:00` (B = Swatch beats, 000–999).
- Untuk GET `/transfer-file`, dikirim JSON body (meniru `CURLOPT_POSTFIELDS` di PHP meskipun method GET).
