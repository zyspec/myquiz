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

if (!defined('XOOPS_ROOT_PATH')){ exit(); }
 
/*********************************************************/
/* Quizz Functions                                       */
/*********************************************************/
function bb2html($text) 
{ 
  $bbcode = array("<", ">", 
                "[list]", "[*]", "[/list]", 
                "[img]", "[/img]", 
                "[b]", "[/b]", 
                "[u]", "[/u]", 
                "[i]", "[/i]", 
                "[color=", "[/color]", 
                "[size=", "[/size]", 
                "[code]", "[/code]", 
                "[quote]", "[/quote]", 
                "]"); 
  $htmlcode = array("<", ">", 
                "<ul>", "<li>", "</ul>", 
                '<img src=\''.XOOPS_URL.'/modules/myquiz/images/', '\' />', 
                "<b>", "</b>", 
                "<u>", "</u>", 
                "<i>", "</i>", 
                "<span style='color:", "</span>", 
                "<span style='font-size:", "</span>", 
                "<code>", "</code>", 
                "<table width=100% bgcolor=black><tr><td bgcolor=white>", "</td></tr></table>", 
                "'>"); 
  $newtext = str_replace($bbcode, $htmlcode, $text); 
  $newtext = nl2br($newtext); //second pass 
  return $newtext; 
} 

/*********************************************************/
/* Quizz Functions                                       */
/*********************************************************/

function addContributeQuizzQuestion ($qid) {
   global $xoopsDB, $xoopsConfig, $question, $optionText, $qid, $answer, $coef, $good, $bad, $comment, $optionSort, $image, $xoopsUser, $xoopsTheme, $xoopsLogger;

   $myts =& MyTextSanitizer::getInstance();
   $good = $_POST['good'];
   $good = $myts->oopsNl2Br($myts->makeTareaData4Save($good));
   $bad = $_POST['bad'];
   $bad = $myts->oopsNl2Br($myts->makeTareaData4Save($bad));
   $comment = $_POST['comment'];
   $comment = $myts->oopsNl2Br($myts->makeTareaData4Save($comment));
   $question = $_POST['question'];
   $question = $myts->oopsNl2Br($myts->makeTboxData4Save($question));
   $coef = $_POST['coef'];
   $answer = $_POST['answer'];

   $timeStamp = time();

   $ordered_answer = implode(",",$optionSort);
   $ordered_answer = preg_replace("/,--|--,|--/","",$ordered_answer);


   # check if sorted answer is needed
   $optionText = $_POST['optionText'];
   if (!empty($ordered_answer)){
	   # check if all availaible answers are sorted
       for($i = 1; $i <= sizeof($optionText); $i++){
		   if ((!empty($optionText[$i]) AND $optionSort[$i] == "--") OR (empty($optionText[$i]) AND $optionSort[$i] != "--")){
			   echo "<center>"._MYQUIZ_INCORRECTORDER."</center>";
			   exit;
		   }
	  }
      # change the answer the the ordered answer
      $answer = $ordered_answer;
    }

    if(!$xoopsDB->query("INSERT INTO ".$xoopsDB->prefix("myquiz_descontrib")." VALUES (NULL, '$question', '$timeStamp', 0, '$qid','$answer','$coef','$good','$bad','$comment','')")){
		echo $xoopsDB->errno(). ": ".$xoopsDB->error(). "<br />";
        return;
    }
    $array = $xoopsDB->fetchArray($xoopsDB->query("SELECT pollID,timeStamp FROM ".$xoopsDB->prefix("myquiz_descontrib")." WHERE timeStamp='$timeStamp'"));
    $id = $array['pollID'];
    $t = $array['timeStamp'];
    for($i = 1; $i <= sizeof($optionText); $i++){
		if($optionText[$i] != ""){
			$optionText[$i] = $myts->MakeTboxData4Save($optionText[$i]);
		}
        if(!$xoopsDB->query("INSERT INTO ".$xoopsDB->prefix("myquiz_datacontrib")." (pollID, optionText, optionCount, voteID) VALUES ($id, '$optionText[$i]', 0, $i)")){
			echo $xoopsDB->errno(). ": ".$xoopsDB->error(). "<br />";
			return;
		}
    }

        # update the image name
        if (!empty($image) and $image != "none"){
			if (!copy($image, XOOPS_ROOT_PATH."/modules/myquiz/images/${t}.gif")){
				echo "Hata ! Kopyalama basarisiz l\'image $image <br />";
			}
			if(!$xoopsDB->query("UPDATE ".$xoopsDB->prefix("myquiz_descontrib")." SET image='${t}.gif' WHERE pollID='$id'")){
				echo $xoopsDB->errno(). ": ".$xoopsDB->error(). "<br />";
				return;
            }

        }
    
    echo "<table border=0 class='even'><tr><td>"._MYQUIZ_CONTRIBTHANKS."<br /><br /><a href=\"".XOOPS_URL."/modules/myquiz/index.php\">"._MYQUIZ_BACKTOINDEX."</a></td></tr></table>";
   
}
/*********************************************************/
/* Quizz Functions                                       */
/*********************************************************/

