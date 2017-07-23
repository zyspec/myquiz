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
 global $xoopsConfig,$xoopsDB,$myts,$xoopsModule, $xoopsUser, $xoopsTheme, $xoopsLogger;
 xoops_cp_header();
include ("admin_menutab.php");
echo "<center><b>"._MYQUIZ_SHOWTESTLER."</b></center><br />";
    echo "<table class='table-cev'><tr><td>";
    $result = $xoopsDB->query("SELECT ".$xoopsDB->prefix("myquiz_admin").".quizzId,".$xoopsDB->prefix("myquiz_admin").".quizzTitle,".$xoopsDB->prefix("myquiz_admin").".active,".$xoopsDB->prefix("myquiz_categories").".name FROM ".$xoopsDB->prefix("myquiz_admin").", ".$xoopsDB->prefix("myquiz_categories")." WHERE ".$xoopsDB->prefix("myquiz_admin").".cid = ".$xoopsDB->prefix("myquiz_categories").".cid ORDER BY timeStamp DESC");
        while(list($qid, $quizzTitle,$active,$category) = $xoopsDB->fetchRow($result)){
			echo "<tr  bgcolor='EDF5FA'><td>";
            if ($active == 1) {
			   echo "<B style='color:#056705'>"._MYQUIZ_ACTIVE."</B> - ";
			}
		    else {
				echo "<B style='color:#DD0000'>"._MYQUIZ_INACTIVE."</B> - ";
			}
			echo ""._MYQUIZ_QUIZZ." <B>$qid</B> : ";
            echo "\"".$myts->MakeTboxData4Show($quizzTitle)."\" ";
            echo "(".$myts->MakeTboxData4Show($category).") <br />";
			echo "[ <a href=\"".XOOPS_URL."/modules/myquiz/admin/index.php?acti=QuizzAddQuestion&qidi=$qid\" ><img src='images/add.png' title='"._MYQUIZ_ADDQUESTION."' /></a> | ";
			            echo "<a href=\"".XOOPS_URL."/modules/myquiz/admin/index.php?acti=QuizzModify&qidi=$qid\"><img src='images/mod.png' title='"._MYQUIZ_MODIFY."' /></a> | ";
            echo "<a href=\"".XOOPS_URL."/modules/myquiz/admin/index.php?acti=QuizzRemove&qidi=$qid\"><img src='images/rest.png' title='"._MYQUIZ_DELETE."' /></a> | ";
            echo "<a href=\"".XOOPS_URL."/modules/myquiz/index.php?qidi=$qid\" target=_blank><img src='images/view.png' title='"._MYQUIZ_SEE."' /></a> | ";
			echo "<a href=\"".XOOPS_URL."/modules/myquiz/admin/index.php?acti=QuizzViewStats&qidi=$qid\"><img src='images/stat.png' title='"._MYQUIZ_VIEWSTAT."' /></a> | ";
            echo "<a href=\"".XOOPS_URL."/modules/myquiz/admin/index.php?acti=QuizzViewScore&qidi=$qid\"><img src='images/status.png' title='"._MYQUIZ_VIEWSCORE."'/></a> | ";
            echo "<a href=\"".XOOPS_URL."/modules/myquiz/admin/index.php?acti=QuizzRemoveScore&qidi=$qid\"><img src='images/remove.png' title='"._MYQUIZ_DELSCORE."' /></a> ]";
            
            echo "<tr><td>&nbsp;</td></tr>\n";
        }
        echo "\n";
        echo "</td></tr>";
		echo "</td></tr></table><br /><br />\n";

xoops_cp_footer();
?>