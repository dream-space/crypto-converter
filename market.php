<?php
require_once("rest.php");
function requestAPI($vs_currency, $order, $per_page, $page){
    $url = "https://api.coingecko.com/api/v3/coins/markets?vs_currency=$vs_currency&order=$order&per_page=$per_page&page=$page&sparkline=false";
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $output = curl_exec($curl);
    curl_close($curl);
    return $output;
}

$rest = new REST();

if($rest->get_request_method() != "GET") $rest->response('',406);
$vs_currency = 'usd';
$order = 'market_cap_desc';
$per_page = '100';
$page = '1';
if(isset($rest->_request['vs_currency'])) $vs_currency = $rest->_request['vs_currency'];
if(isset($rest->_request['order'])) $vs_currency = $rest->_request['order'];
if(isset($rest->_request['per_page'])) $vs_currency = $rest->_request['per_page'];
if(isset($rest->_request['page'])) $vs_currency = $rest->_request['page'];

$data = json_decode(requestAPI($vs_currency, $order, $per_page, $page), true);
$rest->show_response($data);

?>
