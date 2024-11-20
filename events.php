<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/mfm-analytics/utils.php";

$type = get_required(type);
$name = get_required(name);
$value = get_string(value);
$parent = get_int(parent);
$page = get_int(page, 0);
$size = get_int(size, 10);
$time_from = get_int(time_from, time() - 60 * 60 * 24 * 7);

$response[events] =  getEvents($type, $name, $value, $page, $size, $time_from, $parent) ?: [];
$response[success] = true;

echo json_encode($response, JSON_PRETTY_PRINT);