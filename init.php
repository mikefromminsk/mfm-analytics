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
    `ip` varchar(16) COLLATE utf8_bin NOT NULL,
    `app` varchar(16) COLLATE utf8_bin NOT NULL,
    `name` varchar(32) COLLATE utf8_bin NOT NULL,
    `value` varchar(128) COLLATE utf8_bin NULL,
    `user_id` varchar(32) COLLATE utf8_bin NULL,
    `time` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;");

query("DROP TABLE IF EXISTS `objects`;");
query("CREATE TABLE IF NOT EXISTS `objects` (
    `parent` int(11) NULL,
    `key` varchar(64) COLLATE utf8_bin NOT NULL,
    `value` varchar(64) COLLATE utf8_bin NOT NULL,
    `time` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;");

echo json_encode([success => true]);

