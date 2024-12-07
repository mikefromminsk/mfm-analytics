<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/mfm-token/utils.php";

$address = get_required(address);
$pass = get_required(pass);

$gas_domain = get_required(gas_domain);

tokenChangePass($gas_domain, $address, $pass);
commit();

updateWhere(events, [user_id => "deleted_" . $address], [user_id => $address]);
