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


echo "<form method='post' action='export_son.php'>";
	echo "";
	echo "<div align=center><b>"._MYQUIZ_MEXPORT."</b><br /><br />";
	
	echo "<input type='hidden' name='quizzID' value='$quizzID'>";
	
    
	echo "<select name='testno'>";			
        while(list($quizzId, $quizzTitle,$active,$category) = $xoopsDB->fetchRow($result)){
				 
		 if ($quizzTitle != "") { $sel = "selected "; }
			
            echo "<option name='testno' value='$quizzId,$quizzTitle'><b>$quizzTitle</b> ($category) </option>\n";
                $sel = "";
       		}
  		echo "</select><br /><br /></div>";	     
        echo "\n";
		echo "<br /><center><input type='submit' class='optionlite' value='"._MYQUIZ_MAKEXPORT."'></center>";
		echo "<br />";
		echo "</form><br />";
		echo "<font color='#993300'>"._MYQUIZ_CHMODS."</font>";

////////////////////
$folder="export";
$dir_name= ''.XOOPS_ROOT_PATH.'/modules/myquiz/export/';


echo "<table class='table-cev'><tr align='center'><td>";
echo "<br /><b>"._MYQUIZ_ORDOWN."</b>&nbsp;$title<br /><br />";



$handle=opendir($dir_name);
$count=0;
while (($file = readdir($handle))!==false) {	
if (substr($file,-4) == ".zip" ||  substr($file,-4) == ".csv"){
$clist .= "$file ";
}
}
closedir($handle); 
echo "<form method='post' action='myquiz_download.php'>";
echo "<select name='filename'>";
echo "<option name='filename' value=\"$clist[i]\" selected>"._MYQUIZ_SEC."</option>";
$clist = explode(" ", $clist);
sort($clist);
for ($i=1; $i < sizeof($clist); $i++) {
if($clist[$i]!="") {
echo "<option name='filename' value=\"$clist[$i]\">$clist[$i]";
}
}

echo "</select>";
echo "<br /><br /><br /><input type='submit' class='optionlite' value='"._MYQUIZ_DOWNLOAD."'>";

echo "</form><br /></center>";
echo "</td></tr></table>";
xoops_cp_footer();
?>