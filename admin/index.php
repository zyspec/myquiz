<?php
#####################################################
#  Based on Quizz 1.4.1
#  by xbee (xbee@xbee.net) http://www.xbee.net
#  Licence: GPL
#
#  Adapted, modified and improved for Xoops 1.0 RC3
#  by Moumou inconnu0215@noos.fr
#  and Pascal Le Boustouller pascal@xoopsien.net - http://www.xoopsien.net
#  Copyright © 2002
# Thank you to leave this copyright in place...
#####################################################

include_once("admin_header.php");
$myts =& MyTextSanitizer::getInstance();

/*********************************************************/
/* Quizz Functions                                       */
/*********************************************************/

function QuizzAdmin()
{
    global $xoopsConfig,$xoopsDB,$myts,$xoopsModule, $xoopsUser, $xoopsTheme, $xoopsLogger;
xoops_cp_header();
    OpenTable();
        echo "<center>[ <a href=\"".XOOPS_URL."/modules/myquiz/admin/index.php?act=QuizzAdd\">"._MYQUIZ_NEW."</a> ]</center><BR><BR>";
        echo "<table width='100%' border=0>";

    $result = $xoopsDB->query("SELECT
        ".$xoopsDB->prefix("myquiz_admin").".quizzId,
        ".$xoopsDB->prefix("myquiz_admin").".quizzTitle,
        ".$xoopsDB->prefix("myquiz_admin").".active,
        ".$xoopsDB->prefix("myquiz_categories").".name
        FROM ".$xoopsDB->prefix("myquiz_admin").", ".$xoopsDB->prefix("myquiz_categories")."
        WHERE ".$xoopsDB->prefix("myquiz_admin").".cid = ".$xoopsDB->prefix("myquiz_categories").".cid
        ORDER BY timeStamp DESC");
        while(list($qid, $quizzTitle,$active,$category) = $xoopsDB->fetchRow($result))
        {
        echo "<tr><td>";
        if ($active == 1) { echo "<B>"._MYQUIZ_ACTIVE."</B> - "; } else {echo "<B>"._MYQUIZ_INACTIVE."</B> - ";}
        echo ""._MYQUIZ_MYQUIZ." <B>$qid</B> : ";
        echo "\"".$myts->MakeTboxData4Show($quizzTitle)."\" ";
        echo "(".$myts->MakeTboxData4Show($category).") <BR>";
        echo "[ <a href=\"".XOOPS_URL."/modules/myquiz/index.php?qid=$qid\" target=_blank>"._MYQUIZ_SEE."</a> | ";
        echo "<a href=\"".XOOPS_URL."/modules/myquiz/admin/index.php?act=QuizzViewScore&qid=$qid\">"._MYQUIZ_VIEWSCORE."</a> | ";
        echo "<a href=\"".XOOPS_URL."/modules/myquiz/admin/index.php?act=QuizzRemoveScore&qid=$qid\">"._MYQUIZ_DELSCORE."</a> | ";
        echo "<a href=\"".XOOPS_URL."/modules/myquiz/admin/index.php?act=QuizzViewStats&qid=$qid\">"._MYQUIZ_VIEWSTAT."</a> | ";
        echo "<a href=\"".XOOPS_URL."/modules/myquiz/admin/index.php?act=QuizzModify&qid=$qid\">"._MYQUIZ_MODIFY."</a> | ";
        echo "<a href=\"".XOOPS_URL."/modules/myquiz/admin/index.php?act=QuizzRemove&qid=$qid\">"._MYQUIZ_DELETE."</a> ]";
                echo "<tr><td>&nbsp;</td></tr>\n";
        }
        echo "\n";
        echo "</td></tr></table><BR><BR>\n";


 # display if available the contributor questions

    $result = $xoopsDB->query("SELECT pollID, pollTitle, qid FROM ".$xoopsDB->prefix("myquiz_descontrib")." ORDER BY qid");
        $resultS  = mysql_num_rows($result);
        if ($resultS > 0) {
        echo "<b>"._MYQUIZ_ADDCONTRIB."</b><BR><BR>";
    echo "<table width='100%'>";
    while(list($pollID,$pollTitle,$qid) = $xoopsDB->fetchRow($result))
    {
    echo "<tr><td>Quizz $qid : <a href=\"".XOOPS_URL."/modules/myquiz/admin/index.php?act=QuizzAddContrib&pid=$pollID&qid=$qid\">".$myts->MakeTboxData4Show($pollTitle)."</a></td></tr>";
    }
    echo "</table><BR><BR>";
        }

    echo "<b>"._MYQUIZ_ADDCAT."</b><BR><BR>";
    echo "\n<form method='post'action='".XOOPS_URL."/modules/myquiz/admin/index.php'>";
    echo "<INPUT type='hidden' name='act' value='createPostedQuizzCategory'>";
    echo "";
    echo "<table width='100%'>";
    echo "<tr><td>"._MYQUIZ_CAT."</td><td><input type='text' name='CatName' size=30></td>";
    echo "<tr><td>"._MYQUIZ_COMMENT."</td><td><input type='text' name='CatComment' size=30> (*)</td>";
    echo "<tr><td>"._MYQUIZ_CATIMAGE."</td><td><input type='text' name='CatImage' size=30> (*)</td>";
    echo "<td><input type='submit' class=button value='"._MYQUIZ_ADD."'></td></tr></table>";
    echo "* : "._MYQUIZ_HELPOPTION."\n";
    echo "</form><BR>";


    echo "<b>"._MYQUIZ_DELCAT."</b><BR><BR>";
    echo "\n<form method='post'action='".XOOPS_URL."/modules/myquiz/admin/index.php'>";
    echo "<INPUT type='hidden' name='act' value='QuizzDelCategory'>";
    echo "";
    echo "<table width='100%'>";
    echo "<tr><td>"._MYQUIZ_CAT
        ." <SELECT name=\"cid\">";
    $result = $xoopsDB->query("select cid, name from ".$xoopsDB->prefix("myquiz_categories"));
    while(list($cid, $name) = $xoopsDB->fetchRow($result))
        {
            echo "<option value=\"$cid\">".$myts->MakeTboxData4Show($name)."</option>\n";
        }
    echo "</select></td><td><input type='submit' class=button value='"._MYQUIZ_DELETE."'></td></tr></table>";
    echo "</form><BR>";


    echo "<b>"._MYQUIZ_MODIFYCAT."</b><BR><BR>";
    echo "\n<form method='post'action='".XOOPS_URL."/modules/myquiz/admin/index.php'>";
    echo "<INPUT type='hidden' name='act' value='QuizzModifyCategory'>";
    echo "";
    echo "<table width='100%'>";
    echo "<tr><td>"._MYQUIZ_CAT
        ." <SELECT name=\"cid\">";
    $result = $xoopsDB->query("select cid, name from ".$xoopsDB->prefix("myquiz_categories"));
        while(list($cid, $name) = $xoopsDB->fetchRow($result))
            {
                echo "<option value=\"$cid\">".$myts->MakeTboxData4Show($name)."</option>\n";
        }
    echo "</select></td><td><input type='submit' class=button value='"._MYQUIZ_MODIFY."'></td></tr></table>";
    echo "</form>";
    CloseTable();
xoops_cp_footer();
}

/*********************************************************/
/* Quizz Functions                                       */
/*********************************************************/

function QuizzRemoveScore()
{
        global $qid,$xoopsConfig,$xoopsModule,$xoopsDB, $xoopsUser, $xoopsTheme, $xoopsLogger;

xoops_cp_header();
    OpenTable();
    echo "<center>"._MYQUIZ_SURE2DELETESCORE." $qid ?<br><br>"
        ."[ <a href=\"".XOOPS_URL."/modules/myquiz/admin/index.php?act=deletePostedScoreQuizz&qid=$qid\">"._YES."</a> | <a href=\"".XOOPS_URL."/modules/myquiz/admin/index.php?act=QuizzAdmin\">"._NO."</a> ]</center>";
    CloseTable();
xoops_cp_footer();
}

/*********************************************************/
/* Quizz Functions                                       */
/*********************************************************/

function QuizzViewScore()
{
        global $qid,$nblots,$xoopsDB,$xoopsConfig,$myts,$xoopsModule, $xoopsUser, $xoopsTheme, $xoopsLogger;

    $result = $xoopsDB->query("select quizzTitle, voters from ".$xoopsDB->prefix("myquiz_admin")." where quizzID='$qid'");
    list($quizzTitle, $nbscore) = $xoopsDB->fetchRow($result);

xoops_cp_header();
    OpenTable();
    echo "<b>"._MYQUIZ_LISTSCORE."</b><BR>";

    $result = $xoopsDB->query("SELECT username, score, email  FROM ".$xoopsDB->prefix("myquiz_check")." WHERE qid='$qid' ORDER BY score DESC,time DESC LIMIT $nbscore ");
        echo "<table>";
        while(list($username,$score,$email) = $xoopsDB->fetchRow($result))
        {
           echo "<tr><td>$username ($score Punkte) <a href=\"mailto:$email\">$email</td></tr>";
        }
        echo "</table><BR><BR>";


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
            echo "".$myts->MakeTboxData4Show($username)." ($score) : <A HREF=\"mailto:".$myts->makeTboxData4Show($email)."\">".$myts->makeTboxData4Show($email)."</A><br>";
        }
                echo "</td></tr>";
        }
        else
        {
                $nblots=10;
        }

    echo "<tr><td align='left'><BR><form>"._MYQUIZ_NBWINNERS." <input type='text' name='nblots' value='$nblots' size=3> <input type='submit' class=button value='"._MYQUIZ_LAUNCH."'></td></tr>";
        echo "</form>";
    echo "<tr><td><BR><center><a href=\"".XOOPS_URL."/modules/myquiz/admin/index.php?act=QuizzAdmin\"> [ "._MYQUIZ_ADMIN." ]</a></center></td></tr>";
                echo "</table>";
    CloseTable();

