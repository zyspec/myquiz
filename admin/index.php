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

/*********************************************************/
/* Quizz Functions                                       */
/*********************************************************/

function QuizzAdmin()
{
include("kategoriler.php");
}

/*********************************************************/
/* Quizz Functions                                       */
/*********************************************************/

function QuizzRemoveScore()
{
        global $qid,$xoopsConfig,$xoopsModule,$xoopsDB, $xoopsUser, $xoopsTheme, $xoopsLogger;

xoops_cp_header();
    echo "<table class='table-cev'><tr><td>"; 
    echo "<center>"._MYQUIZ_SURE2DELETESCORE." $qid ?<br /><br />"
        ."[ <a href=\"".XOOPS_URL."/modules/myquiz/admin/index.php?acti=deletePostedScoreQuizz&qidi=$qid\">"._YES."</a> | <a href=\"".XOOPS_URL."/modules/myquiz/admin/show_testler.php\">"._NO."</a> ]</center>";
     echo "</td></tr></table>";
xoops_cp_footer();
}

/*********************************************************/
/* Quizz Functions                                       */
/*********************************************************/

function QuizzViewScore()
{
        global $qid,$nblots,$xoopsDB,$xoopsConfig,$myts,$xoopsModule, $xoopsUser, $xoopsTheme, $xoopsLogger;

    $result = $xoopsDB->query("SELECT quizzTitle, voters FROM ".$xoopsDB->prefix("myquiz_admin")." WHERE quizzID='$qid'");
    list($quizzTitle, $nbscore) = $xoopsDB->fetchRow($result);

xoops_cp_header();
    include ("admin_menutab.php");
	 echo "<b>"._MYQUIZ_LISTSCORE."</b><br />";
    echo "<table class='table-cev'><tr><td>"; 

    $result = $xoopsDB->query("SELECT username, score, email  FROM ".$xoopsDB->prefix("myquiz_check")." WHERE qid='$qid' ORDER BY score DESC,time DESC LIMIT $nbscore ");
        echo "<table>";
        while(list($username,$score,$email) = $xoopsDB->fetchRow($result))
        {
           echo "<tr><td>$username [$score point(s)] <a href=\"mailto:$email\">$email</td></tr>";
        }
        echo "</table><br /><br />";


        # display the drawings lots
        echo "<table width='100%'>";
    echo "<form method='post'action='".XOOPS_URL."/modules/myquiz/admin/index.php'>";
    echo "<INPUT type='hidden' name='act' value='QuizzViewScore'>";
    echo "<INPUT type='hidden' name='qid' value='$qid'>";



        # perform the drawings lots if needed
        if (isset($nblots))
        {
            echo "<tr><td colspan=2><B>"._MYQUIZ_ANDTHEWINNERSARE." :</B></td></tr>";
        $result = $xoopsDB->query("SELECT username, score, email FROM ".$xoopsDB->prefix("myquiz_check")." WHERE qid='$qid' ORDER BY score DESC, RAND() LIMIT $nblots");
                echo "<tr><td>";
        while(list($username,$score,$email) = $xoopsDB->fetchRow($result))
        {
            echo "".$myts->MakeTboxData4Show($username)." ($score) : <A HREF=\"mailto:".$myts->makeTboxData4Show($email)."\">".$myts->makeTboxData4Show($email)."</A><br />";
        }
                echo "</td></tr>";
        }
        else
        {
                $nblots=10;
        }

    echo "<tr><td align='left'><br /><form>"._MYQUIZ_NBWINNERS." <input type='text' name='nblots' value='$nblots' size=3> <input type='submit' class=button value='"._MYQUIZ_LAUNCH."'></td></tr>";
        echo "</form>";
    echo "<tr><td><br /><center><a href=\"".XOOPS_URL."/modules/myquiz/admin/show_testler.php\"> [ "._MYQUIZ_ADMIN." ]</a></center></td></tr>";
                echo "</table>";
     echo "</td></tr></table>";

xoops_cp_footer();
}

/*********************************************************/
/* Quizz Functions                                       */
/*********************************************************/

function QuizzViewStats()
{
        global $qid,$xoopsConfig,$xoopsDB,$myts,$xoopsModule, $xoopsUser, $xoopsTheme, $xoopsLogger;

xoops_cp_header();
    include ("admin_menutab.php");
    echo "<table class='table-cev'><tr><td>";
    echo "<center><a href=\"".XOOPS_URL."/modules/myquiz/admin/show_testler.php\"> [ "._MYQUIZ_ADMIN." ]</a></center>";
        echo "<br /><br /><b>"._MYQUIZ_LISTSTATS."</b>";

    $result = $xoopsDB->query("select quizzTitle, voters from ".$xoopsDB->prefix("myquiz_admin")." where quizzID='$qid'");
    list($quizzTitle, $voters) = $xoopsDB->fetchRow($result);

    $result = $xoopsDB->query("select MAX(score), MIN(score), AVG(score) from ".$xoopsDB->prefix("myquiz_check")." where qid='$qid'");
    list($max,$min,$mean) = $xoopsDB->fetchRow($result);

    echo "<table width='100%' border=0>";
    echo "<tr><td>"._MYQUIZ_NBVOTE." : $voters</td></tr>";
    echo "<tr><td>"._MYQUIZ_MEANSCORE." : $mean </td></tr>";
    echo "<tr><td>"._MYQUIZ_MINSCORE." : $min</td></tr>";
    echo "<tr><td>"._MYQUIZ_MAXSCORE." : $max</td></tr>";
    echo "</table><br /><br />";

    echo "<table width='100%' border=0>";
    $result1 = $xoopsDB->query("SELECT pollID, pollTitle FROM ".$xoopsDB->prefix("myquiz_desc")." WHERE qid='$qid'");
    $a = 1;
        while(list($pollID, $question) = $xoopsDB->fetchRow($result1))
        {
                        echo "<tr><td><B> "._MYQUIZ_QUESTION." $a :</B> \"".$myts->MakeTboxData4Show($question)."\" </td></tr>";

                        $result = $xoopsDB->query("SELECT pollID, pollTitle, timeStamp FROM ".$xoopsDB->prefix("myquiz_desc")." WHERE pollID='$pollID'");
                        $result = $xoopsDB->query("SELECT SUM(optionCount) AS SUM FROM ".$xoopsDB->prefix("myquiz_data")." WHERE pollID='$pollID'");
                        $sum = $xoopsDB->fetchArray($result);
                        $sum = $sum['SUM'];
                        echo "<tr><td><table border=\"0\">";
                        /* cycle through all options */
                        for($i = 1; $i <= 5; $i++)
                        {
                                        /* select next vote option */
                                        $result = $xoopsDB->query("SELECT pollID, optionText, optionCount, voteID FROM ".$xoopsDB->prefix("myquiz_data")." WHERE (pollID='$pollID') AND (voteID='$i')");
                                        $array = $xoopsDB->fetchArray($result);
                                        if(is_array($array))
                                        {
                                                        $optionText = $array['optionText'];
                                                        $optionCount = $array['optionCount'];
                                                        if($optionText != "")
                                                        {
                                                                        echo "<tr><td>";
                                                                        echo "".$myts->MakeTboxData4Show($optionText)."";
                                                                        echo "</td>";
                                                                        if($sum)
                                                                        {
                                                                                        $percent = 100 * $optionCount / $sum;
                                                                        }
                                                                        else
                                                                        {
                                                                                        $percent = 0;
                                                                        }
                                                                        echo "<td>";
                                                                        $percentInt = (int)$percent * 4;
                                                                        $percent2 = (int)$percent;
if ($percent > 0)
{
echo "<img src=\"".XOOPS_URL."/modules/myquiz/images/leftbar.gif\" height=\"12\" width=\"7\" Alt=\"$percent2 %\">";
echo "<img src=\"".XOOPS_URL."/modules/myquiz/images/mainbar.gif\" height=\"12\" width=\"$percentInt\" Alt=\"$percent2 %\">";
echo "<img src=\"".XOOPS_URL."/modules/myquiz/images/rightbar.gif\" height=\"12\" width=\"7\" Alt=\"$percent2 %\">";
}
else {
echo "<img src=\"".XOOPS_URL."/modules/myquiz/images/leftbar.gif\" height=\"12\" width=\"7\" Alt=\"$percent2 %\">";
echo "<img src=\"".XOOPS_URL."/modules/myquiz/images/mainbar.gif\" height=\"12\" width=\"3\" Alt=\"$percent2 %\">";
echo "<img src=\"".XOOPS_URL."/modules/myquiz/images/rightbar.gif\" height=\"12\" width=\"7\" Alt=\"$percent2 %\">";
}
printf(" %.2f %% (%d)", $percent, $optionCount);
echo "</td></tr>";
}
}

}
                        echo "</table><br /><br /></td></tr>";
                        $a++;
        }
    echo "</table>";

     echo "</td></tr></table>";
xoops_cp_footer();
}

