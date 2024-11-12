<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/mfm-analytics/utils.php";

$name = get_required(name);
$from = get_string(from);
$from_id = get_string(from_id);
$to = get_string(to);
$to_id = get_string(to_id);
$value = get_string(value);
$session = get_string(session);
$username = get_string(username);
$version = get_string(version);

trackEvent($name, $from, $from_id, $to, $to_id, $value, $session, $username, $version);

echo json_encode(['success' => true]);