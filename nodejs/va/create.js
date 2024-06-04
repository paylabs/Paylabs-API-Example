require('dotenv').config();

const { makeSign, createRequest, VirtualAccount } = require('../core/Paylabs');

const idRequest = Math.floor(Math.random() * (99999999 - 11111111) + 11111111).toString();
const merchantTradeNo = Math.floor(Math.random() * (99999999 - 11111111) + 11111111).toString();
const path = "/payment/v2/va/create";

const jsonBody = VirtualAccount(process.env.MID, merchantTradeNo, idRequest, "MaybankVA", "15000", "Sepeda", "Irfan", process.env.NOTIFY_URL);
console.log(jsonBody + "\r\n\r\n");

async function run() {
  try {
      const signature = await makeSign(jsonBody, path);
      console.log(signature + "\r\n\r\n");

      const result = await createRequest(path, signature, jsonBody);
      console.log(result);
  } catch (error) {
      console.error(error);
  }
}

run();