/*********************************************************/
/* Quizz Functions                                       */
/*********************************************************/

function QuizzModify()
{
    global $qid,$xoopsConfig,$xoopsDB,$myts,$xoopsModule, $xoopsUser, $xoopsTheme, $xoopsLogger;

	
    $result = $xoopsDB->query("select quizzTitle, nbscore, displayscore, displayresults, tektek, comment, image, restrict_user, log_user, active, cid, contrib, expire, emailadmin, admemail, administrator, conditions from ".$xoopsDB->prefix("myquiz_admin")." where quizzId='$qid'");
    list($quizzTitle, $nbscore, $displayscore, $displayresults, $tektek, $comment, $image,$restrict_user,$log_user,$active,$cid,$contrib,$expire,$emailadmin,$admemail, $administrator,$conditions) = $xoopsDB->fetchRow($result);
$jspath = "".XOOPS_URL."/modules/myquiz/include/js_files";
xoops_cp_header();

    include ("admin_menutab.php");
	echo "  
<script type='text/javascript'>

function f(bu){
var el=document.getElementById('conditions');
el.value = (bu.checked)? '30' : '0'; 
}
</script>";
		
    echo "<center><a href=\"".XOOPS_URL."/modules/myquiz/admin/index.php?acti=QuizzAdmin\"> [ "._MYQUIZ_ADMIN." ]</a></center><br /><br />";
        echo "<b>"._MYQUIZ_MODIFY."</b><br /><br />";

    if ($active==1) { $actif = "checked"; } else { $actif = ""; }
    if ($tektek==1) { $tek = "checked"; } else { $tek = ""; }
    if ($displayscore==1) { $dis = "checked"; } else { $dis = ""; }
    if ($displayresults==1) { $res = "checked"; } else { $res = ""; }
    if ($contrib==1) { $con = "checked"; } else { $con = ""; }
	if ($emailadmin==1) { $eadm = "checked"; } else { $eadm = ""; }
    if ($log_user==1) { $log = "checked"; }
	else { $log = ""; }
    if ($restrict_user==1) { $restrict = "checked"; }
	else { $restrict = ""; }

    echo "\n<form method='post' action='".XOOPS_URL."/modules/myquiz/admin/index.php'>";
    echo "<INPUT type='hidden' name='act' value='modifyPostedQuizz'>";
    echo "<INPUT type='hidden' name='qid' value='$qid'>";
	echo "<table class='table-cev'><tr><td>";
    echo "<table width='100%'>";
    echo "<tr><td>"._MYQUIZ_GENINFOS."</td></tr>";
    echo "<tr><td>"._MYQUIZ_TITLE."</td><td><input type='text' name='quizztitle' value=\"".$myts->MakeTboxData4Edit($quizzTitle)."\" size=30></td></tr>";
    echo "<tr><td>"._MYQUIZ_ACTIVE."</td><td><input type='checkbox' name='active' $actif> <img src='$jspath/images/info.png' title='"._MYQUIZ_HELPACTIVE."'></td></tr>";	
	echo "<tr><td>"._MYQUIZ_TEKTEK."</td><td><input type='checkbox' name='tektek' $tek onclick='f(this)'> <img src='$jspath/images/info.png' title='"._MYQUIZ_HELPTEKTEK."'></td></tr>";
	echo "<tr><td>"._MYQUIZ_ZAMAN." </td><td><input type='text' name='conditions' id='conditions' size='3' value='".$myts->MakeTareaData4Edit($conditions)."' onKeypress='if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;' maxlength='3'> <img src='$jspath/images/info.png' title='"._MYQUIZ_YZAMAN."'></td></tr>";
    echo "<tr><td>"._MYQUIZ_VIEWSCORE."</td><td><input type='checkbox' name='displayscore' $dis> <img src='$jspath/images/info.png' title='"._MYQUIZ_HELPVIEWSCORE."'></td></tr>";
    echo "<tr><td>"._MYQUIZ_VIEWANSWER."</td><td><input type='checkbox' name='displayresults' $res> <img src='$jspath/images/info.png' title='"._MYQUIZ_HELPANSWER."'></td></tr>";
    echo "<tr><td>"._MYQUIZ_CONTRIB."</td><td><input type='checkbox' name='contrib' $con> <img src='$jspath/images/info.png' title='"._MYQUIZ_HELPCONTRIB."'></td></tr>";
    echo "<tr><td>"._MYQUIZ_LIMITVOTE."</td><td><input type='checkbox' name='log' $log> <img src='$jspath/images/info.png' title='"._MYQUIZ_HELPLIMITVOTE."'></td></tr>";
    echo "<tr><td>"._MYQUIZ_NBSCORE."</td><td><input type='text' name='nbscore' value='".$myts->MakeTboxData4Edit($nbscore)."' size=3 onKeypress='if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;' maxlength='3' > <img src='$jspath/images/info.png' title='"._MYQUIZ_HELPNBSCORE."'></td></tr>";
	
	echo "<tr><td>"._MYQUIZ_SENDEMAIL."</td><td><input type='checkbox' name='emailadmin' $eadm><img src='$jspath/images/info.png' title='"._MYQUIZ_HELPEMAIL."'> </td></tr>";
	
	
	
    echo "<tr><td>"._MYQUIZ_IMAGE."</td><td><input type='text' name='image' value=\"".$myts->MakeTboxData4Edit($image)."\" size=30> (*)<img src='$jspath/images/info.png' title='"._MYQUIZ_HELPIMAGE."'> </td></tr>";
    echo "<tr><td>"._MYQUIZ_CAT."</td><td><SELECT name=\"cid\">";
    $result = $xoopsDB->query("select cid, name from ".$xoopsDB->prefix("myquiz_categories"));
    while(list($catid, $name) = $xoopsDB->fetchRow($result))
        {
        if ($catid==$cid) { $sel = "selected "; }
            echo "<option $sel value=\"$catid\">".$myts->MakeTboxData4Edit($name)."</option>\n";
                $sel = "";
        }
    echo "</select></td></tr>";

        # expiration date (a lot of code for so few things)
        ereg("([0-9x]{4})-([0-9x]{1,2})-([0-9x]{1,2}) ([0-9x]{1,2}):([0-9x]{1,2})", $expire, $datetime);

        echo "<tr><td>"._MYQUIZ_EXPIRATION." (*)</td><td>";
    $xday = 1;
    echo " "._MYQUIZ_DAY.": <SELECT name=\"day\">";
        if ($datetime[3] == "xx") { $sel="selected"; } else { $sel=""; }
        echo "<option name=\"day\" $sel>xx</option>";
    while ($xday <= 31) {
        if ($xday == $datetime[3]) {
            $sel = "selected";
        } else {
            $sel = "";
        }
        echo "<option name=\"day\" $sel>$xday</option>";
        $xday++;
    }
    echo "</select>";
    $xmonth = 1;
    echo " "._MYQUIZ_UMONTH.": <SELECT name=\"month\">";
        if ($datetime[2] == "xx") { $sel="selected"; } else { $sel=""; }
        echo "<option name=\"month\" $sel>xx</option>";
    while ($xmonth <= 12) {
        if ($xmonth == $datetime[2]) {
            $sel = "selected";
        } else {
            $sel = "";
        }
        echo "<option name=\"month\" $sel>$xmonth</option>";
        $xmonth++;
    }
    echo "</select>";
    echo " "._MYQUIZ_YEAR.": <input type=\"text\" name=\"year\" value=\"$datetime[1]\" size=\"5\" maxlength=\"4\">";
    echo " "._MYQUIZ_HOUR.": <SELECT name=\"hour\">";
    $xhour = 0;
    $cero = "0";
        if ($datetime[4] == "xx") { $sel="selected"; } else { $sel=""; }
        echo "<option name=\"hour\" $sel>xx</option>";
    while ($xhour <= 23) {
        $dummy = $xhour;
        if ($xhour < 10) {
            $xhour = "$cero$xhour";
        }
        if ($xhour == $datetime[4]) {
            $sel = "selected";
        } else {
            $sel = "";
        }
        echo "<option name=\"hour\" $sel>$xhour</option>";
        $xhour = $dummy;
        $xhour++;
    }
    echo "</select>";
    echo ": <SELECT name=\"min\">";
    $xmin = 0;
        if ($datetime[5] == "xx") { $sel="selected"; } else { $sel=""; }
        echo "<option name=\"min\" $sel>xx</option>";
    while ($xmin <= 59) {
        if (($xmin == 0) OR ($xmin == 5)) {
            $xmin = "0$xmin";
        }
        if ($xmin == $datetime[5]) {
            $sel = "selected";
        } else {
            $sel = "";
        }
        echo "<option name=\"min\" $sel>$xmin</option>";
        $xmin = $xmin + 5;
    }
    echo "</select>";


        $now = formatTimestamp(time());
    echo "</select> <img src='$jspath/images/info.png' title='"._MYQUIZ_NOWIS." : $now'></td></tr>";

    echo "<tr><td colspan=2>"._MYQUIZ_COMMENT." (*)<br /><TEXTAREA wrap=\"virtual\" cols=\"50\" rows=\"4\" name=\"comment\">".$myts->MakeTareaData4Edit($comment)."</textarea></td></tr>";
   echo "</table>";
    echo "<br /><center><input type='submit' class=button value='"._MYQUIZ_MODIFY."'></center>";
        echo "* : "._MYQUIZ_HELPOPTION."\n";
    echo "</form><br />";


    echo "<table width='100%'>";
    echo "<tr><td><B>"._MYQUIZ_QUESTIONS."</B></td></tr>";
    echo "<tr><td>";
    echo "<center> <a href=\"".XOOPS_URL."/modules/myquiz/admin/index.php?acti=QuizzAddQuestion&qidi=$qid\"><img src=\"".XOOPS_URL."/modules/myquiz/admin/images/add.png\" /> <br>[ "._MYQUIZ_ADDQUESTION." ]</a> </center>";
    echo "<br /></td></tr>";

    $result = $xoopsDB->query("SELECT pollID, pollTitle FROM ".$xoopsDB->prefix("myquiz_desc")." WHERE qid='$qid'");
    while(list($pollID, $question) = $xoopsDB->fetchRow($result))
    {
        echo "<tr><td>";
        echo ""._MYQUIZ_QUESTION." $pollID : ";
        echo "\"".$myts->MakeTboxData4Edit($question)."\" [ <a href=\"".XOOPS_URL."/modules/myquiz/admin/index.php?acti=QuizzModifyQuestion&pidi=$pollID&qidi=$qid\">"._MYQUIZ_MODIFY."</a> | ";
        echo "<a href=\"".XOOPS_URL."/modules/myquiz/admin/index.php?acti=QuizzDelQuestion&pidi=$pollID&qidi=$qid\">"._MYQUIZ_DELETE."</a> ]</td></tr>";
        }
    echo "</table>";

    echo "</td></tr></table>";
xoops_cp_footer();
}