xoops_cp_footer();
}

/*********************************************************/
/* Quizz Functions                                       */
/*********************************************************/

function QuizzViewStats()
{
        global $qid,$xoopsConfig,$xoopsDB,$myts,$xoopsModule, $xoopsUser, $xoopsTheme, $xoopsLogger;

xoops_cp_header();
    OpenTable();
    echo "<center><a href=\"".XOOPS_URL."/modules/myquiz/admin/index.php?act=QuizzAdmin\"> [ "._MYQUIZ_ADMIN." ]</a></center>";
        echo "<BR><BR><b>"._MYQUIZ_LISTSTATS."</b>";

    $result = $xoopsDB->query("select quizzTitle, voters from ".$xoopsDB->prefix("myquiz_admin")." where quizzID='$qid'");
    list($quizzTitle, $voters) = $xoopsDB->fetchRow($result);

    $result = $xoopsDB->query("select MAX(score), MIN(score), AVG(score) from ".$xoopsDB->prefix("myquiz_check")." where qid='$qid'");
    list($max,$min,$mean) = $xoopsDB->fetchRow($result);

    echo "<table width='100%' border=0>";
    echo "<tr><td>"._MYQUIZ_NBVOTE." : $voters</td></tr>";
    echo "<tr><td>"._MYQUIZ_MEANSCORE." : $mean </td></tr>";
    echo "<tr><td>"._MYQUIZ_MINSCORE." : $min</td></tr>";
    echo "<tr><td>"._MYQUIZ_MAXSCORE." : $max</td></tr>";
    echo "</table><BR><BR>";

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
                        for($i = 1; $i <= 12; $i++)
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
                                                                        else
                                                                        {
echo "<img src=\"".XOOPS_URL."/modules/myquiz/images/leftbar.gif\" height=\"12\" width=\"7\" Alt=\"$percent2 %\">";
echo "<img src=\"".XOOPS_URL."/modules/myquiz/images/mainbar.gif\" height=\"12\" width=\"3\" Alt=\"$percent2 %\">";
                                                                                        echo "<img src=\"".XOOPS_URL."/modules/myquiz/images/rightbar.gif\" height=\"12\" width=\"7\" Alt=\"$percent2 %\">";
                                                                        }
                                                                        printf(" %.2f %% (%d)", $percent, $optionCount);
                                                                        echo "</td></tr>";
                                                        }
                                        }

                        }
                        echo "</table><br><br></td></tr>";
                        $a++;
        }
    echo "</table>";

    CloseTable();
