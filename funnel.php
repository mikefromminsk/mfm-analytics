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
        $response[] = $step;
    }
    return $response;
}

$funnel = parseFunnel($funnel);
$startEvent = array_shift($funnel);
$events = getEvents($startEvent[app], $startEvent[name], $startEvent[value], $time_from, null, 0, 1000000) ?: [];
$response[funnel][$startEvent[str]] = sizeof($events);
if (sizeof($events) > 0) {
    foreach ($funnel as $step) {
        $next_steps = [];
        foreach ($events as $event) {
            $next_step = getEvents($step[app], $step[name], $step[value], $event[time], $event[ip], 0, 1);
            if ($next_step != null) {
                $next_steps[] = $next_step;
            }
        }
        $response[funnel][$step[str]] = sizeof($next_steps);
        $events = $next_steps;
    }
}


if (sizeof($events) == 0) {
    $response[sessions] = 0;
    $response[success_percent] = 0;
} else {
    $response[sessions] = $response[funnel][$startEvent[str]];
    $response[success_percent] = round(sizeof($events) / $response[sessions], 2);
}


echo json_encode($response, JSON_PRETTY_PRINT);