<?php
    //DATA PROCESSING=======================//

    //Populate an array with trimmed data
    function populateData($keys) {
        // Populate the Associative array wih post data
        $data = [];
        foreach($keys as $key) {
            $data[$key] = trim($_POST[$key]);
        }

        return $data;
    }

    //Initialize blank data array
    function initData($keys) {
        // Initialize all keys with blank value
        $data = [];
        foreach($keys as $key) {
            $data[$key] = '';
        }
        
        return $data;
    }

    function isErrorFree($errors_array) {
        foreach($errors_array as $error) {
            if ( !empty($error)) {
                return false;
            }
        }
        return true;
    }

    function checkLength($str, $range) {
        if ( strlen($str) >= $range["min"] && strlen($str) <= $range["max"] ) {
            return true;
        }
        return false;
    }

    function checkEmptyPOSTData($keys, $post_data) {
        if ( empty($post_data[$keys]) ) {
            
        }
    }

    function getBase64EncodedValue($key, $value){
		$encoded_data = "";
			
			if (!empty($key) && !empty($value) ){
				$encoded_data = base64_encode($key . $value);
			}
		
		return $encoded_data;
    }
    
    function getBase64DecodedValue($key, $value){
		$decodedData = "";
			try{
				if (!empty($key) && !empty($value) ){
                    $decoded_data = base64_decode($value);
                    $decoded_Data = explode($key,$decoded_data);
					if( isset($decoded_Data[1]) ){
                        $decodedData = $decoded_Data[1];
					}
				}
			}
			catch(Exception $ex){
                
            }
            
		return $decodedData;
    }
    
    function isAlphaNumeric($str) {
        return ( preg_match("/^[a-zA-Z]*$/", $str) ) ? true : false;
    }

    function isAlphaNumSpaceApos($str) {
        return ( preg_match("/^[A-Za-z][A-Za-z\'\-]+([\ A-Za-z][A-Za-z\'\-]+)*/", $str) ) ? true : false;
    }

    function isPasswordEntryValid($password) {
        return ( preg_match("/^(?=\P{Ll}*\p{Ll})(?=\P{Lu}*\p{Lu})(?=\P{N}*\p{N})(?=[\p{L}\p{N}]*[^\p{L}\p{N}])[\s\S]{8,}$/", $password) ) ? true : false;
    }

    function isUserNameValid($username) {
        return ( preg_match("/^[a-zA-Z0-9\_]*$/", $username) ) ? true : false;
    }

    function isCookieValid($db) {
        //Decode the cookie
        $idFromCookie= getBase64DecodedValue(Constants::$cookie_key, $_COOKIE["rememberMeCookie"]);
        //Find id in the Users table; Return true if found, otherwise return false
        $db->query("SELECT userId FROM users WHERE userId = :id");
        $db->bind(":id", $idFromCookie);
        
        if  ( $rs = $db->getResultRow() ) {
            return $rs;
        }
        else {
            return false;
        }
        // return (Constants::$cookie_key . "-" . $_COOKIE["rememberMeCookie"] . " - " . "CookieId: " . $cookieId);
    }

    function setFingerprint(){
		$_SESSION['fingerprint'] = getBase64EncodedValue($_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']);
        $_SESSION['last_active'] = time();

	}

    function checkFingerprint(){
		$time_limit = 60 * Constants::$timeLimit; //Idle time limit in minutes
		$isValidFingerprint = true;
		
		// $fingerprint = $_SESSION['fingerprint'];
		$current_fingerprint = getBase64EncodedValue($_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']);
		
		
		
		
		if ( isset($_SESSION['fingerprint']) && ($current_fingerprint !=  $_SESSION['fingerprint'])){
            $isValidFingerprint = false;
            redirectTo("users/logout");
            exit();
		}
		elseif ( isset($_SESSION['fingerprint']) && isset($_SESSION['last_active']) ) {
			$session_time = time() - $_SESSION['last_active'];
			if ( $session_time > $time_limit ){
                $isValidFingerprint = false;
				redirectTo("users/logout");
                exit();
			}
			else{
				$_SESSION['last_active'] = time();
				// $isValidFingerprint = true;
			}
		}
		else{
			
			$isValidFingerprint = false;
			// signOut();
		}
	
		return $isValidFingerprint;
	}

?>


