<?php
require_once("get.php");
require_once("post.php");
require_once("sec.php");
//sec_session_start();


/*
* It's here all the ajax calls goes
*/
if(isset($_GET['function'])) {
    if($_GET['function'] == 'logout') {
		logout();
    } 
    elseif($_GET['function'] == 'add') {
	    $name = $_GET["name"];
        $name = strip_tags($name);
		$message = $_GET["message"];
        $message = strip_tags($message);
		addToDB($message, $name);
    }
    elseif($_GET['function'] == 'getMessages') {
        $sinceSerial = 0;
        $count = 0;
        if (isset($_GET['since'])) {
            $sinceSerial = $_GET['since'];
        }
        do {
            $response = getMessages($sinceSerial);
            if ($response) {
                $messages = $response;
                $count = count($messages);
            } else {
                sleep(1);
            }
        } while ($count == 0);
        echo(json_encode($messages));
    }
}