xoops_cp_footer();
}

/*********************************************************/
/* Quizz Functions                                       */
/*********************************************************/

function QuizzModify()
{
    global $qid,$xoopsConfig,$xoopsDB,$myts,$xoopsModule, $xoopsUser, $xoopsTheme, $xoopsLogger;

    $result = $xoopsDB->query("select quizzTitle, nbscore, displayscore, displayresults, emailadmin, comment, image, restrict_user, log_user, active, cid, contrib, expire, admemail, administrator, conditions from ".$xoopsDB->prefix("myquiz_admin")." where quizzId='$qid'");
    list($quizzTitle, $nbscore, $displayscore, $displayresults, $emailadmin, $comment, $image,$restrict_user,$log_user,$active,$cid,$contrib,$expire,$admemail, $administrator,$conditions) = $xoopsDB->fetchRow($result);

xoops_cp_header();
    OpenTable();
    echo "<center><a href=\"".XOOPS_URL."/modules/myquiz/admin/index.php?act=QuizzAdmin\"> [ "._MYQUIZ_ADMIN." ]</a></center><BR><BR>";
        echo "<b>"._MYQUIZ_MODIFY."</b><BR><BR>";

    if ($active==1) { $act = "checked"; } else { $act = ""; }
    if ($emailadmin==1) { $eadm = "checked"; } else { $eadm = ""; }
    if ($displayscore==1) { $dis = "checked"; } else { $dis = ""; }
    if ($displayresults==1) { $res = "checked"; } else { $res = ""; }
    if ($contrib==1) { $con = "checked"; } else { $con = ""; }
    if ($log_user==1) { $log = "checked"; } else { $log = ""; }
   // if ($restrict_user==1) { $restrict = "checked"; } else { $restrict = ""; }

    echo "\n<form method='post'action='".XOOPS_URL."/modules/myquiz/admin/index.php'>";
    echo "<INPUT type='hidden' name='act' value='modifyPostedQuizz'>";
    echo "<INPUT type='hidden' name='qid' value='$qid'>";
    echo "<table width='100%'>";
    echo "<tr><td>"._MYQUIZ_GENINFOS."</td></tr>";
    echo "<tr><td>"._MYQUIZ_TITLE."</td><td><input type='text' name='quizztitle' value=\"".$myts->MakeTboxData4Edit($quizzTitle)."\" size=30></td></tr>";
    echo "<tr><td>"._MYQUIZ_ACTIVE."</td><td><input type='checkbox' name='active' $act> "._MYQUIZ_HELPACTIVE."</td></tr>";
    echo "<tr><td>"._MYQUIZ_VIEWSCORE."</td><td><input type='checkbox' name='displayscore' $dis> "._MYQUIZ_HELPVIEWSCORE."</td></tr>";
    echo "<tr><td>"._MYQUIZ_VIEWANSWER."</td><td><input type='checkbox' name='displayresults' $res> "._MYQUIZ_HELPANSWER."</td></tr>";
    echo "<tr><td>"._MYQUIZ_CONTRIB."</td><td><input type='checkbox' name='contrib' $con> "._MYQUIZ_HELPCONTRIB."</td></tr>";
    echo "<tr><td>"._MYQUIZ_LIMITVOTE."</td><td><input type='checkbox' name='log' $log> "._MYQUIZ_HELPLIMITVOTE."</td></tr>";
    echo "<tr><td>"._MYQUIZ_NBSCORE."</td><td><input type='text' name='nbscore' value='".$myts->MakeTboxData4Edit($nbscore)."' size=3> "._MYQUIZ_HELPNBSCORE."</td></tr>";
    echo "<tr><td>"._MYQUIZ_IMAGE."</td><td><input type='text' name='image' value=\"".$myts->MakeTboxData4Edit($image)."\" size=30> (*) "._MYQUIZ_HELPIMAGE."</td></tr>";
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
    echo "</select><br> ("._MYQUIZ_NOWIS." : $now)</td></tr>";

    echo "<tr><td colspan=2>"._MYQUIZ_COMMENT." (*)<br><TEXTAREA wrap=\"virtual\" cols=\"50\" rows=\"4\" name=\"comment\">".$myts->MakeTareaData4Edit($comment)."</textarea></td></tr>";
    echo "<tr><td colspan=2>"._MYQUIZ_CONDITIONS." (*)<br><TEXTAREA cols=\"50\" rows=\"4\" name=\"conditions\">".$myts->MakeTareaData4Edit($conditions)."</textarea></td></tr>";
    echo "</table>";
    echo "<br><center><input type='submit' class=button value='"._MYQUIZ_MODIFY."'></center>";
        echo "* : "._MYQUIZ_HELPOPTION."\n";
    echo "</form><BR>";

    echo "<table width='100%'>";
    echo "<tr><td><B>"._MYQUIZ_QUESTIONS."</B></td></tr>";
    echo "<tr><td>";
    echo "<center><a href=\"".XOOPS_URL."/modules/myquiz/admin/index.php?act=QuizzAddQuestion&qid=$qid\"> [ "._MYQUIZ_ADDQUESTION." ]</a></center>";
    echo "<BR></td></tr>";

    $result = $xoopsDB->query("SELECT pollID, pollTitle FROM ".$xoopsDB->prefix("myquiz_desc")." WHERE qid='$qid'");
    while(list($pollID, $question) = $xoopsDB->fetchRow($result))
    {
        echo "<tr><td>";
        echo ""._MYQUIZ_QUESTION." $pollID : ";
        echo "\"".$myts->MakeTboxData4Edit($question)."\" [ <a href=\"".XOOPS_URL."/modules/myquiz/admin/index.php?act=QuizzModifyQuestion&pid=$pollID&qid=$qid\">"._MYQUIZ_MODIFY."</a> | ";
        echo "<a href=\"".XOOPS_URL."/modules/myquiz/admin/index.php?act=QuizzDelQuestion&pid=$pollID&qid=$qid\">"._MYQUIZ_DELETE."</a> ]</td></tr>";
        }
    echo "</table>";

    CloseTable();
xoops_cp_footer();
}

