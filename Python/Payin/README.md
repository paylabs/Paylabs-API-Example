# Paylabs Python Connector (Tanpa API Doc)

Connector Python minimal untuk integrasi Paylabs. Tanpa FastAPI/Swagger.

## Struktur
```
paylabs_python_no_apidoc/
├─ paylabs/
│  ├─ __init__.py
│  ├─ client.py
│  └─ utils.py
├─ run.py
├─ requirements.txt
└─ README.md
```

## Instalasi
```
python -m venv .venv
.venv\Scripts\activate   # Windows
pip install -r requirements.txt
```

## Konfigurasi Kunci
Isikan kunci privat/publik sebagai blok base64 **tanpa** header/footer PEM.
Kelas akan menambahkan header/footer PEM otomatis.

## Uji Koneksi
Edit `run.py` lalu jalankan:
```
python run.py
```

## String-to-Sign (kompatibel PHP)
```
POST:/payment/{version}{path}:{sha256(json_body)}:{timestamp}
```
- JSON body: tanpa spasi (separators=(',',':')), unicode tidak di-escape.
- Timestamp: `Asia/Jakarta`, format `YYYY-MM-DDTHH:MM:SS+07:00`.
