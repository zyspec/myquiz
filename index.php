<?php
#####################################################
#  Based on Quizz 1.4.1
#  by xbee (xbee@xbee.net) http://www.xbee.net
#  Licence: GPL
#
#  Adapted, modified and improved for Xoops 1.0 RC3
#  by Moumou inconnu0215@noos.fr
#  and Pascal Le Boustouller pascal@xoopsien.net - http://www.xoopsien.net
#  redesigned and adapted for Xoops V2 by frankblack@01019freenet.de
#  Copyright © 2002
# Thank you to leave this copyright in place...
#####################################################

include("../../mainfile.php");

/*********************************************************/
/* Quizz Functions                                       */
/*********************************************************/

function addContributeQuizzQuestion ($qid) {
   global $xoopsDB, $xoopsConfig, $question, $optionText, $qid, $answer, $coef, $good, $bad, $comment, $optionSort, $image, $xoopsUser, $xoopsTheme, $xoopsLogger;

$myts =& MyTextSanitizer::getInstance();

        $good = $myts->oopsNl2Br($myts->makeTareaData4Save($good));
        $bad = $myts->oopsNl2Br($myts->makeTareaData4Save($bad));
        $comment = $myts->oopsNl2Br($myts->makeTareaData4Save($comment));
        $question = $myts->oopsNl2Br($myts->makeTboxData4Save($question));

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


                                
                                echo "<center>"._MYQUIZ_INCORRECTORDER."</center>";
                               

                                exit;
                        }
                }
                # change the answer the the ordered answer
                $answer = $ordered_answer;
    }

    if(!$xoopsDB->query("INSERT INTO ".$xoopsDB->prefix("myquiz_descontrib")." VALUES (NULL, '$question', '$timeStamp', 0, '$qid','$answer','$coef','$good','$bad','$comment','')"))
        {
        echo $xoopsDB->errno(). ": ".$xoopsDB->error(). "<br>";
        return;
    }
    $array = $xoopsDB->fetchArray($xoopsDB->query("SELECT pollID,timeStamp  FROM ".$xoopsDB->prefix("myquiz_descontrib")." WHERE timeStamp='$timeStamp'"));
    $id = $array['pollID'];
    $t = $array['timeStamp'];
    for($i = 1; $i <= sizeof($optionText); $i++)
        {
        if($optionText[$i] != "")
        {
            $optionText[$i] = $myts->MakeTboxData4Save($optionText[$i]);
        }
        if(!$xoopsDB->query("INSERT INTO ".$xoopsDB->prefix("myquiz_datacontrib")." (pollID, optionText, optionCount, voteID) VALUES ($id, '$optionText[$i]', 0, $i)"))
        {
            echo $xoopsDB->errno(). ": ".$xoopsDB->error(). "<br>";
            return;
        }
    }
        # update the image name
        if (!empty($image) and $image != "none")
        {
                   if (!copy($image, XOOPS_ROOT_PATH."/modules/myquiz/images/${t}.gif"))
                {
                        
                        echo "Error ! Can't copy image $image <br>";
                       
                }

            if(!$xoopsDB->query("UPDATE ".$xoopsDB->prefix("myquiz_descontrib")." SET image='${t}.gif' WHERE pollID='$id'"))
        {
        echo $xoopsDB->errno(). ": ".$xoopsDB->error(). "<br>";
        return;
            }

        }
    
    echo "<table border=0 class='even'><tr><td>"._MYQUIZ_CONTRIBTHANKS."<br><br><a href=\"".XOOPS_URL."/modules/myquiz/index.php\">"._MYQUIZ_BACKTOINDEX."</a></td></tr></table>";
   
}
/*********************************************************/
/* Quizz Functions                                       */
/*********************************************************/

