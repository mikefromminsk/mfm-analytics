<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/mfm-analytics/utils.php";

$key = get_required(key);
$accomulate_key = get_string(accomulate_key);
$period_name = get_required(period_name);

$response[candles] = getCandles($key, $period_name, 50);
$response[accomulate] = getAccomulate($accomulate_key, $period_name, 50);
$response[value] = getCandleLastValue($key);
$response[change24] = getCandleChange24($key);

commit($response);