/*********************************************************/
/* Quizz Functions                                       */
/*********************************************************/

function QuizzAddContrib()
{
    global $xoopsDB,$xoopsConfig,$myts,$pid,$qid,$xoopsModule, $xoopsUser, $xoopsTheme, $xoopsLogger;

xoops_cp_header();
       include ("admin_menutab.php");
      echo "<b>"._MYQUIZ_ADDCONTRIB."</b><br /><br />";

    $result = $xoopsDB->query("SELECT pollTitle, answer, coef, good, bad, comment, image FROM ".$xoopsDB->prefix("myquiz_descontrib")." WHERE pollID='$pid'");
    list ($pollTitle,$answer,$coef,$good,$bad,$comment,$image) = $xoopsDB->fetchRow($result);
        $pollTitle = preg_replace("/\"/","&quot;",$pollTitle);

        # check if the answer was ordered
        if (ereg(",",$answer))
        {
                $array_answer = split(',',$answer);
        }

    echo "<form method='post' action='".XOOPS_URL."/modules/myquiz/admin/index.php'>";
    echo "<INPUT type='hidden' name='act' value='createPostedQuizzQuestion'>";
    echo "<INPUT type='hidden' name='qid' value=\"$qid\">";
    echo "<INPUT type='hidden' name='pid' value=\"$pid\">";
    echo "<INPUT type='hidden' name='mode' value='contrib'>";
    echo "<table><tr><td>"._MYQUIZ_QUESTIONTITLE.": <input type=\"text\" name=\"question\" size=\"50\" maxlength=\"300\" value=\"".$myts->MakeTboxData4Edit($pollTitle)."\"><br /><br /><br /></td></tr></table>";
    echo "<table><tr><td>";
    echo "<tr><td>"._MYQUIZ_COEF."</td><td><input type='text' name='coef' value='$coef' size=3></td></tr>";
    echo "<tr><td>"._MYQUIZ_IMAGE." (*)</td><td colspan=3><input type='text' name='image' value='".$myts->MakeTboxData4Edit($image)."' size=50></td></tr>";
    $i=1;
    echo "<tr><td colspan=2>"._MYQUIZ_ANSWERS."</td><td>"._MYQUIZ_ANSWER."</td><td></td></tr>";
    $result = $xoopsDB->query("SELECT optionText, voteID FROM ".$xoopsDB->prefix("myquiz_datacontrib")." WHERE pollID='$pid' ORDER BY voteID");
    while(list($Text,$voteID) = $xoopsDB->fetchRow($result)){
		if ($answer == $i) {$checked = "checked";} else {$checked = "";}
        echo "<tr><td>"._MYQUIZ_ANSWER." $i :</td><td><input type=\"text\" name=\"optionText[$i]\" size=\"50\" maxlength=\"300\" value=\"".$myts->MakeTboxData4Edit($Text)."\"></td>\n";
        echo "<td><input type=\"radio\" name=\"answer\" value=\"$i\" $checked> ";/*.$myts->MakeTboxData4Edit($optionText)*/
		echo "<br /></td>";

              //  echo "<td><SELECT name=\"optionSort[$i]\">";
             //   echo "<option name=\"rank\">--</option>";
             //   for($j = 1; $j <= 12; $j++)
              //  {
              //  if ($answer == $j) { $sel = "selected"; } else { $sel = "";  }
              //  echo "<option name=\"rank\" $sel>$j</option>";
              //  }
             //   echo "</select>";

        $i++;
    }
    echo "</tr></table>";
	echo "<br /><br />";
	echo "<table width='100%' border=0>";
	echo "<tr><td colspan=2>"._MYQUIZ_COMMENT." (*)<br />";
	echo "<TEXTAREA wrap=\"virtual\" cols=\"50\" rows=\"4\" name=\"comment\">".$myts->MakeTareaData4Edit($comment)."</textarea></td></tr>";
	echo "<tr><td colspan=2>"._MYQUIZ_IFBADANSWER." (*)<br />";
	echo "<TEXTAREA wrap=\"virtual\" cols=\"50\" rows=\"4\" name=\"bad\">".$myts->MakeTareaData4Edit($bad)."</textarea></td></tr>";
	echo "<tr><td colspan=2>"._MYQUIZ_IFGOODANSWER." (*)<br />";
	echo "<TEXTAREA wrap=\"virtual\" cols=\"50\" rows=\"4\" name=\"good\">".$myts->MakeTareaData4Edit($good)."</textarea></td></tr>";
	echo "<tr align='center'><td><br />-<input type=\"submit\" class=button value=\""._MYQUIZ_ADD."\"></form>-";
	echo "<form method='post' action='".XOOPS_URL."/modules/myquiz/admin/index.php'>";
	echo "<INPUT type='hidden' name='act' value='deleteContributorQuizzQuestion'>";
    echo "<INPUT type='hidden' name='qid' value=\"$qid\">";
	echo "<INPUT type='hidden' name='pid' value=\"$pid\">";
	echo "<br />-<input type=\"submit\" class=button value=\""._MYQUIZ_DELCONTRIB."\">-";
	echo "</form></td></td></tr></table>";

    echo "* : "._MYQUIZ_HELPOPTION."\n";
    
xoops_cp_footer();
}
/*********************************************************/
/* Quizz Functions                                       */
/*********************************************************/