function QuizzContribute ($qid)
{
    global $xoopsUser, $xoopsConfig, $qid, $xoopsDB, $xoopsTheme, $xoopsLogger;




    # Abfrage ob registrierter User
  
  if ( !$xoopsUser ) {
                        redirect_header("index.php",2,_MYQUIZ_ADDQUESTIONSORRY);
                exit();
        }




    # the current user
    $logname = $xoopsUser->getVar("uname", "E");
    
    echo "<table border=0 class='even'><tr><td>";
    echo "<form enctype=\"multipart/form-data\" action='".$xoopsConfig["xoops_url"]."/modules/myquiz/index.php' method='post'>";
    echo "<input type='hidden' name='do' value='addContributeQuizzQuestion'>";
    echo "<input type='hidden' name='qid' value=\"$qid\">";
    echo "<table><tr><td colspan='3'><strong>"._MYQUIZ_QUESTIONTITLE.":</strong> <input type=\"text\" name=\"question\" size=\"50\" maxlength=\"100\" value=\"???\"><br><br></td></tr>";

    echo "<tr><td colspan='3'><strong>"._MYQUIZ_COEF.":</strong> <input type='text' name='coef' value='1' size=3><br><br></td></tr></table>";
    
    

    for($i = 1; $i <= 12; $i++)
    {
        if ($i == 1) { $checked = "checked"; } else { $checked = "";}
        
    echo "<table>";    
    echo "<tr><td width=100>"._MYQUIZ_ANSWER." $i:</td>";
    echo "<td><input type=\"text\" name=\"optionText[$i]\" size=\"50\" maxlength=\"50\">";
        $optionTex = isset($optionText) ? $optionText : "";
    echo " <input type=\"radio\" name=\"answer\" value=\"$i\" $checked> $optionTex <- Markieren falls richtig</td></tr>";
    echo "</table>";

    }



    echo "<br><br>"
    .""
        ."<strong>"._MYQUIZ_COMMENT." (*)</strong><br>"
    ."<TEXTAREA cols=\"50\" rows=\"4\" name=\"comment\"></textarea><br><br>"
        ."<strong>"._MYQUIZ_IFBADANSWER." (*)</strong><br>"
    ."<TEXTAREA cols=\"50\" rows=\"4\" name=\"bad\"></textarea><br><br>"
    ."<strong>"._MYQUIZ_IFGOODANSWER." (*)</strong><br>"
    ."<TEXTAREA cols=\"50\" rows=\"4\" name=\"good\"></textarea>"
    ."<br><br><input type=\"submit\" class=button value=\""._MYQUIZ_ADDQUESTION."\">"
    ."</form><br>";
    echo "*: "._MYQUIZ_HELPOPTION."<br><br>";
    echo "</table>";

}

/*********************************************************/
/* Quizz Functions                                       */
/*********************************************************/

