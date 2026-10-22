/**
 * @file Paylabs.js
 * @description SDK Utama Paylabs Payment Gateway untuk Node.js dengan standar produksi enterprise.
 * Mendukung Payin (VA, QRIS, E-Wallet, H5, CC, Direct Debit, OTC), SNAP ASPI, dan Webhook Callback.
 */

'use strict';

const crypto = require('crypto');
const moment = require('moment-timezone');

// ==========================================
// KELAS EXCEPTION / CUSTOM ERRORS
// ==========================================

/**
 * Kelas dasar error Paylabs.
 */
class PaylabsError extends Error {
  constructor(message) {
    super(message);
    this.name = 'PaylabsError';
  }
}

/**
 * Error konfigurasi (misal kredensial belum diisi).
 */
class PaylabsConfigError extends PaylabsError {
  constructor(message) {
    super(message);
    this.name = 'PaylabsConfigError';
  }
}

/**
 * Error validasi parameter request atau callback.
 */
class PaylabsValidationError extends PaylabsError {
  constructor(message) {
    super(message);
    this.name = 'PaylabsValidationError';
  }
}

/**
 * Error kegagalan verifikasi tanda tangan digital.
 */
class PaylabsSignatureError extends PaylabsError {
  constructor(message) {
    super(message);
    this.name = 'PaylabsSignatureError';
  }
}

/**
 * Error koneksi jaringan, timeout, atau gateway upstream.
 */
class PaylabsNetworkError extends PaylabsError {
  constructor(message, status = null, responseBody = null) {
    super(message);
    this.name = 'PaylabsNetworkError';
    this.status = status;
    this.responseBody = responseBody;
  }
}

// ==========================================
// FUNGSI UTILITAS & KRIPTOGRAFI
// ==========================================

/**
 * Mendapatkan timestamp terkini dalam zona waktu Asia/Jakarta format ISO 8601.
 * @returns {string} Timestamp terformat ISO 8601
 */
function nowJakarta() {
  return moment.tz('Asia/Jakarta').format('YYYY-MM-DDTHH:mm:ss.SSSZ');
}

/**
 * Mengonversi kunci privat RSA raw Base64 ke format PEM standar.
 * Mendukung format PKCS#8 dan PKCS#1 secara otomatis.
 * @param {string} key - Kunci privat dalam bentuk PEM atau raw Base64
 * @returns {string} Kunci privat terformat PEM
 */
function toPemPrivate(key) {
  if (!key || typeof key !== 'string') {
    throw new PaylabsConfigError('Kunci privat RSA harus berupa string non-kosong');
  }
  const cleanKey = key.trim();
  if (cleanKey.includes('BEGIN RSA PRIVATE KEY') || cleanKey.includes('BEGIN PRIVATE KEY')) {
    return cleanKey;
  }
  // Deteksi validitas PKCS#8 terlebih dahulu, fallback ke PKCS#1
  try {
    const pkcs8Pem = `-----BEGIN PRIVATE KEY-----\n${cleanKey}\n-----END PRIVATE KEY-----`;
    crypto.createPrivateKey(pkcs8Pem);
    return pkcs8Pem;
  } catch {
    return `-----BEGIN RSA PRIVATE KEY-----\n${cleanKey}\n-----END RSA PRIVATE KEY-----`;
  }
}

/**
 * Mengonversi kunci publik RSA raw Base64 ke format PEM standar.
 * @param {string} key - Kunci publik dalam bentuk PEM atau raw Base64
 * @returns {string} Kunci publik terformat PEM
 */
function toPemPublic(key) {
  if (!key || typeof key !== 'string') {
    throw new PaylabsConfigError('Kunci publik RSA harus berupa string non-kosong');
  }
  const cleanKey = key.trim();
  if (cleanKey.includes('BEGIN PUBLIC KEY') || cleanKey.includes('BEGIN RSA PUBLIC KEY')) {
    return cleanKey;
  }
  return `-----BEGIN PUBLIC KEY-----\n${cleanKey}\n-----END PUBLIC KEY-----`;
}

/**
 * Menghasilkan hash SHA-256 huruf kecil dari string input.
 * @param {string} input - Teks yang akan di-hash
 * @returns {string} String hash heksadesimal lowercase
 */
