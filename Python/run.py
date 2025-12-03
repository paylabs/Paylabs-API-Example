import json
from paylabs_remit.client import PaylabsRemitClient

if __name__ == "__main__":
    client = PaylabsRemitClient(
        mid="010001",
        version="v1.2",
        server="SIT",  # or "PROD"
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
    print("# Inquiry\n", json.dumps(inquiry, indent=2, ensure_ascii=False))

    # Example: get customerReference from inquiry if present (adjust key as per response)
    customer_reference = inquiry.get("referenceNo") or inquiry.get("customerReference") or "20250414214559470"

    # 2) Transfer Interbank
    transfer = client.transfer(
        customer_reference=customer_reference,
        bank_no="081234567890",
        bank_name="John Doe",
        bank_code="DANA",
        remit_type="wallet",
        amount=20000,
        notify_url="https://domain.com/callback.php"
    )
    print("# Transfer\n", json.dumps(transfer, indent=2, ensure_ascii=False))

    # 3) Status
    partner_ref = transfer.get("partnerReferenceNo") or client.id_request
    status = client.status(partner_ref)
    print("# Status\n", json.dumps(status, indent=2, ensure_ascii=False))

    # 4) Balance
    balance = client.balance()
    print("# Balance\n", json.dumps(balance, indent=2, ensure_ascii=False))

    # 5) Transfer File (GET with JSON body)
    tf = client.get_transfer_file(partner_reference_no=partner_ref, date="2025-04-13")
    print("# Transfer File\n", json.dumps(tf, indent=2, ensure_ascii=False))
