from Paylabs import Paylabs

def main():
    # Create an instance of the Paylabs class
    paylabs = Paylabs()
    
    # Set the necessary parameters
    paylabs.set_server("SIT")
    paylabs.set_mid("MERCHANT ID")
    paylabs.set_version("v2.1") #VERSION, PROD = v2, SIT = v2.1
    paylabs.set_private_key("""-----BEGIN RSA PRIVATE KEY-----
MERCHANT PRIVATE KEY
-----END RSA PRIVATE KEY-----""")

    # Set the payment details
    paylabs.set_h5(amount="15000", phone_number="081234567890", product="Product 1", redirect_url="https://example.com/callback", notify_url="https://example.com/callback")
    # paylabs.set_va("MaybankVA", "15000", "Sepatu", "Paylabs")
    # paylabs.set_emoney("DANABALANCE", "15000", "Sepatu")
    # paylabs.set_e_credit("Kredivo", "15000", "Sepeda")
    # paylabs.set_cc("CreditCard", "15000", "Kalung", "Paylabs")
    # paylabs.set_qris("20000", "Roti")
    # paylabs.set_otc("Indomaret", "100000", "Roti", "Paylabs")
    
    # Make the request
    response = paylabs.request()
    
    # Handle the response
    print(response)

if __name__ == "__main__":
    main()