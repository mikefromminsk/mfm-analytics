<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/mfm-analytics/utils.php";

$type = get_required(type);
$name = get_string(name);
$value = get_string(value);
$session = get_string(session);
$username = get_string(username);
$version = get_string(version);
$parent = get_int(parent);

if ($type == 'ui_start' && $name == null) {
    $name = getProtocol();
    $value = $_SERVER['REMOTE_ADDR'];
}

trackEvent($type, $name, $value, $session, $username, $version, $parent);

echo json_encode(['success' => true]);