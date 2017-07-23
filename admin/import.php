<?php
//  ------------------------------------------------------------------------ //
//                   MyQuiz - Xoops Quiz System                          //
//                   Copyright (c) 2008 Metemet                              //
//                   <http://www.xoops-tr.com/>                              //
//  ------------------------------------------------------------------------ //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This module is distributed in the hope that it will be useful,           //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//  -----------------------------------------------------------------------  //

include_once("admin_header.php");
 
$myts =& MyTextSanitizer::getInstance();
global $xoopsConfig,$xoopsDB,$myts,$xoopsModule, $xoopsUser, $xoopsTheme, $xoopsLogger;
xoops_cp_header();
include ("admin_menutab.php");
//test adi sec///////////


 $result = $xoopsDB->query("SELECT quizzId FROM ".$xoopsDB->prefix("myquiz_admin")."WHERE quizzId='quizzID'");
 
    $result = $xoopsDB->query("SELECT ".$xoopsDB->prefix("myquiz_admin").".quizzId,".$xoopsDB->prefix("myquiz_admin").".quizzTitle,".$xoopsDB->prefix("myquiz_admin").".active,".$xoopsDB->prefix("myquiz_categories").".name FROM ".$xoopsDB->prefix("myquiz_admin").", ".$xoopsDB->prefix("myquiz_categories")." WHERE ".$xoopsDB->prefix("myquiz_admin").".cid = ".$xoopsDB->prefix("myquiz_categories").".cid ORDER BY timeStamp DESC");


echo "<form method='post' action='import_son.php'>";

	echo "<div align='center'><b>"._MYQUIZ_TESTSEC."</b><br /><br />";
	
	echo "<input type='hidden' name='quizzID' value='$quizzID'>";
    
	echo "<select name='testno'>";			
        while(list($quizzId, $quizzTitle,$active,$category) = $xoopsDB->fetchRow($result)){
		
		 if ($quizzTitle != "") { $sel = "selected "; }
		
            echo "<option name='testno' value=\"$quizzId\"><b>$quizzTitle</b> ($category) </option>\n";
                $sel = "";
       		}
  		echo "</select><br /><br /></div>";	     
        echo "\n";
		
///////////// 	  

$folder="import";
$dir_name= ''.XOOPS_ROOT_PATH.'/modules/myquiz/import/';

echo "<br /><center><b>"._MYQUIZ_DOSYASEC."</b>&nbsp;$title</center><br />";

echo "<center><table border='0' cellpadding='1' cellspacing='1'><tr align='center'>";
echo "<td><select name='filename'>";
$handle=opendir($dir_name);
$count=0;
while (($file = readdir($handle))!==false) {	
if (substr($file,-4) == ".csv" ||  substr($file,-4) == ".txt"){
$clist .= "$file ";
}
}
closedir($handle); 
echo "<option name='filename' value=\"$clist[i]\" selected>"._MYQUIZ_SEC."</option>";
$clist = explode(" ", $clist);
sort($clist);
for ($i=1; $i < sizeof($clist); $i++) {
if($clist[$i]!="") {
echo "<option name='filename' value=\"$clist[$i]\">$clist[$i]";
}
}

echo "</select></td></tr></table>";
echo "<br /><br /><br /><input type='submit' class='optionlite' value='"._MYQUIZ_YUKLE."'>";

echo "</form><br /></center>";
xoops_cp_footer();
?>