<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/mfm-db/utils.php";

function defaultChartSettings()
{
    return [
        '1M' => 60,
        '1H' => 60 * 60,
        '1D' => 60 * 60 * 24,
        '1W' => 60 * 60 * 24 * 7,
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
    $last_candle = selectRow("select * from candles where `key` = '$key' and `period_name` = '1M' "
        . "  order by `period_time` desc limit 1");
    return $last_candle[close];
}

function getCandleChange24($key)
{
    $last_candle = selectRow("select * from candles where `key` = '$key' and `period_name` = '1D' "
        . "order by `period_time` desc limit 1");
    if ($last_candle == null) return 0;
    return $last_candle[close] - $last_candle[open];
}


function trackEvent($type, $name, $value = "")
{
    insertRow(events, [
        type => $type,
        name => $name,
        value => $value,
        session => get_string(session),
        username => get_string(gas_address),
        version => get_string(version),
        parent => get_int(parent),
        time => time(),
    ]);
}

function getEvent($type, $name, $value = "")
{
    return getEvents($type, $name, $value, 0, 1)[0];
}

function getEvents($type, $name, $value = "", $page = 0, $size = 10, $fromTime = null, $parent = null)
{
    $sql = "select * from events where `type` = '$type'";
    if ($parent != null) $sql .= " and `parent` = '$parent'";
    if ($name != null) $sql .= " and `name` = '$name'";
    if ($value != null) $sql .= " and `value` = '$value'";
    if ($fromTime != null) $sql .= " and `time` >= $fromTime";
    $sql .= " order by `time` desc limit $page, $size";
    return select($sql);
}