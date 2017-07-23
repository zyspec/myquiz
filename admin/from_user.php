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

include_once 'admin_header.php';

$myts =& MyTextSanitizer::getInstance();
global $xoopsConfig,$xoopsDB,$myts,$xoopsModule,$pid, $xoopsUser, $xoopsTheme, $xoopsLogger;
xoops_cp_header();
include ("admin_menutab.php");
echo "<center><b>"._MYQUIZ_FROMUSER."</b></center>";

    # display if available the contributor questions

    $result = $xoopsDB->query("SELECT pollID, pollTitle, qid FROM ".$xoopsDB->prefix("myquiz_descontrib")." ORDER BY qid");
	
    $resultS  = mysql_num_rows($result);
    if ($resultS > 0) {
	
        echo "<b>"._MYQUIZ_ADDCONTRIB."</b><br /><br />";
	
        echo "<table class='table-cev'>";


        while(list($pollID,$pollTitle,$qid) = $xoopsDB->fetchRow($result)){
	echo "<tr><td>";
	echo "<form method='post' name='form".$pollID."' action='".XOOPS_URL."/modules/myquiz/admin/index.php'>";

	echo "<INPUT type='hidden' name='act' value='deleteContributorQuizzQuestion'>";
    echo "<INPUT type='hidden' name='qid' value=\"$qid\">";
	echo "<INPUT type='hidden' name='pollTitle' value=\"$pollTitle\">";
	echo "<INPUT type='hidden' name='pid' value=\"$pollID\">";
	echo "Test $qid  : ";
	echo "<input type=\"submit\" class='button' value=\""._MYQUIZ_TEMIZ."\"> ";	
	
	echo "<a href=\"".XOOPS_URL."/modules/myquiz/admin/index.php?acti=QuizzAddContrib&pidi=$pollID&qidi=$qid\"> ["._MYQUIZ_DUZFROMUSER."] ".$myts->MakeTboxData4Show($pollTitle)."</a>";
	
	echo "</form>";

	echo "</td></tr>";
		}
        echo "</table><br /><br />";
    }
xoops_cp_footer();
?>