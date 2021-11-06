<?
/************************************************************
CREATE BY Thanos255

NO COMMERCIAL USE.
You can use this script, modify etc if you "show the creator".
You can't use it for win money.

Try my game : https://www.lucie-adult-game.com/
Patreon : https://www.patreon.com/lucie_adult_game

If you use it or like it, please support me on Patreon

***********************************************************/

// Database connexion :
$mysqli = new mysqli("localhost", "xxxx", "xxxx", "renpy_translate");
if (mysqli_connect_errno()) {
    printf("Échec de la connexion : %s\n", mysqli_connect_error());
    exit();
}
 
######################### PARAMETERS ##################################
$authkey = "xxxxYOUR KEYSxxxx";

// For you = Tu / vous 
// IF YOU USE FREE VERSION OF DEEPL REMOVE THIS FORMALITY PARAMETERS
$options = "&formality=1";

// Path local, and folder where we put the translate file :
$dirServer = "C:\UwAmp\www\\";
$dirParse = "translation\\";


# Exemple : From EN to French

// Check langage in script / Security
$langueCheck = "french";
// Langage source
$DeepLSRC = "EN";
// Langage target
$DeepLTarget = "FR";
$prenomAModifier = array();
// IdUser in table `translation_ask` who ask
$idUSER = 1;

// Balise need to check at the end, because deepl remove this.
$baliseCheck = array(
	"{t}"=>"{/t}", 
	# ADD YOUR BALISE HERE IF YOU HAVE
);


## IF YOU HAVE THIS ERROR MESSAGE,
## error setting certificate verify locations:
## CAfile: C:\wamp64\www/cacert.pem
## CApath: none"
## install : http://drive.google.com/file/d/1Mp37eBSF9l-HbByB4eN776iKyyq2Fu3b/view?usp=sharing (it's my certificate)

##################### END PARAMETERS ##################################


# NOT USE IN DEFAULT : 
// Paramétratge CSV : not use.
$SymbolOfSeparate = ";";

function display_error($txt) {
	return "<br><div style='font-size:30px; color:red;'>".$txt."</div>";
}

/*
	// CURL PHP DEEPL.
	curl https://api.deepl.com/v2/translate \ 
	-d auth_key=[yourAuthKey] \ 
	-d "text=Hello, world"  \ 
	-d "target_lang=DE"
*/
function traductionByDeepL($textToTranslate, $langueSRC, $langueTarget, $idTTLine, $authkey) {

	global $mysqli, $idUSER, $options;

	if ($textToTranslate == "")
		return false;

	$checkTranslate = $mysqli->real_escape_string($textToTranslate);

	// Cache deepl, if already translate, take it directly.
	$sql2 = "SELECT * FROM translate_cache
		WHERE tc_source = '".$checkTranslate."' 
		AND tc_langage_src = '".$langueSRC."' 
		AND tc_langage_target = '".$langueTarget."' 
		LIMIT 1 
	";
	$result2 = $mysqli->query($sql2);

	// Si y'a uné reponse, on quite direct :
	while ($row2 = $result2->fetch_assoc()) {
		print "<b>CACHE USE OK</b><br>";
		return $row2['tc_translate'];
	}
 
	// HEADERS FROM FIREFOX - APPEARS TO BE A BROWSER REFERRED BY GOOGLE
	$curl = curl_init();
 
	$headers = array();
	$headers[] = 'Content-Type: application/x-www-form-urlencoded';

	// $textToTranslate = "Il fait beau dehors !";

	curl_setopt($curl, CURLOPT_URL, "https://api.deepl.com/v2/translate");
	curl_setopt($curl, CURLOPT_CAINFO, dirname(__FILE__)."/cacert.pem");
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_POST, 1);
	curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($curl, CURLOPT_POSTFIELDS, "auth_key=".$authkey."&target_lang=".$langueTarget."&source_lang=".$langueSRC.$options."&text=".$textToTranslate);
/*
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
*/
	//curl_setopt($curl, CURLOPT_TIMEOUT, 10);
    
	// https://api.deepl.com/v1/translate?text=Hello%20World!&target_lang=EN&auth_key=XXX
	
	$urlCall = "https://api.deepl.com/v2/translate?source_lang=".$langueSRC."&target_lang=".$langueTarget."&auth_key=".$authkey."&text=".$textToTranslate;
	
	$responseArray = false;
	$errorTab = false;

	if (!$response = curl_exec($curl)) {  
        error_log("Passage en CURL PAS OK "); 
        print display_error("Passage en CURL PAS OK ".$urlCall."<br>"); 
		$errorTab = curl_error($curl);

		echo "<pre>";
		var_dump($errorTab);
		echo "</pre>";

    }
    else
	{ 
		print $urlCall."<br>"; 
		$responseArray = json_decode($response);
	}
	
	$urlCall = $mysqli->real_escape_string($urlCall);
	if ($errorTab) $errorTab = $mysqli->real_escape_string($errorTab);
	if ($response) $response = $mysqli->real_escape_string($response);
	
	// Full log de ce qui c'est passé :
	$sql = "INSERT INTO `translation_request` (`tt_id`, `tr_send`, `tr_response`, tr_error, `tr_date`, `ta_id`) 
		VALUES ('".$idTTLine."', '".$urlCall."', '".$response."', '".$errorTab."', now(), '".$idUSER."');";
	$result = $mysqli->query($sql);
	// echo $sql;
 
	if ($response) $responseDeepl = $mysqli->real_escape_string($responseArray->translations['0']->text);
	
	echo "R @".$responseDeepl."@<br>";

	// Add to cache deepl.
	$sql = "INSERT INTO `translate_cache` (`tc_source`, `tc_translate`, `tc_langage_src`, `tc_langage_target`) 
		VALUES ('".$checkTranslate."', '".$responseDeepl."', '".$langueSRC."', '".$langueTarget."');";
	$result = $mysqli->query($sql);
  
	curl_close($curl);
	return $responseArray->translations['0']->text;
}

function ConvertisseurTime($Time){
     if($Time < 3600){ 
       $heures = 0; 
       
       if($Time < 60){$minutes = 0;} 
       else{$minutes = round($Time / 60);} 
       
       $secondes = floor($Time % 60); 
       } 
       else{ 
       $heures = round($Time / 3600); 
       $secondes = round($Time % 3600); 
       $minutes = floor($secondes / 60); 
       } 
       
       $secondes2 = round($secondes % 60); 
      
       $TimeFinal = "$heures h $minutes min $secondes2 s"; 
       return $TimeFinal; 
    }











