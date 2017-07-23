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
xoops_cp_header();
$myts =& MyTextSanitizer::getInstance();
global $xoopsConfig,$xoopsDB,$myts,$xoopsModule, $xoopsUser, $xoopsTheme, $xoopsLogger;

OpenTable();

$filename = $_POST['filename'];
$testno = $_POST['testno'];


$row = 1;
$handle = fopen ("".XOOPS_ROOT_PATH."/modules/myquiz/import/$filename","r");
while ($data = fgetcsv ($handle, 1024, ",")) {
$soru = "".$data[0]."";
$sec1 = "".$data[1]."";
$sec2 = "".$data[2]."";
$sec3 = "".$data[3]."";
$sec4 = "".$data[4]."";
$sec5 = "".$data[5]."";
$search = array("/c/", "", "/s/");
$replace = array("," , "<br />", ";");
$soru = str_replace($search, $replace, $soru);
$sec1 = str_replace($search, $replace, $sec1);
$sec2 = str_replace($search, $replace, $sec2);
$sec3 = str_replace($search, $replace, $sec3);
$sec4 = str_replace($search, $replace, $sec4);
$sec5 = str_replace($search, $replace, $sec5);
$soru = ucwords(strtolower($soru));
$sec1 = ucwords(strtolower($sec1));
$sec2 = ucwords(strtolower($sec2));
$sec3 = ucwords(strtolower($sec3));
$sec4 = ucwords(strtolower($sec4));
$sec5 = ucwords(strtolower($sec5));

$timeStamp = time();
$row = $xoopsDB->query("INSERT INTO ".$xoopsDB->prefix("myquiz_desc")." VALUES (NULL, '$soru', '$timeStamp', '0', '$testno', '".$data[6]."', '1', '', '".$data[7]."', '', '' )")or die(mysql_error());



$sql="SELECT last_insert_id( pollID ) FROM ".$xoopsDB->prefix("myquiz_desc")." ORDER BY pollID DESC LIMIT 0 , 1"; 
    $sorgu=mysql_query($sql); 
    $oku=mysql_fetch_array($sorgu); 
    $id=$oku[0];  
   
$row = $xoopsDB->query("INSERT INTO ".$xoopsDB->prefix("myquiz_data")." VALUES ('$id', '$sec1', '0', '1')")or die(mysql_error());
$row = $xoopsDB->query("INSERT INTO ".$xoopsDB->prefix("myquiz_data")." VALUES ('$id', '$sec2', '0', '2')")or die(mysql_error());
$row = $xoopsDB->query("INSERT INTO ".$xoopsDB->prefix("myquiz_data")." VALUES ('$id', '$sec3', '0', '3')")or die(mysql_error());
$row = $xoopsDB->query("INSERT INTO ".$xoopsDB->prefix("myquiz_data")." VALUES ('$id', '$sec4', '0', '4')")or die(mysql_error());
$row = $xoopsDB->query("INSERT INTO ".$xoopsDB->prefix("myquiz_data")." VALUES ('$id', '$sec5', '0', '5')")or die(mysql_error());


$row++;
$count=$count+1;
}

fclose ($handle);
unlink("modules/$module_name/import/$filename");
echo "<br /><center>";
echo ""._MYQUIZ_MEVCUTSORU.":&nbsp;<b>$count</b>&nbsp;<br /><br />";
echo "<b>"._MYQUIZ_SORUEKLENDI."</b><br />";
echo "<br />$quizzID &nbsp;<a href='".XOOPS_URL."/modules/myquiz/admin/index.php?acti=QuizzModify&qidi=$testno'>"._MYQUIZ_SEE."</a><br /><br />";

CloseTable();
xoops_cp_footer();


?>