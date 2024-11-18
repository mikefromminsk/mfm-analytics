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

query("DROP TABLE IF EXISTS `events`;");
query("CREATE TABLE IF NOT EXISTS `events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent` varchar(64) COLLATE utf8_bin NULL,
  `type` varchar(64) COLLATE utf8_bin NOT NULL,
  `name` varchar(64) COLLATE utf8_bin NULL,
  `value` varchar(64) COLLATE utf8_bin NULL,
  `session` varchar(16) COLLATE utf8_bin NULL,
  `username` varchar(64) COLLATE utf8_bin NULL,
  `version` varchar(16) COLLATE utf8_bin NULL,
  `time` int(11) NOT NULL,
   PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;");

$response[success] = true;

echo json_encode($response);

