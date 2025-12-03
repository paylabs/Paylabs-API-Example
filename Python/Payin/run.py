import json
import pprint
from paylabs.client import PaylabsClient

# Warna ANSI (untuk terminal yang mendukung)
YELLOW = "\033[93m"
CYAN = "\033[96m"
GREEN = "\033[92m"
RESET = "\033[0m"

def pretty_section(title: str, data):
    """Cetak hasil API dalam format blok dengan judul berwarna dan indentasi rapi."""
    print(f"\n{CYAN}{'=' * 80}{RESET}")
    print(f"{YELLOW}{title.center(80)}{RESET}")
    print(f"{CYAN}{'=' * 80}{RESET}")
    pprint.pprint(data, width=120, sort_dicts=False)
    print(f"{CYAN}{'-' * 80}{RESET}\n")

if __name__ == "__main__":
    config = {
        "server": "SIT",                    # or PROD
        "mid": "01XXXX",                    # Merchant ID
        "version": "v2.3",
        "private_key": "",  # base64 block, without PEM headers
        "public_key": "",    # base64 block, without PEM headers
        "log": True
    }
    client = PaylabsClient(**config)

    # Example: create QRIS
    payload = {
        "paymentType": "QRIS",
        "amount": "15000",
        "productName": "Sepeda",
        "notifyUrl": "YOUR_CALLBACK_URL",
    }

    print(f"{GREEN}{'='*80}")
    print(f"{GREEN}{'MENGIRIM REQUEST QRIS CREATE'.center(80)}")
    print(f"{GREEN}{'='*80}{RESET}")
    resp = client.request("/qris/create", payload)

    pretty_section("QRIS CREATE RESPONSE", resp)

    print(f"{GREEN}{'='*80}")
    print(f"{GREEN}{'PROCESS COMPLETED SUCCESSFULLY'.center(80)}")
    print(f"{GREEN}{'='*80}{RESET}")
