const https = require('https');
const crypto = require('crypto');
const moment = require('moment-timezone');
require('dotenv').config();

// Set the timezone to Asia/Jakarta
moment.tz.setDefault('Asia/Jakarta');

const private_key = `-----BEGIN RSA PRIVATE KEY-----
${process.env.MID}
-----END RSA PRIVATE KEY-----`;

const date = moment().format('YYYY-MM-DDTHH:mm:ss.SSSZ');
const MID = process.env.MID;
const idRequest = Math.floor(Math.random() * (99999999 - 11111111) + 11111111).toString();
const merchantTradeNo = Math.floor(Math.random() * (99999999 - 11111111) + 11111111).toString();
console.log(date);

const jsonBody = JSON.stringify({
  partnerServiceId: idRequest,
  customerNo: "34534534",
  virtualAccountNo: "14675773355545",
  virtualAccountName: "Jokul Doe",
  virtualAccountEmail: "jokul@email.com",
  virtualAccountPhone: "6281828384858",
  trxId: merchantTradeNo,
  totalAmount: { value: "15000.00", currency: "IDR" },
  billDetails: [{
    billCode: "01",
    billNo: "123456789012345678",
    billName: "Bill A for Jan",
    billShortName: "Bill A",
    billDescription: { english: "Maintenance", indonesia: "Pemeliharaan" },
    billSubCompany: "00001",
    billAmount: { value: "15000.00", currency: "IDR" },
    additionalInfo: {}
  }],
  freeTexts: [{ english: "Free text", indonesia: "Tulisan bebas" }],
  virtualAccountTrxType: "1",
  feeAmount: { value: "12345678.00", currency: "IDR" },
  expiredDate: "2024-12-29T23:59:59-07:00",
  additionalInfo: { deviceId: "12345679237", channel: "mobilephone", paymentType: "BCAVA" }
});

console.log(jsonBody + "\r\n\r\n");

const shaJson = crypto.createHash('sha256').update(jsonBody).digest('hex');

const signatureBefore = `POST:/transfer-va/create-va:${shaJson}:${date}`;
console.log(signatureBefore + "\r\n\r\n");

const signer = crypto.createSign('RSA-SHA256');
signer.update(signatureBefore);
const signature = signer.sign(private_key, 'base64');

const headers = {
  'X-TIMESTAMP': date,
  'X-PARTNER-ID': MID,
  'X-EXTERNAL-ID': idRequest,
  'X-SIGNATURE': signature,
  'X-IP-ADDRESS': '103.255.243.34',
  'Content-Type': 'application/json;charset=utf-8'
};

console.log(headers);

const options = {
  hostname: 'sit-pay.paylabs.co.id',
  port: 443,
  path: '/api/v1.0/transfer-va/create-va',
  method: 'POST',
  headers: headers
};

const req = https.request(options, res => {
  console.log(`statusCode: ${res.statusCode}`);
  
  res.on('data', d => {
    process.stdout.write(d);
  });
});

req.on('error', error => {
  console.error(error);
});

req.write(jsonBody);
req.end();