function QuizzContribute ($qid){
	
	global $xoopsUser, $xoopsConfig, $qid, $xoopsDB, $xoopsTheme, $xoopsLogger;

    # Si utilisateur pas enregistré
	if ( !$xoopsUser ) {
		redirect_header("index.php",2,_MYQUIZ_ADDQUESTIONSORRY);
		exit();
	}


    # Utilisateur courant
    $logname = $xoopsUser->getVar("uname", "E");    
    echo "<table border=0 class='even'><tr><td>";
    echo "<form enctype=\"multipart/form-data\" action='".$xoopsConfig["xoops_url"]."/modules/myquiz/index.php' method='post'>";
    echo "<input type='hidden' name='do' value='addContributeQuizzQuestion'>";
    echo "<input type='hidden' name='qid' value=\"$qid\">";
    echo "<table>";
	echo "<tr><td colspan='3'><strong>"._MYQUIZ_QUESTIONTITLE.":</strong> <input type=\"text\" name=\"question\" size=\"70\" maxlength=\"255\" value=\"???\"><br /><br /></td></tr>";
    echo "<tr><td colspan='3'><strong>"._MYQUIZ_COEF.":</strong> <input type='text' name='coef' value='1' size=3><br /><br /></td></tr>";
	echo "</table>";
    
    for($i = 1; $i <= 5; $i++){
		if ($i == 1) {
			$checked = "checked";
		}
		else {
			$checked = "";
		}
		echo "<table>";
		echo "<tr><td width=100px>"._MYQUIZ_ANSWER." $i:</td>";
		echo "<td><input type=\"text\" name=\"optionText[$i]\" size=\"70\" maxlength=\"255\">";
		$optionTex = isset($optionText) ? $optionText : "";
		echo " <input type=\"radio\" name=\"answer\" value=\"$i\" $checked> $optionTex <-"._MYQUIZ_ANSWER."</td></tr>";
		echo "</table>";
		}
		echo "<br /><br />".""."<strong>"._MYQUIZ_COMMENT." (*)</strong><br />"."<TEXTAREA cols=\"50\" rows=\"4\" name=\"comment\"></textarea><br /><br />"."<strong>"._MYQUIZ_IFBADANSWER." (*)</strong><br />"."<TEXTAREA cols=\"50\" rows=\"4\" name=\"bad\"></textarea><br /><br />"."<strong>"._MYQUIZ_IFGOODANSWER." (*)</strong><br />"."<TEXTAREA cols=\"50\" rows=\"4\" name=\"good\"></textarea>"."<br /><br /><input type=\"submit\" class=button value=\""._MYQUIZ_ADDQUESTION."\">";
		echo "</form><br />";
		echo "*: "._MYQUIZ_HELPOPTION."<br /><br />";
		echo "</table>";
	}

/*********************************************************/
/* Quizz Functions                                       */
/*********************************************************/

