<?php

// get the specific message
function getMessages() {
    file_put_contents('php_errors.log', "\r\ntrying to get messages", FILE_APPEND);
	$db = null;

	try {
        $db = new PDO("sqlite:db.db");
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	catch(PDOException $e) {
        file_put_contents('php_errors.log', $e->getMessage(), FILE_APPEND);
        unset($db);
		die("Del -> " .$e->getMessage());
	}
	
	$q = "SELECT * FROM messages";
	
	$result = "";
	$stm = "";
	try {
		$stm = $db->prepare($q);
		$stm->execute();
		$result = $stm->fetchAll();
	}
	catch(PDOException $e) {
        file_put_contents('php_errors.log', $e->getMessage(), FILE_APPEND);
        echo("Error creating query: " .$e->getMessage());
        unset($stm);
        unset($db);
		return false;
	}
    unset($db);

	if($result) {
        file_put_contents('php_errors.log', "got results\r\n", FILE_APPEND);
        return $result;
    }
	else {
        file_put_contents('php_errors.log', "got no results\r\n", FILE_APPEND);
        return false;
    }
}