function sha256HexLower(input) {
  return crypto.createHash('sha256').update(input, 'utf8').digest('hex').toLowerCase();
}

/**
 * Menghasilkan pasangan requestId & merchantTradeNo unik berbasis zona Asia/Jakarta.
 * @returns {{ requestId: string, merchantTradeNo: string }}
 */
function genIds() {
  const ts = moment.tz('Asia/Jakarta').format('YYYYMMDDHHmmss');
  const randomSuffix = Math.floor(11111 + Math.random() * 88889);
  const uniqueId = `${ts}${randomSuffix}`;
  return { requestId: uniqueId, merchantTradeNo: uniqueId };
}

/**
 * Menyaring data sensitif (PII) sebelum dicetak ke logger.
 * @param {object} data - Objek yang akan disanitasi
 * @returns {object} Objek hasil sanitasi
 */
function maskSensitiveData(data) {
  if (!data || typeof data !== 'object') return data;
  const clone = Array.isArray(data) ? [...data] : { ...data };
  const sensitiveKeys = new Set([
    'phonenumber', 'phone', 'email', 'payer', 'cardno',
    'customerno', 'accountno', 'virtualaccountno', 'password', 'pin',
  ]);

  for (const [key, value] of Object.entries(clone)) {
    if (sensitiveKeys.has(key.toLowerCase()) && typeof value === 'string') {
      clone[key] = value.length > 4 ? `****${value.slice(-4)}` : '[REDACTED]';
    } else if (value && typeof value === 'object') {
      clone[key] = maskSensitiveData(value);
    }
  }
  return clone;
}

// ==========================================
// KELAS CLIENT UTAMA
// ==========================================

class PaylabsClient {
  /**
   * @param {object} [config={}] - Opsi konfigurasi client
   * @param {'SIT'|'PROD'|'SITCH'} [config.server='SIT'] - Lingkungan server
   * @param {string} [config.mid] - Merchant ID Paylabs
   * @param {string} [config.version='v2.2'] - Versi endpoint API Paylabs
   * @param {boolean} [config.log=false] - Flag debugging log (dengan PII masking)
   * @param {number} [config.timeoutMs=10000] - Batas waktu timeout HTTP request (ms)
   * @param {string} [config.baseUrl] - Kustom base URL bila diperlukan
   * @param {string} [config.privateKey] - Kunci privat RSA (raw Base64 atau PEM)
   * @param {string} [config.publicKey] - Kunci publik RSA (raw Base64 atau PEM)
   */
  constructor(config = {}) {
    this.server = config.server || process.env.PAYLABS_SERVER || process.env.SERVER || 'SIT';
    this.mid = config.mid || process.env.PAYLABS_MID || process.env.MID || null;
    this.version = (config.version || process.env.PAYLABS_VERSION || process.env.VERSION || 'v2.2').replace(/^\/|\/$/g, '');
    this.log = config.log !== undefined ? !!config.log : (process.env.PAYLABS_LOG === 'true');
    this.timeoutMs = Number(config.timeoutMs || process.env.PAYLABS_TIMEOUT_MS || 10000);
    this.customBaseUrl = config.baseUrl || null;

    const privKey = config.privateKey || process.env.PAYLABS_PRIVATE_KEY || process.env.PRIVATE_KEY || null;
    const pubKey = config.publicKey || process.env.PAYLABS_PUBLIC_KEY || process.env.PUBLIC_KEY || null;

    this.privateKey = privKey ? toPemPrivate(privKey) : null;
    this.publicKey = pubKey ? toPemPublic(pubKey) : null;

    this.baseMap = {
      SIT: 'https://sit-pay.paylabs.co.id/payment/',
      PROD: 'https://pay.paylabs.co.id/payment/',
      SITCH: 'https://sitch-pay.paylabs.co.id/payment/',
    };

    // Sub-layanan alias untuk kenyamanan integrasi
    this.payin = this;
  }

  setPrivateKey(key) {
    this.privateKey = toPemPrivate(key);
  }

  setPublicKey(key) {
    this.publicKey = toPemPublic(key);
  }

