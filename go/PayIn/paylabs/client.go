// FILE: paylabs/client.go
// ASAL : paylabs/client.py
//
// Perubahan penting:
// - Class PaylabsClient di Python dikonversi menjadi struct PaylabsClient di Go
//   dengan method yang ekuivalen.
// - Penggunaan requests (Python) diganti dengan net/http (Go).
// - Kriptografi (RSA + SHA256) menggunakan crypto/rsa, crypto/x509, encoding/pem, encoding/base64.
// - Error handling diubah ke pola idiomatik Go: (value, error).

package paylabs

import (
    "crypto"
    "crypto/rand"
    "crypto/rsa"
    "crypto/sha256"
    "crypto/x509"
    "encoding/base64"
    "encoding/hex"
    "encoding/json"
    "encoding/pem"
    "errors"
    "fmt"
    "io"
    "math/big"
    "net/http"
    "strings"
    "time"
)

// baseURL setara dengan konstanta BASE_URL di Python.
var baseURL = map[string]string{
    "SIT":  "https://sit-pay.paylabs.co.id/payment/",
    "PROD": "https://pay.paylabs.co.id/payment/",
}

// Config berisi konfigurasi yang sebelumnya diberikan sebagai **kwargs di Python.
type Config struct {
    Server     string
    Mid        string
    Version    string
    PrivateKey string // base64 body, tanpa header/footer PEM
    PublicKey  string // base64 body, tanpa header/footer PEM
    Log        bool
    HTTPClient *http.Client
}

// PaylabsClient adalah representasi Go dari class PaylabsClient di Python.
type PaylabsClient struct {
    Server          string
    Mid             string
    Version         string
    PrivateKeyRaw   string
    PublicKeyRaw    string
    Log             bool
    Date            string
    RequestID       string
    MerchantTradeNo string

    privateKey *rsa.PrivateKey
    publicKey  *rsa.PublicKey
    httpClient *http.Client
}

// NewPaylabsClient membuat instance PaylabsClient dan memuat kunci RSA.
func NewPaylabsClient(cfg Config) (*PaylabsClient, error) {
    server := strings.ToUpper(strings.TrimSpace(cfg.Server))
    if server == "" {
        server = "SIT"
    }

    privateKeyRaw := strings.TrimSpace(cfg.PrivateKey)
    publicKeyRaw := strings.TrimSpace(cfg.PublicKey)

    if privateKeyRaw == "" {
        return nil, errors.New("private_key is required")
    }
    if publicKeyRaw == "" {
        return nil, errors.New("public_key is required")
    }

    priv, err := loadPrivateKey(privateKeyRaw)
    if err != nil {
        return nil, fmt.Errorf("load private key: %w", err)
    }

    pub, err := loadPublicKey(publicKeyRaw)
    if err != nil {
        return nil, fmt.Errorf("load public key: %w", err)
    }

    date := Iso8601NowJakarta()
    reqID := genRequestID()

    c := &PaylabsClient{
        Server:          server,
        Mid:             cfg.Mid,
        Version:         cfg.Version,
        PrivateKeyRaw:   privateKeyRaw,
        PublicKeyRaw:    publicKeyRaw,
        Log:             cfg.Log,
        Date:            date,
        RequestID:       reqID,
        MerchantTradeNo: reqID, // mengikuti _gen_request_id() Python
        privateKey:      priv,
        publicKey:       pub,
        httpClient:      cfg.HTTPClient,
    }

    if c.httpClient == nil {
        c.httpClient = &http.Client{
            Timeout: 60 * time.Second,
        }
    }

    return c, nil
}

// genRequestID meniru pola _gen_request_id() di Python:
// timestamp (YYYYMMDDHHMMSS) + 5 digit random.
func genRequestID() string {
    now := time.Now()
    ts := now.Format("20060102150405")

    // rentang 11111–99999
    max := big.NewInt(99999 - 11111 + 1)
    n, err := rand.Int(rand.Reader, max)
    if err != nil {
        return ts + "00000"
    }
    v := n.Int64() + 11111
    return fmt.Sprintf("%s%05d", ts, v)
}

// getFullURL menyusun URL dari BASE_URL + versi + path.
func (c *PaylabsClient) getFullURL(path string) string {
    base, ok := baseURL[c.Server]
    if !ok {
        base = baseURL["SIT"]
    }
    // Python: BASE_URL[server] + version + path
    return fmt.Sprintf("%s%s%s", base, c.Version, path)
}

// generateSignature mengimplementasikan _generate_signature() dari Python:
// body_json -> sha256 (hex lower) -> gabung string -> sha256 -> RSA-SHA256 -> base64.
func (c *PaylabsClient) generateSignature(bodyJSON, path string) (string, error) {
    bodyHash := sha256.Sum256([]byte(bodyJSON))
    hashedBodyHex := strings.ToLower(hex.EncodeToString(bodyHash[:]))

    dataToSign := fmt.Sprintf("POST:/payment/%s%s:%s:%s", c.Version, path, hashedBodyHex, c.Date)
    dataHash := sha256.Sum256([]byte(dataToSign))

    sig, err := rsa.SignPKCS1v15(rand.Reader, c.privateKey, crypto.SHA256, dataHash[:])
    if err != nil {
        return "", err
    }

    return base64.StdEncoding.EncodeToString(sig), nil
}

// buildHeaders mengimplementasikan _build_headers() di Python.
func (c *PaylabsClient) buildHeaders(bodyJSON, path string) (map[string]string, error) {
    signature, err := c.generateSignature(bodyJSON, path)
    if err != nil {
        return nil, err
    }

    headers := map[string]string{
        "X-TIMESTAMP":  c.Date,
        "X-SIGNATURE":  signature,
        "X-PARTNER-ID": c.Mid,
        "X-REQUEST-ID": c.RequestID,
        "Content-Type": "application/json;charset=utf-8",
    }
    return headers, nil
}