function QuizzShow ($qid) {
        global $xoopsUser, $xoopsDB, $xoopsConfig, $xoopsTheme, $xoopsLogger;

$myts =& MyTextSanitizer::getInstance();

        # check for  authorization
    $result = $xoopsDB->query("select quizzTitle, comment , active, restrict_user, log_user, image, contrib, expire , displayscore,nbscore, conditions  from ".$xoopsDB->prefix("myquiz_admin")." where quizzID='$qid'");
    list($quizzTitle,$comment,$active, $restrict_user, $log_user,$image,$contrib,$expire,$displayscore,$nbscore,$conditions) = $xoopsDB->fetchRow($result);

        # check if quizz is active

        if ($active == 0) {
                if (!$xoopsUser) {
                       
                        echo "<center>"._MYQUIZ_MUSTBEACTIVE."</center>";
                       
                        return;
                } else {
                        $xoopsModule = XoopsModule::getByDirname("myquiz");
                        if(!$xoopsUser->isAdmin($xoopsModule->mid())) {
                                
                                echo "<center>"._MYQUIZ_MUSTBEACTIVE."</center>";
                              
                                return;
                        }
                }
        }


        # first check if it exists an expiration date (no xx)
        if (!ereg("xx",$expire)) {

$Cann = strftime("%Y",strtotime($expire));
$Cmoi = strftime("%m",strtotime($expire));
$Cjour = strftime("%d",strtotime($expire));
$Cheur = strftime("%H",strtotime($expire));
$Cmin = strftime("%M",strtotime($expire));

    $today = getdate();
    $day = $today[mday];
    if ($day < 10){ $day = "$day"; }
    $month = $today[mon];
    $year = $today[year];
    $hour = $today[hours];
    $min = $today[minutes];
    $sec = $today[seconds];

                # check for the day
            if (($date[1] <= $year)
                AND ($Cmoi <= $month)
                AND ($Cjour <= $day)
            AND ($Cheur <= $hour)
                AND ($Cmin <= $min)) {
                        $expired = 1;
           
            echo "<table border='0' class='even'><tr><td align='center'><strong>"._MYQUIZ_HASEXPIRED."</strong></td></tr></table>";
           
                        return;
                }

                # show the score if needed
                if ($displayscore == 1 AND $expired == 1)
                {
                             
                                echo ""._MYQUIZ_LISTSCORE."<br>";
                                $result = $xoopsDB->query("SELECT username, score FROM ".$xoopsDB->query("myquiz_check")." WHERE qid='$qid' ORDER BY score DESC,time DESC LIMIT $nbscore ");
                                while(list($username,$res) = $xoopsDB->fetchRow($result))
                                {
                                                echo "$username: $res<br>";
                                }
                            

                # bye bye !! :)
               
                echo "<table border=0 class='even'><tr><td>"._MYQUIZ_THANKS."<br></td></tr></table>";
             
                }
                if ($expired == 1) {
                return;
                }
        }

        # get the login infos  for the registered user
    if ($xoopsUser) {
                    $logname = $xoopsUser->getVar("uname", "E");
                        $adrs = $xoopsUser->getVar("email", "E");
                    }

        # check if the user is logged (only one vote for the quizz)
        if ($log_user == 1) {
                # search in the database for a previous vote
                $result = $xoopsDB->query("SELECT username FROM ".$xoopsDB->prefix("myquiz_check")." WHERE  (qid='$qid' AND username='$logname') OR (qid='$qid' AND email='$adrs')");
                if($xoopsDB->getRowsNum($result) > 0) {
                        echo "<table border='0' class='even'><tr><td align='center'><strong>"._MYQUIZ_ALREADYVOTED."</strong></td></tr></table>";
                        return;
                }
        }
        if ($xoopsUser) {
                $xoopsModule = XoopsModule::getByDirname("myquiz");
                if ( $xoopsUser->isAdmin($xoopsModule->mid()) ) {
                        $menu = "[ <a href=\"".XOOPS_URL."/modules/myquiz/admin/index.php?act=QuizzViewScore&qid=$qid\">"._MYQUIZ_VIEWSCORE."</a> | ";
                            $menu .= "<a href=\"".XOOPS_URL."/modules/myquiz/admin/index.php?act=QuizzRemoveScore&qid=$qid\">"._MYQUIZ_DELSCORE."</a> | ";
                            $menu .= "<a href=\"".XOOPS_URL."/modules/myquiz/admin/index.php?act=QuizzViewStats&qid=$qid\">"._MYQUIZ_VIEWSTAT."</a> | ";
                               $menu .= "<a href=\"".XOOPS_URL."/modules/myquiz/admin/index.php?act=QuizzModify&qid=$qid\">"._MYQUIZ_MODIFY."</a> | ";
                               $menu .= "<a href=\"".XOOPS_URL."/modules/myquiz/admin/index.php?act=QuizzRemove&qid=$qid\">"._MYQUIZ_DELETE."</a> ]";
                }
        }

        if (!empty($image))
        {
            echo "<table border='0' class='even'><tr><td><b>$quizzTitle</b> $menu<center><img src='".XOOPS_URL."/modules/myquiz/images/$image' border=0 alt=''></center></td></tr></table>";
                echo "<table border='0' class='even'><tr><td>".$myts->MakeTareaData4Show($comment)."</td></tr></table>";
        }
        else
        {
            $men=isset($menu) ? $menu : "";
            echo "<table border='0' class='even'><tr><td><b>$quizzTitle</b> $men</td></tr></table>";
                echo "<table border='0' class='even'><tr><td>".$myts->MakeTareaData4Show($comment)."</td></tr></table>";
        }

            if (!empty($conditions)) {
                echo "<table border='0' class='even'><tr><td>".sprintf(_MYQUIZ_READCONDITIONS, $xoopsConfig['xoops_url'], $qid)."</td></tr></table>";

                        }

        # print the quizz form
    echo "<form action=\"".XOOPS_URL."/modules/myquiz/index.php\" method=\"post\">";
    echo "<input type=\"hidden\" name=\"qid\" value=\"$qid\">";
    echo "<input type=\"hidden\" name=\"do\" value=\"vote\">";
        if ($xoopsUser)
        {
            echo "<input type=\"hidden\" name=\"logname\" value=\"$logname\">";
            echo "<input type=\"hidden\" name=\"adrs\" value=\"$adrs\">";
        }
        else
        {

                echo "<table border='0' class='even'><tr><td align='center'>"._MYQUIZ_LOGNAME.": <input type='text' name='logname'> ";
                echo ""._MYQUIZ_EMAIL.": <input type='text' name='adrs'></td></tr></table>";
      
                        echo "<P>";
        }


        $num = 1;
    $result = $xoopsDB->query("SELECT pollID, pollTitle, voters, comment, answer, image FROM ".$xoopsDB->prefix("myquiz_desc")." WHERE qid='$qid'");
    while(list($pollID, $pollTitle, $voters, $comment,$answer,$image) = $xoopsDB->fetchRow($result))
    {
                echo "";
                if (!empty($image))
                {
        echo "<table border='0' class='even'><tr><td><img src='".XOOPS_URL."/modules/myquiz/images/$image' border=0 align='absmiddle'>"
                ."<b>"._MYQUIZ_QUESTIONTITLE." $num : ".$myts->MakeTboxData4Show($pollTitle)."</b></td></tr></table>";
                }
                else
                {
        echo "<table border='0' class='even'><tr><td><b>"._MYQUIZ_QUESTIONTITLE." $num : ".$myts->MakeTboxData4Show($pollTitle)."</b></td></tr></table>";
                }

                if ($xoopsUser)
                {
                        $xoopsModule = XoopsModule::getByDirname("myquiz");
                        if ( $xoopsUser->isAdmin($xoopsModule->mid()) ) {
                                       echo "<table border='0' class='even'><tr><td>[ <a href=\"".XOOPS_URL."/modules/myquiz/admin/index.php?act=QuizzModifyQuestion&pid=$pollID&qid=$qid\">"._MYQUIZ_MODIFY."</a> | ";
                                       echo "<a href=\"".XOOPS_URL."/modules/myquiz/admin/index.php?act=QuizzDelQuestion&pid=$pollID&qid=$qid\">"._MYQUIZ_DELETE."</a> ]</td></tr></table>";
                               }
                       }

                
                if (!empty($comment))
                {
            echo "<table border='0' class='even'><tr><td>&nbsp;&nbsp;<i>".$myts->MakeTareaData4Show($comment)."</i></td></tr></table>";
                }


        echo "<input type=\"hidden\" name=\"pollID\" value=\"$pollID\">";

                # ordered answer needed ?
            if (ereg(",",$answer))
            {
            $result1 = $xoopsDB->query("SELECT optionText, voteID FROM ".$xoopsDB->prefix("myquiz_data")." WHERE pollID='$pollID' ORDER BY voteID");
                        # compute the list of answer for the combo box
            while(list($optionText,$voteID) = $xoopsDB->fetchRow($result1))
                        {
                                $array_text[$voteID] = $optionText;
                        }

                       
                                    echo "&nbsp;&nbsp;<SELECT name=\"voteQuizz[$pollID][$i]\">";
                                        for($j = 1; $j <= sizeof($array_text); $j++)

                                        {
                                        if (!empty($array_text[$j]))

                                        {
                                    echo "<option name=\"rank\" value='$j' $sel>".$myts->MakeTboxData4Edit($array_text[$j])."</option>";
                                        }

                                        }
                            echo "</select> ".$myts->MakeTboxData4Edit($optionText)."";
                         
                }
                else
                {
        $ok=0;
        echo "<table border='0' class='even'>";
        for($i = 1; $i <= 12; $i++)
        {
            $result1 = $xoopsDB->query("SELECT optionText FROM ".$xoopsDB->prefix("myquiz_data")." WHERE (pollID='$pollID') AND (voteID='$i')");
            list($optionText) = $xoopsDB->fetchRow($result1);
            if($optionText != "")
            {
                if ($ok == 0) { $checked = "checked" ; } else { $checked = ""; }
                echo "<table border='0' class='even'><tr><td><input type=\"radio\" name=\"voteQuizz[$pollID]\" value=\"$i\" $checked> $optionText</td></tr>";
                $ok++;
                        }
        } # end poll question
                }
        $num++;
                echo "</table>";
    } # end poll loop
    

    echo "<table border='0' class='even'><tr><td align='center'><input type=\"submit\" class=button value='"._MYQUIZ_SUBMIT."'></td></tr></table></form>";


    # display the possibility for contributor to add question if needed
    if ($contrib == 1)
    {
    echo "<table border='0' class='even'><tr><td align='center'><a href=\"".XOOPS_URL."/modules/myquiz/index.php?qid=$qid&do=contrib\">[ "._MYQUIZ_CONTRIBUTE." ]</a></td></tr></table>";

    }
}

