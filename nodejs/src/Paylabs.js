const crypto = require('crypto');
const moment = require('moment-timezone');

// --- Zona waktu Asia/Jakarta ---
function nowJakarta() {
  return moment.tz('Asia/Jakarta').format('YYYY-MM-DDTHH:mm:ss.SSSZ');
}

// --- Util ---
function toPemPrivate(key) {
  // Terima raw (tanpa header) atau sudah PEM
  if (key.includes('BEGIN RSA PRIVATE KEY') || key.includes('BEGIN PRIVATE KEY')) return key;
  return `-----BEGIN RSA PRIVATE KEY-----\n${key}\n-----END RSA PRIVATE KEY-----`;
}
function toPemPublic(key) {
  if (key.includes('BEGIN PUBLIC KEY')) return key;
  return `-----BEGIN PUBLIC KEY-----\n${key}\n-----END PUBLIC KEY-----`;
}
function sha256HexLower(input) {
  return crypto.createHash('sha256').update(input, 'utf8').digest('hex').toLowerCase();
}
function genIds() {
  const d = new Date();
  const pad = (n, z = 2) => String(n).padStart(z, '0');
  const ts = `${d.getFullYear()}${pad(d.getMonth() + 1)}${pad(d.getDate())}${pad(d.getHours())}${pad(d.getMinutes())}${pad(d.getSeconds())}`;
  const rand = Math.floor(11111 + Math.random() * 88889);
  const id = `${ts}${rand}`;
  return { requestId: id, merchantTradeNo: id };
}

class PaylabsClient {
  /**
   * @param {Object} config
   * @param {'SIT'|'PROD'} [config.server='SIT']
   * @param {string} config.mid - Merchant ID
   * @param {string} [config.version='v1'] - mis. 'v1' (tanpa slash depan/belakang)
   * @param {boolean} [config.log=false]
   * @param {string} [config.privateKey] - raw atau PEM
   * @param {string} [config.publicKey] - raw atau PEM
   */
  constructor(config = {}) {
    this.server = config.server || 'SIT';
    this.mid = config.mid || null;
    this.version = (config.version || 'v1').replace(/^\/|\/$/g, ''); // sanitize
    this.log = !!config.log;

    this.privateKey = config.privateKey ? toPemPrivate(config.privateKey) : null;
    this.publicKey  = config.publicKey  ? toPemPublic(config.publicKey)   : null;

    this.baseMap = {
      SIT:  'https://sit-pay.paylabs.co.id/payment/',
      PROD: 'https://pay.paylabs.co.id/payment/',
    };
  }

  setPrivateKey(key) { this.privateKey = toPemPrivate(key); }
  setPublicKey(key)  { this.publicKey  = toPemPublic(key);  }

  getFullUrl(path) {
    const base = this.baseMap[this.server] || this.baseMap.SIT;
    const cleanPath = path.startsWith('/') ? path.slice(1) : path;
    return `${base}${this.version}/${cleanPath}`;
  }

  /**
   * Bangun string yang ditandatangani untuk REQUEST:
   *   "POST:/payment/{version}{path}:{sha256(json)}:{timestamp}"
   */
  buildSignStringForRequest(bodyObj, path, timestamp) {
    // JSON.stringify di JS tidak escape slash, unicode aman.
    const hash = sha256HexLower(JSON.stringify(bodyObj));
    // pastikan path berformat '/{something}'
    const clean = path.startsWith('/') ? path : `/${path}`;
    const str = `POST:/payment/${this.version}${clean}:${hash}:${timestamp}`;
    if (this.log) console.log('[SignStringReq]', str);
    return str;
  }

  /**
   * Bangun string yang diverifikasi untuk CALLBACK:
   *   "POST:{path}:{sha256(rawBody)}:{timestamp}"
   * Di sini {path} adalah path full yang dikirim Paylabs di callback (mulai dari '/...')
   */
  buildSignStringForCallback(path, bodyRaw, timestamp) {
    const hash = sha256HexLower(bodyRaw);
    const clean = path.startsWith('/') ? path : `/${path}`;
    const str = `POST:${clean}:${hash}:${timestamp}`;
    if (this.log) console.log('[SignStringCb]', str);
    return str;
  }

  signBase64(str) {
    if (!this.privateKey) throw new Error('Private key belum diset');
    const sig = crypto.sign('RSA-SHA256', Buffer.from(str, 'utf8'), this.privateKey);
    return sig.toString('base64');
  }

