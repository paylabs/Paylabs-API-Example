const dotenv = require("dotenv");
const { PaylabsClient } = require("./src/Paylabs");

dotenv.config();

const paylabs = new PaylabsClient({
  server: 'SIT',
  mid: process.env.MID,
  version: 'v2.2',
  log: true,
  privateKey: process.env.PRIVATE_KEY,
  publicKey: process.env.PUBLIC_KEY
});

const generateVA = async () => {
  try {
    const response = await paylabs.request('/va/create', {
      paymentType: "DanamonVA",
      amount: 50000,
      productName: "Sepatu",
      payer: "John",
      notifyUrl: "https://webhook.site/4f66d1a6-72af-4efa-b202-4301c453fcc0",
    });
    console.log(response);
  } catch (error) {
    console.error(error);
  }
}

const verifyCallback = async () => {
  try {
  
    const timestamp = '2025-03-18T14:45:43.215+07:00';
    const signature = 'Gx9A2BFfoPQvhpqlEtSiku3n/X8I0aWXUaSE4BfKnClozlZoKJ3DyJ5Ofs1ggWiL/4QiUQqGbBGK0yyjPiEPxByBTCaMESMr2ZhIQDIHR3K26FrP/m/BwYjF3pVNO1iZ10gjMAPF4TFRQuuj5HbkBLOn2ZCMDPgw0AfHi3P/e03FiZdj3B7cskn5VlOgw35YtqNbEWrpVBc9X6UpKkCss5y6/aQ2LVh02fVLYKkimDN8J5M3dC1Mmf4KkOjuoAB0OfQVUXo6nr7dirbUxng9q8UkmVWInACQX/wfulbZQWdAaxbLYjE6w+Nq+kNdS6HQITWUTyUuQponZbP8m5KOLg==';
    const dataToSign = '{"merchantId":"011043","requestId":"N01104320250318043000295061742283943212","errCode":"0","paymentType":"QRIS","amount":"1000.00","createTime":"20250318135334","successTime":"20250318135347","merchantTradeNo":"2025031848494948528131250001","platformTradeNo":"2025031804300029506","status":"02","paymentMethodInfo":{"issuerId":"93600914","nmid":"ID2025375490625","payer":"GOPAY                         ","rrn":"1bg3go439159"},"productName":"Testing","productInfo":[{"id":"99","name":"TEST","price":1000.00,"type":"?","quantity":1}],"transFeeRate":"0.007000","transFeeAmount":"0.00","totalTransFee":"7.00","vatFee":"0"}';

    const verify = paylabs.verifySignature(`/paylabs/qris/notify`, dataToSign, signature, timestamp);
    
    if ( verify ) {
      const reply = paylabs.buildResponseCallback('/callback/va');
    }
  } catch (error) {
    console.error(error);
  }
}

/**
 * Contoh pembuatan Virtual Account berstandar ASPI SNAP BI.
 * Endpoint: /api/v1.0/transfer-va/create-va
 */
const generateVASnap = async () => {
  try {
    console.log('\n--- CONTOH GENERATE VA SNAP BI ---');
    const response = await paylabs.createVASnap({
      customerNo: '34534534',
      virtualAccountNo: '14675773355545',
      virtualAccountName: 'Jokul Doe',
      amount: 15000,
      paymentType: 'BCAVA',
      virtualAccountEmail: 'jokul@email.com',
      virtualAccountPhone: '6281828384858',
      billDetails: [
        {
          billCode: '01',
          billNo: '123456789012345678',
          billName: 'Pembayaran Tagihan',
          billAmount: { value: '15000.00', currency: 'IDR' },
        },
      ],
    });
    console.log('[Response VA SNAP]:', response);
  } catch (error) {
    console.error('[Error VA SNAP]:', error.message || error);
  }
};

verifyCallback();

generateVA();

generateVASnap();