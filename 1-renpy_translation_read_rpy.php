<?php 
/************************************************************
CREATE BY Thanos255
NO COMMERCIAL USE.
You can use this script, modify etc if you show the creator.
You can't use it for win money.

Try my game : https://www.lucie-adult-game.com/
Patreon : https://www.patreon.com/lucie_adult_game

If you use it or like it, please support me on Patreon
***********************************************************/

include("renpy_translation_include.php");
   
$cptLine = 0;
$cptLineGroupe = 0;
$langue = "";
$labelUnique = "";
$tmp = array();
$cas = 0;
$targetFile = "";
$SourceText = "";
$TranslateText = "";
$targetText = "";
$targetFileData = false;
$SymbolOfSeparate = ";";
$etat = 0; // non traduit 
$test1 = "";
$test2 = "";
$withCSVFile = false;

$tarifDeeplL = 100000; // # 1 € !
$cptWord = 0;
$cptCharacteres = 0;

echo "<span style='font-size:30px;'><strong>ETAPE 1 :</strong> We reed the file and take it in database for user: <strong>$idUSER</strong> <br>";
echo "Check langage : <strong>".$langueCheck."</strong><br>";
echo "langage Deepl Source : <strong>".$DeepLSRC."</strong><br>";
echo "langage Deepl Target : <strong>".$DeepLTarget."</strong><br></span>";
echo "<a href='2-renpy_translation_api.php'>Step 2 : Translate BY DEEPL (Paid)</a>";
echo "<br><br><br><br>";
 
