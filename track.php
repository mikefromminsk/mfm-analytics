<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/mfm-analytics/utils.php";

$app = get_required(app);
$name = get_required(name);
$value = get_string(value);
$user_id = get_string(user_id);

trackEvent($app, $name, $value, $user_id);
trackAccumulate("$app:$name");

commit();