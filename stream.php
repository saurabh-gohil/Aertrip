<?php
/*ini_set("display_errors", 1);
error_reporting(E_ALL);*/
// Used for sending the server side events
require_once ("resources/lib/libMultiCurl.php");

header("cache-control: no-cache");
header("Content-Type: text/event-stream");

function sendMsg($id, $data)
{

    echo "id: {$id}" . PHP_EOL;
    echo "data: {$data}" . PHP_EOL;
    echo PHP_EOL;

    ob_flush();
    flush();
} // function sendMsg ends

// print_r($_REQUEST);
if ($_REQUEST["id"] && file_exists(getcwd() . "/files/{$_REQUEST['id']}"))
{
    // File exist and hence we need to hit the Curl
    // But before that retrieve the file contents
    $fileData = explode("\n", file_get_contents(getcwd() . "/files/{$_REQUEST['id']}"));
    // print_r($fileData);
    $multi = new MultiCURL();
    $multi->GetURLData($fileData, $_REQUEST['id']);
}
else
{
    http_response_code(201);
    ob_flush();
    flush();
}
