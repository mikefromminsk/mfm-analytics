<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/mfm-db/utils.php";

onlyInDebug();

$data = select("select * from events");

commit($data);