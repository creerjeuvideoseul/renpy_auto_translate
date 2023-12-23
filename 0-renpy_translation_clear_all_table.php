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
 
echo "<span style='font-size:30px;'><strong>ETAPE 0 :</strong> DEBUG ERASE ALL DATA IN DATABASE Renpy <br>";
echo "<a href='1-renpy_translation_read_rpy.php'>Step 1 : READ YOUR RPY FILE</a>";
echo "<br><br><br><br>";
 
$mysqli->query("TRUNCATE TABLE translation_request");
echo "TABLE translation_request VIDER <br>";

# $mysqli->query("TRUNCATE TABLE translate_cache");
# echo "TABLE translate_cache VIDER <br>";

$mysqli->query("TRUNCATE TABLE translation_text");
echo "TABLE translation_text VIDER <br>";

$mysqli->close(); 
  