/**
 * @file Paylabs.test.js
 * @description Unit test komprehensif untuk PaylabsClient dan modul pendukung.
 */

'use strict';

const test = require('node:test');
const assert = require('node:assert/strict');
const crypto = require('crypto');
const {
  PaylabsClient,
  PaylabsConfigError,
  PaylabsValidationError,
  nowJakarta,
  genIds,
  toPemPrivate,
  toPemPublic,
  sha256HexLower,
  maskSensitiveData,
} = require('../src/Paylabs');

// Generate pasangan kunci RSA untuk testing
const { privateKey: rsaPrivKey, publicKey: rsaPubKey } = crypto.generateKeyPairSync('rsa', {
  modulusLength: 2048,
});
const privPem = rsaPrivKey.export({ type: 'pkcs8', format: 'pem' });
const pubPem = rsaPubKey.export({ type: 'spki', format: 'pem' });
const rawPriv = privPem.replace(/-----BEGIN PRIVATE KEY-----|-----END PRIVATE KEY-----|\r|\n/g, '');
const rawPub = pubPem.replace(/-----BEGIN PUBLIC KEY-----|-----END PUBLIC KEY-----|\r|\n/g, '');

test('nowJakarta() should produce ISO 8601 timestamp with +07:00 timezone', () => {
  const ts = nowJakarta();
  assert.ok(typeof ts === 'string');
  assert.match(ts, /^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\.\d{3}\+07:00$/);
});

test('genIds() should generate matching requestId and merchantTradeNo based on Asia/Jakarta', () => {
  const ids = genIds();
  assert.equal(ids.requestId, ids.merchantTradeNo);
  assert.ok(ids.requestId.length >= 19);
});

test('toPemPrivate() should handle raw Base64 and wrap into valid PEM format', () => {
  const pem = toPemPrivate(rawPriv);
  assert.ok(pem.includes('BEGIN PRIVATE KEY') || pem.includes('BEGIN RSA PRIVATE KEY'));
  assert.doesNotThrow(() => crypto.createPrivateKey(pem));
});

test('toPemPublic() should handle raw Base64 and wrap into valid PEM format', () => {
  const pem = toPemPublic(rawPub);
  assert.ok(pem.includes('BEGIN PUBLIC KEY'));
  assert.doesNotThrow(() => crypto.createPublicKey(pem));
});

test('maskSensitiveData() should mask PII fields while keeping others intact', () => {
  const raw = {
    merchantId: '010366',
    phoneNumber: '081234567890',
    payer: 'Budi Santoso',
    amount: 50000,
  };
  const masked = maskSensitiveData(raw);
  assert.equal(masked.merchantId, '010366');
  assert.equal(masked.amount, 50000);
  assert.equal(masked.phoneNumber, '****7890');
  assert.equal(masked.payer, '****toso');
});

test('PaylabsClient should sign and verify digital signature successfully', () => {
  const client = new PaylabsClient({
    server: 'SIT',
    mid: '010366',
    privateKey: privPem,
    publicKey: pubPem,
  });

  const payload = { amount: 10000, productName: 'Kopi' };
  const signString = client.buildSignStringForRequest(payload, '/va/create', '2026-10-22T15:00:00.000+07:00');
  const signature = client.signBase64(signString);

  assert.ok(signature.length > 50);
  const isValid = client.verifyBase64(signString, signature);
  assert.equal(isValid, true);
});

test('verifySignature() should throw PaylabsValidationError if passed a parsed JS object', () => {
  const client = new PaylabsClient({ mid: '010366', publicKey: pubPem, privateKey: privPem });
  assert.throws(
    () => client.verifySignature('/callback', { foo: 'bar' }, 'dummy-sig', '2026-10-22T15:00:00.000+07:00'),
    PaylabsValidationError
  );
});

test('verifySignature() should verify valid raw callback string', () => {
  const client = new PaylabsClient({ mid: '010366', publicKey: pubPem, privateKey: privPem });
  const rawBody = JSON.stringify({ status: '02', merchantTradeNo: 'TRX123' });
  const timestamp = '2026-10-22T15:00:00.000+07:00';
  const path = '/callback/paylabs';

  const signString = client.buildSignStringForCallback(path, rawBody, timestamp);
  const signature = client.signBase64(signString);

  const isVerified = client.verifySignature(path, rawBody, signature, timestamp);
  assert.equal(isVerified, true);
});

test('buildResponseCallback() should return valid standard acknowledgment payload', () => {
  const client = new PaylabsClient({ mid: '010366', privateKey: privPem });
  const response = client.buildResponseCallback('/callback/paylabs');

  assert.equal(response.body.errCode, '0');
  assert.equal(response.body.merchantId, '010366');
  assert.ok(response.headers['X-SIGNATURE']);
  assert.ok(response.headers['X-TIMESTAMP']);
});

test('buildResponseCallbackSnap() should return valid ASPI SNAP responseCode 2002500', () => {
  const client = new PaylabsClient({ mid: '010366', privateKey: privPem });
  const snapData = {
    customerNo: '00001',
    virtualAccountNo: '1467575170245378',
    virtualAccountName: 'Budi',
    paymentRequestId: 'REQ123',
  };
  const response = client.buildResponseCallbackSnap(snapData);

  assert.equal(response.body.responseCode, '2002500');
  assert.equal(response.body.responseMessage, 'Success');
  assert.equal(response.body.virtualAccountData.virtualAccountNo, '1467575170245378');
  assert.ok(response.headers['X-TIMESTAMP']);
});

test('Convenience methods should validate mandatory parameters', async () => {
  const client = new PaylabsClient({ mid: '010366', privateKey: privPem });

  await assert.rejects(
    () => client.createVA({ amount: 5000 }),
    /Parameter wajib tidak boleh kosong: 'paymentType'/
  );

  await assert.rejects(
    () => client.createQRIS({}),
    /Parameter wajib tidak boleh kosong: 'amount'/
  );

  await assert.rejects(
    () => client.queryTransaction({}),
    /Diperlukan merchantTradeNo atau requestId/
  );

  await assert.rejects(
    () => client.createVASnap({ customerNo: '123' }),
    /Parameter wajib tidak boleh kosong: 'virtualAccountNo'/
  );
});