function QuizzModifyCategory()
{
    global $xoopsConfig,$xoopsDB,$cid,$myts,$xoopsModule, $xoopsUser, $xoopsTheme, $xoopsLogger;

xoops_cp_header();

    
    echo "<table bgcolor='#E7F1F8' border='1'><tr><td><b>"._MYQUIZ_MODIFYCAT."</b><br /><br />";

	$cid = $_POST['cid'];

    $result = $xoopsDB->query("SELECT ustid, name, comment, image from ".$xoopsDB->prefix("myquiz_categories")." where cid='$cid'");
    list ($ustid, $name, $comment, $image) = $xoopsDB->fetchRow($result);

    echo "\n<form method='post'action='".XOOPS_URL."/modules/myquiz/admin/index.php'>";
    echo "<INPUT type='hidden' name='act' value='modifyPostedQuizzCategory'>";
	
    echo "<INPUT type='hidden' name='cid' value='$cid'>";
    echo "";
    echo "<table width='100%'>";	
    echo "<tr><td>"._MYQUIZ_CAT."</td><td><input type='text' name='CatName' value='".$myts->MakeTboxData4Edit($name)."'size=30></td>";
    echo "<tr><td>"._MYQUIZ_COMMENT."</td><td><input type='text' name='CatComment' value='".$myts->MakeTboxData4Edit($comment)."'size=30> (*)</td>";
    echo "<tr><td>"._MYQUIZ_CATIMAGE."</td><td><input type='text' name='CatImage' value='".$myts->MakeTboxData4Edit($image)."'size=30> (*)</td>";
    echo "<td><input type='submit' class=button value='"._MYQUIZ_MODIFY."'></td></tr></table>";
    echo "</form>";
    echo "* : "._MYQUIZ_HELPOPTION."\n </td></tr></table>";

    
xoops_cp_footer();
}

/*********************************************************/
/* Quizz Functions                                       */
/*********************************************************/

function QuizzModifyQuestion(){
    global $xoopsConfig,$xoopsDB,$qid,$pid,$myts,$xoopsModule, $xoopsUser, $xoopsTheme, $xoopsLogger;

xoops_cp_header();
    include ("admin_menutab.php");
	 echo "<center><b>"._MYQUIZ_MODIFYQUESTION."</b></center><br /><br />";
	echo "<table class='table-cev'><tr><td>";
   

    $result = $xoopsDB->query("SELECT pollTitle, answer, coef, good, bad, comment, image FROM ".$xoopsDB->prefix("myquiz_desc")." WHERE pollID='$pid'");
    list ($pollTitle,$answer,$coef,$good,$bad,$comment,$image) = $xoopsDB->fetchRow($result);
        $pollTitle = preg_replace("/\"/","&quot;",$pollTitle);

        # check if the answer was ordered
        if (ereg(",",$answer))
        {
                $array_answer = split(',',$answer);
        }

    echo "\n<form method='post'action='".XOOPS_URL."/modules/myquiz/admin/index.php'>";
    echo "<INPUT type='hidden' name='act' value='modifyPostedQuizzQuestion'>";
    echo "<INPUT type='hidden' name='qid' value=\"$qid\">";
    echo "<INPUT type='hidden' name='pid' value=\"$pid\">";
    echo "<table width='100%' border=0><tr><td>"._MYQUIZ_QUESTIONTITLE.": <input type=\"text\" name=\"question\" size=\"50\" maxlength=\"300\" value=\"".$myts->MakeTboxData4Edit($pollTitle)."\"><br /><br /><br /></td></tr></table>";
    echo "<table width='100%' border=0><tr><td>";
    echo "<tr><td>"._MYQUIZ_COEF."</td><td colspan=3><input type='text' name='coef' value='$coef' size=3></td></tr>";
    echo "<tr><td>"._MYQUIZ_IMAGE." (*)</td><td colspan=3><input type='text' name='image' value='".$myts->MakeTboxData4Edit($image)."' size=50></td></tr>";
	$i=1;
	echo "<tr><td colspan=2>"._MYQUIZ_ANSWERS."</td><td>"._MYQUIZ_ANSWER."</td><td></td></tr>";
    $result = $xoopsDB->query("select optionText, voteID from ".$xoopsDB->prefix("myquiz_data")." WHERE pollID='$pid' ORDER BY voteID");
    while(list($Text,$voteID) = $xoopsDB->fetchRow($result)){
		if ($answer == $i) {$checked = "checked";} else {$checked = "";}
        echo "<tr><td>"._MYQUIZ_ANSWER." $i :</td><td><input type=\"text\" name=\"optionText[$i]\" size=\"50\" maxlength=\"300\" value=\"".$myts->MakeTboxData4Edit($Text)."\"></td>\n";
        echo "<td><input type=\"radio\" name=\"answer\" value=\"$i\" $checked><br /></td>";

        /*
        echo "<td><SELECT name=\"optionSort[$i]\">";
        echo "<option name=\"rank\">--</option>";
        for($j = 1; $j <= 12; $j++){
			if ($answer == $j){$sel = "selected";}
			else {$sel = "";}
            echo "<option name=\"rank\" $sel>$j</option>";
        }
        echo "</select>";
		*/
       $i++;
    }
    echo "</tr></table><br /><br />";
    echo "<table width='100%' border=0>";
	echo "<tr><td>"._MYQUIZ_COMMENT." (*)<br />";
	echo "<TEXTAREA wrap=\"virtual\" cols=\"50\" rows=\"4\" name=\"comment\">".$myts->MakeTareaData4Edit($comment)."</textarea></td></tr>";
	echo "<tr><td>"._MYQUIZ_IFBADANSWER." (*)<br />";
	echo "<TEXTAREA wrap=\"virtual\" cols=\"50\" rows=\"4\" name=\"bad\">".$myts->MakeTareaData4Edit($bad)."</textarea></td></tr>";
	echo "<tr><td>"._MYQUIZ_IFGOODANSWER." (*)<br />";
	echo "<TEXTAREA wrap=\"virtual\" cols=\"50\" rows=\"4\" name=\"good\">".$myts->MakeTareaData4Edit($good)."</textarea></td></tr></table><br /><br />";
	echo "<input type=\"submit\" class=button value=\""._MYQUIZ_MODIFYQUESTION."\">";
	echo "</form>";
    echo "* : "._MYQUIZ_HELPOPTION."\n </td></tr></table>";
   
xoops_cp_footer();
}

/*********************************************************/
/* Quizz Functions                                       */
/*********************************************************/

function modifyPostedQuizzQuestion()
{
    global $question, $optionText,$qid,$pid,$answer,$coef,$good,$bad,$comment,$optionSort,$image,$myts,$xoopsConfig,$xoopsDB, $xoopsUser, $xoopsTheme, $xoopsLogger;
    
	$question = $_POST['question'];
    $question = $myts->MakeTboxData4Save($question);
	$good = $_POST['good'];
    $good = $myts->MakeTareaData4Save($good);
	$bad = $_POST['bad'];
    $bad = $myts->MakeTareaData4Save($bad);
	$comment = $_POST['comment'];
    $comment = $myts->MakeTareaData4Save($comment);
	$answer = $_POST['answer'];
	$coef = $_POST['coef'];
	$pid = $_POST['pid'];
	$image = $_POST['image'];
	$optionText = $_POST['optionText'];
	$qid = $_POST['qid'];

        $ordered_answer = implode(",",$optionSort);
        $ordered_answer = preg_replace("/,--|--,|--/","",$ordered_answer);

        # check if sorted answer is needed
        if (!empty($ordered_answer)){
			# check if all availaible answers are sorted
			for($i = 1; $i <= sizeof($optionText); $i++){
				if ((!empty($optionText[$i]) and $optionSort[$i] == "--") or (empty($optionText[$i]) and $optionSort[$i] != "--")){
					xoops_cp_header();
					OpenTable();
					echo "<center>"._MYQUIZ_INCORRECTORDER."</center>";
					CloseTable();
					xoops_cp_footer();
					exit;
				}
			}
			# change the answer the the ordered answer
			$answer = $ordered_answer;
		}

        # update general information about current question ...
		if(!$xoopsDB->query("UPDATE ".$xoopsDB->prefix("myquiz_desc")." SET pollTitle='$question', answer='$answer', coef='$coef', good='$good', bad='$bad', comment='$comment', image='$image' WHERE pollID='$pid'")){
			echo $xoopsDB->errno(). ": ".$xoopsDB->error(). "<br />";
			return;
		}

    for($i = 1; $i <= sizeof($optionText); $i++){
		if($optionText[$i] != ""){
			$optionText[$i] = $myts->MakeTboxData4Save($optionText[$i]);
		}
        if(!$xoopsDB->query("UPDATE ".$xoopsDB->prefix("myquiz_data")." SET optionText='$optionText[$i]' WHERE voteID='$i' AND pollID='$pid'")){
			echo $xoopsDB->errno(). ": ".$xoopsDB->error(). "<br />";
			return;
		}
	}
    Header("Location: ".XOOPS_URL."/modules/myquiz/admin/index.php?acti=QuizzModify&qidi=$qid");
}


