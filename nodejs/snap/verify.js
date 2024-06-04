const crypto = require('crypto');

const timestamp = '2024-04-22T17:32:09+07:00';
const signature = 'QtiXA6abK2ZgObY/N8NCEGy0WaACjYQVNSrdTZB1Hxp3Y/hDAALdeRIzJL7UctJxwO3D5lQt/mpfVuoKBb6MHIOd2uNzvHkPTAwjnE+nGRF1QkAbvXmVmzYJgPxJG94AZ4Qrl8KbOV6X5Ffk/Rk024gmBOLmOuazz/sHl2BBIPBQEGU+JJOEaL0Gdk77L0hVkKpRTOU5V84hxAjx4JXYPrK/tFgYu3a9CqQV8kbHOnREqXsel16XAoNvbCoE8mTS9xl2JYMkfiRH8jmScY711KzuXgVDCPOnBuKbs/L928kNUIQXwym28/C+EEEhO08C5yobsKcPB6VmildqeNVg2g==';
const dataToSign = '{"partnerServiceId":"010366","customerNo":"34534534","virtualAccountNo":"1467578669465654","virtualAccountName":"Jokul Doe","virtualAccountEmail":"jokul@email.com","virtualAccountPhone":"6281828384858","trxId":"56068807","paymentRequestId":"2024042236600000019","channelCode":630,"paidAmount":{"value":"15000.00","currency":"IDR"},"totalAmount":{"value":"15000.00","currency":"IDR"},"trxDateTime":"2024-04-22T16:39:53+07:00","referenceNo":"2024042236600000019","billDetails":[{"billCode":"01","billNo":"123456789012345678","billName":"Bill A for Jan","billShortName":"Bill A","billDescription":{"english":"Maintenance","indonesia":"Pemeliharaan"},"billSubCompany":"00001","billAmount":{"value":"15000.00","currency":"IDR"},"additionalInfo":{}}],"freeTexts":[{"english":"Free text","indonesia":"Tulisan bebas"}],"additionalInfo":{"channel":"mobilephone","deviceId":"12345679237","paymentType":"BCAVA"}}';
const publicKey = process.env.PUBLIC_KEY;
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