function QuizzShow ($qid) {
        global $xoopsUser, $xoopsDB, $xoopsConfig, $xoopsTheme, $xoopsLogger;

    $myts =& MyTextSanitizer::getInstance();

    # Vérifie l'autorisation
    $result = $xoopsDB->query("SELECT quizzTitle, comment , active, restrict_user, log_user, image, contrib, expire , displayscore, tektek, nbscore, conditions  FROM ".$xoopsDB->prefix("myquiz_admin")." where quizzID='$qid'");
    list($quizzTitle,$comment,$active, $restrict_user, $log_user,$image,$contrib,$expire,$displayscore,$tektek,$nbscore,$conditions) = $xoopsDB->fetchRow($result);

        # Vérifie si le quizz est actif
        if ($active == 0) {
                if (!$xoopsUser) {
                        echo "<center>"._MYQUIZ_MUSTBEACTIVE."</center>";
                        return;
                }
				else {
					$xoopsModule = XoopsModule::getByDirname("myquiz");
                    if(!$xoopsUser->isAdmin($xoopsModule->mid())) {
						echo "<center>"._MYQUIZ_MUSTBEACTIVE."</center>";
                        return;
                    }
                }
         }


        # Vérifie d'abord s'il existe une date limite (pas de xx)
        if (!ereg("xx",$expire)) {
			$Cann = strftime("%Y",strtotime($expire));
			$Cmoi = strftime("%m",strtotime($expire));
			$Cjour = strftime("%d",strtotime($expire));
			$Cheur = strftime("%H",strtotime($expire));
			$Cmin = strftime("%M",strtotime($expire));

            $today = getdate();
			$day = $today['mday'];
			if ($day < 10){ $day = "$day"; }
			$month = $today['mon'];
			$year = $today['year'];
			$hour = $today['hours'];
			$min = $today['minutes'];
			$sec = $today['seconds'];

            # Vérifie le jour
            # if (($date[1] <= $year) AND ($Cmoi <= $month) AND ($Cjour <= $day) AND ($Cheur <= $hour) AND ($Cmin <= $min)) {
            if (($Cann <= $year) AND ($Cmoi <= $month) AND ($Cjour <= $day) AND ($Cheur <= $hour) AND ($Cmin <= $min)) {
				$expired = 1;
				echo "<table border='0' class='even'><tr><td align='center'><strong>"._MYQUIZ_HASEXPIRED."</strong></td></tr></table>";
                return;
            }

            # Affiche le score si nécessaire
             if ($displayscore == 1 AND $expired == 1){
				 echo ""._MYQUIZ_LISTSCORE."<br />";
				 $result = $xoopsDB->query("SELECT username, score FROM ".$xoopsDB->query("myquiz_check")." WHERE qid=!'$qid' ORDER BY score DESC,time DESC LIMIT $nbscore "); while(list($username,$res) = $xoopsDB->fetchRow($result)){
					 echo "$username: $res<br />";
				 }
                            

                # bye bye !! :)
               
                echo "<table border=0 class='even'><tr><td>"._MYQUIZ_THANKS."<br /></td></tr></table>";
             
                }
                if ($expired == 1) {
                return;
                }
        }

        # Récupère les info de connexion des utilisateurs enregistrés
    if ($xoopsUser) {
                    $logname = $xoopsUser->getVar("uname", "E");
                        $adrs = $xoopsUser->getVar("email", "E");
                    }

        # Vérifie si l'utilisateur est connecté (un seul vote pour le quizz)
        if ($log_user == 1) {
                # Recherche dans la base de données un éventuel vote précédent
                $result = $xoopsDB->query("SELECT username FROM ".$xoopsDB->prefix("myquiz_check")." WHERE  (qid='$qid' AND username='$logname') OR (qid='$qid' AND email='$adrs')");
                if($xoopsDB->getRowsNum($result) > 0) {
                        echo "<table border='0' class='even'><tr><td align='center'><strong style='color:#DD0000'>"._MYQUIZ_ALREADYVOTED."</strong></td></tr></table>";
                        return;
                }
        }
        if ($xoopsUser) {
                $xoopsModule = XoopsModule::getByDirname("myquiz");
                if ( $xoopsUser->isAdmin($xoopsModule->mid()) ) {
					$menu = "[ <a href=\"".XOOPS_URL."/modules/myquiz/admin/index.php?acti=QuizzViewScore&qidi=$qid\">"._MYQUIZ_VIEWSCORE."</a> | ";                         $menu .= "<a href=\"".XOOPS_URL."/modules/myquiz/admin/index.php?acti=QuizzRemoveScore&qidi=$qid\">"._MYQUIZ_DELSCORE."</a> | ";                         $menu .= "<a href=\"".XOOPS_URL."/modules/myquiz/admin/index.php?acti=QuizzViewStats&qidi=$qid\">"._MYQUIZ_VIEWSTAT."</a> | ";                           $menu .= "<a href=\"".XOOPS_URL."/modules/myquiz/admin/index.php?acti=QuizzModify&qidi=$qid\">"._MYQUIZ_MODIFY."</a> | ";                              $menu .= "<a href=\"".XOOPS_URL."/modules/myquiz/admin/index.php?acti=QuizzRemove&qidi=$qid\">"._MYQUIZ_DELETE."</a> ]";
                }
        }

        if (!empty($image))
        {
            echo "<table border='0' class='even'><tr><td><b>$quizzTitle</b> $menu<center><img src='".XOOPS_URL."/modules/myquiz/images/$image' border='0' alt='' /></center></td></tr></table>";
			echo "<table border='0' class='even'><tr><td>".$myts->MakeTareaData4Show($comment)."</td></tr></table>";
        }
        else{
            $men=isset($menu) ? $menu : "";
            echo "<table border='0' class='even'><tr><td><b>$quizzTitle</b> $men</td></tr></table>";
			echo "<table border='0' class='even'><tr><td>"._MYQUIZ_COMMENT.": ".$myts->MakeTareaData4Show($comment)."</td></tr></table>";
        }

if ($tektek == 0) {
            if ($conditions != 0) {
			
			//zaman sayaci
echo "<script>
var sec = 00;   // set the seconds
var min =".$myts->MakeTareaData4Show($conditions) ;   // set the minutes
echo "
function countDown() {
  sec--;
  if (sec == -01) {
    sec = 59;
    min = min - 1;
  } else {
   min = min;
  }
if (sec<=9) { sec = '0' + sec; }
  time = (min<=9 ? '0' + min : min) + '"._MYQUIZ_CDOWNM."' + sec + '"._MYQUIZ_CDOWNS." ';
if (document.getElementById) { document.getElementById('theTime').innerHTML = time; }
  SD=window.setTimeout('countDown();', 1000);
if (min == '00' && sec == '00') { sec = '00'; window.clearTimeout(SD);
document.yolla.submit(); 
 }
}

function addLoadEvent(func) {
  var oldonload = window.onload;
  if (typeof window.onload != 'function') {
    window.onload = func;
  } else {
    window.onload = function() {
      if (oldonload) {
        oldonload();
      }
      func();
    }
  }
}

addLoadEvent(function() {
  countDown();
});
</script>

<style>
.timeClass {
  font-family:arial,verdana,helvetica,sans-serif;
  font-weight:bold;
  font-size:10pt;
  color:#000000;
}
</style>";
echo "<table border='0' class='even'><tr><td>"._MYQUIZ_ZAMAN.": ".$myts->MakeTareaData4Show($conditions).""._MYQUIZ_CDOWNM."</td></tr></table>";
echo "<table bgcolor='#fafafa' border='1' width='100%'>
 <tr><td width='100%' align='center'><span id='theTime' class='timeClass'></span></td></tr>
</table>";
                                
			}


        # Afficher le formulaire de quizz
		echo "<form name='yolla' action=\"".XOOPS_URL."/modules/myquiz/index.php\" method=\"post\">";
		echo "<input type=\"hidden\" name=\"qid\" value=\"$qid\">";
		echo "<input type=\"hidden\" name=\"do\" value=\"vote\">";
        if ($xoopsUser){
			echo "<input type=\"hidden\" name=\"logname\" value=\"$logname\">";
            echo "<input type=\"hidden\" name=\"adrs\" value=\"$adrs\">";
			       }
        else{
			echo "<table border='0' class='even'><tr><td align='center'>"._MYQUIZ_LOGNAME.": <input type='text' name='logname'> ";
			echo ""._MYQUIZ_EMAIL.": <input type='text' name='adrs'></td></tr></table>";
			echo "<P>";
        }

        $num = 1;
		$result = $xoopsDB->query("SELECT pollID, pollTitle, voters, comment, answer, image FROM ".$xoopsDB->prefix("myquiz_desc")." WHERE qid='$qid'");
        while(list($pollID, $pollTitle, $voters, $comment,$answer,$image) = $xoopsDB->fetchRow($result)){
			echo "";
			if (!empty($image)){
				echo "<table border='0' class='even'><tr><td><center><img src='".XOOPS_URL."/modules/myquiz/images/$image' border='0' style='align:absmiddle' /></center>"."<b>"._MYQUIZ_QUESTIONTITLE." $num : ".bb2html($myts->MakeTboxData4Show($pollTitle))."</b></td></tr></table>";
            }
            else{
				echo "<table border='0' class='even'><tr><td><b>"._MYQUIZ_QUESTIONTITLE." $num : ".bb2html($myts->MakeTboxData4Show($pollTitle))."</b></td></tr></table>";
            }

            if ($xoopsUser){
				$xoopsModule = XoopsModule::getByDirname("myquiz");
				if ( $xoopsUser->isAdmin($xoopsModule->mid()) ) {
					echo "<table border='0' class='even'><tr><td>[ <a href=\"".XOOPS_URL."/modules/myquiz/admin/index.php?acti=QuizzModifyQuestion&pidi=$pollID&qidi=$qid\">"._MYQUIZ_MODIFY."</a> | ";
					echo "<a href=\"".XOOPS_URL."/modules/myquiz/admin/index.php?acti=QuizzDelQuestion&pidi=$pollID&qidi=$qid\">"._MYQUIZ_DELETE."</a> ]</td></tr></table>";
                }
            }

            if (!empty($comment)){
				echo "<table border='0' class='even'><tr><td>&nbsp;&nbsp;<i>".$myts->MakeTareaData4Show($comment)."</i></td></tr></table>";
			}

            echo "<input type=\"hidden\" name=\"pollID\" value=\"$pollID\">";
            # ordered answer needed ?
            if (ereg(",",$answer)){
				$result1 = $xoopsDB->query("SELECT optionText, voteID FROM ".$xoopsDB->prefix("myquiz_data")." WHERE pollID='$pollID' ORDER BY voteID");
                # compute the list of answer for the combo box
				while(list($optionText,$voteID) = $xoopsDB->fetchRow($result1)){
					$array_text[$voteID] = $optionText;
				}
                echo "&nbsp;&nbsp;<SELECT name=\"voteQuizz[$pollID][$i]\">";
				for($j = 1; $j <= sizeof($array_text); $j++){
					if (!empty($array_text[$j])){
						echo "<option name=\"rank\" value='$j' $sel>".$myts->MakeTboxData4Edit($array_text[$j])."</option>";
					}
				}
                echo "</select> ".$myts->MakeTboxData4Edit($optionText)."";
				#echo '<br/>';
            }
            else{
				$ok=0;
				echo "<table border='0' class='even'>";
				for($i = 1; $i <= 5; $i++){
					$result1 = $xoopsDB->query("SELECT optionText FROM ".$xoopsDB->prefix("myquiz_data")." WHERE (pollID='$pollID') AND (voteID='$i')");
					list($optionText) = $xoopsDB->fetchRow($result1);
					if($optionText != ""){
						# if ($ok == 0) { $checked = "checked" ; } else { $checked = ""; } <- Version originale précochée sur 1
						if ($ok == 0) { $checked = "" ; } else { $checked = ""; }
						echo "<table border='0' class='even'><tr><td><input type=\"radio\" name=\"voteQuizz[$pollID]\" value=\"$i\" $checked>".bb2html($optionText)."</td></tr>";       $ok++;
					}
                } # end poll question
            }
            $num++;
            echo "</table>";
	        echo '<br/>';
  } # end poll loop

    echo "<table border='0' class='even'><tr><td align='center'><input type=\"submit\" class=button value='"._MYQUIZ_SUBMIT."'></td></tr></table></form>";


    # display the possibility for contributor to add question if needed
    if ($contrib == 1){
		echo "<table border='0' class='even'><tr><td align='center'><a href=\"".XOOPS_URL."/modules/myquiz/index.php?qidi=$qid&doi=contrib\">[ "._MYQUIZ_CONTRIBUTE." ]</a></td></tr></table>";
	}
	}
	else {
include ("tek_question.php");
}
}

