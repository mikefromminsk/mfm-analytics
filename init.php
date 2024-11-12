<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/mfm-db/utils.php";

onlyInDebug();

query("DROP TABLE IF EXISTS `candles`;");
query("CREATE TABLE IF NOT EXISTS `candles` (
  `key` varchar(256) COLLATE utf8_bin NOT NULL,
  `period_name` varchar(2) COLLATE utf8_bin NOT NULL,
  `period_time` int(11) NOT NULL,
  `low` float NOT NULL,
  `high` float NOT NULL,
  `open` float NOT NULL,
  `close` float NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;");

//query("DROP TABLE IF EXISTS `events`;");
query("CREATE TABLE IF NOT EXISTS `events` (
  `name` varchar(64) COLLATE utf8_bin NOT NULL,
  `from` varchar(64) COLLATE utf8_bin NULL,
  `from_id` varchar(64) COLLATE utf8_bin NULL,
  `to` varchar(64) COLLATE utf8_bin NULL,
  `to_id` varchar(64) COLLATE utf8_bin NULL,
  `value` varchar(64) COLLATE utf8_bin NULL,
  `session` varchar(64) COLLATE utf8_bin NULL,
  `username` varchar(64) COLLATE utf8_bin NULL,
  `version` varchar(64) COLLATE utf8_bin NULL,
  `time` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;");

$response[success] = true;

echo json_encode($response);