/*********************************************************/
/* Quizz Functions                                       */
/*********************************************************/

function QuizzAddContrib()
{
    global $xoopsDB,$xoopsConfig,$myts,$pid,$qid,$xoopsModule, $xoopsUser, $xoopsTheme, $xoopsLogger;

xoops_cp_header();
    OpenTable();
    echo "<b>"._MYQUIZ_ADDCONTRIB."</b><BR><BR>";

    $result = $xoopsDB->query("select pollTitle, answer, coef, good, bad, comment,image from ".$xoopsDB->prefix("myquiz_descontrib")." where pollID='$pid'");
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
    echo "<table><tr><td>"._MYQUIZ_QUESTIONTITLE.": <input type=\"text\" name=\"question\" size=\"50\" maxlength=\"100\" value=\"".$myts->MakeTboxData4Edit($pollTitle)."\"><br><br><br></td></tr></table>";
    echo "<table><tr><td>";
    echo "<tr><td>"._MYQUIZ_COEF."</td><td><input type='text' name='coef' value='$coef' size=3></td></tr>";
    echo "<tr><td>"._MYQUIZ_IMAGE." (*)</td><td colspan=3><input type='text' name='image' value='".$myts->MakeTboxData4Edit($image)."' size=50></td></tr>";
    $i=1;
    echo "<tr><td colspan=2>"._MYQUIZ_ANSWERS."</td><td>"._MYQUIZ_ANSWER."</td><td></td></tr>";
    $result = $xoopsDB->query("select optionText, voteID from ".$xoopsDB->prefix("myquiz_datacontrib")." WHERE pollID='$pid' ORDER BY voteID");
    while(list($Text,$voteID) = $xoopsDB->fetchRow($result))
    {
        if ($answer == $i) {$checked = "checked";} else {$checked = "";}
        echo "<tr><td>"._MYQUIZ_ANSWER." $i :</td><td><input type=\"text\" name=\"optionText[$i]\" size=\"50\" maxlength=\"50\" value=\"".$myts->MakeTboxData4Edit($Text)."\"></td>\n";
        echo "<td><input type=\"radio\" name=\"answer\" value=\"$i\" $checked> "/*.$myts->MakeTboxData4Edit($optionText)*/."<br></td>";

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
    echo "</tr></table><br><br>"
    ."<table width='100%' border=0>"
        ."<tr><td colspan=2>"._MYQUIZ_COMMENT." (*)<br>"
    ."<TEXTAREA wrap=\"virtual\" cols=\"50\" rows=\"4\" name=\"comment\">".$myts->MakeTareaData4Edit($comment)."</textarea></td></tr>"
        ."<tr><td colspan=2>"._MYQUIZ_IFBADANSWER." (*)<br>"
    ."<TEXTAREA wrap=\"virtual\" cols=\"50\" rows=\"4\" name=\"bad\">".$myts->MakeTareaData4Edit($bad)."</textarea></td></tr>"
    ."<tr><td colspan=2>"._MYQUIZ_IFGOODANSWER." (*)<br>"
    ."<TEXTAREA wrap=\"virtual\" cols=\"50\" rows=\"4\" name=\"good\">".$myts->MakeTareaData4Edit($good)."</textarea></td></tr>"
    ."<tr><td><input type=\"submit\" class=button value=\""._MYQUIZ_ADD."\"></form></td>"
    ."<td>"
    ."<form method='post' action='".XOOPS_URL."/modules/myquiz/admin/index.php'>"
    ."<INPUT type='hidden' name='act' value='deleteContributorQuizzQuestion'>"
    ."<INPUT type='hidden' name='qid' value=\"$qid\">"
    ."<INPUT type='hidden' name='pid' value=\"$pid\">"
    ."<input type=\"submit\" class=button value=\""._MYQUIZ_DELCONTRIB."\">"
    ."</form></td></tr></table>";

    echo "* : "._MYQUIZ_HELPOPTION."\n";
    CloseTable();
xoops_cp_footer();
}
/*********************************************************/
/* Quizz Functions                                       */
/*********************************************************/

function QuizzModifyCategory()
{
    global $xoopsConfig,$xoopsDB,$cid,$myts,$xoopsModule, $xoopsUser, $xoopsTheme, $xoopsLogger;

xoops_cp_header();
    OpenTable();
    echo "<b>"._MYQUIZ_MODIFYCAT."</b><BR><BR>";

    $result = $xoopsDB->query("SELECT name, comment, image from ".$xoopsDB->prefix("myquiz_categories")." where cid='$cid'");
    list ($name, $comment, $image) = $xoopsDB->fetchRow($result);

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
    echo "* : "._MYQUIZ_HELPOPTION."\n";

    CloseTable();
xoops_cp_footer();
}

/*********************************************************/
/* Quizz Functions                                       */
/*********************************************************/

function QuizzModifyQuestion()
{
    global $xoopsConfig,$xoopsDB,$qid,$pid,$myts,$xoopsModule, $xoopsUser, $xoopsTheme, $xoopsLogger;

xoops_cp_header();
    OpenTable();
    echo "<b>"._MYQUIZ_MODIFYQUESTION."</b><BR><BR>";

    $result = $xoopsDB->query("select pollTitle, answer, coef, good, bad, comment, image from ".$xoopsDB->prefix("myquiz_desc")." where pollID='$pid'");
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
    echo "<table width='100%' border=0><tr><td>"._MYQUIZ_QUESTIONTITLE.": <input type=\"text\" name=\"question\" size=\"50\" maxlength=\"100\" value=\"".$myts->MakeTboxData4Edit($pollTitle)."\"><br><br><br></td></tr></table>";
        echo "<table width='100%' border=0><tr><td>";
    echo "<tr><td>"._MYQUIZ_COEF."</td><td colspan=3><input type='text' name='coef' value='$coef' size=3></td></tr>";
    echo "<tr><td>"._MYQUIZ_IMAGE." (*)</td><td colspan=3><input type='text' name='image' value='".$myts->MakeTboxData4Edit($image)."' size=50></td></tr>";
        $i=1;
        echo "<tr><td colspan=2>"._MYQUIZ_ANSWERS."</td><td>"._MYQUIZ_ANSWER."</td><td></td></tr>";
    $result = $xoopsDB->query("select optionText, voteID from ".$xoopsDB->prefix("myquiz_data")." WHERE pollID='$pid' ORDER BY voteID");

    while(list($Text,$voteID) = $xoopsDB->fetchRow($result))
    {
        if ($answer == $i) {$checked = "checked";} else {$checked = "";}
        echo "<tr><td>"._MYQUIZ_ANSWER." $i :</td><td><input type=\"text\" name=\"optionText[$i]\" size=\"50\" maxlength=\"50\" value=\"".$myts->MakeTboxData4Edit($Text)."\"></td>\n";
        echo "<td><input type=\"radio\" name=\"answer\" value=\"$i\" $checked><br></td>";


          //      echo "<td><SELECT name=\"optionSort[$i]\">";
           //     echo "<option name=\"rank\">--</option>";
           //     for($j = 1; $j <= 12; $j++)
           //     {
            //     if ($answer == $j) { $sel = "selected"; } else { $sel = "";  }
            //    echo "<option name=\"rank\" $sel>$j</option>";
            //    }
            //    echo "</select>";
       $i++;
    }
    echo "</tr></table><br><br>"
    ."<table width='100%' border=0>"
        ."<tr><td>"._MYQUIZ_COMMENT." (*)<br>"
    ."<TEXTAREA wrap=\"virtual\" cols=\"50\" rows=\"4\" name=\"comment\">".$myts->MakeTareaData4Edit($comment)."</textarea></td></tr>"
        ."<tr><td>"._MYQUIZ_IFBADANSWER." (*)<br>"
    ."<TEXTAREA wrap=\"virtual\" cols=\"50\" rows=\"4\" name=\"bad\">".$myts->MakeTareaData4Edit($bad)."</textarea></td></tr>"
    ."<tr><td>"._MYQUIZ_IFGOODANSWER." (*)<br>"
    ."<TEXTAREA wrap=\"virtual\" cols=\"50\" rows=\"4\" name=\"good\">".$myts->MakeTareaData4Edit($good)."</textarea></td></tr></table><br><br>"
    ."<input type=\"submit\" class=button value=\""._MYQUIZ_MODIFYQUESTION."\">"
    ."</form>";
    echo "* : "._MYQUIZ_HELPOPTION."\n";
    CloseTable();
xoops_cp_footer();
}

/*********************************************************/
/* Quizz Functions                                       */
/*********************************************************/

function modifyPostedQuizzQuestion()
{
    global $question, $optionText,$qid,$pid,$answer,$coef,$good,$bad,$comment,$optionSort,$image,$myts,$xoopsConfig,$xoopsDB, $xoopsUser, $xoopsTheme, $xoopsLogger;

    $question = $myts->MakeTboxData4Save($question);
    $good = $myts->MakeTareaData4Save($good);
    $bad = $myts->MakeTareaData4Save($bad);
    $comment = $myts->MakeTareaData4Save($comment);

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
    if(!$xoopsDB->query("UPDATE ".$xoopsDB->prefix("myquiz_desc")." SET pollTitle='$question', answer='$answer', coef='$coef', good='$good', bad='$bad', comment='$comment', image='$image' WHERE pollID='$pid'"))
        {
        echo $xoopsDB->errno(). ": ".$xoopsDB->error(). "<br>";
        return;
    }

    for($i = 1; $i <= sizeof($optionText); $i++)
        {
        if($optionText[$i] != "")
        {
            $optionText[$i] = $myts->MakeTboxData4Save($optionText[$i]);
        }

        if(!$xoopsDB->query("UPDATE ".$xoopsDB->prefix("myquiz_data")." SET optionText='$optionText[$i]' WHERE voteID='$i' AND pollID='$pid'"))
        {
            echo $xoopsDB->errno(). ": ".$xoopsDB->error(). "<br>";
            return;
        }
    }
    Header("Location: ".XOOPS_URL."/modules/myquiz/admin/index.php?act=QuizzModify&qid=$qid");
}


/*********************************************************/
/* Quizz Functions                                       */
/*********************************************************/

function QuizzAddQuestion()
{
    global $qid,$xoopsConfig,$xoopsModule, $xoopsDB, $xoopsUser, $xoopsTheme, $xoopsLogger;

xoops_cp_header();
    OpenTable();
    echo "<b>"._MYQUIZ_ADDQUESTION."</b><BR><BR>";

    echo "\n<form method='post'action='".XOOPS_URL."/modules/myquiz/admin/index.php'>";
    echo "<INPUT type='hidden' name='act' value='createPostedQuizzQuestion'>";
    echo "<INPUT type='hidden' name='qid' value=\"$qid\">";
    echo "<table width='100%' border=0><tr><td>"._MYQUIZ_QUESTIONTITLE.": <input type=\"text\" name=\"question\" size=\"50\" maxlength=\"100\" value=\"???\"><br><br><br></td></tr></table><table width='100%' border=0><tr><td>";
    echo "<tr><td>"._MYQUIZ_COEF."</td><td colspan=3><input type='text' name='coef' value='1' size=3></td></tr>";
    echo "<tr><td>"._MYQUIZ_IMAGE." (*)</td><td colspan=3><input type='text' name='image' size=50></td></tr>";
        echo "<tr><td colspan=2>"._MYQUIZ_ANSWERS."</td><td>"._MYQUIZ_ANSWER."</td></tr>";
    for($i = 1; $i <= 12; $i++)
    {
        if ($i == 1) { $checked = "checked"; } else { $checked = "";}
        echo "<tr><td>"._MYQUIZ_ANSWER." $i:</td>";
                echo "<td><input type=\"text\" name=\"optionText[$i]\" size=\"50\" maxlength=\"50\"></td>";
        echo "<td><input type=\"radio\" name=\"answer\" value=\"$i\" $checked> <br></td>";

      //          echo "<td><SELECT name=\"optionSort[$i]\">";
      //          echo "<option name=\"rank\">--</option>";
     //           for($j = 1; $j <= 12; $j++)
       //         {
       //                         echo "<option name=\"rank\" $sel>$j</option>";
        //        }
         //       echo "</select>";
    }
    echo "</tr></table><br><br>"
    ."<table width='100%' border=0>"
        ."<tr><td>"._MYQUIZ_COMMENT." (*)<br>"
    ."<TEXTAREA wrap=\"virtual\" cols=\"50\" rows=\"6\" name=\"comment\"></textarea></td></tr>"
        ."<tr><td>"._MYQUIZ_IFBADANSWER." (*)<br>"
    ."<TEXTAREA wrap=\"virtual\" cols=\"50\" rows=\"6\" name=\"bad\"></textarea></td></tr>"
    ."<tr><td>"._MYQUIZ_IFGOODANSWER." (*)<br>"
    ."<TEXTAREA wrap=\"virtual\" cols=\"50\" rows=\"6\" name=\"good\"></textarea></td></tr>"
    ."</table><br><br><input type=\"submit\" class=button value=\""._MYQUIZ_ADDQUESTION."\">"
    ."</form>";
    echo "* : "._MYQUIZ_HELPOPTION."\n";
    CloseTable();
xoops_cp_footer();
}

/*********************************************************/
/* Quizz Functions                                       */
/*********************************************************/

function QuizzAdd()
{
    global $xoopsConfig,$xoopsDB,$myts,$xoopsModule, $xoopsUser, $xoopsTheme, $xoopsLogger;

xoops_cp_header();
    OpenTable();
    echo "<b>"._MYQUIZ_NEW."</b><BR><BR>";

    echo "\n<form method='post'action='".XOOPS_URL."/modules/myquiz/admin/index.php'>";
    echo "<INPUT type='hidden' name='act' value='createPostedQuizz'>";
    echo "";
    echo "<table width='100%'>";

    echo "<tr><td>"._MYQUIZ_TITLE."</td><td><input type='text' name='quizztitle' size=30></td></tr>";
    echo "<tr><td>"._MYQUIZ_VIEWANSWER."</td><td><input type='checkbox' name='displayresults' checked> "._MYQUIZ_HELPANSWER."</td></tr>";
    echo "<tr><td>"._MYQUIZ_VIEWSCORE."</td><td><input type='checkbox' name='displayscore' checked> "._MYQUIZ_HELPVIEWSCORE."</td></tr>";
//    echo "<tr><td>"._MYQUIZ_SENDEMAIL."</td><td><input type='checkbox' name='emailadmin' checked> "._MYQUIZ_HELPEMAIL."</td></tr>";
//    echo "<tr><td>"._MYQUIZ_ONLYREGISTERED."</td><td><input type='checkbox' name='restrict' checked> "._MYQUIZ_HELPONLYREGISTERED."</td></tr>";
    echo "<tr><td>"._MYQUIZ_LIMITVOTE."</td><td><input type='checkbox' name='log' checked> "._MYQUIZ_HELPLIMITVOTE."</td></tr>";
    echo "<tr><td>"._MYQUIZ_CONTRIB."</td><td><input type='checkbox' name='contrib' checked> "._MYQUIZ_HELPCONTRIB."</td></tr>";
    echo "<tr><td>"._MYQUIZ_NBSCORE."</td><td><input type='text' name='nbscore' size=3 value=10> "._MYQUIZ_HELPNBSCORE."</td></tr>";

//    echo "<tr><td>"._MYQUIZ_ADMEMAIL."</td><td><input type='text' name='admemail' size=30> (*) "._MYQUIZ_HELPADMEMAIL."</td></tr>";
//    echo "<tr><td>"._MYQUIZ_ADMINISTRATOR."</td><td><input type='text' name='administrator' size=30> (*) "._MYQUIZ_HELPADMINISTRATOR."</td></tr>";
    echo "<tr><td>"._MYQUIZ_IMAGE."</td><td><input type='text' name='image' size=30> (*) "._MYQUIZ_HELPIMAGE."</td></tr>";
        echo "<tr><td>"._MYQUIZ_CAT."</td><td><SELECT name=\"cid\">";
    $result = $xoopsDB->query("select cid, name from ".$xoopsDB->prefix("myquiz_categories"));
    while(list($catid, $name) = $xoopsDB->fetchRow($result))
    {
        if ($catid==$cid) { $sel = "selected "; }
            echo "<option $sel value=\"$catid\">".$myts->MakeTboxData4PreviewInForm($name)."</option>\n";
                $sel = "";
    }
    echo "</select></td></tr>";

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
    echo "</select><br> ("._MYQUIZ_NOWIS." : $now)</td></tr>";


    echo "<tr><td colspan=2>"._MYQUIZ_COMMENT." (*)<br><TEXTAREA wrap=\"virtual\" cols=\"50\" rows=\"4\" name=\"comment\"></textarea></td></tr>";
    echo "<tr><td colspan=2>"._MYQUIZ_CONDITIONS." (*)<br><TEXTAREA cols=\"50\" rows=\"4\" name=\"conditions\"></textarea></td></tr>";


    echo "</table>";
    echo "<br><center><input type='submit' class=button value='"._MYQUIZ_CREATE."'></center>";
    echo "</form>";

    echo "* : "._MYQUIZ_HELPOPTION."\n";
    CloseTable();
xoops_cp_footer();
}

/*********************************************************/
/* Quizz Functions                                       */
/*********************************************************/

function modifyPostedQuizz()
{
        global $qid, $quizztitle, $displayresults, $displayscore, $emailadmin, $nbscore, $comment, $image, $log, $restrict, $active, $cid, $contrib, $year, $month, $day, $hour, $min, $admemail, $administrator, $conditions,$myts,$xoopsConfig,$xoopsDB, $xoopsUser, $xoopsTheme, $xoopsLogger;

    $quizztitle = $myts->MakeTboxData4Save($quizztitle);
    $comment = $myts->MakeTareaData4Save($comment);
    $conditions = $myts->MakeTareaData4Save($conditions);
    $expire = "$year-$month-$day $hour:$min";



        if(isset($emailadmin)) $emailadmin=1; else $emailadmin=0;
        if(isset($displayresults)) $displayresults=1; else $displayresults=0;
        if(isset($displayscore)) $displayscore=1; else $displayscore=0;
        if(isset($restrict)) $restrict=1; else $restrict=0;
        if(isset($log)) $log=1; else $log=0;
        if(isset($active)) $active=1; else $active=0;
        if(isset($contrib)) $contrib=1; else $contrib=0;

    if(!$xoopsDB->query("UPDATE ".$xoopsDB->prefix("myquiz_admin")." SET quizzTitle='$quizztitle', nbscore='$nbscore', displayscore='$displayscore', displayresults='$displayresults', emailadmin='$emailadmin', comment='$comment', image='$image', restrict_user='$restrict', log_user='$log', active='$active', cid='$cid', contrib='$contrib', expire='$expire', admemail='$admemail', administrator='$administrator', conditions='$conditions' WHERE quizzID='$qid'")) {
        echo $xoopsDB->errno(). ": ".$xoopsDB->error(). "<br>";
        return;
    }
    Header("Location: ".XOOPS_URL."/modules/myquiz/admin/index.php?act=QuizzAdmin");
}

/*********************************************************/
/* Quizz Functions                                       */
/*********************************************************/

function createPostedQuizz()
{
    global $quizztitle, $displayresults, $displayscore,$emailadmin,$nbscore,$comment,$image,$restrict,$log,$cid,$contrib,$year,$month,$day,$hour,$min,$prefix,$base_url,$admemail,$administrator,$conditions,$myts,$xoopsConfig,$xoopsDB, $xoopsUser, $xoopsTheme, $xoopsLogger;

    if(isset($emailadmin)) $emailadmin=1; else $emailadmin=0;
    if(isset($displayresults)) $displayresults=1; else $displayresults=0;
    if(isset($displayscore)) $displayscore=1; else $displayscore=0;
    if(isset($restrict)) $restrict=1; else $restrict=0;
    if(isset($log)) $log=1; else $log=0;
    if(isset($contrib)) $contrib=1; else $contrib=0;
    $expire = "$year-$month-$day $hour:$min";

    $timeStamp = time();
    $quizztitle = $myts->MakeTboxData4Save($quizztitle);
    $comment = $myts->MakeTareaData4Save($comment);
    $conditions = $myts->MakeTareaData4Save($conditions);
    if(!$xoopsDB->query("INSERT INTO ".$xoopsDB->prefix("myquiz_admin")." VALUES (NULL, '$quizztitle', '$timeStamp', 0, '$nbscore','$displayscore','$displayresults','$emailadmin','$comment',0,'$restrict','$log','$image','$cid','$contrib','$expire','$admemail','$administrator','$conditions')")) {
        echo $xoopsDB->errno(). ": ".$xoopsDB->error(). "<br>";
        return;
    }
    Header("Location: ".XOOPS_URL."/modules/myquiz/admin/index.php?act=QuizzAdmin");
}

/*********************************************************/
/* Quizz Functions                                       */
/*********************************************************/

function createPostedQuizzQuestion()
{
    global $question, $optionText,$qid,$answer,$coef,$good,$bad,$mode,$pid,$comment,$prefix,$base_url,$optionSort,$image,$myts,$xoopsConfig,$xoopsDB, $xoopsUser, $xoopsTheme, $xoopsLogger;

    $good = $myts->MakeTareaData4Save($good);
    $bad = $myts->MakeTareaData4Save($bad);
    $comment = $myts->MakeTareaData4Save($comment);
    $question = $myts->MakeTboxData4Save($question);

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
        echo $xoopsDB->errno(). ": ".$xoopsDB->error(). "<br>";
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
            echo $xoopsDB->errno(). ": ".$xoopsDB->error(). "<br>";
            return;
        }
    }

    # delete contributor question if needed
    if ($mode == "contrib")
    {
         $xoopsDB->query("DELETE FROM ".$xoopsDB->prefix("myquiz_datacontrib")." WHERE pollID='$pid'");
         $xoopsDB->query("DELETE FROM ".$xoopsDB->prefix("myquiz_descontrib")." WHERE pollID='$pid'");
    }
    Header("Location: ".XOOPS_URL."/modules/myquiz/admin/index.php?&act=QuizzModify&qid=$qid");
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

    Header("Location: ".XOOPS_URL."/modules/myquiz/admin/index.php?act=QuizzAdmin");
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
    Header("Location: ".XOOPS_URL."/modules/myquiz/admin/index.php?&act=QuizzAdmin");
}