  generateId() {
    return genIds().requestId;
  }

  getFullUrl(path) {
    if (this.customBaseUrl) {
      const base = this.customBaseUrl.endsWith('/') ? this.customBaseUrl : `${this.customBaseUrl}/`;
      const cleanPath = path.startsWith('/') ? path.slice(1) : path;
      return `${base}${cleanPath}`;
    }
    const baseUrl = this.baseMap[this.server] || this.baseMap.SIT;
    const cleanPath = path.startsWith('/') ? path.slice(1) : path;
    return `${baseUrl}${this.version}/${cleanPath}`;
  }

  buildSignStringForRequest(bodyObj, path, timestamp) {
    const hash = sha256HexLower(JSON.stringify(bodyObj));
    const cleanPath = path.startsWith('/') ? path : `/${path}`;
    const signString = `POST:/payment/${this.version}${cleanPath}:${hash}:${timestamp}`;
    if (this.log) {
      console.log('[PaylabsClient] SignString Request:', signString);
    }
    return signString;
  }

  buildSignStringForCallback(path, bodyRaw, timestamp) {
    const hash = sha256HexLower(bodyRaw);
    const cleanPath = path.startsWith('/') ? path : `/${path}`;
    const signString = `POST:${cleanPath}:${hash}:${timestamp}`;
    if (this.log) {
      console.log('[PaylabsClient] SignString Callback:', signString);
    }
    return signString;
  }

  signBase64(str) {
    if (!this.privateKey) {
      throw new PaylabsConfigError('Private key belum dikonfigurasi pada PaylabsClient');
    }
    const signature = crypto.sign('RSA-SHA256', Buffer.from(str, 'utf8'), this.privateKey);
    return signature.toString('base64');
  }

  verifyBase64(str, signatureB64) {
    if (!this.publicKey) {
      throw new PaylabsConfigError('Public key belum dikonfigurasi pada PaylabsClient');
    }
    const isVerified = crypto.verify(
      'RSA-SHA256',
      Buffer.from(str, 'utf8'),
      this.publicKey,
      Buffer.from(signatureB64, 'base64')
    );
    return !!isVerified;
  }

  /**
   * Mengirim HTTP POST dengan proteksi timeout dan penanganan status non-2xx.
   * @param {string} url - Target URL endpoint
   * @param {object} headers - HTTP request headers
   * @param {object} jsonObj - Payload body
   * @param {number} [timeoutMs] - Override timeout dalam milidetik
   * @returns {Promise<object>} Objek respons ter-parse
   */
  async sendHttp(url, headers, jsonObj, timeoutMs = this.timeoutMs) {
    const controller = new AbortController();
    const timeoutTimer = setTimeout(() => controller.abort(), timeoutMs);

    try {
      const response = await fetch(url, {
        method: 'POST',
        headers,
        body: JSON.stringify(jsonObj),
        signal: controller.signal,
      });

      const responseText = await response.text();
      let parsedBody;
      try {
        parsedBody = JSON.parse(responseText);
      } catch {
        parsedBody = { raw: responseText, status: response.status };
      }

      if (!response.ok && response.status >= 500) {
        throw new PaylabsNetworkError(
          `Paylabs Gateway Error (HTTP ${response.status})`,
          response.status,
          parsedBody
        );
      }
      return parsedBody;
    } catch (error) {
      if (error.name === 'AbortError') {
        throw new PaylabsNetworkError(`Request ke Paylabs melebihi batas waktu timeout (${timeoutMs}ms)`);
      }
      throw error;
    } finally {
      clearTimeout(timeoutTimer);
    }
  }