// Request ekuivalen dengan method request() di Python.
// Mengembalikan map yang berisi hasil JSON, atau {status_code, text} jika non-JSON.
func (c *PaylabsClient) Request(path string, payload map[string]interface{}) (map[string]interface{}, error) {
    body := map[string]interface{}{
        "merchantId":      c.Mid,
        "requestId":       c.RequestID,
        "merchantTradeNo": c.MerchantTradeNo,
    }
    for k, v := range payload {
        body[k] = v
    }

    bodyJSON, err := CompactJSON(body)
    if err != nil {
        return nil, fmt.Errorf("encode request body: %w", err)
    }

    url := c.getFullURL(path)
    headers, err := c.buildHeaders(bodyJSON, path)
    if err != nil {
        return nil, fmt.Errorf("build headers: %w", err)
    }

    if c.Log {
        fmt.Println("[Paylabs] POST", url)
        fmt.Println("[Paylabs] Payload:", bodyJSON)
        fmt.Println("[Paylabs] Headers:", headers)
    }

    req, err := http.NewRequest(http.MethodPost, url, strings.NewReader(bodyJSON))
    if err != nil {
        return nil, fmt.Errorf("create request: %w", err)
    }
    for k, v := range headers {
        req.Header.Set(k, v)
    }

    resp, err := c.httpClient.Do(req)
    if err != nil {
        return nil, fmt.Errorf("execute request: %w", err)
    }
    defer resp.Body.Close()

    respBody, err := io.ReadAll(resp.Body)
    if err != nil {
        return nil, fmt.Errorf("read response body: %w", err)
    }

    result := make(map[string]interface{})
    if err := json.Unmarshal(respBody, &result); err != nil {
        // fallback sama seperti Python saat response bukan JSON.
        result = map[string]interface{}{
            "status_code": resp.StatusCode,
            "text":        string(respBody),
        }
    }

    if c.Log {
        fmt.Println("[Paylabs] Response status:", resp.StatusCode)
        fmt.Println("[Paylabs] Response body:", string(respBody))
    }

    return result, nil
}

// VerifySignature ekuivalen dengan verify_signature() di Python.
// Mengembalikan true jika signature valid, false jika tidak valid.
func (c *PaylabsClient) VerifySignature(path, bodyRaw, signatureB64, timestamp string) (bool, error) {
    bodyHash := sha256.Sum256([]byte(bodyRaw))
    hashedBodyHex := strings.ToLower(hex.EncodeToString(bodyHash[:]))

    data := fmt.Sprintf("POST:%s:%s:%s", path, hashedBodyHex, timestamp)
    sig, err := base64.StdEncoding.DecodeString(signatureB64)
    if err != nil {
        return false, fmt.Errorf("decode signature: %w", err)
    }

    dataHash := sha256.Sum256([]byte(data))
    if err := rsa.VerifyPKCS1v15(c.publicKey, crypto.SHA256, dataHash[:], sig); err != nil {
        // signature tidak valid
        return false, nil
    }

    return true, nil
}

// loadPrivateKey memuat private key RSA dari base64 body (PKCS#1 atau PKCS#8).
func loadPrivateKey(b64 string) (*rsa.PrivateKey, error) {
    pemPKCS1 := fmt.Sprintf("-----BEGIN RSA PRIVATE KEY-----\n%s\n-----END RSA PRIVATE KEY-----", b64)
    if key, err := parseRSAPrivateKeyFromPEM([]byte(pemPKCS1)); err == nil {
        return key, nil
    }

    pemPKCS8 := fmt.Sprintf("-----BEGIN PRIVATE KEY-----\n%s\n-----END PRIVATE KEY-----", b64)
    if key, err := parseRSAPrivateKeyFromPEM([]byte(pemPKCS8)); err == nil {
        return key, nil
    }

    return nil, errors.New("failed to parse private key in both PKCS#1 and PKCS#8 formats")
}

func parseRSAPrivateKeyFromPEM(pemBytes []byte) (*rsa.PrivateKey, error) {
    block, _ := pem.Decode(pemBytes)
    if block == nil {
        return nil, errors.New("failed to decode PEM block")
    }

    switch block.Type {
    case "RSA PRIVATE KEY":
        return x509.ParsePKCS1PrivateKey(block.Bytes)
    case "PRIVATE KEY":
        key, err := x509.ParsePKCS8PrivateKey(block.Bytes)
        if err != nil {
            return nil, err
        }
        rsaKey, ok := key.(*rsa.PrivateKey)
        if !ok {
            return nil, errors.New("PKCS#8 key is not RSA")
        }
        return rsaKey, nil
    default:
        return nil, fmt.Errorf("unsupported private key type %q", block.Type)
    }
}

// loadPublicKey memuat public key RSA dari base64 body (BEGIN PUBLIC KEY).
func loadPublicKey(b64 string) (*rsa.PublicKey, error) {
    pemData := fmt.Sprintf("-----BEGIN PUBLIC KEY-----\n%s\n-----END PUBLIC KEY-----", b64)
    block, _ := pem.Decode([]byte(pemData))
    if block == nil {
        return nil, errors.New("failed to decode public key PEM")
    }

    iface, err := x509.ParsePKIXPublicKey(block.Bytes)
    if err != nil {
        return nil, err
    }

    pub, ok := iface.(*rsa.PublicKey)
    if !ok {
        return nil, errors.New("public key is not RSA")
    }

    return pub, nil
}
