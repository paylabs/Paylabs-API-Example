// FILE: paylabs/utils.go
// ASAL : paylabs/utils.py
//
// Perubahan penting:
// - iso8601_now_jakarta() -> Iso8601NowJakarta() menggunakan time.FixedZone +07:00.
// - compact_json() -> CompactJSON() menggunakan encoding/json dengan output compact.

package paylabs

import (
    "bytes"
    "encoding/json"
    "strings"
    "time"
)

// jakartaLocation merepresentasikan zona waktu Asia/Jakarta (+07:00).
var jakartaLocation = time.FixedZone("Asia/Jakarta", 7*60*60)

// Iso8601NowJakarta mengembalikan timestamp saat ini di Jakarta
// dalam format: 2006-01-02T15:04:05+07:00
func Iso8601NowJakarta() string {
    now := time.Now().In(jakartaLocation)
    return now.Format("2006-01-02T15:04:05-07:00")
}

// CompactJSON mengubah struktur data Go menjadi JSON string kompak,
// mirip json.dumps(data, ensure_ascii=False, separators=(",", ":")) di Python.
func CompactJSON(data interface{}) (string, error) {
    var buf bytes.Buffer

    enc := json.NewEncoder(&buf)
    enc.SetEscapeHTML(false) // mirip ensure_ascii=False
    // Tidak menggunakan indent agar menjadi kompak.
    if err := enc.Encode(data); err != nil {
        return "", err
    }

    // Hapus newline di akhir yang ditambahkan Encoder.
    s := strings.TrimSpace(buf.String())
    return s, nil
}
