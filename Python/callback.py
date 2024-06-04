from Paylabs import Paylabs

def main():
    # Create an instance of the Paylabs class
    paylabs = Paylabs()

    paylabs.set_public_key("""-----BEGIN PUBLIC KEY-----
PAYLABS PUBLIC KEY
-----END PUBLIC KEY-----""")

    #verify callback
    json = '{"merchantId":"010366","requestId":"N01036620240604366000000211717484634765","errCode":"0","paymentType":"MaybankVA","amount":"15000.00","createTime":"20240604140200","successTime":"20240604140301","merchantTradeNo":"2024060414015445005","platformTradeNo":"2024060436600000021","status":"02","paymentMethodInfo":{"vaCode":"9999900000000006"},"productName":"Product 1","transFeeRate":"0.000000","transFeeAmount":"5000.00","totalTransFee":"5000.00"}';
    sign = 'rgX/D59369OFD7BV5ONQ8FII4FpbWAhKjsBNcjViFKLsgZr/mcuuWRh3sE5j4USgxHp2Vzmfl3Kmk1kdta+kpIV+XwXn8Z2Cox/AybnPJgiQWeaKrKmVzX/Ys5/1soRm+7ze8YhfIOWHgxRiIuCD3a+RGiIBcIedQ0QXSLnST3iYrjrkvCcJ+SslGM7S4efn4SYxYOI6OXWUSW8XETsNkOXIEzJLl7avFIHdlYOpRzTSXXzFdZCq3ydcEf5ZKs5BPUl2jlxEuJ8LX/e6e/NpW6kXPANf4sDaEuB5TBDRypxuNg51nNpy9yjG85dZOOSNDxWwiwevglhG/m3i3AlixQ==';
    dateTime = '2024-06-04T14:03:54.766+07:00';
    validate = paylabs.verify_sign("/callback", json, sign, dateTime)
    print(validate)

if __name__ == "__main__":
    main()