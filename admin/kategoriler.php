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

include_once ("admin_header.php");
$myts =& MyTextSanitizer::getInstance();
 global $xoopsConfig,$xoopsDB,$myts,$xoopsModule, $xoopsUser, $xoopsTheme, $xoopsLogger;
  xoops_cp_header();
  include ("admin_menutab.php");
  
#Kategori	

function listele($ustid,$derinlik) { 
 global $xoopsConfig,$xoopsDB,$myts, $xoopsUser, $xoopsTheme, $xoopsLogger;
    $derinlik = $derinlik + 1; 
    $s = mysql_query("select cid, ustid, name from ".$xoopsDB->prefix("myquiz_categories")." where ustid = '$ustid';"); 
	    while ( $c = mysql_fetch_row($s)) { 
	echo "<option value=".$c[0].">";
	if ( $derinlik == 1) { echo str_repeat("#",$derinlik) . " " .$c[2] . '<br>'; }
	else {
        echo str_repeat("-",$derinlik) . " " .$c[2] . '<br>'; 
		}
	echo "</option>\n";
			
			
        $s2 = mysql_query("select count(*) from ".$xoopsDB->prefix("myquiz_categories")." where ustid = '$c[0]';"); 
        $c2 = mysql_fetch_row($s2); 
        if ( $c2[0] > 0 ) { 
            listele($c[0],$derinlik); 
        } 
    } 
	
} 

#Kategori Ekle
    echo "<center><b>"._MYQUIZ_ADDCAT."</b></center><br />";
	echo "<form method='post' action='".XOOPS_URL."/modules/myquiz/admin/index.php'>";
	echo "<table class='table-cev'><tr align='center'><tr><td>";    
    echo "<INPUT type='hidden' name='act' value='createPostedQuizzCategory'>";		
	echo "<table><tr><td align='right'>"._MYQUIZ_PARENTCAT." :</td><td align=center> <SELECT name=\"cid\">";
	echo "<option value=\"0\">---</option>\n";	
listele("0","0");	
	echo "</select></td></tr> ";	 
	 
    echo "<tr><td align='right'>"._MYQUIZ_CAT." :</td><td align=center><input type='text' name='CatName' size=30></td></tr>";
    echo "<tr><td align='right'>"._MYQUIZ_COMMENT." :</td><td align=center><input type='text' name='CatComment' size=30></td></tr>";
    echo "<tr><td align='right'>"._MYQUIZ_CATIMAGE." :</td><td align=center><input type='text' name='CatImage' size=30></td></tr>";   
    echo "<tr><td></td>\n</tr></table><table><tr><td align=center><input type=\"submit\" class=button value='"._MYQUIZ_ADD."'></td></tr></table>";   
    echo "</form>";
    echo "</td></tr></table>";
#Duzelt	
    echo "<br /><br /><center><b>"._MYQUIZ_MODIFYCAT."</b></center><br />";	
    echo "<form method='post'action='".XOOPS_URL."/modules/myquiz/admin/index.php'>";
    echo "<INPUT type='hidden' name='act' value='QuizzModifyCategory'>";
	echo "<table class='table-cev'><tr>";
    echo "<td align=center>"._MYQUIZ_CAT." <SELECT name=\"cid\">";
   listele("0","0");
    echo "</select></td><tr><td colspan='2' align='center'><br /><input type='submit' class='button' value='"._MYQUIZ_MODIFY."'></td></tr>";
    echo "</table></form>"; 
#Sil
    echo "<br /><br /><center><b>"._MYQUIZ_DELCAT."</b></center>";
	echo "<form method='post' action='".XOOPS_URL."/modules/myquiz/admin/index.php'>";
	echo "<INPUT type='hidden' name='act' value='QuizzDelCategory'>";
	echo "<table class='table-cev'><tr><td align='center'>"._MYQUIZ_CAT." ";
	echo " <SELECT name=\"cid\">";
listele("0","0");
	echo "</select></td><tr>";
    echo "<td colspan='2' align='center'><br /><input type='submit' class='button' value='"._MYQUIZ_DELETE."'></td></tr>";
    echo "</table></form>";
	 xoops_cp_footer();
?>