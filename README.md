# renpy_auto_translate
Renpy auto translate - no commercial use

How to use ?
https://www.youtube.com/watch?v=R6QBtp73fcE

----------------------------------
STEP 1
----------------------------------
- copy your file you need to translate :
C:\Users\xxxxxx\Documents\yourgame\game\tl\english\*.rpy
In 
C:\UwAmp\www\yourrenpyautotranslation\translation\*.rpy
- Create database with renpy_translate.sql
- Add you deepl api Key in renpy_translation_include.php and your database connexion.

----------------------------------
STEP 2:
----------------------------------
- Lauch this file in first :
renpy_translation_read_rpy.php
All data are read from *.rpy and add in database.
All data for this file and this user are delete/clean for each load. (except translate).
No expense at this level.

----------------------------------
STEP 3: DeepL - WARNING  5$ / month + 2$ for 100 000 caracteres.
----------------------------------
- Lauch :
renpy_translation_api.php
We take all data for this file and this user, and we will send to deepl API.
==>> 5$ / month + 2$ for 100 000 caracteres.
Wait a little. 1 hour for 200 000 caracteres.
You can run the script several times, because you have a "cache". 
You paid only the first time for each line.

----------------------------------
STEP 3: DeepL
----------------------------------
- Lauch :
renpy_translation_create_rpy.php
Create the file with all translation in C:\UwAmp\www\yourrenpyautotranslation\translation\fill\
You can run the script several times, no destruction about the source file.