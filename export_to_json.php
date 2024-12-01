<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/mfm-db/utils.php";

onlyInDebug();

$data = select("select * from events");

echo json_encode($data, JSON_PRETTY_PRINT);