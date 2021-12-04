<?php

namespace smukhidev\ShurjopayLaravelPackage;

class ShurjopayService
{
    protected $merchant_username;
    protected $merchant_password;
    protected $client_ip;
    protected $merchant_key_prefix;
    protected $tx_id;

    public function __construct()
    {
        $this->merchant_username = config('shurjopay.merchant_username');
        $this->merchant_password = config('shurjopay.merchant_password');
        $this->client_ip = $_SERVER["REMOTE_ADDR"] ?? '127.0.0.1';
        $this->merchant_key_prefix = config('shurjopay.merchant_key_prefix');
    }

    public function generateTxId($unique_id = null)
    {
        if ($unique_id) {
            $tx_id = $this->merchant_key_prefix . $unique_id;
        } else {
            $tx_id = $this->merchant_key_prefix . uniqid();
        }
        $this->tx_id = $tx_id;
        return $tx_id;
    }

    public function sendPayment($reqdata, $success_url = null)
    {
        $return_url = route('shurjopay.response');
        if ($success_url) {
            $return_url .= "?success_url={$success_url}";
        }
        $amount=$reqdata['amount'];
        $data = array(
            'merchantName' => $this->merchant_username,
            'merchantPass' => $this->merchant_password,
            'userIP' => $this->client_ip,
            'uniqID' => $this->tx_id,
            'custom1' => $reqdata['custom1'],
            'custom2' => $reqdata['custom2'],
            'custom3' => $reqdata['custom3'],
            'custom4' => $reqdata['custom4'],
            //'school' => $reqdata['amount'],
            'paymentterm' => '', //Tenure Months like 3,6,12,18,36
            'minimumamount' => '', //Minimum Amount 10000
            'totalAmount' => $amount,
            'is_emi'=>$reqdata['is_emi'], //0 NO EMI 1 EMI True
            'paymentOption' => 'shurjopay',
            'returnURL' => $return_url,
        );
        $payload = array("spdata" => json_encode($data));

        $ch = curl_init();
        $server_url = config('shurjopay.server_url');
        // $url = $server_url . "/sp-pp.php";
        $url = $server_url . "/sp-data.php";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);                //0 for a get request
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $response = curl_exec($ch);
        curl_close($ch);
        print_r($response);

    }
},
,,,
