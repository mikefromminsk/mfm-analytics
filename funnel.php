<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/mfm-analytics/utils.php";

$time_from = get_int(time_from, time() - 60 * 60 * 24 * 7);
$time_to = get_int(time_to, time());
$funnel_event_names = get_required(event_names);

$funnel_event_names = explode(",", $funnel_event_names);

$session_events = select("select * from events where 1=1"
    . " and name = 'ui_start'"
    . " and time >= $time_from and time <= $time_to");

$funnel = [];
foreach ($funnel_event_names as $funnel_event_name) {
    $filtered_session_events = [];
    foreach ($session_events as $session_event) {
        if ($session_event[session] != cukkwa) continue;
        $session_with_funnel_event = scalar("select * from events where 1=1"
            . " and `session` = '$session_event[session]'"
            . " and `name` = 'ui_call'"
            . " and `to` = '$funnel_event_name'"
            . " and `time` >= '$session_event[time]'");
        if ($session_with_funnel_event !== null) {
            $filtered_session_events[] = $session_event;
        }
    }
    $funnel[$funnel_event_name] = round(sizeof($filtered_session_events) / sizeof($session_events), 2);
    $session_events = $filtered_session_events;
}

$funnel_sum = 1;
foreach ($funnel as $funnel_event_name => $funnel_event_value) {
    $funnel_sum *= $funnel_event_value;
}

$response[funnel_sum] = $funnel_sum;
$response[funnel] = $funnel;
$response[success] = true;

echo json_encode($response);