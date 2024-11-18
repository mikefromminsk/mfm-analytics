<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/mfm-analytics/utils.php";

$time_from = get_int(time_from, time() - 60 * 60 * 24 * 7);
$page = get_int(page, 0);
$size = get_int(size, 10);
$funnel_event_names = get_required(event_names);
$funnel_event_names = explode(",", $funnel_event_names);

$session_events = select("select * from events where 1=1"
    . " and `type` = 'ui_start'"
    . " and `time` >= $time_from");

$response[sessions] = sizeof($session_events);
$funnel = [sessions => sizeof($session_events)];
if (sizeof($session_events) > 0) {
    foreach ($funnel_event_names as $funnel_event_name) {
        $filtered_session_events = [];
        foreach ($session_events as $session_event) {
            $session_with_funnel_event = selectRow("select * from events where 1=1"
                . " and `session` = '$session_event[session]'"
                . " and `type` = 'ui_call'"
                . " and `name` = '$funnel_event_name'"
                . " and `time` >= '$session_event[time]'");
            if ($session_with_funnel_event != null) {
                $filtered_session_events[] = $session_with_funnel_event;
            }
        }
        $funnel[$funnel_event_name] = sizeof($filtered_session_events);
        $session_events = $filtered_session_events;
    }
}

$response[funnel] = $funnel;
$response[success_percent] = round($response[sessions] / sizeof($session_events), 2);

echo json_encode($response, JSON_PRETTY_PRINT);