/*********************************************************/
/* Quizz Functions                                       */
/*********************************************************/

function QuizzAddQuestion()
{
    global $qid,$xoopsConfig,$xoopsModule, $xoopsDB, $xoopsUser, $xoopsTheme, $xoopsLogger;

xoops_cp_header();
    include ("admin_menutab.php");
	   echo "<center><b>"._MYQUIZ_ADDQUESTION."</b></center><br /><br />";
	echo "<table class='table-cev'><tr><td>";
 

    echo "\n<form method='post' action='".XOOPS_URL."/modules/myquiz/admin/index.php'>";
    echo "<INPUT type='hidden' name='act' value='createPostedQuizzQuestion'>";
    echo "<INPUT type='hidden' name='qid' value=\"$qid\">";
    echo "<table width='100%' border=0><tr><td>"._MYQUIZ_QUESTIONTITLE.": <input type=\"text\" name=\"question\" size=\"50\" maxlength=\"300\" value=\"?\"><br /><br /><br /></td></tr></table><table width='100%' border=0><tr><td>";
    echo "<tr><td>"._MYQUIZ_COEF."</td><td colspan=3><input type='text' name='coef' value='1' size=3></td></tr>";
    echo "<tr><td>"._MYQUIZ_IMAGE." (*)</td><td colspan=3><input type='text' name='image' size=50></td></tr>";
        echo "<tr><td colspan=2>"._MYQUIZ_ANSWERS."</td><td>"._MYQUIZ_ANSWER."</td></tr>";
    for($i = 1; $i <= 5; $i++)
    {
        if ($i == 1) { $checked = "checked"; } else { $checked = "";}
        echo "<tr><td>"._MYQUIZ_ANSWER." $i:</td>";
                echo "<td><input type=\"text\" name=\"optionText[$i]\" size=\"50\" maxlength=\"300\"></td>";
        echo "<td><input type=\"radio\" name=\"answer\" value=\"$i\" $checked> <br /></td>";

      //          echo "<td><SELECT name=\"optionSort[$i]\">";
      //          echo "<option name=\"rank\">--</option>";
     //           for($j = 1; $j <= 12; $j++)
       //         {
       //                         echo "<option name=\"rank\" $sel>$j</option>";
        //        }
         //       echo "</select>";
    }
    echo "</tr></table><br /><br />"
    ."<table width='100%' border=0>"
        ."<tr><td>"._MYQUIZ_COMMENT." (*)<br />"
    ."<TEXTAREA wrap=\"virtual\" cols=\"50\" rows=\"6\" name=\"comment\"></textarea></td></tr>"
        ."<tr><td>"._MYQUIZ_IFBADANSWER." (*)<br />"
    ."<TEXTAREA wrap=\"virtual\" cols=\"50\" rows=\"6\" name=\"bad\"></textarea></td></tr>"
    ."<tr><td>"._MYQUIZ_IFGOODANSWER." (*)<br />"
    ."<TEXTAREA wrap=\"virtual\" cols=\"50\" rows=\"6\" name=\"good\"></textarea></td></tr>"
    ."</table><br /><br /><input type=\"submit\" class=button value=\""._MYQUIZ_ADDQUESTION."\">"
    ."</form>";
    echo "* : "._MYQUIZ_HELPOPTION."\n";
    echo "</td></tr></table>";
xoops_cp_footer();
}

/*********************************************************/
/* Quizz Functions                                       */
/*********************************************************/

function QuizzAdd()
{
    global $xoopsConfig,$xoopsDB,$myts,$xoopsModule, $xoopsUser, $xoopsTheme, $xoopsLogger;
	 xoops_cp_header();
	 include ("admin_menutab.php");
$jspath = "".XOOPS_URL."/modules/myquiz/include/js_files";
	
	echo "  
<script type='text/javascript'>

function f(bu){
var el=document.getElementById('conditions');
el.value = (bu.checked)? '30' : '0'; 
}
</script>";
	
	$say = "SELECT COUNT(cid) FROM ".$xoopsDB->prefix("myquiz_categories"); 
	 
$resultsay = mysql_query($say) or die(mysql_error());

// Print out result
while($row = mysql_fetch_array($resultsay)){

$sayili = $row['COUNT(cid)'];
	
	
}
if ($sayili != 0){


    echo "<center><b>"._MYQUIZ_NEW."</b></center><br />";

    echo "\n<form method='post' action='".XOOPS_URL."/modules/myquiz/admin/index.php'>";
    echo "<INPUT type='hidden' name='act' value='createPostedQuizz'>";
    echo "";
    	echo "<table class='table-cev'>";

    echo "<tr><td>"._MYQUIZ_TITLE."</td><td><input type='text' name='quizztitle' size=30></td></tr>";
        echo "<tr><td><font color='#8d0909'>"._MYQUIZ_CAT."</font></td><td><SELECT name=\"cid\">";
    $result = $xoopsDB->query("select cid, name from ".$xoopsDB->prefix("myquiz_categories"));
    while(list($catid, $name) = $xoopsDB->fetchRow($result))
    {
        if ($catid==$cid) { $sel = "selected "; }
            echo "<option $sel value=\"$catid\">".$myts->MakeTboxData4PreviewInForm($name)."</option>\n";
                $sel = "";
    }
    echo "</select></td></tr>";
	echo "<tr><td>"._MYQUIZ_VIEWANSWER."</td><td><input type='checkbox' name='displayresults' checked> <img src='$jspath/images/info.png' title='"._MYQUIZ_HELPANSWER."'> </td></tr>";
	echo "<tr><td>"._MYQUIZ_TEKTEK."</td><td><input type='checkbox' name='tektek' checked onclick='f(this)'> <img src='$jspath/images/info.png' title='"._MYQUIZ_HELPTEKTEK."'></td></tr>";
		echo "<tr><td><font color='#8d0909'>"._MYQUIZ_ZAMAN."</font></td><td><input type='text' name='conditions' id='conditions' size=3 value=30 onKeypress='if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;' maxlength='3'> <img src='$jspath/images/info.png' title='"._MYQUIZ_YZAMAN."'></td></tr>";
    echo "<tr><td>"._MYQUIZ_VIEWSCORE."</td><td><input type='checkbox' name='displayscore' checked> <img src='$jspath/images/info.png' title='"._MYQUIZ_HELPVIEWSCORE."'></td></tr>";
    echo "<tr><td>"._MYQUIZ_ONLYREGISTERED."</td><td><input type='checkbox' name='restrict' checked> <img src='$jspath/images/info.png' title='"._MYQUIZ_HELPONLYREGISTERED."'> </td></tr>";
    echo "<tr><td>"._MYQUIZ_LIMITVOTE."</td><td><input type='checkbox' name='log' checked> <img src='$jspath/images/info.png' title='"._MYQUIZ_HELPLIMITVOTE."'> </td></tr>";
    echo "<tr><td>"._MYQUIZ_CONTRIB."</td><td><input type='checkbox' name='contrib' checked> <img src='$jspath/images/info.png' title='"._MYQUIZ_HELPCONTRIB."'> </td></tr>";
    echo "<tr><td>"._MYQUIZ_NBSCORE."</td><td><input type='text' name='nbscore' size='3' value='10' onKeypress='if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;' maxlength='3'> <img src='$jspath/images/info.png' title='"._MYQUIZ_HELPNBSCORE."'></td></tr>";
   echo "<tr><td>"._MYQUIZ_SENDEMAIL."</td><td><input type='checkbox' name='emailadmin' ><img src='$jspath/images/info.png' title='"._MYQUIZ_HELPEMAIL."'> </td></tr>";
//   echo "<tr><td>"._MYQUIZ_ADMEMAIL."</td><td><input type='text' name='admemail' size=30> (*)<img src='$jspath/images/info.png' title='"._MYQUIZ_HELPADMEMAIL."'> </td></tr>";
//   echo "<tr><td>"._MYQUIZ_ADMINISTRATOR."</td><td><input type='text' name='administrator' size=30>// (*)<img src='$jspath/images/info.png' title='"._MYQUIZ_HELPADMINISTRATOR."'> </td></tr>";
    echo "<tr><td>"._MYQUIZ_IMAGE."</td><td><input type='text' name='image' size=30> (*)<img src='$jspath/images/info.png' title='"._MYQUIZ_HELPIMAGE."'> </td></tr>";


        echo "<tr><td>"._MYQUIZ_EXPIRATION." (*)</td><td>";
    $xday = 1;
    echo " "._MYQUIZ_DAY.": <SELECT name=\"day\">";
        echo "<option name=\"day\" selected>xx</option>"; # by default print the "no expiration date" tag
    while ($xday <= 31)
        {
        echo "<option name=\"day\">$xday</option>";
        $xday++;
    }
    echo "</select>";
    $xmonth = 1;
    echo " "._MYQUIZ_UMONTH.": <SELECT name=\"month\">";
        echo "<option name=\"month\" selected>xx</option>"; # by default print the "no expiration date" tag
    while ($xmonth <= 12)
        {
        echo "<option name=\"month\">$xmonth</option>";
        $xmonth++;
    }
    echo "</select>";
    echo " "._MYQUIZ_YEAR.": <input type=\"text\" name=\"year\" value=\"xxxx\" size=\"5\" maxlength=\"4\">";
    echo " "._MYQUIZ_HOUR.": <SELECT name=\"hour\">";
    $xhour = 0;
    $cero = "0";
        echo "<option name=\"hour\" selected>xx</option>"; # by default print the "no expiration date" tag
    while ($xhour <= 23)
        {
        $dummy = $xhour;
        if ($xhour < 10) { $xhour = "$cero$xhour"; }
        echo "<option name=\"hour\">$xhour</option>";
        $xhour = $dummy;
        $xhour++;
    }
    echo "</select>";
    echo ": <SELECT name=\"min\">";
    $xmin = 0;
        echo "<option name=\"min\" selected>xx</option>"; # by default print the "no expiration date" tag
    while ($xmin <= 59)
        {
        if (($xmin == 0) OR ($xmin == 5)) { $xmin = "0$xmin"; }
        echo "<option name=\"min\">$xmin</option>";
        $xmin = $xmin + 5;
    }
    echo "</select>";


        $now = FormatTimestamp(time());
    echo "</select> <img src='$jspath/images/info.png' title='"._MYQUIZ_NOWIS." : $now'></td></tr>";


    echo "<tr><td colspan=2>"._MYQUIZ_COMMENT." (*)<br /><TEXTAREA wrap=\"virtual\" cols=\"50\" rows=\"4\" name=\"comment\"></textarea></td></tr>";
    

    echo "</table>";
    echo "<br /><center><input type='submit' class=button value='"._MYQUIZ_CREATE."'></center>";
    echo "</form>";

    echo "* : "._MYQUIZ_HELPOPTION."\n";
	}
	else {
	echo "<br /><br /><br /><center><b>"._MYQUIZ_CATEWARN."</b><br /><br />";
	echo "<a href='".XOOPS_URL."/modules/myquiz/admin/index.php'>"._MYQUIZ_KATEGORI."</a><br /><br /></center>";
}
echo "</div>";
 xoops_cp_footer();
}

