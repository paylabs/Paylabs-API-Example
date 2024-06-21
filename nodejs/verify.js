const crypto = require('crypto');

const timestamp = '2024-06-10T10:17:20.024+07:00';
const signature = 'bTQBfMxFbgie0EGVYzdyT40hvTb5iDjXsw8+2u3fWlCb0zkUHqXj6tjLwrgku3iUkR0iDloimHWGf8It6McO+wM/Ak+1hAOl4k5N5Lkote0B0ltiZ9D5LrVx0yDRGmf8yDkIqRHi6UiiQnIgnwQJvW2cPuuGEaiMP/vgON+R/2T2trvZVTAnhWtt5ondzLIFDxWgFqZEXrj0fYYlwyzCwSAmQY8SFPptPcuKvjQ8xzfm1iabPtzJFVfzpkpG424z9S/EywtkljppibZVRXRHuve01ihdzIvlrfjWuTn5dLj3ttbfOukxE652i16MZVaRwfsRsQDWLRydYvkPjQzenA==';
const dataToSign = '{"merchantId":"010332","requestId":"N01033220240610332000000031717989440023","errCode":"0","paymentType":"PermataVA","amount":"162933.00","createTime":"20240610101423","successTime":"20240610101525","merchantTradeNo":"202406100314239776","platformTradeNo":"2024061033200000003","status":"02","paymentMethodInfo":{"vaCode":"9999900000000003"},"productName":"Wireless Mouse"}';
const publicKey = `-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAmHUFQPYmz2SZMYltz+H4E2BXBr5WfVNQdndCtvhW38D7JnewEqnRlDkGDhIjKezznrE8eG02lBG87CV+xSjiQeR4jyQeVb14XcVCMHzGD0Z88RH4UDnHZFPl6mlFr5FB3Q3O9nX/hYp8JGPOUCVbKVxz9BpZyhhxBtjkiuGPx6GRmSNetC03XJ59rC8HvPr5N6krcuIZg3Fwu2PCepDj4kjK5mf5R09VeQaI4gr+Bg/qvHRwxZtK/RLyXSmXtV9huWO5J18hsjmT3tClzX5bO/jcY8wx0t3stQr8B6x2UaJOcYUyE733Y0jxDCdWgU4yg0H6Ygc3YitZzMg1vnMIzwIDAQAB
-----END PUBLIC KEY-----`;

const binary_signature = Buffer.from(signature, 'base64');

const shaJson = crypto.createHash('sha256').update(dataToSign).digest('hex');
const signatureAfter = `POST:/payment-service/webhook/external/notify/va:${shaJson}:${timestamp}`;
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
