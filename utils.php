<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/mfm-db/utils.php";

function defaultChartSettings()
{
    return [
        'M' => 60,
        'H' => 60 * 60,
        'D' => 60 * 60 * 24,
        'W' => 60 * 60 * 24 * 7,
    ];
}

function trackLinear($key, $value)
{
    $timestamp = time();
    foreach (defaultChartSettings() as $period_name => $period) {
        $last_candle = selectRow("select * from candles where `key` = '$key' and `period_name` = '$period_name' "
            . "order by `period_time` desc limit 1");

        $period_time = ceil($timestamp / $period) * $period;
        if ($period_time != $last_candle[period_time]) {
            insertRow(candles, [
                key => $key,
                period_name => $period_name,
                period_time => $period_time,
                low => $value,
                high => $value,
                open => $last_candle[close] ?: $value,
                close => $value
            ]);
        } else {
            updateWhere(candles, [
                low => min($last_candle[low], $value),
                high => max($last_candle[high], $value),
                close => $value
            ], [
                key => $key,
                period_name => $period_name,
                period_time => $period_time
            ]);
        }
    }
}

function trackAccumulate($key, $value = 1)
{
    trackLinear($key, getCandleLastValue($key) + $value);
}

function optimizeCandles($candles)
{
    return array_map(function ($candle) {
        return [
            time => $candle[period_time],
            low => $candle[low],
            high => $candle[high],
            open => $candle[open],
            close => $candle[close],
        ];
    }, $candles);
}

function getCandles($key, $period_name, $count = 10)
{
    $period = defaultChartSettings()[$period_name];
    if ($period == null) error("unavailable period");

    $candles = select("select * from candles where `key` = '$key' and `period_name` = '$period_name' "
        . "order by `period_time` desc limit $count");

    return optimizeCandles(array_reverse($candles));
}

function getCandleLastValue($key)
{
    $last_candle = selectRow("select * from candles where `key` = '$key' and `period_name` = 'M' "
        . "  order by `period_time` desc limit 1");
    return $last_candle[close];
}

function getCandleChange24($key)
{
    $last_candle = selectRow("select * from candles where `key` = '$key' and `period_name` = 'D' "
        . "order by `period_time` desc limit 1");
    if ($last_candle == null) return 0;
    return $last_candle[close] - $last_candle[open];
}


function trackEvent($app, $name, $value = null)
{
    if (is_array($value))
        $value = trackObject($value);
    insertRowAndGetId(events, [
        ip => $_SERVER['REMOTE_ADDR'],
        app => $app,
        name => $name,
        value => $value,
        user_id => get_string(user_id),
        time => time(),
    ]);
    return $value;
}

function getEvent($app, $name, $value = "")
{
    return getEvents($app, $name, $value, 0, 1)[0];
}

function getEvents($app, $name, $value = "", $time_from = "", $ip = "", $page = 0, $size = 20)
{
    $sql = "select * from events"
        . " where `app` = '$app'"
        . " and `name` = '$name'";
    if ($value != "")
        $sql .= " and `value` = '$value'";
    if ($time_from != "")
        $sql .= " and `time` >= $time_from";
    if ($ip != "")
        $sql .= " and `ip` = '$ip'";
    $sql .= " order by `time` desc limit " . ($page * $size) . ", $size";
    return select($sql);
}


function limitPassSec($sec, $postfix = "")
{
    $path = getScriptPath();
    $last_event = getEvent("call_limit", $path . $postfix, $sec);
    if ($last_event != null && time() - $last_event[time] < $sec) {
        return false;
    }
    trackEvent("call_limit", $path . $postfix, $sec);
    return true;
}

function callLimitPassSec($sec, $postfix = "")
{
    if (!limitPassSec($sec, $postfix))
        error("call limit $sec sec");
}


function trackObject($object)
{
    $parent = random_key(objects, parent);
    foreach ($object as $key => $value) {
        insertRow(objects, [
            parent => $parent,
            key => $key,
            value => $value,
            time => time(),
        ]);
    }
    return $parent;
}

function getObject($parent)
{
    $objects = select("select * from objects where `parent` = $parent");
    $object = [];
    foreach ($objects as $item)
        $object[$item[key]] = $item[value];
    return $object;
}