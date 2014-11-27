<?php

// get the specific message
function getMessages($sinceSerial) {
	$db = null;
    if (!$sinceSerial) {
        $sinceSerial = 0;
    }

	try {
        $db = new PDO("sqlite:db.db");
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	catch(PDOException $e) {
        unset($db);
		die("Del -> " .$e->getMessage());
	}
	
	$q = "SELECT * FROM messages WHERE serial > :serial";

	$result = "";
	$stm = "";
	try {
		$stm = $db->prepare($q);
        $stm->bindParam(':serial', $sinceSerial, PDO::PARAM_INT);
        $stm->execute();
		$result = $stm->fetchAll();
	}
	catch(PDOException $e) {
        echo("Error creating query: " .$e->getMessage());
        unset($stm);
        unset($db);
		return false;
	}
    unset($db);

	if($result) {
        return $result;
    }
	else {
        return false;
    }
}