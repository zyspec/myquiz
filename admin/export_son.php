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
global $xoopsDB;

$main_dir = "".XOOPS_ROOT_PATH."/modules/myquiz/export";
$path = "$main_dir";
if (!file_exists("$main_dir")) {
$old_umask = umask(0);
mkdir("$main_dir", 0777);
}
$export_dir = "".XOOPS_ROOT_PATH."/modules/myquiz/exportem";
$path = "$export_dir";
if (!file_exists("$export_dir")) {
$old_umask = umask(0);
mkdir("$export_dir", 0777);
}

$qid= $_POST['testno'];

$randomdigit = rand(000,999);

$title = $randomdigit."_".$qid;

$title = str_replace(" ", "_", $title);  
$showtitle = str_replace($randomdigit, "", $title);
$showtitle2 = str_replace("_", "", $showtitle);
   

$fp = fopen("".XOOPS_ROOT_PATH."/modules/myquiz/exportem/$title.csv","a");
$dp = fopen("".XOOPS_ROOT_PATH."/modules/myquiz/exportem/$title.doc","a");

fwrite($dp,"$showtitle2");

$result = $xoopsDB->query("SELECT pollID, qid, pollTitle, answer, bad FROM ".$xoopsDB->prefix("myquiz_desc")." WHERE qid='$qid'");


while(list($pollID, $qid, $pollTitle, $answer, $bad ) = $xoopsDB->fetchrow($result))
{
 
$result1 = $xoopsDB->query("SELECT optionText as cvp1 FROM ".$xoopsDB->prefix("myquiz_data")." WHERE (pollID='$pollID') AND (voteID='1')");
   list($cvp1) = $xoopsDB->fetchRow($result1);

$result2 = $xoopsDB->query("SELECT optionText as cvp2 FROM ".$xoopsDB->prefix("myquiz_data")." WHERE (pollID='$pollID') AND (voteID='2')");
   list($cvp2) = $xoopsDB->fetchRow($result2);

$result3 = $xoopsDB->query("SELECT optionText as cvp3 FROM ".$xoopsDB->prefix("myquiz_data")." WHERE (pollID='$pollID') AND (voteID='3')");
   list($cvp3) = $xoopsDB->fetchRow($result3);
 
$result4 = $xoopsDB->query("SELECT optionText as cvp4 FROM ".$xoopsDB->prefix("myquiz_data")." WHERE (pollID='$pollID') AND (voteID='4')");
   list($cvp4) = $xoopsDB->fetchRow($result4);
 
$result5 = $xoopsDB->query("SELECT optionText as cvp5 FROM ".$xoopsDB->prefix("myquiz_data")." WHERE (pollID='$pollID') AND (voteID='5')");
   list($cvp5) = $xoopsDB->fetchRow($result5);


$pollTitle = ucwords(strtolower($pollTitle));
$bad = ucwords(strtolower($bad));
$cvp1 = ucwords(strtolower($cvp1));
$cvp2 = ucwords(strtolower($cvp2));
$cvp3 = ucwords(strtolower($cvp3));
$cvp4 = ucwords(strtolower($cvp4));
$cvp5 = ucwords(strtolower($cvp5));

$search = array(",", "'", "<br/>", "<br />", ";");
$replace  = array("/c/" , "\'", "", "", "/s/");
$pollTitle = str_replace($search, $replace, $pollTitle);
$optionText = str_replace($search, $replace, $optionText);
$bad = str_replace($search, $replace, $bad);
$cvp1 = str_replace($search, $replace, $cvp1);
$cvp2 = str_replace($search, $replace, $cvp2);
$cvp3 = str_replace($search, $replace, $cvp3);
$cvp4 = str_replace($search, $replace, $cvp4);
$cvp5 = str_replace($search, $replace, $cvp5);

fwrite($fp,"$pollTitle,$cvp1,$cvp2,$cvp3,$cvp4,$cvp5,$answer,$bad\n");

$secenekler = " A) $cvp1 \n B) $cvp2 \n C) $cvp3 \n D) $cvp4 \n E) $cvp5 ";

$no=$no+1;

fwrite($dp,"\n\n$no. $pollTitle\n\n$secenekler\n");

}
fwrite($dp,"\n\n\n\n");

$qid= $_POST['testno'];
$result = $xoopsDB->query("SELECT qid, answer FROM ".$xoopsDB->prefix("myquiz_desc")." WHERE qid='$qid'");

while(list($qid,$answer) = $xoopsDB->fetchrow($result))
{
if($answer == 1){
$answer = "A";
}
elseif($answer == 2){
$answer = "B";
}
elseif($answer == 3){
$answer = "C";
}
elseif($answer == 4){
$answer = "D";
}
elseif($answer == 5){
$answer = "E";
}
$num = $num+1;
fwrite($dp,"$num-$answer, ");
}

fwrite($dp,"\n\n\n\n Made with myquiz @ metemet\n\n");
fclose($fp);
fclose($dp);

include_once("myquiz_archive.php");

$folderpath ="".XOOPS_ROOT_PATH."/modules/myquiz/exportem/";
$name ="".XOOPS_ROOT_PATH."/modules/myquiz/export/$title.zip";
$z = new PHPZip();
$z -> Zip($folderpath, $name); 

unlink("".XOOPS_ROOT_PATH."/modules/myquiz/exportem/$title.csv");
unlink("".XOOPS_ROOT_PATH."/modules/myquiz/exportem/$title.doc");

echo "<table bgcolor='#E0EEF7' border='1' bordercolor='#ffffff'><tr><td>";
echo "<br /><center>";
echo "<b>"._MYQUIZ_EXPORT."</b><br /><br />";
echo _MYQUIZ_EXDOWN." =&nbsp;<a href='".XOOPS_URL."/modules/myquiz/export/$title.zip'>$title</a>&nbsp;<br /><br /><br/><a href='".XOOPS_URL."/modules/myquiz/admin/export.php'>"._MYQUIZ_TURNEXPAGE."</a></center>";
echo "</td></tr></table>";

xoops_cp_footer();
?>