/*********************************************************/
/* Quizz Functions                                       */
/*********************************************************/

function QuizzDelCategory()
{
        global $cid,$xoopsConfig,$xoopsDB,$xoopsModule, $xoopsUser, $xoopsTheme, $xoopsLogger;

xoops_cp_header();
    OpenTable();
    echo "<b>"._MYQUIZ_DELCAT."</b><br><br><center>"
        .""._MYQUIZ_SURE2DELETECAT." $cid & "._MYQUIZ_ALLCONTENTS."<br><br>"
        ."[ <a href=\"".XOOPS_URL."/modules/myquiz/admin/index.php?act=removePostedQuizzCategory&cid=$cid\">"._YES."</a> | <a href=\"".XOOPS_URL."/modules/myquiz/admin/index.php?act=QuizzAdmin\">"._NO."</a> ]</center>";
    CloseTable();
xoops_cp_footer();


}

/*********************************************************/
/* Quizz Functions                                       */
/*********************************************************/

function createPostedQuizzCategory()
{
        global $CatName,$CatComment,$CatImage,$xoopsConfig,$xoopsDB,$myts, $xoopsUser, $xoopsTheme, $xoopsLogger;

        $CatName=$myts->MakeTboxData4Save($CatName);
        $CatComment=$myts->MakeTboxData4Save($CatComment);

    if(!$xoopsDB->query("INSERT INTO ".$xoopsDB->prefix("myquiz_categories")." VALUES (NULL, '$CatName','$CatComment','$CatImage')"))
        {
                echo $xoopsDB->errno(). ": ".$xoopsDB->error(). "<br>";
                return;
    }
    Header("Location: ".XOOPS_URL."/modules/myquiz/admin/index.php?act=QuizzAdmin");
}

