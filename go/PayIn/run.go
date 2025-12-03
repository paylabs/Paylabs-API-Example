// FILE: run.go
// ASAL : run.py
//
// Perubahan penting:
// - Fungsi utama Python diganti dengan func main() di Go.
// - Fungsi pretty_section() diganti prettySection() menggunakan encoding/json.MarshalIndent.
// - ANSI color code dipertahankan untuk tampilan terminal.

package main

import (
	"encoding/json"
	"fmt"
	"log"
	"strings"

	"paylabs-go/paylabs"
)

const (
    Yellow = "\033[93m"
    Cyan   = "\033[96m"
    Green  = "\033[92m"
    Reset  = "\033[0m"
)

// prettySection menampilkan blok data dengan judul dan pemisah, mirip fungsi Python.
func prettySection(title string, data interface{}) {
    fmt.Printf("\n%s%s%s\n", Cyan, strings.Repeat("=", 80), Reset)
    centerPrint(Yellow, title, 80)
    fmt.Printf("%s%s%s\n", Cyan, strings.Repeat("=", 80), Reset)

    b, err := json.MarshalIndent(data, "", "  ")
    if err != nil {
        fmt.Println("error pretty printing data:", err)
    } else {
        fmt.Println(string(b))
    }

    fmt.Printf("%s%s%s\n\n", Cyan, strings.Repeat("-", 80), Reset)
}

func centerPrint(color string, text string, width int) {
    if len(text) >= width {
        fmt.Println(text)
        return
    }
    padding := (width - len(text)) / 2
    fmt.Printf("%s%s%s%s\n",
        color,
        strings.Repeat(" ", padding),
        text,
        Reset,
    )
}

func main() {
    // Konfigurasi setara dengan config di run.py.
    cfg := paylabs.Config{
        Server:     "SIT",          // atau "PROD"
        Mid:        "01XXXX",       // Merchant ID
        Version:    "v2.3",         // versi API
        PrivateKey: "YOUR_PRIVATE_KEY_BASE64_OR_PEM",
        PublicKey:  "YOUR_PUBLIC_KEY_BASE64_OR_PEM",
        Log:        true,
    }

    client, err := paylabs.NewPaylabsClient(cfg)
    if err != nil {
        log.Fatalf("failed to initialize PaylabsClient: %v", err)
    }

    payload := map[string]interface{}{
        "paymentType": "QRIS",
        "amount":      "15000",
        "productName": "Sepeda",
        "notifyUrl":   "https://webhook.site",
    }

    fmt.Printf("%s%s%s\n", Green, strings.Repeat("=", 80), Reset)
    centerPrint(Green, "MENGIRIM REQUEST QRIS CREATE", 80)
    fmt.Printf("%s%s%s\n", Green, strings.Repeat("=", 80), Reset)

    resp, err := client.Request("/qris/create", payload)
    if err != nil {
        log.Fatalf("error calling Paylabs API: %v", err)
    }

    prettySection("QRIS CREATE RESPONSE", resp)

    fmt.Printf("%s%s%s\n", Green, strings.Repeat("=", 80), Reset)
    centerPrint(Green, "PROCESS COMPLETED SUCCESSFULLY", 80)
    fmt.Printf("%s%s%s\n", Green, strings.Repeat("=", 80), Reset)
}
