import json
import pprint
from colorama import Fore, Style, init
from paylabs.client import PaylabsRemitClient

init(autoreset=True)  # aktifkan warna otomatis

def pretty_section(title: str, data):
    print(f"\n{Fore.CYAN}{'=' * 80}")
    print(f"{Fore.YELLOW}{title.center(80)}")
    print(f"{Fore.CYAN}{'=' * 80}{Style.RESET_ALL}")
    pprint.pprint(data, width=120, sort_dicts=False)
    print(f"{Fore.CYAN}{'-' * 80}{Style.RESET_ALL}\n")

if __name__ == "__main__":
    client = PaylabsRemitClient(
        mid="01XXXX", # Merchant ID
        version="v1.2",
        server="SIT",  # or "PROD"
        #private_key="YOUR_PRIVATE_KEY_BASE64_OR_PEM",
        private_key="YOUR_PRIVATE_KEY_BASE64_OR_PEM",
        public_key="YOUR_PUBLIC_KEY_BASE64_OR_PEM",
        log=True
    )

    # 1) Account Inquiry
    inquiry = client.inquiry(
        bank_no="081234567890",
        bank_code="DANA",
        remit_type="wallet",
        amount=20000,
        notify_url=None
    )
    pretty_section("ACCOUNT INQUIRY", inquiry)

    # Example: get customerReference from inquiry
    customer_reference = inquiry.get("referenceNo") or inquiry.get("customerReference") or "20251114214559470"

    # 2) Transfer Interbank
    transfer = client.transfer(
        customer_reference=customer_reference,
        bank_no="081234567890",
        bank_name="081234567890",
        bank_code="DANA",
        remit_type="wallet",
        amount=20000,
        notify_url="YOUR_WEBHOOK_URL"
    )
    pretty_section("TRANSFER INTERBANK", transfer)

    # 3) Status
    partner_ref = transfer.get("partnerReferenceNo") or client.id_request
    status = client.status(partner_ref)
    pretty_section("STATUS INQUIRY", status)

    # 4) Balance
    balance = client.balance()
    pretty_section("BALANCE INQUIRY", balance)

    # 5) Transfer File (GET with JSON body)
    tf = client.get_transfer_file(partner_reference_no=partner_ref, date="2025-11-11")
    pretty_section("TRANSFER FILE", tf)

    print(f"{Fore.GREEN}{'='*80}")
    print(f"{Fore.GREEN}{'PROCESS COMPLETED SUCCESSFULLY'.center(80)}")
    print(f"{Fore.GREEN}{'='*80}{Style.RESET_ALL}")