/*********************************************************/
/* Quizz Functions                                       */
/*********************************************************/

function QuizzVote ($qid,$voteQuizz,$logname,$adrs) {
        global $xoopsConfig, $xoopsUser, $xoopsDB, $optionSort, $xoopsTheme, $xoopsLogger;

$myts =& MyTextSanitizer::getInstance();

        # check if the mandatory infos aren't empty
        #print "logname = $logname email = $adrs<br>";
        if (empty($logname) or empty($adrs)) {
        echo "<center>"._MYQUIZ_MISSINGINFOS."</center>";
        return;
        }

    $result = $xoopsDB->query("select quizzTitle, nbscore, displayscore, displayresults, emailadmin, active, restrict_user, log_user, image, contrib, admemail from ".$xoopsDB->prefix("myquiz_admin")." where quizzID='$qid'");
    list($quizzTitle, $nbscore, $displayscore,$displayresults , $emailadmin, $active, $restrict_user, $log_user, $image, $contrib,$admemail) = $xoopsDB->fetchRow($result);

        # check if quizz is active
        if ($active == 0) {

                echo "<center>"._MYQUIZ_MUSTBEACTIVE."</center>";

                return;
        }


        # check if the user is logged (only one vote for the quizz)
        if ($log_user == 1) {
                # search in the database for a previous vote
            $result = $xoopsDB->query("SELECT username FROM ".$xoopsDB->prefix("myquiz_check")." WHERE (qid='$qid' AND username='$logname') OR (qid='$qid' AND email='$adrs')");
                list($username) = $xoopsDB->fetchRow($result);

                # already recorded ?
                if ($username == $logname or $email==$adrs) {
                      echo "<table border='0' class='even'><tr><td align='center'><strong>"._MYQUIZ_ALREADYVOTED."</strong></td></tr></table>";
                   return;
                }
        }

        # display a header if you have choose to show the score
        if ($displayscore == 1)
        {
        $commen = isset($comment) ? $myts->MakeTareaData4Show($comment) : "";
                if (!empty($image)) {
                    echo "<table border='0' class='even'><tr><td><b>"._MYQUIZ_RESULT." \"$quizzTitle\"</b><center><img src='".XOOPS_URL."/modules/myquiz/images/$image' border=0 alt=''></center></td></tr>";
                        echo "<tr><td>$commen</td></tr></table>";
                } else {
                    echo "<table border='0' class='even'><tr><td><b>"._MYQUIZ_RESULT." \"$quizzTitle\"</b></td></tr>";
                        echo "<tr><td>$commen</td></tr></table>";
                }
        }

        # update nb voter for current quizz
    $xoopsDB->query("UPDATE ".$xoopsDB->prefix("myquiz_admin")." SET voters=voters+1 WHERE quizzID='$qid'");
    $result = $xoopsDB->query("SELECT pollID, pollTitle, voters, answer, coef, good, bad, comment, image FROM ".$xoopsDB->prefix("myquiz_desc")." WHERE qid='$qid'");
        $score = 0;
                $num = 1;
    while(list($pollID, $pollTitle, $voters,$answer,$coef,$good,$bad,$comment,$image) = $xoopsDB->fetchRow($result))
    {
                # check if ordered answer needed
            if (ereg(",",$answer))
            {
                    $array_answer = split(',',$answer);

                    $ordered_answer = implode(",",$voteQuizz[$pollID]);
                    $ordered_answer = preg_replace("/,--|--,|--/","",$ordered_answer);

                        foreach (explode(',',$answer) as $val)
                        {
                        $xoopsDB->query("UPDATE ".$xoopsDB->prefix("myquiz_data")." SET optionCount=optionCount+1 WHERE (pollID='$pollID') AND (voteID='$val')");
                        }
                # update nb result for current answer (stat purpose)
            }
                else
                {
                $ordered_answer =  "";

        # update nb result for current answer (stat purpose)
        $xoopsDB->query("UPDATE ".$xoopsDB->prefix("myquiz_data")." SET optionCount=optionCount+1 WHERE (pollID='$pollID') AND (voteID='$voteQuizz[$pollID]')");
                }

        # update nb result for current question (stat purpose)
        $xoopsDB->query("UPDATE ".$xoopsDB->prefix("myquiz_desc")." SET voters=voters+1 WHERE pollID='$pollID'");

        # display results ?
        if ($displayresults == 1)
        {
                        # check if ordered answers to display results
                        if (!empty($ordered_answer))
                        {

            $result1 = $xoopsDB->query("SELECT optionText, voteID FROM ".$xoopsDB->prefix("myquiz_data")." WHERE pollID='$pollID' ORDER BY voteID");
                        # compute the list of answer for the combo box
            while(list($optionText,$voteID) = $xoopsDB->fetchRow($result1))
                        {
                                $array_text[$voteID] = $optionText;
                        }

                        # sort of the user answer
                        $optionText = "";
                        foreach ($voteQuizz[$pollID] as $val)
                        {
                                $optionText .= "$array_text[$val] ";
                        }
                        $answerText = "";
                        foreach (explode(',',$answer) as $val)
                        {
                                $answerText .= "$array_text[$val] ";
                        }

                        }
                        else
                        {
                $result1 = $xoopsDB->query("SELECT optionText FROM ".$xoopsDB->prefix("myquiz_data")." WHERE (pollID='$pollID') AND (voteID='$voteQuizz[$pollID]')");
                 list($optionText) = $xoopsDB->fetchRow($result1);
                 $result1 = $xoopsDB->query("SELECT optionText FROM ".$xoopsDB->prefix("myquiz_data")." WHERE (pollID='$pollID') AND (voteID='$answer')");
                 list($answerText) = $xoopsDB->fetchRow($result1);
                         }


             echo "<table border='0' class='even'><tr><td><B>$num</B> - "._MYQUIZ_FORTHEQUESTION." \"".$myts->MakeTboxData4Show($pollTitle)."\", ";
             echo ""._MYQUIZ_YOUHAVECHOOSE." \"".$myts->MakeTboxData4Show($optionText)."\" "._MYQUIZ_CORRECTANSWER." \"".$myts->MakeTboxData4Show($answerText)."\"</td></tr></table> ";

             $report = "<table border='0' class='even'><tr><td><B>$num</B> - "._MYQUIZ_FORTHEQUESTION." \"$".$myts->MakeTboxData4Show($pollTitle)."\", ";
             $report .= ""._MYQUIZ_YOUHAVECHOOSE." \"".$myts->MakeTboxData4Show($optionText)."\" "._MYQUIZ_CORRECTANSWER." \"".$myts->MakeTboxData4Show($answerText)."\"</td></tr></table>";

                        if ((empty($ordered_answer) and $voteQuizz[$pollID] == $answer) or
                                (!empty($ordered_answer) and $ordered_answer == $answer))
             {
                 if (!empty($good)) { echo "<table border='0' class='even'><tr><td><B>".$myts->MakeTareaData4Show($good)."</B></td></tr></table>"; }
             }
             else
             {
                 if (!empty ($bad)) {echo "<table border='0' class='even'><tr><td><B>".$myts->MakeTareaData4Show($bad)."</B></td></tr></table>"; }
             }
             
        }

        # check for the correct answer ?
                if ((empty($ordered_answer) and $voteQuizz[$pollID] == $answer) or
                        (!empty($ordered_answer) and $ordered_answer == $answer))
        {
           $score= $score + $coef;
        }

                if (empty($ordered_answer))
                {
                    if(empty($answers)) { $answers = "$voteQuizz[$pollID]|"; } else { $answers .= "$voteQuizz[$pollID]|"; }
                }
                else
                {
                    if(empty($answers)) { $answers = "$ordered_answer|"; } else { $answers .= "$ordered_answer|"; }
                }

                $num++;
    }

    # display the final score ?
    if ($displayscore == 1) {
            echo "<table border=0 class='even'><tr><td><strong>"._MYQUIZ_YOURSCORE.":</strong> $score</td></tr></table>";
            }

        # store the available infos if needed
        $ctime = time();
        if(!$xoopsDB->query("INSERT INTO ".$xoopsDB->prefix("myquiz_check")." VALUES (NULL,'$ctime','$logname','$adrs','$qid','$score','$answers')"))
        {
                echo $xoopsDB->errno(). ": ".$xoopsDB->error(). "<br>";
                return;
        }

        # show the score if needed
        if ($displayscore == 1) {
            echo "<table border='0' class='even'><tr><td><strong>"._MYQUIZ_LISTSCORE."</strong></td></tr>";
            $result = $xoopsDB->query("SELECT username, score FROM ".$xoopsDB->prefix("myquiz_check")." WHERE qid='$qid' ORDER BY score DESC,time DESC LIMIT $nbscore ");
                while(list($username,$res) = $xoopsDB->fetchRow($result))
                {
                        echo "<tr><td>$username: $res</td></tr>";
                }
        echo "</table>";
            }

    # bye bye !! :)

    echo "<table border=0 class='even'><tr><td align='center'><strong>"._MYQUIZ_THANKS."</strong></td></tr></table>";

    
}

