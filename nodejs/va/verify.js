const crypto = require('crypto');
require('dotenv').config();

/* timestamp, signature & dataToSign : from callback paylabs */
const timestamp = '';
const signature = '';
const dataToSign = '';

const publicKey = process.env.PUBLIC_KEY;
const binary_signature = Buffer.from(signature, 'base64');

const shaJson = crypto.createHash('sha256').update(dataToSign).digest('hex');
const signatureAfter = `POST:/:${shaJson}:${timestamp}`;
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
