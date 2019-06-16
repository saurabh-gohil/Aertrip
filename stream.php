<?php

// Used for sending the server side events

header("cache-control: no-cache");
header("Content-Type: text/event-stream");

function sendMsg($id, $msg)
{

    echo "id: $id" . PHP_EOL;
    echo "data: $msg" . PHP_EOL;
    echo PHP_EOL;

    ob_flush();
    flush();
} // function sendMsg ends