  verifyBase64(str, signatureB64) {
    if (!this.publicKey) throw new Error('Public key belum diset');
    const ok = crypto.verify('RSA-SHA256', Buffer.from(str, 'utf8'), this.publicKey, Buffer.from(signatureB64, 'base64'));
    return !!ok;
  }

  /**
   * Kirim HTTP POST (pakai fetch bawaan Node 18+)
   */
  async sendHttp(url, headers, jsonObj) {
    const res = await fetch(url, {
      method: 'POST',
      headers,
      body: JSON.stringify(jsonObj),
    });
    const text = await res.text();
    try {
      return JSON.parse(text);
    } catch {
      // kalau bukan JSON, kembalikan raw (untuk debug)
      return { raw: text, status: res.status };
    }
  }

  /**
   * request() — set default payload, header, signature, kirim
   * @param {string} path - mis. '/virtual-account/create'
   * @param {object} body - field spesifik request
   * @param {object} [opts]
   * @param {string} [opts.timestamp]  - override timestamp
   * @param {string} [opts.requestId]  - override
   * @param {string} [opts.merchantTradeNo] - override
   */
  async request(path, body = {}, opts = {}) {
    if (!this.mid) throw new Error('mid (merchantId) belum diset');

    const timestamp = opts.timestamp || nowJakarta();
    const { requestId, merchantTradeNo } = {
      ...genIds(),
      ...(opts.requestId ? { requestId: opts.requestId } : {}),
      ...(opts.merchantTradeNo ? { merchantTradeNo: opts.merchantTradeNo } : {}),
    };

    // default fields
    const payload = {
      merchantId: this.mid,
      requestId,
      merchantTradeNo,
      ...body,
    };

    // signature
    const signString = this.buildSignStringForRequest(payload, path, timestamp);
    const signature = this.signBase64(signString);

    const headers = {
      'X-TIMESTAMP': timestamp,
      'X-SIGNATURE': signature,
      'X-PARTNER-ID': this.mid,
      'X-REQUEST-ID': requestId,
      'Content-Type': 'application/json;charset=utf-8',
    };

    const url = this.getFullUrl(path);

    if (this.log) {
      console.log('[URL]', url);
      console.log('[Headers]', headers);
      console.log('[Payload]', payload);
    }

    return this.sendHttp(url, headers, payload);
  }

  /**
   * verifySignature() — untuk verifikasi callback Paylabs
   * @param {string} path - path callback (harus persis yg dipakai Paylabs, contoh: '/payment/v1/va/notify')
   * @param {string|Buffer} bodyRaw - raw body string yang diterima
   * @param {string} signatureB64 - dari header 'X-SIGNATURE'
   * @param {string} timestamp - dari header 'X-TIMESTAMP'
   */
  verifySignature(path, bodyRaw, signatureB64, timestamp) {
    const str = this.buildSignStringForCallback(path, bodyRaw.toString('utf8'), timestamp);
    return this.verifyBase64(str, signatureB64);
  }

  /**
   * responseCallback() — balasan standar
   * Kembalikan headers & body agar gampang dipakai di Express.
   */
  buildResponseCallback(path) {
    const timestamp = nowJakarta();
    const { requestId } = genIds();
    const payload = {
      merchantId: this.mid,
      requestId,
      errCode: '0',
    };
    const signStr = this.buildSignStringForRequest(payload, path, timestamp);
    const signature = this.signBase64(signStr);

    return {
      headers: {
        'Content-Type': 'application/json;charset=utf-8',
        'X-TIMESTAMP': timestamp,
        'X-SIGNATURE': signature,
        'X-PARTNER-ID': this.mid,
        'X-REQUEST-ID': requestId,
      },
      body: payload,
    };
  }

  /**
   * responseCallbackSnap(data)
   */
  buildResponseCallbackSnap(data) {
    const timestamp = nowJakarta();
    const body = {
      responseCode: '2002500',
      responseMessage: 'Success',
      virtualAccountData: {
        partnerServiceId: `  ${this.mid}`,
        customerNo: data.customerNo,
        virtualAccountNo: data.virtualAccountNo,
        virtualAccountName: data.virtualAccountName,
        paymentRequestId: data.paymentRequestId,
      },
    };
    return {
      headers: {
        'Content-Type': 'application/json;charset=utf-8',
        'X-TIMESTAMP': timestamp,
      },
      body,
    };
  }
}

module.exports = { PaylabsClient };