/*********************************************************/
/* Quizz Functions                                       */
/*********************************************************/

function modifyPostedQuizz()
{
    global $qid, $quizztitle, $displayresults, $displayscore, $tektek, $nbscore, $comment, $image, $log, $restrict, $active, $cid, $contrib, $year, $month, $day, $hour, $min, $emailadmin, $admemail, $administrator, $conditions,$myts,$xoopsConfig,$xoopsDB, $xoopsUser, $xoopsTheme, $xoopsLogger;

    $quizztitle = $_POST['quizztitle'];
    $quizztitle = $myts->MakeTboxData4Save($quizztitle);
	$comment = $_POST['comment'];
    $comment = $myts->MakeTareaData4Save($comment);
	$conditions = $_POST['conditions'];
	$conditions = $myts->MakeTareaData4Save($conditions);
	$year = $_POST['year'];
	$month = $_POST['month'];
	$day = $_POST['day'];
	$hour = $_POST['hour'];
	$min = $_POST['min'];
    $expire = "$year-$month-$day $hour:$min";



    if(isset($_POST['tektek'])) $tektek=1; else $tektek=0;
    if(isset($_POST['displayresults'])) $displayresults=1; else $displayresults=0;
    if(isset($_POST['displayscore'])) $displayscore=1; else $displayscore=0;
    if(isset($_POST['restrict'])) $restrict=1; else $restrict=0;
    if(isset($_POST['log'])) $log=1; else $log=0;
    if(isset($_POST['active'])) $active=1; else $active=0;
	if(isset($_POST['emailadmin'])) $emailadmin=1; else $emailadmin=0;
    if(isset($_POST['contrib'])) $contrib=1; else $contrib=0;

	$qid = $_POST['qid'];
	$cid = $_POST['cid'];
	$nbscore = $_POST['nbscore'];
	$image = $_POST['image'];
	# $restrict = $_POST['restrict'];
	# $log = $_POST['log'];
	# $expire = $_POST['expire'];#
	$admemail = $_POST['admemail'];
	$administrator = $_POST['administrator'];


    if(!$xoopsDB->query("UPDATE ".$xoopsDB->prefix("myquiz_admin")." SET quizzTitle='$quizztitle', nbscore='$nbscore', displayscore='$displayscore', displayresults='$displayresults', tektek='$tektek', comment='$comment', image='$image', restrict_user='$restrict', log_user='$log', active='$active', cid='$cid', contrib='$contrib', expire='$expire', emailadmin='$emailadmin', admemail='$admemail', administrator='$administrator', conditions='$conditions' WHERE quizzID='$qid'")) {
        echo $xoopsDB->errno(). ": ".$xoopsDB->error(). "<br />";
        return;
    }
    Header("Location: ".XOOPS_URL."/modules/myquiz/admin/show_testler.php");
}

/*********************************************************/
/* Quizz Functions                                       */
/*********************************************************/

function createPostedQuizz()
{
    global $quizztitle, $displayresults, $displayscore,$tektek,$nbscore,$comment,$image,$restrict,$log,$cid,$contrib,$year,$month,$day,$hour,$min,$prefix,$base_url,$emailadmin,$admemail,$administrator,$conditions,$myts,$xoopsConfig,$xoopsDB, $xoopsUser, $xoopsTheme, $xoopsLogger;

 	if(isset($_POST['tektek'])) $tektek=1; else $tektek=0;
    if(isset($_POST['displayresults'])) $displayresults=1; else $displayresults=0;
    if(isset($_POST['displayscore'])) $displayscore=1; else $displayscore=0;
    if(isset($_POST['restrict'])) $restrict=1; else $restrict=0;
    if(isset($_POST['log'])) $log=1; else $log=0;
	if(isset($_POST['emailadmin'])) $emailadmin=1; else $emailadmin=0;
    if(isset($_POST['contrib'])) $contrib=1; else $contrib=0;
	$year = $_POST['year'];
	$month = $_POST['month'];
	$day = $_POST['day'];
	$hour = $_POST['hour'];
	$min = $_POST['min'];
    $expire = "$year-$month-$day $hour:$min";

    $timeStamp = time();
	$quizztitle = $_POST['quizztitle'];
    $quizztitle = $myts->MakeTboxData4Save($quizztitle);
	$comment = $_POST['comment'];
    $comment = $myts->MakeTareaData4Save($comment);
	$conditions = $_POST['conditions'];
    $conditions = $myts->MakeTareaData4Save($conditions);	
	$nbscore = $_POST['nbscore'];
    $nbscore = $myts->MakeTareaData4Save($nbscore);	
	// v4.0
    $admemail = $_POST['admemail'];
    $admemail = $myts->MakeTareaData4Save($admemail);
	
	$cid = $_POST['cid'];
    if(!$xoopsDB->query("INSERT INTO ".$xoopsDB->prefix("myquiz_admin")." VALUES (NULL, '$quizztitle', '$timeStamp', 0, '$nbscore', '$displayscore', '$displayresults', '$tektek', '$comment', 1, '$restrict', '$log', '$image', '$cid', '$contrib', '$expire', '$emailadmin', '$admemail', '$administrator', '$conditions')")) {
        echo $xoopsDB->errno(). ": ".$xoopsDB->error(). "<br />";
        return;
    }
    Header("Location: ".XOOPS_URL."/modules/myquiz/admin/show_testler.php");
}

