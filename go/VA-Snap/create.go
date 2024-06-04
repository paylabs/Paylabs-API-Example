package main

import (
	"bytes"
	"crypto"
	"crypto/rand"
	"crypto/rsa"
	"crypto/sha256"
	"crypto/x509"
	"encoding/base64"
	"encoding/json"
	"encoding/pem"
	"fmt"
	mrand "math/rand" // Alias to avoid conflict with rand package
	"net/http"
	"time"
)

func main() {
	// Set the timezone
	time.Local = time.FixedZone("Asia/Jakarta", 7*60*60)

	// Define the request data
	currentTime := time.Now().UTC().Add(7 * time.Hour) // UTC +07:00
	date := currentTime.Format("2006-01-02T15:04:05.999+07:00")
	merchantId := "" //MID
	idRequest := fmt.Sprintf("%d", mrand.Intn(99999999-11111111)+11111111)
	merchantTradeNo := fmt.Sprintf("%d", mrand.Intn(99999999-11111111)+11111111)

	jsonBody := map[string]interface{}{
		"partnerServiceId":    idRequest,
		"customerNo":          "34534534",
		"virtualAccountNo":    "14675773355545",
		"virtualAccountName":  "Jokul Doe",
		"virtualAccountEmail": "jokul@email.com",
		"virtualAccountPhone": "6281234567890",
		"trxId":               merchantTradeNo,
		"totalAmount": map[string]interface{}{
			"value":    "15000.00",
			"currency": "IDR",
		},
		"billDetails": []map[string]interface{}{
			{
				"billCode":       "01",
				"billNo":         "123456789012345678",
				"billName":       "Bill A for Jan",
				"billShortName":  "Bill A",
				"billDescription": map[string]string{
					"english":   "Maintenance",
					"indonesia": "Pemeliharaan",
				},
				"billSubCompany": "00001",
				"billAmount": map[string]interface{}{
					"value":    "15000.00",
					"currency": "IDR",
				},
				"additionalInfo": map[string]interface{}{},
			},
		},
		"freeTexts": []map[string]string{
			{
				"english":   "Free text",
				"indonesia": "Tulisan bebas",
			},
		},
		"virtualAccountTrxType": "1",
		"feeAmount": map[string]interface{}{
			"value":    "15000.00",
			"currency": "IDR",
		},
		"expiredDate": "2025-03-29T23:59:59-07:00",
		"additionalInfo": map[string]interface{}{
			"deviceId":    "12345679237",
			"channel":     "mobilephone",
			"paymentType": "BCAVA",
		},
	}
	
	path := "/transfer-va/create-va"

	// Encode JSON body
	jsonData, err := json.Marshal(jsonBody)
	if err != nil {
		panic(err)
	}

	// Hash the JSON body
	shaJson := sha256.Sum256(jsonData)

	// Define the private key in PEM format
	privateKeyPEM := `-----BEGIN RSA PRIVATE KEY-----
	MERCHANT PRIVATE KEY
-----END RSA PRIVATE KEY-----
`

	// Parse the PEM encoded private key
	blockPrivate, _ := pem.Decode([]byte(privateKeyPEM))
	privateKey, err := x509.ParsePKCS1PrivateKey(blockPrivate.Bytes)
	if err != nil {
		panic(err)
	}

	// Construct signature before hashing
	signatureBefore := fmt.Sprintf("POST:%s:%x:%s", path, shaJson, date)
	fmt.Printf(signatureBefore)

	// Hash the signature before
	h := sha256.New()
	h.Write([]byte(signatureBefore))
	hashed := h.Sum(nil)

	// Sign the hash
	signature, err := rsa.SignPKCS1v15(rand.Reader, privateKey, crypto.SHA256, hashed)
	if err != nil {
		panic(err)
	}

	// Base64 encode the signature
	signatureB64 := base64.StdEncoding.EncodeToString(signature)

	// Create HTTP headers
	headers := map[string]string{
		"Content-Type": "application/json;charset=utf-8",
		"X-TIMESTAMP":  date,
		"X-SIGNATURE":  signatureB64,
		"X-PARTNER-ID": merchantId,
		"X-EXTERNAL-ID": idRequest,
		"X-IP-ADDRESS": "172.24.281.24",
	}

	// Send HTTP request
	url := "https://sit-pay.paylabs.co.id/api/v1.0" + path
	req, err := http.NewRequest("POST", url, bytes.NewBuffer(jsonData))
	if err != nil {
		panic(err)
	}
	for key, value := range headers {
		req.Header.Set(key, value)
	}
	client := &http.Client{}
	resp, err := client.Do(req)
	if err != nil {
		panic(err)
	}
	defer resp.Body.Close()

	// Decode response
	var response map[string]interface{}
	err = json.NewDecoder(resp.Body).Decode(&response)
	if err != nil {
		panic(err)
	}

	// Print response
	fmt.Printf("%+v\n", response)
}
