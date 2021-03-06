<?php
sec_session_start();
/**
Just som simple scripts for session handling
*/
function sec_session_start() {
        $session_name = 'sec_session_id'; // Set a custom session name
        $secure = false; // Set to true if using https.
        ini_set('session.use_only_cookies', 1); // Forces sessions to only use cookies.
        $cookieParams = session_get_cookie_params(); // Gets current cookies params.
        session_set_cookie_params(0, $cookieParams["path"], $cookieParams["domain"], $secure, false);
        //$httponly = true; // This stops javascript being able to access the session id.
        ini_set("session.cookie_httponly", true); // This stops javascript being able to access the session id.
        session_name($session_name); // Sets the session name to the one set above.
        session_start(); // Start the php session
        session_regenerate_id(); // regenerated the session, delete the old one.
}

function checkUser() {
	if(!session_id()) {
		sec_session_start();
	}

//	if(!isset($_SESSION["user"])) {header('HTTP/1.1 401 Unauthorized'); die();}
	if(!isset($_SESSION["username"])) {header('HTTP/1.1 401 Unauthorized'); die();}

//	$user = getUser($_SESSION["user"]);
//	$un = $user[0]["username"];
    $un = $_SESSION["username"];

//	if(isset($_SESSION['login_string'])) {
//		if($_SESSION['login_string'] !== hash('sha512', $p.$un) ) {
//			header('HTTP/1.1 401 Unauthorized'); die();
//		} else
    if (isset($_SESSION['user_agent'])) {

        if ($_SERVER['HTTP_USER_AGENT'] !== $_SESSION['user_agent']) {
            header('HTTP/1.1 401 Unauthorized'); die();
        }
    } else {
        header('HTTP/1.1 401 Unauthorized'); die();
    }
	return true;
}

function isUser($u, $p) {
	$db = null;

    $p = hash('sha512', $p.$u);

	try {
		$db = new PDO("sqlite:db.db");
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	catch(PDOException $e) {
		die("Del -> " .$e->getMessage());
	}
	//$q = "SELECT id FROM users WHERE username = '$u' AND password = '$p'";
    $q = "SELECT id FROM users WHERE username = :username AND password = :password";

	$result;
	$stm;
	try {
		$stm = $db->prepare($q);
        $stm->bindParam(':username', $u, PDO::PARAM_STR);
        $stm->bindParam(':password', $p, PDO::PARAM_STR);
		$stm->execute();
		$result = $stm->fetchAll();
		if(!$result) {
			echo "Could not find the user";
            return false;
		}
	}
	catch(PDOException $e) {
        echo("Error creating query: " .$e->getMessage());
		return false;
	}
	return $result;
	
}

//obs, fixa skydd mot sql injection om denna metod ska användas. (används dock inte till något nu)
function getUser($user) {
	$db = null;

	try {
		$db = new PDO("sqlite:db.db");
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	catch(PDOException $e) {
        die("Del -> " .$e->getMessage());
	}
	$q = "SELECT * FROM users WHERE username = '$user'";

	$result;
	$stm;
	try {
		$stm = $db->prepare($q);
		$stm->execute();
		$result = $stm->fetchAll();
	}
	catch(PDOException $e) {
        echo("Error creating query: " .$e->getMessage());
		return false;
	}

	return $result;
}

function logout() {


	if(!session_id()) {
		sec_session_start();
	}
    unset($_COOKIE[session_name()]);
    setcookie(session_name(), null, -1, '/');
    session_destroy();
    session_unset();
}

//funktionalitet för förhindrande av CSRF-attack.
function setToken() {
    //använder tips för CSRF-token-hashning på http://www.eschrade.com/page/generating-secure-cross-site-request-forgery-tokens-csrf/
    $_SESSION['token'] = base64_encode( openssl_random_pseudo_bytes(32));
}

function getToken() {
    return $_SESSION['token'];
}

function checkToken() {
    $isAuthenticated = false;
    if (isset($_SERVER['HTTP_X_AUTH_TOKEN'])) {
        $isAuthenticated = ($_SERVER['HTTP_X_AUTH_TOKEN'] == getToken());
    }
    return $isAuthenticated;
}

