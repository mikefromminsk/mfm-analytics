<?php
require_once $_SERVER[DOCUMENT_ROOT] . "/mfm-analytics/utils.php";

$key = get_required(key);
$period_name = get_required(period_name);

$response[candles] = getCandles($key, $period_name, 30);
$response[value] = getCandleLastValue($key);
$response[change24] = getCandleChange24($key);

echo json_encode($response);