/*********************************************************/
/* Quizz Functions                                       */
/*********************************************************/

function createPostedQuizzQuestion()
{
    global $question, $optionText,$qid,$answer,$coef,$good,$bad,$mode,$pid,$comment,$prefix,$base_url,$optionSort,$image,$myts,$xoopsConfig,$xoopsDB, $xoopsUser, $xoopsTheme, $xoopsLogger;

    $good = $_POST['good'];
    $good = $myts->MakeTareaData4Save($good);
	$bad = $_POST['bad'];
    $bad = $myts->MakeTareaData4Save($bad);
	$comment = $_POST['comment'];
    $comment = $myts->MakeTareaData4Save($comment);
	$question = $_POST['question'];
    $question = $myts->MakeTboxData4Save($question);

	$image = $_POST['image'];
	$coef = $_POST['coef'];
	$answer = $_POST['answer'];
	$qid = $_POST['qid'];
	$optionText = $_POST['optionText'];
    

    $timeStamp = time();

        $ordered_answer = implode(",",$optionSort);
        $ordered_answer = preg_replace("/,--|--,|--/","",$ordered_answer);

        # check if sorted answer is needed
        if (!empty($ordered_answer))
        {
                # check if all availaible answers are sorted
                for($i = 1; $i <= sizeof($optionText); $i++)
                {
                        if ((!empty($optionText[$i]) and $optionSort[$i] == "--") or
                           (empty($optionText[$i]) and $optionSort[$i] != "--"))
                        {
                                include (XOOPS_ROOT_PATH."/header.php");
                                OpenTable();
                                echo "<center>"._MYQUIZ_INCORRECTORDER."</center>";
                                CloseTable();
                                xoops_cp_footer();
                                exit;
                        }
                }
                # change the answer the the ordered answer
                $answer = $ordered_answer;
    }

    if(!$xoopsDB->query("INSERT INTO ".$xoopsDB->prefix("myquiz_desc")." VALUES (NULL, '$question', '$timeStamp', 0, '$qid','$answer','$coef','$good','$bad','$comment','$image')"))
        {
        echo $xoopsDB->errno(). ": ".$xoopsDB->error(). "<br />";
        return;
    }
    $array = $xoopsDB->fetchArray($xoopsDB->query("SELECT pollID FROM ".$xoopsDB->prefix("myquiz_desc")." WHERE timeStamp='$timeStamp'"));
    $id = $array['pollID'];
    for($i = 1; $i <= sizeof($optionText); $i++)
        {
        if($optionText[$i] != "")
        {
            $optionText[$i] = $myts->MakeTboxData4Save($optionText[$i]);
        }
        if(!$xoopsDB->query("INSERT INTO ".$xoopsDB->prefix("myquiz_data")." (pollID, optionText, optionCount, voteID) VALUES ($id, '$optionText[$i]', 0, $i)"))
        {
            echo $xoopsDB->errno(). ": ".$xoopsDB->error(). "<br />";
            return;
        }
    }

    # delete contributor question if needed
    if ($mode == "contrib")
    {
         $xoopsDB->query("DELETE FROM ".$xoopsDB->prefix("myquiz_datacontrib")." WHERE pollID='$pid'");
         $xoopsDB->query("DELETE FROM ".$xoopsDB->prefix("myquiz_descontrib")." WHERE pollID='$pid'");
    }
    Header("Location: ".XOOPS_URL."/modules/myquiz/admin/index.php?&acti=QuizzModify&qidi=$qid");
}


/*********************************************************/
/* Quizz Functions                                       */
/*********************************************************/

function deletePostedScoreQuizz()
{

 global $qid,$xoopsDB,$xoopsConfig, $xoopsUser, $xoopsTheme, $xoopsLogger;

    # delete all the score and stats for the selected Quiz
    $xoopsDB->queryF("UPDATE ".$xoopsDB->prefix("myquiz_admin")." SET voters='0' WHERE quizzID='$qid'");

    $result = $xoopsDB->query("SELECT pollID FROM ".$xoopsDB->prefix("myquiz_desc")." WHERE qid='$qid'");
    while(list($pollID) = $xoopsDB->fetchRow($result))
    {
        $xoopsDB->queryF("UPDATE ".$xoopsDB->prefix("myquiz_data")." SET optionCount='0' WHERE pollID='$pollID'");
        }

    $xoopsDB->queryF("DELETE FROM ".$xoopsDB->prefix("myquiz_check")." WHERE qid='$qid'");

    Header("Location: ".XOOPS_URL."/modules/myquiz/admin/show_testler.php");
}

/*********************************************************/
/* Quizz Functions                                       */
/*********************************************************/

function deletePostedContributorQuizzQuestion()
{

 global $pid,$xoopsConfig,$xoopsDB, $xoopsUser, $xoopsTheme, $xoopsLogger;

  # delete contributor question
         $xoopsDB->queryF("DELETE FROM ".$xoopsDB->prefix("myquiz_datacontrib")." WHERE pollID='$pid'");
         $xoopsDB->queryF("DELETE FROM ".$xoopsDB->prefix("myquiz_descontrib")." WHERE pollID='$pid'");
    Header("Location: ".XOOPS_URL."/modules/myquiz/admin/show_testler.php");
}

/*********************************************************/
/* Quizz Functions                                       */
/*********************************************************/

function QuizzDelCategory()
{
        global $cid,$xoopsConfig,$xoopsDB,$xoopsModule, $xoopsUser, $xoopsTheme, $xoopsLogger;


xoops_cp_header();
   include ("admin_menutab.php");
   echo "<table class='table-cev'><tr><td>";
	$cid = $_POST['cid'];
    echo "<b>"._MYQUIZ_DELCAT."</b><br /><br /><center>"
        .""._MYQUIZ_SURE2DELETECAT." $cid & "._MYQUIZ_ALLCONTENTS."<br /><br />"
        ."[ <a href=\"".XOOPS_URL."/modules/myquiz/admin/index.php?acti=removePostedQuizzCategory&cidi=$cid\">"._YES."</a> | <a href=\"".XOOPS_URL."/modules/myquiz/admin/index.php?acti=QuizzAdmin\">"._NO."</a> ]</center></td></tr></table>";
   
xoops_cp_footer();

}

########################


/*********************************************************/
/* Quizz Functions                                       */
/*********************************************************/

function createPostedQuizzCategory()
{
        global $CatName,$CatComment,$CatImage,$xoopsConfig,$xoopsDB,$myts, $xoopsUser, $xoopsTheme, $xoopsLogger;

        $CatName=$myts->MakeTboxData4Save($CatName);
        $CatComment=$myts->MakeTboxData4Save($CatComment);

        $cid = $_POST['cid'];
		$CatName = $_POST['CatName'];
        $CatComment = $_POST['CatComment'];
		$CatImage = $_POST['CatImage'];

    if(!$xoopsDB->query("INSERT INTO ".$xoopsDB->prefix("myquiz_categories")." VALUES (NULL,  '$cid','$CatName','$CatComment','$CatImage')"))
        {
                echo $xoopsDB->errno(). ": ".$xoopsDB->error(). "<br />";
                return;
    }
    Header("Location: ".XOOPS_URL."/modules/myquiz/admin/kategoriler.php");
}

/*********************************************************/
/* Quizz Functions                                       */
/*********************************************************/

