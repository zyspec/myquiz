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

echo "<script>
var rew = '"._MYQUIZ_QUESTIONSNB."';
</script>";

echo "<link rel='stylesheet' href='include/js_files/jquery.slider.css' type='text/css' media='screen, projection' /><script language='javascript' type='text/javascript' src='include/js_files/jquery.js'></script>
<script language='javascript' type='text/javascript' src='include/js_files/jquery.slider.js'></script>
<script language='javascript' type='text/javascript' src='include/js_files/formed.js'></script>
<script language='javascript' type='text/javascript'>
$(function() {
    $('.quiz').accessNews({
        newsHeadline: 'quiz',
        newsSpeed: 'normal'
    });
});
</script>";

$seci = $myts->MakeTareaData4Show($conditions)+2;
       $res = $xoopsDB->query("SELECT COUNT(*) FROM ".$xoopsDB->prefix("myquiz_desc")." WHERE qid='$qid'");
                    list($nb) = $xoopsDB->fetchRow($res);

echo "<script>
var sec = ".$myts->MakeTareaData4Show($conditions)."+2;
var sayim = $nb;
</script>";
	        # ilk giris
			echo "<form name='yolla' action=\"".XOOPS_URL."/modules/myquiz/index.php\" method=\"post\">";
		echo "<input type=\"hidden\" name=\"qid\" value=\"$qid\">";
		echo "<input type=\"hidden\" name=\"do\" value=\"vote\">";
        if ($xoopsUser){
			echo "<input type=\"hidden\" name=\"logname\" value=\"$logname\">";
            echo "<input type=\"hidden\" name=\"adrs\" value=\"$adrs\">";
			       }
        else{
			echo "<table border='0' ><tr><td align='center'>"._MYQUIZ_LOGNAME.": <input type='text' name='logname'> ";
			echo ""._MYQUIZ_EMAIL.": <input type='text' name='adrs'></td></tr></table><br>";			
        }		
echo "<div class='news_slider quiz' align='center'>		
		
		<table class='alttab' align='center' border='0' cellpadding='0' cellspacing='0'><tr><td align='left'><img  src='include/js_files/images/sol.png' /></td><td class='tab-orta' align='center'></td><td><img align='right' src='include/js_files/images/sag.png' /></td></tr></table>   		
		      
		<a href='#' class='next'><img src='images/next.png' width='32px' height='32px' alt='Next' title='Next' env='images' /></a>
                    <div class='news_items'>			
<DIV class='f2' STYLE='position:relative ; top:-19px; left:0px; width:38px; height:38px'>
<CENTER><FONT SIZE='+1' COLOR='002525'><div class='countdown' secs=$seci tsecs=$seci>- </div>
</FONT></CENTER>
</DIV> 		
                
                <div class='container fl'>";	
				
	echo "<div class='item fl'>    				
						<br><br><br><center>"._MYQUIZ_READY."</center>
						<a href='#' class='next' STYLE='left:200px; top:75px;'><img src='images/start.gif' alt='Start' title='Next' env='images' /></a>
							<br><br><br> </div>";   				
				
							
        $num = 1;					
$result = $xoopsDB->query("SELECT pollID, pollTitle, voters, comment, answer, image FROM ".$xoopsDB->prefix("myquiz_desc")." WHERE qid='$qid'");
		
        while(list($pollID, $pollTitle, $voters, $comment,$answer,$image) = $xoopsDB->fetchRow($result)){
		
echo "<div class='item fl'>
<IMG class='fl' SRC='images/photo.gif'>
<DIV class='sayilar' STYLE='position:relative ; top:16px; left:-49px; width:38px; height:38px'>
<CENTER>$num</CENTER>
</DIV>                     
 <div class='fl'>";				

	if (!empty($image)){
				echo "<table><tr><td><center><img src='".XOOPS_URL."/modules/myquiz/images/$image' border=0 align='absmiddle'></center><b> ".bb2html($myts->MakeTboxData4Show($pollTitle))."</b></td></tr></table><br />";
            }
            else{
				echo "<table border='0' ><tr><td><b>".bb2html($myts->MakeTboxData4Show($pollTitle))."</b></td></tr></table>";				
            }
            if ($xoopsUser){
				$xoopsModule = XoopsModule::getByDirname("myquiz");
				if ( $xoopsUser->isAdmin($xoopsModule->mid()) ) {
					echo "<table border='0'><tr><td>[ <a href=\"".XOOPS_URL."/modules/myquiz/admin/index.php?acti=QuizzModifyQuestion&pidi=$pollID&qidi=$qid\">"._MYQUIZ_MODIFY."</a> | ";
					echo "<a href=\"".XOOPS_URL."/modules/myquiz/admin/index.php?acti=QuizzDelQuestion&pidi=$pollID&qidi=$qid\">"._MYQUIZ_DELETE."</a> ]</td></tr></table>";
                }
            }
            if (!empty($comment)){
				echo "<table border='0' ><tr><td>&nbsp;&nbsp;<i>".$myts->MakeTareaData4Show($comment)."</i></td></tr></table>";
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
				echo "<table border='0' >";
				for($i = 1; $i <= 5; $i++){
					$result1 = $xoopsDB->query("SELECT optionText FROM ".$xoopsDB->prefix("myquiz_data")." WHERE (pollID='$pollID') AND (voteID='$i')");
					list($optionText) = $xoopsDB->fetchRow($result1);
					if($optionText != ""){
						# if ($ok == 0) { $checked = "checked" ; } else { $checked = ""; } <- Version originale précochée sur 1
						if ($ok == 0) { $checked = "" ; } else { $checked = ""; }
						echo "<table  class='borders' ><tr><td><input type=\"radio\" class='styled' name=\"voteQuizz[$pollID]\" value=\"$i\" $checked>".bb2html($optionText)."</td></tr>";       $ok++;
		# cevaplarin templatesi		
					}
                } # end poll question
            }
            $num++;
            echo "</table><br>";
	       # echo '<br/>';
  
 echo " </div>
                    </div>";
					}			
echo "<div class='item fl'>    				
						<br>
						<center>"._MYQUIZ_FINISHTEK."<center>
							<br><br><br><right>					
                           <input class='finish' type=\"image\" src='images/submit.gif' value='"._MYQUIZ_SUBMIT."'></right> 
	  </div>";   					
 
echo "</div></div></div></form>";

?>