// Si $chemin est un dossier => on appelle la fonction explorer() pour chaque élément (fichier ou dossier) du dossier$chemin
if( is_dir($dirParse) ){
	$me = opendir($dirParse);
	echo "New file found : ".str_replace($dirServer, "", $dirParse)."<br>";
	while( $fileTranslate = readdir($me) ){

		if (substr($fileTranslate, -3) == "rpy")
		{
			echo "<b>".htmlentities($dirParse.$fileTranslate)."</b><br>";

			$fileTranslateMysql = $mysqli->real_escape_string($fileTranslate);

			/*Ouverture du fichier en lecture seule*/
			$handle = fopen($dirParse.$fileTranslate, 'r');
			$targetFile = $dirParse."target_".$fileTranslate.".csv";
	
			$mysqli->query("DELETE FROM translation_text WHERE ta_id = 1 AND tt_file = '".$fileTranslateMysql."' "); // Suppression des datas existants sur ce fichier.

			if ($targetFile && file_exists($targetFile)) {
				if (unlink($targetFile) === false)
				{
					print display_error("Impossible d'ouvrir le fichier de destination $targetFile. Fermez Excel !");
					die();
				}
			}

			if ($withCSVFile == true) {
				$targetFileData = fopen($dirParse."target_".$fileTranslate.".csv", 'a+');
				fputs($targetFileData, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) )."\n"); // CAS SPECIAL POUR OUVRIR SOUS EXCEL EN FORCANT UTF8 !!!!
				fputs($targetFileData, "Line in rpy".$SymbolOfSeparate."Cas".$SymbolOfSeparate."ID Line".$SymbolOfSeparate."Source".$SymbolOfSeparate."Translation"."\n");
			}
 
			$cptLine = 0;

			/*Si on a réussi à ouvrir le fichier*/
			if ($handle) {
				/*Tant que l'on est pas à la fin du fichier*/
				while (!feof($handle))	{
					/*On lit la ligne courante*/
					$buffer = fgets($handle);
					$cptLine ++;


					// CASE 1  :
					/*
# game/script_part1.rpy:1999
translate english day5_living_room_test_lucie_2d0d402c:

	# t "J'ai aussi ressenti un moment fort hier, et je pense qu'on devrait aller plus loin!"
	t ""
					*/
					if (preg_match("@^# game/@", $buffer)) // Si c'est la ligne de début du groupe d'une traduction :
					{
						if (isset($_GET['debug'])) echo "CAS 1 : LINE 1 : ".htmlentities($buffer)."<br>";
						$idUniqueInProgress = $buffer; // # game/screens.rpy:321 Stock la signature
						$cptLineGroupe = 0;
						$langue = "";
						$targetText = "";
						$labelUnique = "";
						$cas = 1;
						$cptLineGroupe ++; // ligne 1 faite.
					}


					if ($cas == 1 && $cptLineGroupe == 1 && preg_match("@^(translate )([^ ]*) ([^ ]*)$@", $buffer, $tmp)) // 2 eme ligne : translate english day5_living_room_test_lucie_2d0d402c:
					{
/*
  0 => string 'translate english day_history_1_729787bd:' (length=43)
  1 => string 'translate ' (length=10)
  2 => string 'english' (length=7)
  3 => string 'day_history_1_729787bd:
*/
						$langue = $tmp['2'];
						if ($langueCheck != $langue) {
							print display_error("LANGUE DIFFERENCE DE CELUI ATTENDU : $langueCheck != $langue");
							die();
						}

						$labelUnique = $tmp['3']; // Label unique ----------- day_history_1_729787bd
						$cptLineGroupe ++; // ligne 2 faite.
						if (isset($_GET['debug'])) echo "CAS 1 : LINE 2 : ".htmlentities($langue)." ".htmlentities($labelUnique)."OK<br>";
						unset($tmp);
					}

					if ($cas == 1 && $cptLineGroupe == 2 && preg_match("@^    # @", $buffer, $tmp)) // on cherche la 3 eme ligne. le old ou le # t 
					{
						if (isset($_GET['debug'])) echo "CAS 1 : LINE 3 : Ancienne valeur # : ".htmlentities($buffer)."<br>";
						$SourceText = $buffer;
						$cptLineGroupe ++;
					}

					if ($cas == 1 && $cptLineGroupe == 3 && (preg_match("@^    ([a-zA-Z0-9\_]*) @", $buffer) OR preg_match("@^    \"\"@", $buffer))) // on cherche la 3 eme ligne. le old ou le # t SAUF NEW !
					{
						if (isset($_GET['debug'])) echo "CAS 1 : LINE 4 : Traduction : ".htmlentities($buffer)."<br>";
						$cptLineGroupe = 0;
						$targetText = $buffer;

						$cptWord += str_word_count($SourceText);
						$cptCharacteres += strlen($SourceText);

						if ($withCSVFile == true) 
							fputs($targetFileData, $cptLine.$SymbolOfSeparate.$cas.$SymbolOfSeparate.trim($labelUnique).$SymbolOfSeparate.trim($SourceText).$SymbolOfSeparate.trim($targetText)."\n");



					}
 

// CASE 2 :
/*
translate english strings:

    # game/screens.rpy:321
    old "Nouvelle partie"
    new "New game"

    # game/script_part1.rpy:179
    old "Ne pas lui donner tout de suite"
    new ""
*/
					// LINE 1 ON commence par :  
					if ($buffer && preg_match("@^translate ".$langueCheck." strings:@", $buffer)) // 2 eme ligne : translate english strings:
					{
						/*
						  0 => string 'translate english strings:' (length=28)
						  1 => string 'translate ' (length=10)
						  2 => string 'english' (length=7)
						  3 => string 'strings:
						*/
						$labelUnique = "";
						$targetText = "";
						$cptLineGroupe = 0; // ligne 2 faite.
						$cptLineGroupe ++; // ligne 1 faite.
						$cas = 2;
						if (isset($_GET['debug'])) echo "CAS 2 : LINE 1 : ".htmlentities($langueCheck)." strings OK<br>";
						unset($tmp);
					}

					if ($cas == 2 && $cptLineGroupe == 1 && preg_match("@^    # ([a-zA-Z0-9\_]{1,})/@", $buffer)) // Si c'est la ligne de début du groupe d'une traduction :
					{
						if (isset($_GET['debug'])) echo "CAS 2 : LINE 2 : ".htmlentities($buffer)."<br>";
						$labelUnique = $buffer;// # game/screens.rpy:321 --------- Stock la signature
						$cptLineGroupe ++; // ligne 1 faite.
					}
					if ($cas == 2 && $cptLineGroupe == 2 && preg_match("@^    old \"@", $buffer)) // on cherche la 3 eme ligne. le old ou le # t 
					{
						if (isset($_GET['debug'])) echo "CAS 2 : LINE 3 : Ancienne valeur  : ".htmlentities($buffer)."<br>";
						//$SourceText = preg_replace("@^old \"@", "", substr(trim($buffer), 0, -1)); // on retire le " de fin et le old "
						//if ($SourceText == "")
						$SourceText = $buffer;

						$cptLineGroupe ++;
					}
					if ($cas == 2 && $cptLineGroupe == 3 && preg_match("@^    new \"@", $buffer)) // on cherche la 3 eme ligne. le old ou le # t 
					{
						if (isset($_GET['debug'])) echo "CAS 2 : LINE 4 : La traduction  : ".htmlentities($buffer)."<br>";
						// $targetText = preg_replace("@new \"@", "", substr(trim($buffer), 0, -1)); // on retire le " de fin et le new "
//						if (trim($targetText) == "")
						$targetText = $buffer;

						$cptWord += str_word_count($SourceText);
						$cptCharacteres += strlen($SourceText);

						if ($withCSVFile == true) 
							fputs($targetFileData, $cptLine.$SymbolOfSeparate.$cas.$SymbolOfSeparate.trim($labelUnique).$SymbolOfSeparate.trim($SourceText).$SymbolOfSeparate.trim($targetText)."\n");
						$cptLineGroupe = 1; // 1 est important, car on ne reviendra pas dans le bloc 1. On reprend direct avec un game.
					}


/*
Savoir si c'est déjà traduit :
    # t "(*En pensée*) {t}*J'ai bien dormi! Je suis en pleine forme! \nQuel jour sommes-nous? Dimanche! Mais c'est aujourd'hui que Lucie arrive! Il ne faut pas trop que je traîne!*{/t} "
    t "traduit" 
	=> t ""
ou
    # game/script_part1.rpy:201
    old "La reprendre"
    new "azeaze"

*/
					// if no translation found :
					if ($targetText)
					{
						$test1 = trim($targetText);
						$test2 = trim($SourceText);

						if ($test1 == 'new ""' OR preg_match('@^([a-zA-Z0-9\_]{1,}) ""@', $test1) OR preg_match('@^""$@', $test1)) // Vide à traduire.
							$etat = 0;
						elseif ("# ".$test1 == $test2) // Si remplit DANS la meme langue. 
							$etat = 1;
						elseif (preg_replace("@new \"@", "", $test1) == preg_replace("@old \"@", "", $test2)) // ===# new "Window"===old "Window"===
							$etat = 1;
						else // Déjà traduit ou remplit.
							$etat = 2;

						$targetText = preg_replace("(\r\n|\n|\r)", "", $targetText); // We remove caracteres \n because deepl don't like it
					}
					else
						$etat = 0;

					$targetText = $mysqli->real_escape_string($targetText);
					$buffer = $mysqli->real_escape_string($buffer);

					// On insére CHAQUE LIGNE dans mysql, et on remplit uniquement 
					$mysqli->query("INSERT into translation_text (`tt_file`, `tt_line`, `tt_data`, `tt_translate`, `tt_etat`, `tt_case`, `tt_langue`, ta_id) 
					VALUES ('".$fileTranslateMysql."', '".$cptLine."', '".$buffer."', '".$targetText."', '".$etat."', '".$cas."', '".$langueCheck."', '".$idUSER."')");

					if ($targetText)
						$targetText = "";

				} 
				echo htmlentities($fileTranslateMysql)." done, ".$cptLine." lines ";
				echo "<br><span style='font-size:25px;'>".$cptWord." mots et ". $cptCharacteres." charactères.<br>Donc ".round(($cptCharacteres / $tarifDeeplL),2)."€ sur DeepL<br></span><br>";
	

				/*On ferme le fichier*/
				fclose($handle);
				//fclose($targetFileData);
				set_time_limit(0);
			}
			else {
				print display_error("Impossible d'ouvrir le fichier source : ".$dirParse.$fileTranslate);
				die();
			}
		}
	}
}

echo "<br><br><span style='font-size:45px;'>".$cptWord." words and ". $cptCharacteres." characteres.<br>So ".round(($cptCharacteres / $tarifDeeplL),2)."€ on DeepL<br></span>";

$mysqli->close(); 
  