package main

import (
	"crypto"
	"crypto/rsa"
	"crypto/sha256"
	"crypto/x509"
	"encoding/base64"
	"encoding/pem"
	"fmt"
	"log"
)

func main() {
	timestamp := "2024-06-20T15:48:19+07:00"
	signature := "fMVRZqpsERjwYETVKzDAJAYgojcNDGI+UbqDjyTyH5LR/tXj+8cvI34lZHIQ9LxUbxFBHeNs7KlQRV5b1jalTsoZM4jVOVR2mP2WHYI3eo6lH+abbMkSNdrkP1xm7tYWbj3aVuDMByUDuEKb9lgo1AKnK/Spg3NWt0W+JFeCyj8PDit67wgfiM0xulzXKE0KWAjemJNkOUwovHGn/8GSqlhIrT0zy2QwUdTPFhthItF0Lv8boyJXnhw+5b7PPANhMsc1w6/EqLfgxlNDYaXxfrL265hbPvEPMZRmbcvncBGbZDbk8GC2KBJPhgJ9zZ67WVQMLd6lvYjxmRlgqNaiWg=="
	dataToSign := `{"partnerServiceId":"010422","customerNo":"16449827796527518018","virtualAccountNo":"0007798428761030","virtualAccountName":"FOR","trxId":"d80f44300b31974fa1a0da46e","paymentRequestId":"2024062042200000032","channelCode":450,"paidAmount":{"value":"10000.00","currency":"IDR"},"totalAmount":{"value":"10000.00","currency":"IDR"},"trxDateTime":"2024-06-20T14:56:15+07:00","referenceNo":"2024062042200000032","billDetails":[{"billAmount":{"value":"10000.00","currency":"IDR"}}],"additionalInfo":{"paymentType":"BRIVA"}}`
	publicKeyStr := `-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAgQ7RuKGXaiWXUDAmMZBBQwYkcmL1Pwjs3bVoQlXuOKBsoHVYbT2zLpUlhmPH8ACsILRM0VLc+LPm6LmsP2Ps/H/Mkw+juXBwDuax9M5+4N4izIBWdPCNkaOhk0O23T2tciBVbAdEfOHHKeTPyeoKNaS8TUHuQNudp+rKb64KWf9H/DBtIpwmFZWpSDK8aYMGS+8c3kCZ4j2lLmPssJ1so+F1anIQWX5Kc6NL3/D7SgmeLJkJEITauPlP3EXsAHKEp23F0NpEiMIFW2Be7azdX6y4ZTfTBDJ4albmNJOjJhYrwjDKGTd9Mvp6SPBPj6IlRa/Sn8y/3jxYIKO5bXOC3QIDAQAB
-----END PUBLIC KEY-----`

	// Decode base64 signature
	binarySignature, err := base64.StdEncoding.DecodeString(signature)
	if err != nil {
		log.Fatalf("Failed to decode signature: %v", err)
	}

	// Compute SHA-256 hash of dataToSign
	hash := sha256.Sum256([]byte(dataToSign))
	shaJson := fmt.Sprintf("%x", hash)
	signatureAfter := fmt.Sprintf("POST:/transfer-va/payment:%s:%s", shaJson, timestamp)
	fmt.Println(signatureAfter)

	// Parse the public key
	block, _ := pem.Decode([]byte(publicKeyStr))
	if block == nil || block.Type != "PUBLIC KEY" {
		log.Fatalf("Failed to decode PEM block containing public key")
	}
	pubKey, err := x509.ParsePKIXPublicKey(block.Bytes)
	if err != nil {
		log.Fatalf("Failed to parse public key: %v", err)
	}
	rsaPubKey, ok := pubKey.(*rsa.PublicKey)
	if !ok {
		log.Fatalf("Not an RSA public key")
	}

	// Verify the signature
	hashed := sha256.Sum256([]byte(signatureAfter))
	err = rsa.VerifyPKCS1v15(rsaPubKey, crypto.SHA256, hashed[:], binarySignature)
	if err != nil {
		fmt.Println("Signature ini invalid.")
	} else {
		fmt.Println("Signature ini valid.")
	}
}