/*********************************************************/
/* Quizz Functions                                       */
/*********************************************************/

function QuizzList ()
{
        global $xoopsDB, $xoopsConfig, $xoopsUser, $cid, $prefix, $xoopsTheme, $xoopsLogger;

$myts =& MyTextSanitizer::getInstance();


        if(!isset($cid))
        {

                echo "<table border=0 class='even'><tr><td><p></p><b>"._MYQUIZCATLIST."</b><br><br></td></td></table>";

            $result = $xoopsDB->query("select cid, name, comment, image from ".$xoopsDB->prefix("myquiz_categories"));
            while(list($cid, $name,$comment,$image) = $xoopsDB->fetchRow($result))
                {

                $reque =  "AND active ='1'";

        if ( $xoopsUser ) {
                if ( $xoopsUser->isAdmin() ) {
                $reque = "";
                }
                }

                                        # compute the number of Quizz by Category
                            $res = $xoopsDB->query("select COUNT(*) from ".$xoopsDB->prefix("myquiz_admin")." where cid='$cid' $reque");
                                        list($nb) = $xoopsDB->fetchRow($res);

                       
                                        if ($nb > 0)
                                        {
                        echo "<table border=0 class='even'><tr><td><a href=\"".XOOPS_URL."/modules/myquiz/index.php?cid=$cid\">  ".$myts->MakeTboxData4Show($name)."</a>";
                                        }
                                        else
                                        {
                        echo " ".$myts->MakeTboxData4Show($name)." ";
                                        }
                        if (!empty($comment)) { echo ":</td></tr><tr><td>".$myts->MakeTareaData4Show($comment)."</td></tr>"; }
                                                echo "<tr><td><br>($nb "._MYQUIZ_MYQUIZ.")";
                        echo "\n";
                                                # print the admin menu if needed
                                                if ($xoopsUser) {
                                                        $xoopsModule = XoopsModule::getByDirname("myquiz");
                                                        if ( $xoopsUser->isAdmin($xoopsModule->mid()) ) {
                                                                echo " [ <a href=\"".XOOPS_URL."/modules/myquiz/admin/index.php?act=QuizzModifyCategory&cid=$cid\">"._MYQUIZ_MODIFY."</a> | ";
                                                                       echo "<a href=\"".XOOPS_URL."/modules/myquiz/admin/index.php?act=QuizzDelCategory&cid=$cid\">"._MYQUIZ_DELETE."</a> ]</td></tr>";
                                                        }
                                                }
                        if (!empty($image))
                        {
                                echo "<tr><td><img src='".XOOPS_URL."/modules/myquiz/images/$image' border=0 alt='$comment'></td></tr>";
                        }
                        echo "</table>";
                }
                echo "";

        } else {

                    echo "<table border=0 class='even'><tr><td><b>"._MYQUIZ_LIST."</b><br><br></td></tr></table>";
            echo "";

            $result = $xoopsDB->query("select quizzId, quizzTitle, active, restrict_user, administrator,displayscore,nbscore from ".$xoopsDB->prefix("myquiz_admin")." where cid='$cid' order by timeStamp desc");
                while(list($qid, $quizzTitle,$active,$restrict_user,$administrator,$displayscore,$nbscore) = $xoopsDB->fetchRow($result)) {

                # display the inactive quizz if admin
                if ($xoopsUser) {
                        $xoopsModule = XoopsModule::getByDirname("myquiz");
                        if ( $xoopsUser->isAdmin($xoopsModule->mid()) or ($administrator != "" and $xoopsUser->getVar("uname", "E") == $administrator)) {
                        if ($active == 1) { $img = "green_dot.gif"; $alt = _MYQUIZ_ACTIVE;  } else { $img = "red_dot.gif"; $alt = _MYQUIZ_INACTIVE; }
                        echo "";
            echo "";
echo "<table border=0 class='even'><tr><td><a href=\"".XOOPS_URL."/modules/myquiz/index.php?qid=$qid\">$quizzTitle</a> <img src='".XOOPS_URL."/modules/myquiz/images/$img' alt='$alt' align='absmiddle'>";
echo "<br><br>[ <a href=\"".XOOPS_URL."/modules/myquiz/admin/index.php?act=QuizzViewScore&qid=$qid\">"._MYQUIZ_VIEWSCORE."</a> | ";
echo "<a href=\"".XOOPS_URL."/modules/myquiz/admin/index.php?act=QuizzRemoveScore&qid=$qid\">"._MYQUIZ_DELSCORE."</a> | ";
echo "<a href=\"".XOOPS_URL."/modules/myquiz/admin/index.php?act=QuizzViewStats&qid=$qid\">"._MYQUIZ_VIEWSTAT."</a> | ";
echo "<a href=\"".XOOPS_URL."/modules/myquiz/admin/index.php?act=QuizzModify&qid=$qid\">"._MYQUIZ_MODIFY."</a> | ";
echo "<a href=\"".XOOPS_URL."/modules/myquiz/admin/index.php?act=QuizzRemove&qid=$qid\">"._MYQUIZ_DELETE."</a> ]";
                                                # compute the number of Question in the Quizz
                                                    $res = $xoopsDB->query("select COUNT(*) from ".$xoopsDB->prefix("myquiz_desc")." where qid='$qid'");
                                                list($nb) = $xoopsDB->fetchRow($res);
                                                    echo " ($nb "._MYQUIZ_QUESTIONSNB.")</td></tr></table>";

                                                if ($displayscore) {
                                                            $ress = $xoopsDB->query("SELECT username, score FROM ".$xoopsDB->prefix("myquiz_check")." WHERE qid='$qid' ORDER BY score DESC,time DESC LIMIT $nbscore ");
                                                  
                                                        if ($xoopsDB->getRowsNum($ress)>0) {
                                                                echo "<table border=0 class='even'><tr><td><br><B>"._MYQUIZ_LISTSCORE.":</B></td></tr>";
                                                                    while(list($username,$res) = $xoopsDB->fetchRow($ress)) {
echo "<tr><td>$username: $res</td></tr><tr><td></td></tr>";
                                                                }
                                                                echo "</table>";
                                                        }
                                                           
                                                    }

                                } elseif ($active == 1) {
                                       
                                        echo "<table border=0 class='even'><tr><td><a href=\"".XOOPS_URL."/modules/myquiz/index.php?qid=$qid\">  ".$myts->MakeTboxData4Show($quizzTitle)." </a>";

                                        # compute the number of Question in the Quizz

            $res = $xoopsDB->query("select COUNT(*) from ".$xoopsDB->prefix("myquiz_desc")." where qid='$qid'");
                list($nb) = $xoopsDB->fetchRow($res);
                                            echo " ($nb "._MYQUIZ_QUESTIONSNB.")</td></tr></table>";
                                        if ($displayscore) {
                                                    $ress = $xoopsDB->query("SELECT username, score FROM ".$xoopsDB->prefix("myquiz_check")." WHERE qid='$qid' ORDER BY score DESC,time DESC LIMIT $nbscore ");
                                              
                                                if ($xoopsDB->getRowsNum($ress)>0) {
                                                        echo "<table border=0 class='even'><tr><td><br><B>"._MYQUIZ_LISTSCORE.":</B></td></tr>";
                                                            while(list($username,$res) = $xoopsDB->fetchRow($ress)) {
                                                                echo "<tr><td>$username: $res</td></tr><tr><td></td></tr>";
                                                        }
                                                        echo "</table>";
                                                }
                                                    
                                            }
                                                                }
                                
                                }
                }
                
        }

                if (!$xoopsUser) {
                            $result = $xoopsDB->query("select quizzId, quizzTitle, active, restrict_user, administrator,displayscore,nbscore from ".$xoopsDB->prefix("myquiz_admin")." where cid='$cid' order by timeStamp desc");
                while(list($qid, $quizzTitle,$active,$restrict_user,$administrator,$displayscore,$nbscore) = $xoopsDB->fetchRow($result)) {
                                if ($active == 1) {


                                       
                                        echo "<table border=0 class='even'><tr><td><a href=\"".XOOPS_URL."/modules/myquiz/index.php?qid=$qid\">  ".$myts->MakeTboxData4Show($quizzTitle)." </a>";

                                        # compute the number of Question in the Quizz

            $res = $xoopsDB->query("select COUNT(*) from ".$xoopsDB->prefix("myquiz_desc")." where qid='$qid'");
                list($nb) = $xoopsDB->fetchRow($res);
                                            echo " ($nb "._MYQUIZ_QUESTIONSNB.")</td></tr></table>";
                                        if ($displayscore) {
                                                    $ress = $xoopsDB->query("SELECT username, score FROM ".$xoopsDB->prefix("myquiz_check")." WHERE qid='$qid' ORDER BY score DESC,time DESC LIMIT $nbscore ");
                                                
                                                if ($xoopsDB->getRowsNum($ress)>0) {
                                                        echo "<table border=0 class='even'><tr><td><br><B>"._MYQUIZ_LISTSCORE.":</B></td></tr>";
                                                            while(list($username,$res) = $xoopsDB->fetchRow($ress)) {
                                                                echo "<tr><td>$username: $res</td></tr><tr><td></td></tr>";
                                                        }
                                                        echo "</table>";
                                                }
                                                   
                                            }
                                
                }
                }
                
        }

}

