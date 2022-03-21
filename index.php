<?php
require_once("rest.php");
function requestAPI($amount, $from, $to){
    $url = "https://api.coingecko.com/api/v3/simple/price?ids=$from&vs_currencies=$to&include_last_updated_at=true";
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $output = curl_exec($curl);
    curl_close($curl);
    return $output;
}

function getDateUTC($date_time){
    $dt = new DateTime();
    $dt->setTimestamp($date_time);
    $dt->setTimezone(new DateTimeZone('UTC'));
    return $dt->format('M d, Y, H:i e');
}

function floatValue($val){
    $val = str_replace(",",".",$val);
    $val = preg_replace('/\.(?=.*\.)/', '', $val);
    return floatval($val);
}

$rest = new REST();

if($rest->get_request_method() != "GET") $rest->response('',406);
if(!isset($rest->_request['from'])) $rest->responseInvalidParam('from');
if(!isset($rest->_request['to'])) $rest->responseInvalidParam('to');
$from = $rest->_request['from'];
$to = $rest->_request['to'];
$to_arr = array();
$multi = false;
$amount = 1;

if(isset($rest->_request['amount'])){
    $amount = floatValue($rest->_request['amount']);
}

if (strpos($to, ',') !== false) {
    $to_arr = explode(',', $to);
    $multi = true;
}

$result = array(
    'from' => $from,
    'to' => $to,
    'amount' => $amount
);

if(!$multi){
    $data = json_decode(requestAPI($amount, $from, $to), true);
    $last_updated_at = $data[$from]['last_updated_at'];
    $result['date'] = getDateUTC($last_updated_at);
    $result['date_time'] = $last_updated_at;
    $result['rate'] = $data[$from][$to];

} else {
    $multi = array();
    foreach ($to_arr as $t) {
        $data = json_decode(requestAPI($amount, $from, $t), true);
        $last_updated_at = $data[$from]['last_updated_at'];
        $rate = $data[$from][$t];
        $result['date'] = getDateUTC($last_updated_at);
        $result['date_time'] = $last_updated_at;

        $multi[] = array('to' => $t, 'rate' => $rate);
    }
    $result['multi'] = $multi;
}

$rest->show_response($result);

?>
