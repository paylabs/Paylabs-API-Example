const crypto = require('crypto');

const timestamp = '2024-04-23T10:08:30+07:00';
const signature = 'OL7olwXdBkE9VKw6aQMa5aJ5T1kfkea0HSQP+TXn1trMbtLjXAKnJitQLRG2l9GRdqBYmKmhWkksVpi0J3UKNjMscrTnLLmVfV3R/N+NemIb4BrGDUcVdCfG5fWCLCyFYgP5aaOk36ruu270Bibp+C2Tb/M7Lm6AKJR0dS8fbQ/I+f/cCX3KumGv4WqUHECxmQjS/sXwBOBRqczZ4WB+RwTamFXMI29IgKavRA3STbNrucxHHjGzOdvHUBVN7GwBILT03nePIisIqrXhvsy1xEHm80G89+aJwQHkNY1SdjJvYx2f9TMnaUx03gdnKjApOwe/g4x+WCx8gxoJuXXRrQ==';
const dataToSign = '{"partnerServiceId":"010417","customerNo":"010417081231551231","virtualAccountNo":"1467571390516591","virtualAccountName":"RMA001","trxId":"USER-12037773911245","paymentRequestId":"2024042341700000003","channelCode":630,"paidAmount":{"value":"30000.00","currency":"IDR"},"totalAmount":{"value":"30000.00","currency":"IDR"},"trxDateTime":"2024-04-23T10:06:34+07:00","referenceNo":"2024042341700000003","additionalInfo":{"paymentType":"BCAVA"}}';
const publicKey = `-----BEGIN PUBLIC KEY-----
PAYLABS PUBLIC KEY
-----END PUBLIC KEY-----`;

const binary_signature = Buffer.from(signature, 'base64');

const shaJson = crypto.createHash('sha256').update(dataToSign).digest('hex');
const signatureAfter = `POST:/transfer-va/payment:${shaJson}:${timestamp}`;
console.log(signatureAfter);

const verificationResult = crypto.verify(
    'sha256',
    Buffer.from(signatureAfter),
    publicKey,
    binary_signature
);

if (verificationResult) {
    console.log("Signature ini valid.");
} else {
    console.log("Signature ini invalid.");
}
