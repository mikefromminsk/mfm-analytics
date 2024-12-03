<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/mfm-analytics/utils.php";

$time_from = get_int(time_from, time() - 60 * 60 * 24 * 7);
$funnel = get_required(funnel);


function parseFunnel($funnel)
{
    $response = [];
    foreach (explode(",", $funnel) as $stepStr) {
        $step = [];
        $step[str] = $stepStr;
        $appNameValue = explode(":", $stepStr);
        $step[app] = $appNameValue[0];
        $nameValue = explode("=", $appNameValue[1] ?: "");
        $step[name] = $nameValue[0];
        $step[value] = $nameValue[1] ?: "";
        $step[count] = 0;
        $response[] = $step;
    }
    return $response;
}

$funnel = parseFunnel($funnel);
$firstStep = array_shift($funnel);
$events = getEvents([
    app => $firstStep[app],
    name => $firstStep[name],
    value => $firstStep[value],
    time => $time_from,
    size => 10000
]) ?: [];

$firstStep[count] = sizeof($events);

if (sizeof($events) > 0) {
    foreach ($events as $event) {
        $ip = $event[ip];
        $user_id = $event[user_id];
        foreach ($funnel as &$step) {
            $next_step = getEvents([
                app => $step[app],
                name => $step[name],
                value => $step[value],
                time => $event[time],
                ip => $ip,
                user_id => $user_id,
                size => 1,
            ]);
            if ($next_step != null) {
                $step[count] += 1;
                $next_step = $next_step[0];
                $ip = $next_step[ip];
                $user_id = $next_step[user_id];
            }
        }
    }
}


if ($firstStep[count] == 0) {
    $response[sessions] = 0;
    $response[success_percent] = 0;
} else {
    $response[sessions] = $firstStep[count];
    $response[success_percent] = round(end($funnel)[count] / $firstStep[count], 4) * 100;
}

array_unshift($funnel, $firstStep);
foreach ($funnel as $item) {
    $response[funnel][$item[str]] = $item[count];
}

commit($response);