/*********************************************************/
/* Quizz Functions                                       */
/*********************************************************/

function modifyPostedQuizzCategory()
{
        global $CatName,$CatComment,$CatImage,$cid,$xoopsConfig,$xoopsDB,$myts, $xoopsUser, $xoopsTheme, $xoopsLogger;

        $CatName=$myts->MakeTboxData4Save($CatName);
        $CatComment=$myts->MakeTboxData4Save($CatComment);


    if(!$xoopsDB->query("UPDATE ".$xoopsDB->prefix("myquiz_categories")." SET name='$CatName',comment='$CatComment',image='$CatImage' WHERE cid='$cid'"))
        {
                echo $xoopsDB->errno(). ": ".$xoopsDB->error(). "<br>";
                return;
    }
    Header("Location: ".XOOPS_URL."/modules/myquiz/admin/index.php?act=QuizzAdmin");
}


/*********************************************************/
/* Quizz Functions                                       */
/*********************************************************/

function deleteContributorQuizzQuestion() {
    global $qid,$pid,$xoopsConfig,$xoopsModule, $xoopsDB, $xoopsUser, $xoopsTheme, $xoopsLogger;

xoops_cp_header();
    OpenTable();
    echo "<b>"._MYQUIZ_DELCONTRIB." $pid?</b><br><br><center>"
        ."[ <a href=\"".XOOPS_URL."/modules/myquiz/admin/index.php?act=deletePostedContributorQuizzQuestion&qid=$qid&pid=$pid\">"._YES."</a> | <a href=\"".XOOPS_URL."/modules/myquiz/admin/index.php?act=QuizzAdmin\">"._NO."</a> ]</center>";
    CloseTable();
xoops_cp_footer();

}
/*********************************************************/
/* Quizz Functions                                       */
/*********************************************************/

