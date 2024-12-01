<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/mfm-analytics/utils.php";

$app = get_required(app);
$name = get_required(name);
$value = get_string(value);

trackEvent($app, $name, $value);
trackAccumulate("$app:$name");

echo json_encode(['success' => true]);