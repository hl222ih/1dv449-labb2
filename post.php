<?php

/**
* Called from AJAX to add stuff to DB
*/
function addToDB($message, $user) {
    file_put_contents('php_errors.log', "message: $message, user: $user", FILE_APPEND);
    $db = null;

	try {
        $db = new PDO("sqlite:db.db");
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	catch(PDOException $e) {
        file_put_contents('php_errors.log', $e->getMessage(), FILE_APPEND);
        unset($db);
        die("Something went wrong -> " .$e->getMessage());
	}
	$q = "INSERT INTO messages (message, name) VALUES('$message', '$user')";
	
	try {
        if(!$db->query($q)) {
            file_put_contents('php_errors.log', "couldn't insert message", FILE_APPEND);
        }
	}
	catch(PDOException $e) {
        file_put_contents('php_errors.log', $e->getMessage(), FILE_APPEND);
        unset($db);
    }
	
	$q = "SELECT * FROM users WHERE username = '" .$user ."'";
	$result;
	$stm;
	try {
		$stm = $db->prepare($q);
		$stm->execute();
		$result = $stm->fetchAll();
		if(!$result) {
            return false;
//			return "Could not find the user";
		}
	}
	catch(PDOException $e) {
        file_put_contents('php_errors.log', $e->getMessage(), FILE_APPEND);
        echo("Error creating query: " .$e->getMessage());
        unset($stm);
        unset($db);
        return false;
	}
	// Send the message back to the client
    unset($db);
    echo "Message saved by user: " .json_encode($result);
}