  /**
   * Mengirim request transaksi ke Paylabs API.
   * @param {string} path - Path endpoint API (misal: '/va/create')
   * @param {object} [body={}] - Parameter spesifik transaksi
   * @param {object} [opts={}] - Opsi kustom (timestamp, requestId, merchantTradeNo, timeoutMs)
   * @returns {Promise<object>} Respons JSON transaksi dari Paylabs
   */
  async request(path, body = {}, opts = {}) {
    if (!this.mid) {
      throw new PaylabsConfigError('Merchant ID (mid) belum dikonfigurasi');
    }

    const timestamp = opts.timestamp || nowJakarta();
    const idPair = genIds();
    const requestId = opts.requestId || idPair.requestId;
    const merchantTradeNo = opts.merchantTradeNo || idPair.merchantTradeNo;

    const payload = {
      merchantId: this.mid,
      requestId,
      merchantTradeNo,
      ...body,
    };

    const signString = this.buildSignStringForRequest(payload, path, timestamp);
    const signature = this.signBase64(signString);

    const headers = {
      'X-TIMESTAMP': timestamp,
      'X-SIGNATURE': signature,
      'X-PARTNER-ID': this.mid,
      'X-REQUEST-ID': requestId,
      'Content-Type': 'application/json;charset=utf-8',
    };

    const targetUrl = this.getFullUrl(path);

    if (this.log) {
      console.log('[PaylabsClient] Target URL:', targetUrl);
      console.log('[PaylabsClient] Request Headers:', headers);
      console.log('[PaylabsClient] Sanitized Payload:', maskSensitiveData(payload));
    }

    return this.sendHttp(targetUrl, headers, payload, opts.timeoutMs);
  }

  /**
   * Mengirim request transaksi berstandar ASPI SNAP BI.
   * Target URL: https://sit-pay.paylabs.co.id/api/v1.0/{path}
   * String to Sign: POST:{path}:{sha256Hex(body)}:{timestamp}
   * @param {string} path - Path endpoint SNAP (misal: '/transfer-va/create-va')
   * @param {object} [body={}] - Payload request berstandar ASPI SNAP
   * @param {object} [opts={}] - Opsi request (externalId, timestamp, ipAddress, timeoutMs)
   * @returns {Promise<object>} Respons JSON dari endpoint SNAP Paylabs
   */
  async requestSnap(path, body = {}, opts = {}) {
    if (!this.mid) {
      throw new PaylabsConfigError('Merchant ID (mid) belum dikonfigurasi');
    }

    const timestamp = opts.timestamp || nowJakarta();
    const externalId = opts.externalId || opts.requestId || genIds().requestId;
    const cleanPath = path.startsWith('/') ? path : `/${path}`;

    const snapBaseMap = {
      SIT: 'https://sit-pay.paylabs.co.id/api/v1.0',
      PROD: 'https://pay.paylabs.co.id/api/v1.0',
      SITCH: 'https://sitch-pay.paylabs.co.id/api/v1.0',
    };
    const snapBase = opts.baseUrl || snapBaseMap[this.server] || snapBaseMap.SIT;
    const targetUrl = `${snapBase}${cleanPath}`;

    const rawBody = JSON.stringify(body);
    const signString = this.buildSignStringForCallback(cleanPath, rawBody, timestamp);
    const signature = this.signBase64(signString);

    const headers = {
      'X-TIMESTAMP': timestamp,
      'X-SIGNATURE': signature,
      'X-PARTNER-ID': this.mid,
      'X-EXTERNAL-ID': externalId,
      'Content-Type': 'application/json;charset=utf-8',
      ...(opts.ipAddress ? { 'X-IP-ADDRESS': opts.ipAddress } : {}),
    };

    if (this.log) {
      console.log('[PaylabsClient SNAP] Target URL:', targetUrl);
      console.log('[PaylabsClient SNAP] Request Headers:', headers);
      console.log('[PaylabsClient SNAP] Sanitized Payload:', maskSensitiveData(body));
    }

    return this.sendHttp(targetUrl, headers, body, opts.timeoutMs);
  }

  /**
   * Memverifikasi tanda tangan callback dari Paylabs.
   * @param {string} path - Path callback webhook (misal: '/transfer-va/payment')
   * @param {string|Buffer} bodyRaw - String mentah atau Buffer isi body callback
   * @param {string} signatureB64 - Nilai dari header 'X-SIGNATURE'
   * @param {string} timestamp - Nilai dari header 'X-TIMESTAMP'
   * @returns {boolean} True jika tanda tangan valid, false jika tidak cocok
   */
  verifySignature(path, bodyRaw, signatureB64, timestamp) {
    if (!path || !bodyRaw || !signatureB64 || !timestamp) {
      return false;
    }
    if (typeof bodyRaw === 'object' && !Buffer.isBuffer(bodyRaw)) {
      throw new PaylabsValidationError(
        'Parameter bodyRaw harus berupa string JSON mentah atau Buffer. Jangan menggunakan objek parsed dari express.json() karena menyebabkan verifikasi gagal.'
      );
    }
    const rawString = Buffer.isBuffer(bodyRaw) ? bodyRaw.toString('utf8') : String(bodyRaw);
    const signString = this.buildSignStringForCallback(path, rawString, timestamp);
    return this.verifyBase64(signString, signatureB64);
  }