function modifyPostedQuizzCategory()
{
        global $CatName,$CatComment,$CatImage,$cid,$xoopsConfig,$xoopsDB,$myts, $xoopsUser, $xoopsTheme, $xoopsLogger;

        $CatName=$myts->MakeTboxData4Save($CatName);
        $CatComment=$myts->MakeTboxData4Save($CatComment);

	$CatName = $_POST['CatName'];
	$CatComment = $_POST['CatComment'];
	$CatImage = $_POST['CatImage'];
	$cid = $_POST['cid'];


    if(!$xoopsDB->query("UPDATE ".$xoopsDB->prefix("myquiz_categories")." SET name='$CatName',comment='$CatComment',image='$CatImage' WHERE cid='$cid'"))
        {
                echo $xoopsDB->errno(). ": ".$xoopsDB->error(). "<br />";
                return;
    }
    Header("Location: ".XOOPS_URL."/modules/myquiz/admin/kategoriler.php");
}


/*********************************************************/
/* Quizz Functions                                       */
/*********************************************************/

function deleteContributorQuizzQuestion() {
    global $qid,$pid,$xoopsConfig,$xoopsModule, $xoopsDB, $xoopsUser, $xoopsTheme, $xoopsLogger;

xoops_cp_header();
       include ("admin_menutab.php");
	$pid = $_POST['pid'];
	$qid = $_POST['qid'];
    echo "<center><b>"._MYQUIZ_DELCONTRIB." $pid?</b><br /><br />";
	echo "[ <a href=\"".XOOPS_URL."/modules/myquiz/admin/index.php?acti=deletePostedContributorQuizzQuestion&qidi=$qid&pidi=$pid\">"._YES."</a> | <a href=\"".XOOPS_URL."/modules/myquiz/admin/from_user.php\">"._NO."</a> ]</center></td></tr></table>";
    
xoops_cp_footer();

}
/*********************************************************/
/* Quizz Functions                                       */
/*********************************************************/

function QuizzDelQuestion() {
    global $qid,$pid,$xoopsConfig,$xoopsModule, $xoopsDB, $xoopsUser, $xoopsTheme, $xoopsLogger;

xoops_cp_header();
    include ("admin_menutab.php");
    echo "<center><b>"._MYQUIZ_DELQUESTION."</b><br /><br />"
        .""._MYQUIZ_SURE2DELQUESTION." $pid?<br /><br />"
        ."[ <a href=\"".XOOPS_URL."/modules/myquiz/admin/index.php?acti=removePostedQuizzQuestion&qidi=$qid&pidi=$pid\">"._YES."</a> | <a href=\"".XOOPS_URL."/modules/myquiz/admin/index.php?acti=QuizzModify&qidi=$qid\">"._NO."</a> ]</center>";

xoops_cp_footer();

}

/*********************************************************/
/* Quizz Functions                                       */
/*********************************************************/

function QuizzRemove() {
    global $xoopsConfig,$qid,$xoopsModule, $xoopsDB, $xoopsUser, $xoopsTheme, $xoopsLogger;

xoops_cp_header();
    include ("admin_menutab.php");
    echo "<b>"._MYQUIZ_DELETE."</b><br /><br /><center>"
        .""._MYQUIZ_SURE2DELETE." $qid ?<br /><br />"
        ."[ <a href=\"".XOOPS_URL."/modules/myquiz/admin/index.php?acti=removePostedQuizz&qidi=$qid\">"._YES."</a> | <a href=\"".XOOPS_URL."/modules/myquiz/admin/show_testler.php\">"._NO."</a> ]</center>";

xoops_cp_footer();

}

/*********************************************************/
/* Quizz Functions                                       */
/*********************************************************/

function removePostedQuizzCategory() {
    global $cid,$xoopsDB,$xoopsConfig, $xoopsUser, $xoopsTheme, $xoopsLogger;

    $xoopsDB->queryF("DELETE FROM ".$xoopsDB->prefix("myquiz_categories")." WHERE cid='$cid'");
    Header("Location: ".XOOPS_URL."/modules/myquiz/admin/kategoriler.php");
}



/*********************************************************/
/* Quizz Functions                                       */
/*********************************************************/

function removePostedQuizz() {
    global $qid,$xoopsDB, $xoopsUser, $xoopsTheme, $xoopsLogger, $xoopsConfig;

    $result = $xoopsDB->queryF("SELECT pollID FROM ".$xoopsDB->prefix("myquiz_desc")." WHERE qid='$qid'");
    while(list($pollID) = $xoopsDB->fetchRow($result))
    {
        $xoopsDB->queryF("DELETE FROM ".$xoopsDB->prefix("myquiz_desc")." WHERE pollID='$pollID'");
        $xoopsDB->queryF("DELETE FROM ".$xoopsDB->prefix("myquiz_data")." WHERE pollID='$pollID'");
        }

    $xoopsDB->queryF("DELETE FROM ".$xoopsDB->prefix("myquiz_admin")." WHERE quizzID='$qid'");
	deletePostedScoreQuizz();
    Header("Location: ".XOOPS_URL."/modules/myquiz/admin/show_testler.php");
}

/*********************************************************/
/* Quizz Functions                                        */
/*********************************************************/

function removePostedQuizzQuestion() {
    global $qid,$pid,$xoopsDB, $xoopsUser, $xoopsTheme, $xoopsLogger, $xoopsConfig;
    $xoopsDB->queryF("DELETE FROM ".$xoopsDB->prefix("myquiz_desc")." WHERE pollID='$pid'");
    $xoopsDB->queryF("DELETE FROM ".$xoopsDB->prefix("myquiz_data")." WHERE pollID='$pid'");
    Header("Location: ".XOOPS_URL."/modules/myquiz/admin/index.php?acti=QuizzModify&qidi=$qid");
}

/*********************************************************/


if (!isset($_POST['act'])){
    /*QuizzAdmin();*/
	$act = $_GET['acti'];
    $cid = $_GET['cidi'];
	$qid = $_GET['qidi'];
	$pid = $_GET['pidi'];
}
else
{
	$act = $_POST['act'];
}

switch($act) {

	case "":
	QuizzAdmin();
	
	case "makePQuizz":
    makePQuizz();
    break;	

    case "createPostedQuizzQuestion":
    createPostedQuizzQuestion();
    break;

    case "modifyPostedQuizz":
    modifyPostedQuizz();
    break;

    # OK #
    case "modifyPostedQuizzCategory":
    modifyPostedQuizzCategory();
    break;

    case "modifyPostedQuizzQuestion":
    modifyPostedQuizzQuestion();
    break;

    case "deletePostedScoreQuizz":
    deletePostedScoreQuizz();
    break;

    # OK #
    case "removePostedQuizzCategory":
    removePostedQuizzCategory();
    break;
	

    # OK #
    case "removePostedQuizz":
    removePostedQuizz();
    break;

    case "removePostedQuizzQuestion":
    removePostedQuizzQuestion();
    break;

    case "QuizzAddQuestion":
    QuizzAddQuestion();
    break;

    case "QuizzDelQuestion":
    QuizzDelQuestion();
    break;

    case "QuizzViewStats":
    QuizzViewStats();
    break;

    case "QuizzRemoveScore":
    QuizzRemoveScore();
    break;

    case "QuizzViewScore":
    QuizzViewScore();
    break;

    # OK #
    case "QuizzRemove":
    QuizzRemove();
    break;

    # en cours #
    case "createPostedQuizz":
    createPostedQuizz();
    break;

    # OK #
    case "QuizzModifyCategory":
    QuizzModifyCategory();
    break;

    case "QuizzModifyQuestion":
    QuizzModifyQuestion();
    break;

    case "QuizzAddContrib":
    QuizzAddContrib();
    break;

    # en cours #
    case "QuizzModify":
    QuizzModify();
    break;

    # OK #
    case "QuizzDelCategory":
    QuizzDelCategory();
    break;
	
    # OK #
    case "createPostedQuizzCategory":
    createPostedQuizzCategory();
    break;

    case "deletePostedContributorQuizzQuestion":
    deletePostedContributorQuizzQuestion();
    break;

    case "deleteContributorQuizzQuestion":
    deleteContributorQuizzQuestion();
    break;

    # en cours #
    case "QuizzAdd":
    QuizzAdd();
    break;

    case "QuizzAdmin":
    QuizzAdmin();
    break;

    default:
    QuizzAdmin();
    break;
	
}
/*}*/

?>