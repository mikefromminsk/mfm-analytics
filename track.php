<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/mfm-analytics/utils.php";

$type = get_required(type);
$name = get_string(name);
$value = get_string(value);

if ($type == 'ui_start' && $name == null) {
    $name = getProtocol();
    $value = $_SERVER['REMOTE_ADDR'];
}

trackEvent($type, $name, $value);

echo json_encode(['success' => true]);