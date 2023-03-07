
Host ID: 150999
Terminal ID: 15532069
Merchant ID: 15002069
Vcard : 960798
VCard Password : asdasdasd

You should use the following URLs for your test requests:
https://91.199.226.106/services/authorize.php  - for customer redirect
https://91.199.226.106/services/emv_authorize.php - (for MasterCard)
https://91.199.226.106/ssljson.php - for merchant request

5420470100008842,MC GOLD,Exp - 0815,CVC2 - 675

Пример кода:

    <?php

    $params = array(
        'hostID'=>'',
        'orderID'=>'',
        'amount'=>'',
        'currency'=>'',
        'mid'=>'',
        'tid'=>'',
        'mtpass'=>'',
        'trxnDetails'=>'' );

    callArcaRpc('merchant_check',$params);

    callArcaRpc('confirmation',$params);

//callArcaRpc('refuse',$params);

    /**
     *
     * @param string $method
     * @param array $arr
     * @return array
     */
function callArcaRpc($method, $arr) {
    $url = "https://195.250.88.228:8193/ssljson.yaws"; // Merchant should use the URL provided by ArCa!!!
    $postData = array (
        "id"=>"remoteRequest",
        "method"=>$method,
        "params"=> array($arr)
    );

    $postData = json_encode($postData);

    #SSL module
    $private_cert="c:\\wamp\\www\\client.crt"; // Path to the .cert file provided by ArCa
    $private_key="c:\\wamp\\www\\client.key";   // Path to the .key file provided by ArCa
    $private_pass = ""; // Merchant should use the certificate password provided by ArCa

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
    curl_setopt($ch, CURLOPT_SSLCERT, $private_cert);
    curl_setopt($ch, CURLOPT_SSLKEY, $private_key);
    curl_setopt($ch, CURLOPT_SSLKEYPASSWD, $private_pass);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

    $ret = curl_exec($ch);
    curl_close($ch);

    $decoded = get_object_vars(json_decode($ret)->result);
    return $decoded;
}