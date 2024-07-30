# Paylabs API Example
This repository will help you understand how to integrate with the Paylabs API.

## How It Works
Make sure you have a Merchant Id & private key registered with Paylabs.

After that, enter the Merchant ID & Private key in the program that you will use, maybe each code has its own method, some are in the .env, some are in the run file

## Installation

### Python
Make sure you have installed the required libraries, or you can do it with the command below
```
pip install -r requirements.txt
```

###  Node.Js
```javascript
npm install
```

### Java
We created this sample code with Java version 21.0.1

Environment payin :
```env
merchantId=010001
privateKey=MIIEvxxxxx
url=https://sit-pay.paylabs.co.id
version=v2.1
```

Environment payout :
```env
merchantId=010001
privateKey=MIIEvxxxxx
url=https://sit-remit-api.paylabs.co.id
version=v1.2
```

Don't forget to setup MID & Private Key
