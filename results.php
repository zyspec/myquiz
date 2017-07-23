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

include("header.php");
include(XOOPS_ROOT_PATH."/header.php");
include("../../../mainfile.php");
global $xoopsUser, $xoopsDB, $xoopsConfig, $xoopsTheme, $xoopsLogger;
$myts =& MyTextSanitizer::getInstance();

echo "<table class='outer'><tr><th align='center'><font size='+1'>"._MYQUIZ_ALLRESULTS."</font><br>"._MYQUIZ_MYQUIZ."("._MYQUIZ_LISTSCORE.")</th></tr></table><br><table>";

 $quiz = $xoopsDB->query("SELECT quizzID, quizzTitle FROM ".$xoopsDB->prefix("myquiz_admin"));
 while(list($quizzID,$quizzTitle) = $xoopsDB->fetchRow($quiz)) {
$i = $i + 1;
$son = $i%3;
if ( $son == 1 ) { echo "<tr>"; }
  echo "<td name='$i'><table><tr class='outer'><th align='center'>";
 echo "<a href=\"".XOOPS_URL."/modules/myquiz/index.php?qidi=$quizzID\"><br><b><u>$quizzTitle</u></b></a></th>"; 
 
 $quiz2 = $xoopsDB->query("SELECT qid, username, score FROM ".$xoopsDB->prefix("myquiz_check")." where qid='$quizzID' ORDER BY score DESC LIMIT 5 "); 
 
 echo "<tr class='even'><td align='center'>";
 while(list($qid,$username,$score) = $xoopsDB->fetchRow($quiz2)){

 echo "$username : $score <br>"; 
 }
 echo "</td></tr>";
 echo "</tr></table></td>";
  if ( $son == 0 ) { echo "</tr>"; }

 }
 echo "</tr></table>";

include(XOOPS_ROOT_PATH."/footer.php");
?>