/*********************************************************/
/* Quizz Functions                                       */
/*********************************************************/

## 2009 v4.0
    function sendEmail($user ,$score ,$qid, $admemail)
    {	
	    global $xoopsConfig;
        
        if (!is_object($user)) {
            $user =& $GLOBALS["xoopsUser"];
        }
        $msg  = _MYQUIZ_WHOINFORM."\n\n";
        $msg .= sprintf(_MYQUIZ_WHOSOLVED, $user->getVar("uname"));
        $msg .= "\n";
		$msg .= _MYQUIZ_RESULT." = ".$score."\n\n";
		$msg .=  _MYQUIZ_SEE. ": ". XOOPS_URL . "/modules//myquiz/index.php?qidi=".$qid."\n";
		$xoopsMailer =& getMailer();
        $xoopsMailer->useMail();
        $xoopsMailer->useMail();
        $xoopsMailer->setToEmails($xoopsConfig['adminmail'],'$admemail');
        $xoopsMailer->setFromEmail($xoopsConfig['adminmail']);
        $xoopsMailer->setFromName($xoopsConfig['sitename']);
        $xoopsMailer->setSubject(_MYQUIZ_RESULT);
        $xoopsMailer->setBody($msg);
        return $xoopsMailer->send();
    }


function QuizzVote ($qid,$voteQuizz,$logname,$adrs) {

        global $xoopsConfig,$xoopsTpl, $xoopsOption, $xoopsUser, $xoopsDB, $optionSort, $xoopsTheme, $myts, $xoopsModuleConfig, $xoopsModule, $xoopsLogger;
        $myts =& MyTextSanitizer::getInstance();

        # check if the mandatory infos aren't empty
        #print "logname = $logname email = $adrs<br />";
        if (empty($logname) OR empty($adrs)) {
			echo "<center>"._MYQUIZ_MISSINGINFOS."</center>";
        return;
        }
		
		$result = $xoopsDB->query("SELECT quizzTitle, nbscore, displayscore, displayresults, tektek, active, restrict_user, log_user, image, contrib, emailadmin, admemail FROM ".$xoopsDB->prefix("myquiz_admin")." WHERE quizzID='$qid'");
		list($quizzTitle, $nbscore, $displayscore,$displayresults , $tektek, $active, $restrict_user, $log_user, $image, $contrib, $emailadmin, $admemail) = $xoopsDB->fetchRow($result);

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
        if ($displayscore == 1){
			$commen = isset($comment) ? $myts->MakeTareaData4Show($comment) : "";
			if (!empty($image)) {
				echo "<table border='0' class='even'><tr><td><b>"._MYQUIZ_RESULT." \"$quizzTitle\"</b><center><img src='".XOOPS_URL."/modules/myquiz/images/$image' border='0' alt='' /></center></td></tr>";
				echo "<tr><td  width=100% >$commen</td>";
			}
			else {
				echo "<table border='0' class='even'><tr><td><b>"._MYQUIZ_RESULT." \"$quizzTitle\"</b></td></tr>";
				echo "<tr><td  width=100% >$commen</td>";
			}
	
$module_handler = xoops_gethandler("module"); 
$my_module = $module_handler->getByDirname("myquiz"); 
$comment_handler = xoops_gethandler("comment"); 
$criteria = $my_module->getVar('mid'); 

$say = "SELECT  COUNT(*)  FROM ".$xoopsDB->prefix("xoopscomments")." WHERE com_modid ='$criteria' AND com_itemid ='$qid'"; 
 
$resultsay = mysql_query($say) or die(mysql_error());
while($row = mysql_fetch_array($resultsay)){

$yorumlar = _MYQUIZ_QUIZYORUMS." (". $row['COUNT(*)'].")";
	echo "<td><form method='post' action='comment_s.php?qidir=$qid'>";
	echo "<input type='hidden' align='right' name='qid' value='$qid'><input type=\"submit\" class=button value='$yorumlar'></form></td></tr></table><br>";
	
	}
        }

        # update nb voter for current quizz
		$xoopsDB->query("UPDATE ".$xoopsDB->prefix("myquiz_admin")." SET voters=voters+1 WHERE quizzID='$qid'");
		$result = $xoopsDB->query("SELECT pollID, pollTitle, voters, answer, coef, good, bad, comment, image FROM ".$xoopsDB->prefix("myquiz_desc")." WHERE qid='$qid'");
        $score = 0;
		$num = 1;
		while(list($pollID, $pollTitle, $voters,$answer,$coef,$good,$bad,$comment,$image) = $xoopsDB->fetchRow($result)){
			# check if ordered answer needed
            if (ereg(",",$answer)){
				$array_answer = split(',',$answer);
				$ordered_answer = implode(",",$voteQuizz[$pollID]);
				$ordered_answer = preg_replace("/,--|--,|--/","",$ordered_answer);
				foreach (explode(',',$answer) as $val){
					$xoopsDB->query("UPDATE ".$xoopsDB->prefix("myquiz_data")." SET optionCount=optionCount+1 WHERE (pollID='$pollID') AND (voteID='$val')");
				}
                # update nb result for current answer (stat purpose)
            }
            else{
				$ordered_answer =  "";
				# update nb result for current answer (stat purpose)
				$xoopsDB->query("UPDATE ".$xoopsDB->prefix("myquiz_data")." SET optionCount=optionCount+1 WHERE (pollID='$pollID') AND (voteID='$voteQuizz[$pollID]')");
			}

            # update nb result for current question (stat purpose)
            $xoopsDB->query("UPDATE ".$xoopsDB->prefix("myquiz_desc")." SET voters=voters+1 WHERE pollID='$pollID'");

            # display results ?
            if ($displayresults == 1){
				# check if ordered answers to display results
				if (!empty($ordered_answer)){
					$result1 = $xoopsDB->query("SELECT optionText, voteID FROM ".$xoopsDB->prefix("myquiz_data")." WHERE pollID='$pollID' ORDER BY voteID");
                    # compute the list of answer for the combo box
					while(list($optionText,$voteID) = $xoopsDB->fetchRow($result1)){
						$array_text[$voteID] = $optionText;
					}
					# sort of the user answer
					$optionText = "";
					foreach ($voteQuizz[$pollID] as $val){
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
						 
             echo "<table border='1' class='even'><tr><td><B>&nbsp <U>$num</U> -"._MYQUIZ_FORTHEQUESTION." \"</B>";
			 echo "".bb2html($myts->MakeTboxData4Show($pollTitle))."\",<br />";
			 
			 if ((empty($ordered_answer) and $voteQuizz[$pollID] == $answer) or
                        (!empty($ordered_answer) and $ordered_answer == $answer))
        {
			 echo "&nbsp<img src='".XOOPS_URL."/modules/myquiz/images/correct.png' border='0' alt='"._MYQUIZ_DOGRU." ' />";
			 echo "<b>"._MYQUIZ_YOUHAVECHOOSE." ::\"</b>".bb2html($myts->MakeTboxData4Show($optionText))."\"<br /></td></tr>";
        }
		else
		{
				 echo "<img src='".XOOPS_URL."/modules/myquiz/images/wrong.png' border='0' alt='"._MYQUIZ_YANLIS." ' /><b>"._MYQUIZ_YOUHAVECHOOSE." ::\"</b>".bb2html($myts->MakeTboxData4Show($optionText))."\"<br />&nbsp<img src='".XOOPS_URL."/modules/myquiz/images/dgru.gif' border='0' />"._MYQUIZ_DRCVP." \"".bb2html($myts->MakeTboxData4Show($answerText))."\"</td></tr>";}




             $report = "<table border='0' class='even'><tr><td><B>$num</B> - "._MYQUIZ_FORTHEQUESTION." \"$".$myts->MakeTboxData4Show($pollTitle)."\"</td></tr></table>, ";
             $report .= ""._MYQUIZ_YOUHAVECHOOSE." \"".$myts->MakeTboxData4Show($optionText)."\" "._MYQUIZ_CORRECTANSWER." \"".$myts->MakeTboxData4Show($answerText)."\"</td></tr></table>";

                        if ((empty($ordered_answer) and $voteQuizz[$pollID] == $answer) or
                                (!empty($ordered_answer) and $ordered_answer == $answer))
             {
                 if (!empty($good)) { echo "<tr><td><B>".$myts->MakeTareaData4Show($good)."</B></td></tr>"; }
             }
             else
             {
                 if (!empty ($bad)) {echo "<tr><td><B>".$myts->MakeTareaData4Show($bad)."</B></td></tr>"; }
             }
           
        }
echo "</table><br />";
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


    # store the available infos if needed
    $ctime = time();
        if(!$xoopsDB->query("INSERT INTO ".$xoopsDB->prefix("myquiz_check")." VALUES (NULL,'$ctime','$logname','$adrs','$qid','$score','$answers')"))
        {
                echo $xoopsDB->errno(). ": ".$xoopsDB->error(). "<br />";
                return;
        }

    # show the score if needed
    if ($displayscore == 1) {
		echo "<table border='0' class='even'>";
		echo "<tr><td><center><font size='4px'><b>"._MYQUIZ_YOURSCORE.":</b></font><SPAN style=\"font-weight:bold;font-size:30px;color:#000099\">$score</SPAN></center><br /><strong>"._MYQUIZ_LISTSCORE."</strong></td></tr>";
		$result = $xoopsDB->query("SELECT username, score FROM ".$xoopsDB->prefix("myquiz_check")." WHERE qid='$qid' ORDER BY score DESC,time DESC LIMIT $nbscore ");        while(list($username,$res) = $xoopsDB->fetchRow($result)){
			echo "<tr><td>$username: $res</td></tr>";
		}
        echo "</table>";
	}
	 if ($contrib == 1){
		echo "<table border='0' class='even'><tr><td align='center'><a href=\"".XOOPS_URL."/modules/myquiz/index.php?qidi=$qid&doi=contrib\">[ "._MYQUIZ_CONTRIBUTE." ]</a></td></tr></table>";
	}
	if ($emailadmin == 1) {
	sendEmail($user ,$score ,$qid, $admemail);
	}

    # bye bye !! :)
	  echo "<table border=0 class='even'><tr><td align='center'><strong>"._MYQUIZ_THANKS."</strong></td></tr></table>";

}

/*********************************************************/
/* Quizz Functions                                       */
/*********************************************************/
function listele($ustid,$derinlik) { 
 global $xoopsConfig,$xoopsDB,$myts, $xoopsUser, $xoopsTheme, $xoopsLogger;
    $derinlik = $derinlik + 1; 
    $s = mysql_query("select cid, ustid, name, comment, image from ".$xoopsDB->prefix("myquiz_categories")." where ustid = '$ustid';"); 
	    while ( $c = mysql_fetch_row($s)) { 
		 $reque =  "AND active ='1'";
				if ( $xoopsUser ) {
					if ($xoopsUser->isAdmin()){$reque = "";}
                }		
		                $res = $xoopsDB->query("SELECT COUNT(*) FROM ".$xoopsDB->prefix("myquiz_admin")." WHERE cid=".$c[0]." $reque");
                list($nb) = $xoopsDB->fetchRow($res);	
	if ( $derinlik == 1) { 	
	echo "<tr class='outer'><th align='center'>";	
	if (!empty($c[4])) {echo "<a href=\"".XOOPS_URL."/modules/myquiz/index.php?cidi=".$c[0]."\"><img src='".XOOPS_URL."/modules/myquiz/images/".$c[4]."' border='0' alt='".$c[3]."' /></a>";}
	echo "</th><th width='100%'><a href=\"".XOOPS_URL."/modules/myquiz/index.php?cidi=".$c[0]."\"><img src='".XOOPS_URL."/modules/myquiz/images/cvp.png' border='0' /><b>".$c[2]."</b></a><br>".$c[3]."</th><th align='center'> ( $nb )</th></tr>";	
	}
	else {	
	echo "<tr class='even'><td align='center'>";
	if (!empty($c[4])) { echo "<a href=\"".XOOPS_URL."/modules/myquiz/index.php?cidi=".$c[0]."\"><img src='".XOOPS_URL."/modules/myquiz/images/".$c[4]."' border='0' alt='".$c[3]."' /></a>"; }
	echo "</td><td>";
        echo str_repeat("-",$derinlik) . " &nbsp<a href=\"".XOOPS_URL."/modules/myquiz/index.php?cidi=".$c[0]."\"><b>" .$c[2] . "</b></a><br>".$c[3]; 		
	echo "</td><td align='center'> <span style='color:#CC0000'>( $nb )</span></td>";	
 
 echo "</tr>";
 		}		
						
        $s2 = mysql_query("select count(*) from ".$xoopsDB->prefix("myquiz_categories")." where ustid = '$c[0]';"); 
        $c2 = mysql_fetch_row($s2); 
        if ( $c2[0] > 0 ) { 
            listele($c[0],$derinlik); 
        } 
    } 	
} 

function QuizzList (){
        global $xoopsDB, $xoopsConfig, $xoopsUser, $cid, $prefix, $xoopsTheme, $xoopsLogger;
        $myts =& MyTextSanitizer::getInstance();		 

		# kategorile
        if(!isset($cid)){
			echo "<table class='outer'>";
			echo "<tr><th>"._MYQUIZ_IMAGE."</th><th>"._MYQUIZ_CATLIST."</th><th align='center'>"._MYQUIZ_NUMQUIZES."</th></tr>";			
            $result = $xoopsDB->query("SELECT cid, ustid, name, comment, image FROM ".$xoopsDB->prefix("myquiz_categories"));						
			listele("0","0");      
		}
        # 
		else {	
		 
		echo "<table border='0' class='even'><tr><td>";	
		echo "<a href=\"".XOOPS_URL."/modules/myquiz/index.php\"><img src='".XOOPS_URL."/modules/myquiz/images/home.png' align='left' border='0' alt='"._MYQUIZ_CATMENU."'/></a>";		   		   
		   $baslik = $xoopsDB->query("SELECT cid, ustid, name, comment, image from ".$xoopsDB->prefix("myquiz_categories")."  WHERE cid='$cid'");
		   list($cid, $ustid, $name, $comment, $image) = $xoopsDB->fetchRow($baslik);
		   if (!empty($image)) { echo "<center><img src='".XOOPS_URL."/modules/myquiz/images/$image' border='0''/></center>"; }
			echo "<center><b>$name</b><br>$comment</center></td></tr></table>";		  
		   	$result00 = $xoopsDB->query("SELECT cid, ustid, name from ".$xoopsDB->prefix("myquiz_categories")."  WHERE ustid='$cid'");			
			$sayi = mysql_numrows($result00);	
			if($sayi != 0) {	
			echo "<table border='0' class='even'><tr><td align='right' width='50%'><b>"._MYQUIZ_SUBCAT.":</b></td><td align='left' width='50%'> ";			
			
			while(list($cidsub, $ustid, $name) = $xoopsDB->fetchRow($result00)) {
			echo "<br><img src='".XOOPS_URL."/modules/myquiz/images/sub.png' border='0' /><a href=\"".XOOPS_URL."/modules/myquiz/index.php?cidi=$cidsub\"><b>$name</b></a> ";			
			}
			echo "</td></tr></table>";
			}
	
	echo "<style>
.borders td {
  border-bottom: 1px solid black;

}
</style>";	


$result = $xoopsDB->query("SELECT quizzId, quizzTitle, active, restrict_user, image, administrator,displayscore,tektek,comment,nbscore FROM ".$xoopsDB->prefix("myquiz_admin")." WHERE cid='$cid' ORDER BY timeStamp desc");
	
if(mysql_num_rows($result) != 0) {			 		   
			echo "<table border='0' class='even'><tr><td align='center'><b>"._MYQUIZ_LIST."</b></td></tr></table>";   
				echo "<table class='outer' cellspacing='1' ><tr align='center'><th>&nbsp</th><th>"._MYQUIZ_TITLE."</th><th>"._MYQUIZ_NUMQUESTION."</th><th><img src='images/comment.png' title='"._MYQUIZ_YORUMS."' /></th><th><img src='images/cup.png' title='"._MYQUIZ_LISTSCORE."' /></th><th><img src='images/statistic.png' title='"._MYQUIZ_LISTSTATS."' /></th>";     							
   }    
   else { echo "<table class='outer' cellspacing='1' >"; }
					 
            while(list($qid, $quizzTitle,$active,$restrict_user,$image,$administrator,$displayscore,$tektek,$comment,$nbscore) = $xoopsDB->fetchRow($result)) {			
			   # display the inactive quizz if admin
                if ($xoopsUser) {
					$xoopsModule = XoopsModule::getByDirname("myquiz");
                    if ( $xoopsUser->isAdmin($xoopsModule->mid()) or ($administrator != "" and $xoopsUser->getVar("uname", "E") == $administrator)) {
                        if ($active == 1) {
							$img = "correct.png"; $alt = _MYQUIZ_ACTIVE;
						}
						else {
							$img = "wrong.png"; $alt = _MYQUIZ_INACTIVE;
						}
                    echo "<tr class='even'><td><img src='".XOOPS_URL."/modules/myquiz/images/$img' alt='$alt' style='align:absmiddle' /></td><td><a href=\"".XOOPS_URL."/modules/myquiz/index.php?qidi=$qid\">$quizzTitle</a>";
                    echo "<br />[ <a href=\"".XOOPS_URL."/modules/myquiz/admin/index.php?acti=QuizzViewScore&qidi=$qid\"><img src='admin/images/status.png' title='"._MYQUIZ_VIEWSCORE."'/></a> | ";
                    echo "<a href=\"".XOOPS_URL."/modules/myquiz/admin/index.php?acti=QuizzRemoveScore&qidi=$qid\"><img src='admin/images/remove.png' title='"._MYQUIZ_DELSCORE."' /></a> | ";
                    echo "<a href=\"".XOOPS_URL."/modules/myquiz/admin/index.php?acti=QuizzViewStats&qidi=$qid\"><img src='admin/images/stat.png' title='"._MYQUIZ_VIEWSTAT."' /></a> | ";
                    echo "<a href=\"".XOOPS_URL."/modules/myquiz/admin/index.php?acti=QuizzModify&qidi=$qid\"><img src='admin/images/mod.png' title='"._MYQUIZ_MODIFY."' /></a> | ";
                    echo "<a href=\"".XOOPS_URL."/modules/myquiz/admin/index.php?acti=QuizzRemove&qidi=$qid\"><img src='admin/images/rest.png' title='"._MYQUIZ_DELETE."' /></a> ]</td>";
                    # compute the number of Question in the Quizz
                    $res = $xoopsDB->query("SELECT COUNT(*) FROM ".$xoopsDB->prefix("myquiz_desc")." WHERE qid='$qid'");
                    list($nb) = $xoopsDB->fetchRow($res);
                    echo "<td align='center'>( $nb )</td>";
					
$module_handler = xoops_gethandler("module"); 
$my_module = $module_handler->getByDirname("myquiz"); 
$comment_handler = xoops_gethandler("comment"); 
$criteria = $my_module->getVar('mid'); 

$say = "SELECT  COUNT(*)  FROM ".$xoopsDB->prefix("xoopscomments")." WHERE com_modid ='$criteria' AND com_itemid ='$qid'"; 
 
$resultsay = mysql_query($say) or die(mysql_error());
while($row = mysql_fetch_array($resultsay)){
echo "<td> <a href=\"".XOOPS_URL."/modules/myquiz/comment_s.php?qidir=$qid\">(". $row['COUNT(*)'] . ")</a></td>" ;
}
					
                              if ($displayscore) {
							$ress = $xoopsDB->query("SELECT username, score FROM ".$xoopsDB->prefix("myquiz_check")." WHERE qid='$qid' ORDER BY score DESC,time DESC LIMIT $nbscore ");                                                
                        if ($xoopsDB->getRowsNum($ress)>0) {
							echo "<td>";
                            while(list($username,$res) = $xoopsDB->fetchRow($ress)) {
								echo "$username: $res<br>";
                            }
                            echo "</td><td>";
							
	$resultst = $xoopsDB->query("select quizzTitle, voters from ".$xoopsDB->prefix("myquiz_admin")." where quizzID='$qid'");
    list($quizzTitle, $voters) = $xoopsDB->fetchRow($resultst);

    $resultst = $xoopsDB->query("select MAX(score), MIN(score), AVG(score) from ".$xoopsDB->prefix("myquiz_check")." where qid='$qid'");
    list($max,$min,$mean) = $xoopsDB->fetchRow($resultst);
    echo _MYQUIZ_NBVOTE." : $voters<br>";
    echo _MYQUIZ_MEANSCORE." : $mean <br>";
    echo _MYQUIZ_MINSCORE." : $min <br>";
    echo _MYQUIZ_MAXSCORE." : $max <br>";
    echo "</td></tr>";							
							
                        }
						else { echo "<td></td><td></td></tr>"; }
                    }
					
					 else { echo "<td></td><td></td></tr>"; }
                }
				elseif ($active == 1) {
			
echo "<tr class='even'><td align='center'>";
					
					if (!empty($image)) {echo "<a href=\"".XOOPS_URL."/modules/myquiz/index.php?qidi=$qid \"><img src='".XOOPS_URL."/modules/myquiz/images/$image' border='0' /></a>";}
					else { echo "<img src='".XOOPS_URL."/modules/myquiz/images/qm.gif' border=0 alt=''>"; }
									
echo "</td><td><a href=\"".XOOPS_URL."/modules/myquiz/index.php?qidi=$qid\"><b>".$myts->MakeTboxData4Show($quizzTitle)."</b></a><br>$comment</td>";
                    # compute the number of Question in the Quizz
                    $res = $xoopsDB->query("select COUNT(*) from ".$xoopsDB->prefix("myquiz_desc")." where qid='$qid'");
                    list($nb) = $xoopsDB->fetchRow($res);
                    echo "<td align='center'> ( $nb ) </td>";
					
					$module_handler = xoops_gethandler("module"); 
$my_module = $module_handler->getByDirname("myquiz"); 
$comment_handler = xoops_gethandler("comment"); 
$criteria = $my_module->getVar('mid'); 

$say = "SELECT  COUNT(*)  FROM ".$xoopsDB->prefix("xoopscomments")." WHERE com_modid ='$criteria' AND com_itemid ='$qid'"; 
 
$resultsay = mysql_query($say) or die(mysql_error());
while($row = mysql_fetch_array($resultsay)){
echo "<td align='center'><a href=\"".XOOPS_URL."/modules/myquiz/comment_s.php?qidir=$qid\">(".$row['COUNT(*)'].")</a></td>" ;
}

					
                    if ($displayscore) {
							$ress = $xoopsDB->query("SELECT username, score FROM ".$xoopsDB->prefix("myquiz_check")." WHERE qid='$qid' ORDER BY score DESC,time DESC LIMIT $nbscore ");                                                
                        if ($xoopsDB->getRowsNum($ress)>0) {
							echo "<td>";
                            while(list($username,$res) = $xoopsDB->fetchRow($ress)) {
								echo "$username: $res<br>";
                            }
                            echo "</td><td>";
							
	$resultst = $xoopsDB->query("select quizzTitle, voters from ".$xoopsDB->prefix("myquiz_admin")." where quizzID='$qid'");
    list($quizzTitle, $voters) = $xoopsDB->fetchRow($resultst);

    $resultst = $xoopsDB->query("select MAX(score), MIN(score), AVG(score) from ".$xoopsDB->prefix("myquiz_check")." where qid='$qid'");
    list($max,$min,$mean) = $xoopsDB->fetchRow($resultst);
    echo _MYQUIZ_NBVOTE." : $voters<br>";
    echo _MYQUIZ_MEANSCORE." : $mean <br>";
    echo _MYQUIZ_MINSCORE." : $min <br>";
    echo _MYQUIZ_MAXSCORE." : $max <br>";
    echo "</td></tr>";							
							
                        }
						else { echo "<td></td><td></td></tr>"; }
                    }
					
					else { echo "<td></td><td></td></tr>"; }
                }
				
            }
        }
        }
        if (!$xoopsUser) {
			$result = $xoopsDB->query("select quizzId, quizzTitle, active, restrict_user, image, administrator,displayscore,tektek,comment,nbscore from ".$xoopsDB->prefix("myquiz_admin")." where cid='$cid' order by timeStamp desc");

            while(list($qid, $quizzTitle,$active,$restrict_user,$image,$administrator,$displayscore,$tektek,$comment,$nbscore) = $xoopsDB->fetchRow($result)) {
				if ($active == 1) {
					echo "<tr class='even'><td align='center'>";
					
					if (!empty($image)) {echo "<a href=\"".XOOPS_URL."/modules/myquiz/index.php?qidi=$qid \"><img src='".XOOPS_URL."/modules/myquiz/images/$image' border='0' /></a>";}
					else { echo "<img src='".XOOPS_URL."/modules/myquiz/images/qm.gif' border=0 alt=''>"; }
					
					
					echo "</td><td><a href=\"".XOOPS_URL."/modules/myquiz/index.php?qidi=$qid\"><b>".$myts->MakeTboxData4Show($quizzTitle)."</b></a><br>$comment</td>";
                    # compute the number of Question in the Quizz
                    $res = $xoopsDB->query("select COUNT(*) from ".$xoopsDB->prefix("myquiz_desc")." where qid='$qid'");
                    list($nb) = $xoopsDB->fetchRow($res);
                    echo "<td align='center'> ( $nb ) </td>";
					
					
$module_handler = xoops_gethandler("module"); 
$my_module = $module_handler->getByDirname("myquiz"); 
$comment_handler = xoops_gethandler("comment"); 
$criteria = $my_module->getVar('mid'); 

$say = "SELECT  COUNT(*)  FROM ".$xoopsDB->prefix("xoopscomments")." WHERE com_modid ='$criteria' AND com_itemid ='$qid'"; 
 
$resultsay = mysql_query($say) or die(mysql_error());
while($row = mysql_fetch_array($resultsay)){
echo "<td align='center'> <a href=\"".XOOPS_URL."/modules/myquiz/comment_s.php?qidir=$qid\">(". $row['COUNT(*)'] . ")</a></td>" ;
}


                    if ($displayscore) {
						$ress = $xoopsDB->query("SELECT username, score FROM ".$xoopsDB->prefix("myquiz_check")." WHERE qid='$qid' ORDER BY score DESC,time DESC LIMIT $nbscore ");                                                
                        if ($xoopsDB->getRowsNum($ress)>0) {
							echo "<td>";
                            while(list($username,$res) = $xoopsDB->fetchRow($ress)) {
								echo "$username: $res<br>";
                            }
                            echo "</td><td>";
							
	$resultst = $xoopsDB->query("select quizzTitle, voters from ".$xoopsDB->prefix("myquiz_admin")." where quizzID='$qid'");
    list($quizzTitle, $voters) = $xoopsDB->fetchRow($resultst);

    $resultst = $xoopsDB->query("select MAX(score), MIN(score), AVG(score) from ".$xoopsDB->prefix("myquiz_check")." where qid='$qid'");
    list($max,$min,$mean) = $xoopsDB->fetchRow($resultst);
    echo _MYQUIZ_NBVOTE." : $voters<br>";
    echo _MYQUIZ_MEANSCORE." : $mean <br>";
    echo _MYQUIZ_MINSCORE." : $min <br>";
    echo _MYQUIZ_MAXSCORE." : $max <br>";
    echo "</td></tr>";							
							
                        }
						 else { echo "<td></td><td></td></tr>"; }
                     }
					else { echo "<td></td><td></td></tr>"; }
                }
            }
			
        }
		echo "</table>";
}

/*********************************************************/
/* Quizz Functions                                       */
/*********************************************************/

function ViewConditions($qid)
{
      global $xoopsDB, $xoopsTheme, $xoopsLogger, $xoopsUser, $xoopsConfig;
        $result = $xoopsDB->query("SELECT quizzTitle, conditions  FROM ".$xoopsDB->prefix("myquiz_admin")." WHERE quizzID='$qid'");
        list($quizzTitle,$conditions) = $xoopsDB->fetchRow($result);
        
        echo "<table border='0' class='even'>";
		echo "<tr><td>";
		echo "<center><b>$quizzTitle</b><br /><br /><b>"._MYQUIZ_CONDITIONS.":<br /><br /></center></b>";
        echo "$conditions<br /><br /><center><a href=\"javascript:history.go(-1)\">"._MYQUIZ_BACK."</a></center>";
		echo "</td></tr>";
		echo "</table>";
      
}

?>