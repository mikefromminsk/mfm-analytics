<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/mfm-analytics/utils.php";

$app = get_required(app);
$name = get_required(name);
$value = get_string(value);
$page = get_int(page, 0);
$size = get_int(size, 10);
$time = get_int(time, time() - 60 * 60 * 24 * 7);

$response[events] = getEvents([
    app => $app,
    name => $name,
    value => $value,
    time => $time,
    page => $page,
    size => $size
]) ?: [];
$response[success] = true;

echo json_encode($response, JSON_PRETTY_PRINT);