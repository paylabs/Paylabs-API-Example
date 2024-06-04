const https = require('https');
const crypto = require('crypto');
const moment = require('moment-timezone');

// Set the timezone to Asia/Jakarta
moment.tz.setDefault('Asia/Jakarta');

const date = moment().format('YYYY-MM-DDTHH:mm:ss.SSSZ');

const makeSign = async (jsonBody, path) => {
    const shaJson = crypto.createHash('sha256').update(jsonBody).digest('hex');

    const signatureBefore = `POST:${path}:${shaJson}:${date}`;
    
    const signer = crypto.createSign('RSA-SHA256');
    signer.update(signatureBefore);
    const sign = signer.sign(process.env.PRIVATE_KEY, 'base64');
    
    return sign;
}

const createRequest = (path, signature, jsonBody) => {
    return new Promise((resolve, reject) => {
        
        const jsonData = JSON.parse(jsonBody);
        
        const headers = {
            'X-TIMESTAMP': date,
            'X-PARTNER-ID': jsonData.merchantId,
            'X-REQUEST-ID': jsonData.requestId,
            'X-SIGNATURE': signature,
            'Content-Type': 'application/json;charset=utf-8'
        };
        
        const options = {
            hostname: 'sit-pay.paylabs.co.id',
            port: 443,
            path: path,
            method: 'POST',
            headers: headers
        };
        
        const req = https.request(options, res => {
            let resBody = '';
            
            res.on('data', chunk => {
                resBody = resBody + chunk.toString(); 
            });
            
            res.on('end', () => { 
                const body = JSON.parse(resBody);
                resolve(body);
            }); 
        });
        
        req.on('error', error => {
            reject(error);
        });
        
        req.write(jsonBody);
        req.end();
    });
}

const VirtualAccount = (MID, merchantTradeNo, idRequest, channel, amount, productName, payer, notify_url = '') => {
    return JSON.stringify({
        merchantId: MID,
        merchantTradeNo: merchantTradeNo,
        requestId: idRequest,
        paymentType: channel,
        amount: amount,
        productName: productName,
        payer: payer,
        notifyUrl: notify_url
    });
}

module.exports = { makeSign, createRequest, VirtualAccount };