function QuizzDelQuestion() {
    global $qid,$pid,$xoopsConfig,$xoopsModule, $xoopsDB, $xoopsUser, $xoopsTheme, $xoopsLogger;

xoops_cp_header();
    OpenTable();
    echo "<b>"._MYQUIZ_DELQUESTION."</b><br><br><center>"
        .""._MYQUIZ_SURE2DELQUESTION." $pid?<br><br>"
        ."[ <a href=\"".XOOPS_URL."/modules/myquiz/admin/index.php?act=removePostedQuizzQuestion&qid=$qid&pid=$pid\">"._YES."</a> | <a href=\"".XOOPS_URL."/modules/myquiz/admin/index.php?act=QuizzModify&qid=$qid\">"._NO."</a> ]</center>";
    CloseTable();
xoops_cp_footer();

}

/*********************************************************/
/* Quizz Functions                                       */
/*********************************************************/

function QuizzRemove() {
    global $xoopsConfig,$qid,$xoopsModule, $xoopsDB, $xoopsUser, $xoopsTheme, $xoopsLogger;

xoops_cp_header();
    OpenTable();
    echo "<b>"._MYQUIZ_DELETE."</b><br><br><center>"
        .""._MYQUIZ_SURE2DELETE." $qid?<br><br>"
        ."[ <a href=\"".XOOPS_URL."/modules/myquiz/admin/index.php?act=removePostedQuizz&qid=$qid\">"._YES."</a> | <a href=\"".XOOPS_URL."/modules/myquiz/admin/index.php?act=QuizzAdmin\">"._NO."</a> ]</center>";
    CloseTable();
xoops_cp_footer();

}

