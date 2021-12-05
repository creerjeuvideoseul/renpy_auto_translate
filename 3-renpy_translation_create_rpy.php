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
$data = "";

$folderTarget = "fill\\";
$suffixe_file = ".rpy";
$originLangage = "";

echo "STEP 3 : Rewrite rpy file completed in repository /fill/<br>";

// Si $chemin est un dossier => on appelle la fonction explorer() pour chaque élément (fichier ou dossier) du dossier$chemin
if( is_dir($dirParse) ){
	
	if (!is_dir($dirServer.$folderTarget))
		mkdir($folderTarget);

	$me = opendir($dirParse); 

	while( $fileTranslate = readdir($me) ){

		if (substr($fileTranslate, -3) == "rpy")
		{
			
			echo "<br><b>On traite : ".htmlentities($dirParse.$fileTranslate)."</b><br>";

			$fileTranslateMysql = $mysqli->real_escape_string($fileTranslate);
			$fileTarget = $dirParse.$folderTarget.substr($fileTranslate, 0, -4).$suffixe_file;
			echo "fileTarget ".htmlentities($fileTarget)."<br>";
			if (file_exists($fileTarget))
				unlink($fileTarget);
			$targetFileData = fopen($fileTarget, 'a'); // On ouvre le fichier de destination

			// On va reconstruire le fichier avec les traductions :
			$sql = "SELECT * FROM translation_text WHERE tt_file = '".$fileTranslateMysql."' AND ta_id = '".$idUSER."' ORDER BY tt_line ASC ";
			$result = $mysqli->query($sql);
			echo $sql."<br><br>";
			while ($row = $result->fetch_assoc()) {
				$cptLine++;

				if ($row['tt_translate'] == '' && $row['tt_data'] && $row['tt_etat'] == 0) // On veut la ligne de la source, le old juste au dessus.
				{
					$originLangage = $row['tt_data']; 
					//echo "originLangage ".$originLangage."<br>";
				}

				if ($row['tt_translate'] && $row['tt_etat'] == 2)
				{
					$data = $row['tt_translate'];
					$dataSave = $data;

					$chaineARemplir = "";
					$texteSource = $originLangage;
					$texteTraduit = $data;
					$texteTraduitClean = "";
					$texteSourceBalise = "";
			
					// ****************************************************
					// Clean text source
					// ****************************************************
					if ($texteSource && preg_match("@^old \"@", trim($texteSource)))
						$texteSourceClean = preg_replace("@^old \"@", "", trim($texteSource)); // On retire le cas du old
					elseif ($texteSource && preg_match("@^# ([a-zA-Z0-9\_\.]{1,}) \"@", trim($texteSource)))
						$texteSourceClean = preg_replace("@^# ([a-zA-Z0-9\_\.]{1,}) \"@", "", trim($texteSource)); // On retire le cas du t
 					else
						$texteSourceClean = preg_replace("@^# \"@", "", trim($texteSource)); // On retire le cas de rien.
					if (substr($texteSourceClean, -1) == '"')
						$texteSourceClean = substr(trim($texteSourceClean), 0, -1); // On supprime le " de fin.				
					// Chaine à traduire : texteSourceClean sans le new bidule.
					// ****************************************************
					if (isset($_GET['debug'])) echo "<strong>chaine Source</strong><br>".$texteSourceClean."<br>";


					// ****************************************************
					// Clean texte target
					// ****************************************************
					if ($texteTraduit && preg_match("@^new \"@", trim($texteTraduit)))
						$texteTraduitClean = preg_replace("@^new \"@", "", trim($texteTraduit)); // On retire le cas du old
					elseif ($texteTraduit && preg_match("@^([a-zA-Z0-9\_\.]{1,}) \"@", trim($texteTraduit)))
						$texteTraduitClean = preg_replace("@^([a-zA-Z0-9\_\.]{1,}) \"@", "", trim($texteTraduit)); // On retire le cas du t
 					else
						$texteTraduitClean = preg_replace("@^\"@", "", trim($texteTraduit)); // On retire le cas de rien.

					if (substr($texteTraduitClean, -1) == '"')
						$texteTraduitClean = substr(trim($texteTraduitClean), 0, -1); // On supprime le " de fin.
					
					// Attention, ici uniquement le * est géré. quel sont les autres symboles ?
					$chaineARemplir = str_replace($texteTraduitClean, "TXTARPLUNIQUED789", $data); // On remplace la chaine à traduire par une balise UNIQUE.
					// Chaine à traduire : texteTraduitClean sans le new bidule.
					// ****************************************************
					if (isset($_GET['debug'])) echo "<strong>Chaine traduite</strong><br>on remplace ".$texteTraduitClean."<br>PAR TXTARPLUNIQUED789 dans ".$data."<br> Résultat : ".$chaineARemplir."<br><br><br>";


					// ****************************************************
					// Manage "
					if (preg_match('@"@', $data)) // Si y'a un " DANS la traduction :
						$texteTraduitClean = preg_replace('@"@', '\"', $texteTraduitClean); // On remplace " par \" sinon bug.
					// ****************************************************

 
					// ****************************************************
					// Manage balise {t} 
					// ****************************************************
					if ($chaineARemplir != "") {

						foreach($baliseCheck as $balise => $baliseFin){  
						
							if (preg_match("#^".$balise."#" , $texteSourceClean)) // Si la source contient une balise alors on vérifie que la traduction aussi :
							{
								$texteSourceBalise = preg_replace('#'.$balise.'$#', "*".$baliseFin, $texteTraduitClean); 
								if (!preg_match('#^'.$balise.'#', $texteSourceBalise))
								{
									if (!preg_match('#^'.$balise.'\*#', $balise.$texteSourceBalise))
										$texteSourceBalise = $balise."*".$texteSourceBalise;
									else
										$texteSourceBalise = $balise.$texteSourceBalise;
								}
								if (!preg_match('#'.$baliseFin.'$#', $texteSourceBalise))
								{
									if (!preg_match('#\*'.$baliseFin.'$#', $texteSourceBalise.$baliseFin))
										$texteSourceBalise = $texteSourceBalise."*".$baliseFin;
									else
										$texteSourceBalise = $texteSourceBalise.$baliseFin;
								}

								$texteSourceBalise = preg_replace('#^'.$balise.'\*\*#', $balise.'* ', $texteSourceBalise);
								$texteSourceBalise = preg_replace('#\*\*'.$baliseFin.'$#', '*'.$baliseFin, $texteSourceBalise);
								$texteSourceBalise = preg_replace('#'.$balise.'\*'.$baliseFin.'$#', '*'.$baliseFin, $texteSourceBalise);
								
								if ($texteSourceBalise != $texteTraduitClean) {
									if (isset($_GET['debug'])) echo "DEBUG==".$texteSourceBalise."<br>".$chaineARemplir."<br>";
									// On remplit la chaine en remplissant le contenu
									$data = preg_replace("@TXTARPLUNIQUED789@", $texteSourceBalise, $chaineARemplir);
									if (isset($_GET['debug'])) echo "REPLACE balise<br>$dataSave<br> PAR <br>$data<br><br>";
								}
							}
						}
					}

					// ****************************************************
					// Manage firstname / pseudo in translation
					// ****************************************************
					foreach($prenomAModifier as $AncienPrenom => $NouveauPrenom){ 
						if (preg_match("@".$AncienPrenom."@", $data))
						{
							$data = preg_replace("@".$AncienPrenom."@", $NouveauPrenom, $data);
							if (isset($_GET['debug'])) echo "REPLACE Firstname <br>$AncienPrenom<br>$NouveauPrenom<br>$data<br><br>";
						}
					}
					// ****************************************************

				}
				else
					$data = $row['tt_data'];

				if (isset($_GET['debug'])) 
					echo $data."<br>";
				
				fputs($targetFileData, rtrim($data)."\n");
			}

			fclose($targetFileData);

			set_time_limit(0);
  		}
	}
}
echo $cptLine." lines wrotes";

echo "<br><br>Array use : <pre>";
var_dump($prenomAModifier);
var_dump($baliseCheck);
echo "</pre>";
$mysqli->close(); 
  