/*********************************************************/
/* Quizz Functions                                       */
/*********************************************************/

function ViewConditions ($qid)
{
      global $xoopsDB, $xoopsTheme, $xoopsLogger, $xoopsUser, $xoopsConfig;
        $result = $xoopsDB->query("select quizzTitle, conditions  from ".$xoopsDB->prefix("myquiz_admin")." where quizzID='$qid'");
        list($quizzTitle,$conditions) = $xoopsDB->fetchRow($result);
        
                echo "<table border=0 class='even'><tr><td><center><b>$quizzTitle</b><br><br><b>"._MYQUIZ_CONDITIONS.":<br><br></center></b>";
                      echo "$conditions<br><br><center><a href=\"javascript:history.go(-1)\">"._MYQUIZ_BACK."</a></center></tr></td></table>";
      
}
/*********************************************************/

if($xoopsConfig['startpage'] == "myquiz"){
        $xoopsOption['show_rblock'] =1;
        include(XOOPS_ROOT_PATH."/header.php");
        make_cblock();
}else{
        $xoopsOption['show_rblock'] =0;
        include(XOOPS_ROOT_PATH."/header.php");
}

       

include_once(XOOPS_ROOT_PATH."/class/xoopsmodule.php");
if ($xoopsUser) {
        $xoopsModule = XoopsModule::getByDirname("myquiz");
        if ( $xoopsUser->isAdmin($xoopsModule->mid()) ) {
         
                    echo "<table border=0 class='outer'><tr><td align='center'><a href=\"".XOOPS_URL."/modules/myquiz/admin/index.php?act=QuizzAdmin\"> [ "._MYQUIZ_ADMIN." ]</a><br><br></td></tr><td>";
        

        }
        
}

else {
        echo "<table border=0 class='outer'><td>";
        }

if(!isset($qid)) {
        QuizzList();
} else if (!isset($do)) {
        QuizzShow($qid);
} else {
        switch($do)
        {
        case "vote":
              quizzVote($qid,$voteQuizz,$logname,$adrs);
              break;
        case "contrib":
              QuizzContribute($qid);
              break;

        case "addContributeQuizzQuestion":
              addContributeQuizzQuestion($qid);
              break;
        case "viewConditions":
                      ViewConditions($qid);
                      break;
        default:
              QuizzShow($qid);
              break;
        }
}




echo "<table border=0 class='odd'><tr><td>";
echo _MYQUIZ_COPY;
echo "</td></tr></table></td></tr></table>";


include(XOOPS_ROOT_PATH."/footer.php");
?>