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

/* ********************************************************************************************************************************* */
/* ENTREE PAGE
/* ********************************************************************************************************************************* */

if($xoopsConfig['startpage'] == "myquiz"){
        $xoopsOption['show_rblock'] =1;
        include(XOOPS_ROOT_PATH."/header.php");
        make_cblock();
}
else {
        $xoopsOption['show_rblock'] =0;
        include(XOOPS_ROOT_PATH."/header.php");
}

include_once(XOOPS_ROOT_PATH."/class/xoopsmodule.php");

if ($xoopsUser) {
        $xoopsModule = XoopsModule::getByDirname("myquiz");
        if ( $xoopsUser->isAdmin($xoopsModule->mid()) ) {
           echo "<table border=0 class='outer'><tr><td align='center'><a href=\"".XOOPS_URL."/modules/myquiz/admin/index.php?acti=QuizzAdmin\"> [ "._MYQUIZ_ADMIN." ]</a><br /><br /></td></tr></table>";
        }
}


/* -- Récupération des variables ------------------------------------------------------------------------------------------------- */

if(!isset($_POST['qid'])){
	if(isset($_GET['qidi'])){$qid = $_GET['qidi'];}
}
else{
	$qid = $_POST['qid'];
}

if(!isset($_POST['qid'])){
	if(isset($_GET['qidir'])){$qid = $_GET['qidir'];}
}
else{
	$qid = $_POST['qid'];
}

if(!isset($_POST['do'])){
	if(isset($_GET['doi'])){$do = $_GET['doi'];}
}
else{
	$do = $_POST['do'];
}

if(!isset($_POST['cid'])){
	if(isset($_GET['cidi'])){$cid = $_GET['cidi'];}
}
else{
	$cid = $_POST['cid'];
}

if(!isset($_POST['logname'])){
	if(isset($_GET['lognamei'])){$logname = $_GET['lognamei'];}
}
else{
	$logname = $_POST['logname'];
}

if(!isset($_POST['adrs'])){
	if(isset($_GET['adrsi'])){$adrs = $_GET['adrsi'];}
}
else{
	$adrs = $_POST['adrs'];
}

if(!isset($_POST['pollID'])){
	if(isset($_GET['pollIDi'])){$pollID = $_GET['pollIDi'];}
}
else{
	$pollID = $_POST['pollID'];
}

if(!isset($_POST['voteQuizz'])){
	if(isset($_GET['voteQuizzi'])){$voteQuizz = $_GET['voteQuizzi'];}
}
else{
	$voteQuizz = $_POST['voteQuizz'];
}
/* -------------------------------------------------------------------------------------------------------------------------------- */

if(!isset($qid)) {
    QuizzList();
}
else if (!isset($do)) {
    QuizzShow($qid);
}
else {
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

/* -------------------------------------------------------------------------------------------------------------------------------- */


echo "<br><br><table border='0' class='odd'><tr><td>";
echo _MYQUIZ_COPY;
echo "</td></tr></table>";
 
include(XOOPS_ROOT_PATH."/footer.php");


?>
