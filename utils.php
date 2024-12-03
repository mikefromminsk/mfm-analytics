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
    $GLOBALS[mfm_candles][$key] = $value;
}

function commitCandles()
{
    if ($GLOBALS[mfm_candles] != null) {
        foreach ($GLOBALS[mfm_candles] as $key => $value) {
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


function trackEvent($app, $name, $value = null, $user_id = null)
{
    $GLOBALS[mfm_events][] = [
        app => $app,
        name => $name,
        value => $value,
        user_id => $user_id,
    ];
    return $value;
}

function commitEvents()
{
    if ($GLOBALS[mfm_events] != null) {
        foreach ($GLOBALS[mfm_events] as $event) {
            if (is_array($event[value]))
                $event[value] = trackObject($event[value]);
            insertRowAndGetId(events, [
                ip => $_SERVER['REMOTE_ADDR'],
                app => $event[app],
                name => $event[name],
                value => $event[value],
                user_id => $event[user_id] ?: get_string(gas_address),
                time => time(),
            ]);
        }
    }
}

function getEvent($app, $name, $value = null, $user_id = null)
{
    return getEvents([
        app => $app,
        name => $name,
        value => $value,
        user_id => $user_id,
        size => 1
    ])[0];
}

function getEvents($params)
{
    $app = $params[app];
    $name = $params[name];
    $value = $params[value];
    $time = $params[time];
    $ip = $params[ip];
    $user_id = $params[user_id];
    $page = $params[page] ?: 0;
    $size = $params[size] ?: 1000;
    $sql = "select * from events"
        . " where `app` = '$app'"
        . " and `name` = '$name'";
    if ($value != null)
        $sql .= " and `value` = '$value'";
    if ($time != null)
        $sql .= " and `time` >= $time";
    if ($ip != null && $user_id != null){
        $sql .= " and (`ip` = '$ip' or `user_id` = '$user_id')";
    } else {
        if ($ip != null)
            $sql .= " and `ip` = '$ip'";
        if ($user_id != null)
            $sql .= " and `user_id` = '$user_id'";
    }
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
    $GLOBALS[mfm_objects][$parent] = $object;
    return $parent;
}

function commitObjects()
{
    if ($GLOBALS[mfm_objects] != null){
        foreach ($GLOBALS[mfm_objects] as $parent => $object) {
            foreach ($object as $key => $value) {
                insertRow(objects, [
                    parent => $parent,
                    key => $key,
                    value => $value,
                    time => time(),
                ]);
            }
        }
    }
}

function getObject($parent)
{
    $objects = select("select * from objects where `parent` = $parent");
    $object = [];
    foreach ($objects as $item)
        $object[$item[key]] = $item[value];
    return $object;
}

function commitAnalytics()
{
    commitCandles();
    commitEvents();
    commitObjects();
}