  /**
   * Membangun payload respons callback standar Paylabs (errCode: '0').
   * @param {string} path - Path endpoint callback
   * @returns {{ headers: Record<string, string>, body: object }}
   */
  buildResponseCallback(path) {
    const timestamp = nowJakarta();
    const { requestId } = genIds();
    const payload = {
      merchantId: this.mid,
      requestId,
      errCode: '0',
    };
    const signString = this.buildSignStringForRequest(payload, path, timestamp);
    const signature = this.signBase64(signString);

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
   * Membangun payload respons SNAP VA callback berstandar ASPI (responseCode: '2002500').
   * @param {object} data - Data callback SNAP yang diterima
   * @returns {{ headers: Record<string, string>, body: object }}
   */
  buildResponseCallbackSnap(data = {}) {
    const timestamp = nowJakarta();
    const partnerServiceId = data.partnerServiceId || String(this.mid || '').padStart(8, ' ');
    const body = {
      responseCode: '2002500',
      responseMessage: 'Success',
      virtualAccountData: {
        partnerServiceId,
        customerNo: data.customerNo || '',
        virtualAccountNo: data.virtualAccountNo || '',
        virtualAccountName: data.virtualAccountName || '',
        paymentRequestId: data.paymentRequestId || '',
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

  // ==========================================
  // CONVENIENCE METHODS KANAL PEMBAYARAN (PAYIN)
  // ==========================================

  /**
   * Membuat transaksi Virtual Account.
   * @param {object} params
   * @param {string} params.paymentType - Kode bank VA (misal: 'DanamonVA', 'BCAVA', 'MandiriVA')
   * @param {number} params.amount - Nominal pembayaran
   * @param {string} params.productName - Nama transaksi
   */
  async createVA(params = {}) {
    this._validateFields(params, ['paymentType', 'amount', 'productName']);
    return this.request('/va/create', params);
  }

  /**
   * Membuat transaksi Virtual Account berstandar ASPI SNAP BI.
   * Endpoint: /transfer-va/create-va
   * @param {object} params
   * @param {string} params.customerNo - Nomor unik pelanggan / customer ID
   * @param {string} params.virtualAccountNo - Nomor Virtual Account
   * @param {string} params.virtualAccountName - Nama akun VA
   * @param {number|string} params.amount - Nominal pembayaran
   * @param {string} [params.paymentType='BCAVA'] - Kode bank VA
   * @param {string} [params.trxId] - ID transaksi unik merchant
   * @param {object} [opts={}] - Opsi request SNAP (externalId, timestamp, ipAddress)
   * @returns {Promise<object>} Respons pembuatan VA dari SNAP Paylabs
   */
  async createVASnap(params = {}, opts = {}) {
    this._validateFields(params, ['customerNo', 'virtualAccountNo', 'virtualAccountName', 'amount']);
    const trxId = params.trxId || genIds().merchantTradeNo;
    const amountVal = typeof params.amount === 'number' ? params.amount.toFixed(2) : String(params.amount);

    const snapPayload = {
      partnerServiceId: params.partnerServiceId || String(this.mid).padStart(8, ' '),
      customerNo: String(params.customerNo),
      virtualAccountNo: String(params.virtualAccountNo),
      virtualAccountName: params.virtualAccountName,
      trxId,
      totalAmount: {
        value: amountVal,
        currency: params.currency || 'IDR',
      },
      ...(params.virtualAccountEmail ? { virtualAccountEmail: params.virtualAccountEmail } : {}),
      ...(params.virtualAccountPhone ? { virtualAccountPhone: params.virtualAccountPhone } : {}),
      ...(params.billDetails ? { billDetails: params.billDetails } : {}),
      ...(params.expiredDate ? { expiredDate: params.expiredDate } : {}),
      additionalInfo: {
        paymentType: params.paymentType || 'BCAVA',
        ...(params.additionalInfo || {}),
      },
    };

    return this.requestSnap('/transfer-va/create-va', snapPayload, {
      externalId: opts.externalId || trxId,
      ...opts,
    });
  }

  /**
   * Membuat transaksi QRIS Dinamis.
   * @param {object} params
   * @param {number} params.amount - Nominal pembayaran
   * @param {string} params.productName - Nama transaksi
   */
  async createQRIS(params = {}) {
    this._validateFields(params, ['amount', 'productName']);
    return this.request('/qris/create', { paymentType: 'QRIS', ...params });
  }

  /**
   * Membuat transaksi E-Wallet (OVO, DANA, LINKAJA, SHOPEEPAY).
   * @param {object} params
   * @param {string} params.paymentType - Kode E-Wallet
   * @param {number} params.amount - Nominal pembayaran
   * @param {string} params.productName - Nama transaksi
   */
  async createEWallet(params = {}) {
    this._validateFields(params, ['paymentType', 'amount', 'productName']);
    return this.request('/ewallet/create', params);
  }

  /**
   * Membuat tautan pembayaran H5 (Payment Link).
   * @param {object} params
   * @param {number} params.amount - Nominal pembayaran
   * @param {string} params.phoneNumber - Nomor HP pelanggan
   * @param {string} params.productName - Nama produk
   * @param {string} params.redirectUrl - URL pengalihan kembali
   */
  async createH5Link(params = {}) {
    this._validateFields(params, ['amount', 'phoneNumber', 'productName', 'redirectUrl']);
    return this.request('/h5/createLink', params);
  }

  /**
   * Membuat transaksi Kartu Kredit.
   * @param {object} params
   */
  async createCreditCard(params = {}) {
    this._validateFields(params, ['amount', 'productName']);
    return this.request('/cc/create', { paymentType: 'CC', ...params });
  }

  /**
   * Membuat transaksi Direct Debit.
   * @param {object} params
   */
  async createDirectDebit(params = {}) {
    this._validateFields(params, ['paymentType', 'amount', 'productName']);
    return this.request('/dd/create', params);
  }

  /**
   * Membuat transaksi Gerai Retail (Alfamart, Indomaret).
   * @param {object} params
   */
  async createStore(params = {}) {
    this._validateFields(params, ['paymentType', 'amount', 'productName']);
    return this.request('/store/create', params);
  }

  /**
   * Mengecek status transaksi pembayaran.
   * @param {object} params
   * @param {string} [params.merchantTradeNo] - ID transaksi merchant
   * @param {string} [params.requestId] - ID request transaksi
   */
  async queryTransaction(params = {}) {
    if (!params.merchantTradeNo && !params.requestId) {
      throw new PaylabsValidationError('Diperlukan merchantTradeNo atau requestId untuk query transaksi');
    }
    return this.request('/payment/query', params);
  }

  /**
   * Membatalkan transaksi pembayaran.
   * @param {object} params
   * @param {string} params.merchantTradeNo - ID transaksi merchant
   */
  async cancelTransaction(params = {}) {
    this._validateFields(params, ['merchantTradeNo']);
    return this.request('/payment/cancel', params);
  }

  /**
   * Helper internal untuk memvalidasi atribut wajib.
   * @private
   */
  _validateFields(targetObj, fields) {
    for (const field of fields) {
      if (targetObj[field] === undefined || targetObj[field] === null || targetObj[field] === '') {
        throw new PaylabsValidationError(`Parameter wajib tidak boleh kosong: '${field}'`);
      }
    }
  }
}

module.exports = {
  PaylabsClient,
  PaylabsError,
  PaylabsConfigError,
  PaylabsValidationError,
  PaylabsSignatureError,
  PaylabsNetworkError,
  nowJakarta,
  genIds,
  toPemPrivate,
  toPemPublic,
  sha256HexLower,
  maskSensitiveData,
};
