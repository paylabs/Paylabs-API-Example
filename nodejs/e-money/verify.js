const crypto = require('crypto');
require('dotenv').config();

/* timestamp, signature & dataToSign : from callback paylabs */
const timestamp = '2024-05-02T11:14:09.218+07:00';
const signature = 'jEXuMnud0UrDGALkbKbZuqrlP4b0FrRgKC5mizlcIj1aoWEMoO2zQpq/oGWq1icOOqCp42oDdG3+pb3t6WZVGZ12N2p7SVRHITZf657tP692oaWkOnyTkkVGSu3fN7SP50HxeOhDQT+zQ1poDW/H4KC6nYFaTx/B0LK8vms7VsrMEJ4LJ0QSOMw2U/oltgSxlyEWaEx8jD9+PFJ28aHU6uUOJGA3CsnMrV5O61F2uJAIMR2A0psrIpVNRvJsHFEydl0+N4H+zT3X2Khqag7K8FgYC0N2uBgquxpuTMkyDVQ7nzmwR2S8WNj1Lc0vJBkYZVpfu3nFuw9IQA3SxXQcqA==';
const dataToSign = '{"merchantId":"010366","requestId":"N01036620240502366000000071714623249218","errCode":"0","paymentType":"SHOPEEBALANCE","amount":"15000.00","createTime":"20240502111213","successTime":"20240502111314","merchantTradeNo":"66911793","platformTradeNo":"2024050236600000007","status":"02","paymentMethodInfo":{},"productName":"Sepeda"}';

const publicKey = process.env.PUBLIC_KEY;
const binary_signature = Buffer.from(signature, 'base64');

const shaJson = crypto.createHash('sha256').update(dataToSign).digest('hex');
const signatureAfter = `POST:/25f50679-363a-4c34-81e0-17e4c4074a9c:${shaJson}:${timestamp}`;
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