/*********************************************************/
/* Quizz Functions                                       */
/*********************************************************/

function removePostedQuizzCategory() {
    global $cid,$xoopsDB,$xoopsConfig, $xoopsUser, $xoopsTheme, $xoopsLogger;

    $xoopsDB->queryF("DELETE FROM ".$xoopsDB->prefix("myquiz_categories")." WHERE cid='$cid'");
    Header("Location: ".XOOPS_URL."/modules/myquiz/admin/index.php?act=QuizzAdmin");
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
    Header("Location: ".XOOPS_URL."/modules/myquiz/admin/index.php?act=QuizzAdmin");
}

/*********************************************************/
/* Quizz Functions                                        */
/*********************************************************/

function removePostedQuizzQuestion() {
    global $qid,$pid,$xoopsDB, $xoopsUser, $xoopsTheme, $xoopsLogger, $xoopsConfig;
    $xoopsDB->queryF("DELETE FROM ".$xoopsDB->prefix("myquiz_desc")." WHERE pollID='$pid'");
    $xoopsDB->queryF("DELETE FROM ".$xoopsDB->prefix("myquiz_data")." WHERE pollID='$pid'");
    Header("Location: ".XOOPS_URL."/modules/myquiz/admin/index.php?act=QuizzModify&qid=$qid");
}

/*********************************************************/

if (!isset($act))
{
    QuizzAdmin();
}
else
{
switch($act) {

    case "createPostedQuizzQuestion":
    createPostedQuizzQuestion();
    break;

    case "modifyPostedQuizz":
    modifyPostedQuizz();
    break;

    case "modifyPostedQuizzCategory":
    modifyPostedQuizzCategory();
    break;

    case "modifyPostedQuizzQuestion":
    modifyPostedQuizzQuestion();
    break;

    case "deletePostedScoreQuizz":
    deletePostedScoreQuizz();
    break;

    case "removePostedQuizzCategory":
    removePostedQuizzCategory();
    break;

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

    case "QuizzRemove":
    QuizzRemove();
    break;

    case "createPostedQuizz":
    createPostedQuizz();
    break;

    case "QuizzModifyCategory":
    QuizzModifyCategory();
    break;

    case "QuizzModifyQuestion":
    QuizzModifyQuestion();
    break;

    case "QuizzAddContrib":
    QuizzAddContrib();
    break;

    case "QuizzModify":
    QuizzModify();
    break;

    case "QuizzDelCategory":
    QuizzDelCategory();
    break;

    case "createPostedQuizzCategory":
    createPostedQuizzCategory();
    break;

    case "deletePostedContributorQuizzQuestion":
    deletePostedContributorQuizzQuestion();
    break;

    case "deleteContributorQuizzQuestion":
    deleteContributorQuizzQuestion();
    break;

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
}

?>