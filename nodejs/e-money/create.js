const https = require('https');
const crypto = require('crypto');
const moment = require('moment-timezone');
require('dotenv').config();

// Set the timezone to Asia/Jakarta
moment.tz.setDefault('Asia/Jakarta');

const private_key = `-----BEGIN RSA PRIVATE KEY-----
${process.env.PRIVATE_KEY}
-----END RSA PRIVATE KEY-----`;

const date = moment().format('YYYY-MM-DDTHH:mm:ss.SSSZ');
const MID = process.env.MID;
const idRequest = Math.floor(Math.random() * (99999999 - 11111111) + 11111111).toString();
const merchantTradeNo = Math.floor(Math.random() * (99999999 - 11111111) + 11111111).toString();
const path = "/payment/v2/ewallet/create";
console.log(date);

const jsonBody = JSON.stringify({
    merchantId: MID,
    merchantTradeNo: merchantTradeNo,
    requestId: idRequest,
    paymentType: "SHOPEEBALANCE",
    amount: "15000",
    productName: "Sepeda",
    notifyUrl: "https://webhook.site/25f50679-363a-4c34-81e0-17e4c4074a9c",
    paymentParams: {
      redirectUrl: "http://google.com"
    }
});

console.log(jsonBody + "\r\n\r\n");

const shaJson = crypto.createHash('sha256').update(jsonBody).digest('hex');

const signatureBefore = `POST:${path}:${shaJson}:${date}`;
console.log(signatureBefore + "\r\n\r\n");

const signer = crypto.createSign('RSA-SHA256');
signer.update(signatureBefore);
const signature = signer.sign(private_key, 'base64');

const headers = {
  'X-TIMESTAMP': date,
  'X-PARTNER-ID': MID,
  'X-REQUEST-ID': idRequest,
  'X-SIGNATURE': signature,
  'Content-Type': 'application/json;charset=utf-8'
};

console.log(headers);

const options = {
  hostname: 'sit-pay.paylabs.co.id',
  port: 443,
  path: path,
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
