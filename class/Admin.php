<?php
namespace XoopsModules\Myquiz;

/*
 You may not change or alter any portion of this comment or credits of
 supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit
 authors.

 This program is distributed in the hope that it will be useful, but
 WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */
/**
 * Module: Myquiz - a quiz/test module for XOOPS
 *
 * @package   \XoopsModules\Myquiz
 * @link      https://github.com/XoopsModules25x/myquiz
 * @author    XOOPS Module Development Team
 * @copyright Copyright (c) 2001-2020 {@link https://xoops.org XOOPS Project}
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GNU Public License
 * @since     4.10
 */

use \XoopsModules\Myquiz\Helper as Helper;

defined('XOOPS_ROOT_PATH') || exit('Restricted access');

class Admin extends \XoopsObject
{
    /**
     * constructor
     */
    function __construct()
    {
        parent::__construct();
        $this->initVar('quizzID', XOBJ_DTYPE_INT, null, false);
        $this->initVar('quizzTitle', XOBJ_DTYPE_TXTBOX, null, true, 255);
        $this->initVar('timeStamp', XOBJ_DTYPE_TIMESTAMP, null, false);
        $this->initVar('voters', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('nbscore', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('displayscore', XOBJ_DTYPE_INT, 1, false);
        $this->initVar('displayresults', XOBJ_DTYPE_INT, 1, false);
        $this->initVar('tektek', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('comment', XOBJ_DTYPE_TXTAREA, null, false);
        $this->initVar('active', XOBJ_DTYPE_INT, 1, false);
        $this->initVar('restrict_user', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('log_user', XOBJ_DTYPE_INT, 1, false);
        $this->initVar('image', XOBJ_DTYPE_TXTBOX, null, false, 50);
        $this->initVar('cid', XOBJ_DTYPE_INT, 1, false);
        $this->initVar('contrib', XOBJ_DTYPE_INT, 1, false);
        $this->initVar('expire', XOBJ_DTYPE_TXTBOX, 'xx-xx-xxxx xx:xx', false, 16);
        $this->initVar('emailadmin', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('admemail', XOBJ_DTYPE_TXTBOX, null, false, 50);
        $this->initVar('administrator', XOBJ_DTYPE_TXTBOX, null, false, 50);
        $this->initVar('conditions', XOBJ_DTYPE_TXTBOX, 0, false, 50);
    }

    function __toString()
    {
        return trim($this->getVar('quizzTitle'));
    }

    function display()
    {
        echo $this->render();
        return;
    }

    /**
     * Render the Admin object
     * @return void|NULL|string
     */
    function render()
    {

        $myquizHelper  = Helper::getInstance();
        $myquizHelper->loadLanguage('main');
        $myts = \MyTextSanitizer::getInstance();

        $retVal  = null;
        $quizzID = $this->getVar('quizzID');

        $GLOBALS['xoopsTpl']->assign('qid', $quizzID);
        $GLOBALS['xoopsTpl']->assign('is_active', $this->getVar('active'));
        $GLOBALS['xoopsTpl']->assign('is_mod_admin', $myquizHelper->isUserAdmin());

        if ($myquizHelper->isUserAdmin() || (1 == $this->getVar('active'))) { // make sure quiz is active or this is an admin
            // Check first if there is a deadline (no xx)
            // @todo fix this with new expire storage / retrieval
            if (false !== strstr($this->getVar('expire'), "xx")) {
                $Cyear  = strftime("%Y",strtotime($expire));
                $Cmonth = strftime("%m",strtotime($expire));
                $Cday   = strftime("%d",strtotime($expire));
                $Chour  = strftime("%H",strtotime($expire));
                $Cmin   = strftime("%M",strtotime($expire));

                $today = getdate();
                $day   = $today['mday'];
                if ($day < 10){ $day = "$day"; }
                $month  = $today['mon'];
                $year  = $today['year'];
                $hour  = $today['hours'];
                $min  = $today['minutes'];
                $sec  = $today['seconds'];

                // Check the day
                // if (($date[1] <= $year) AND ($Cmonth <= $month) AND ($Cday <= $day) AND ($Chour <= $hour) AND ($Cmin <= $min)) {
                if (($Cyear <= $year) AND ($Cmonth <= $month) AND ($Cday <= $day) AND ($Chour <= $hour) AND ($Cmin <= $min)) {
                    $expired = 1;
                    $retVal = "<table class='even bnone'><tr><td class='center bold'>" . _MYQUIZ_HASEXPIRED . "</td></tr></table>";
                    return;
                }

                // Displays the score if necessary
                if (1 == $this->getVar('expire')) {
                    if (1 == $this->getVar('displayscore')) {
                        $myquizCheckHandler = $myquizHelper->getHandler('Check');
                        $criteria = new \CriteriaCompo();
                        $criteria->add(new \Criteria('qid', $quizzID));
                        $criteria->setSort('score DESC, time');  // trick criteria to allow 2 sort criteria
                        $criteria->setOrder('DESC');
                        $criteria->setLimit($this->getVar('nbscore'));
                        $checkArray = $myquizCheckHandler->getAll($criteria, array('username', 'score'));

                        $retVal .= _MD_MYQUIZ_LISTSCORE . "<br>\n";
                        foreach ($checkObjArray as $checkItem) {
                            $retVal .= "{$checkItem['username']}: {$checkItem['score']}<br>";
                        }
                        unset($checkArray);
                        $retVal .= "<table class='even bnone'><tr><td><br>" . _MD_MYQUIZ_THANKS . "<br></td></tr></table>";
                    } else {
                        return $retVal;
                    }
                }
            }

            // Get the user's name/email if they're a registered user
            $logname = $adrs = null;
            if ($GLOBALS['xoopsUser'] instanceof \XoopsUser && !$GLOBALS['xoopsUser']->isGuest()) {
                $logname = $GLOBALS['xoopsUser']->getVar('uname', 'E');
                $adrs    = $GLOBALS['xoopsUser']->getVar('email', 'E');
            }

            // Verify if the user is registered & only allow a single vote for the quiz
            if (1 == $this->getVar('log_user')) {
                // Check the dB to see if this user email/name already exists
                $myquizCheckHandler = $myquizHelper->getHandler('Check');
                $criteria2 = new \CriteriaCompo(new \Criteria('email', $adrs));
                $criteria2->add(new \Criteria('username', $logname), 'OR');
                $criteria = new \CriteriaCompo(new \Criteria('qid', $quizzID));
                $criteria->add($criteria2);
                $checkArray = $myquizCheckHandler->getAll($criteria, array('username'), false);
                if (!empty($checkArray)) {
                    $retVal .= "<table class='even bnone'><tr><td class='center bold' style='color:#DD0000'>" . _MD_MYQUIZ_ALREADYVOTED . "</td></tr></table>";
                    return $retVal;
                }
            }
            // Display admin links if user is module admin
            if ($myquizHelper->isUserAdmin()) {
                $menu = "[ <a href='" . $myquizHelper->url("admin/main.php?act=QuizzViewScore&qid={$quizzID}") . "'>" . _MD_MYQUIZ_VIEWSCORE . "</a> | "
                      . "<a href='"  . $myquizHelper->url("admin/main.php?act=QuizzRemoveScore&qid={$quizzID}") . "'>"._MYQUIZ_DELSCORE . "</a> | "
                      . "<a href='"  . $myquizHelper->url("admin/main.php?act=QuizzViewStats&qid={$quizzID}") . "'>" . _MD_MYQUIZ_VIEWSTAT . "</a> | "
                      . "<a href='"  . $myquizHelper->url("admin/main.php?act=QuizzModify&qid={$quizzID}") . "'>" . _MD_MYQUIZ_MODIFY . "</a> | "
                      . "<a href='"  . $myquizHelper->url("admin/main.php?act=QuizzRemove&qid={$quizzID}") . "'>" . _DELETE . "</a> ]";
            }

            $quizImg = $this->getVar('image');

            if (!empty($quizImg)) {
                $retVal .= "<table class='even bnone'><tr><td><b>" . $this->getVar('quizzTitle') ."</b> {$menu}<img class='center bnone' src='" . $myquizHelper->url("assets/images/{$quizImg}") . "' alt='' /></td></tr></table>\n"
                         . "<table class='even bnone'><tr><td>" . $this->getVar('comment', 's') . "</td></tr></table>";
            } else {
                $men = isset($menu) ? $menu : '';
                $retVal .= "<table class='even bnone'><tr><td><b>" . $this->getVar('quizzTitle', 's') . "</b> {$men}</td></tr></table>\n"
                         . "<table class='even bnone'><tr><td>"._MD_MYQUIZ_COMMENT.": " . $this->getVar('comment', 's') . "</td></tr></table>";
            }

            if (0 == $this->getVar('tektek')) {
                if (0 != $this->getVar('conditions')) {
                    //zaman sayaci
                    $retVal .= "<script>\n"
                       . "var sec = 00;   // set the seconds\n"
                       . "var min =" . $this->getVar('conditions') . ";   // set the minutes\n"
                       . "function countDown() {\n"
                       . "  sec--;\n"
                       . "  if (sec == -01) {\n"
                       . "    sec = 59;\n"
                       . "    min = min - 1;\n"
                       . "  } else {\n"
                       . "    min = min;\n"
                       . "  }\n"
                       . "  if (sec<=9) { sec = '0' + sec; }\n"
                       . "  time = (min<=9 ? '0' + min : min) + '" . _MYQUIZ_CDOWNM . "' + sec + '" . _MYQUIZ_CDOWNS . " ';\n"
                       . "  if (document.getElementById) { document.getElementById('theTime').innerHTML = time; }\n"
                       . "  SD=window.setTimeout('countDown();', 1000);\n"
                       . "  if (min == '00' && sec == '00') {\n"
                       . "    sec = '00';\n"
                       . "    window.clearTimeout(SD);\n"
                       . "    document.yolla.submit();\n"
                       . "  }\n"
                       . "}\n"
                       . "function addLoadEvent(func) {\n"
                       . "  var oldonload = window.onload;\n"
                       . "  if (typeof window.onload != 'function') {\n"
                       . "    window.onload = func;\n"
                       . "  } else {\n"
                       . "    window.onload = function() {\n"
                       . "      if (oldonload) {\n"
                       . "        oldonload();\n"
                       . "      }\n"
                       . "      func();\n"
                       . "    }\n"
                       . "  }\n"
                       . "}\n\n"
                       . "addLoadEvent(function() {\n"
                       . "  countDown();\n"
                       . "});\n"
                       . "</script>\n\n"
                       . "<style>\n;"
                       . ".timeClass {\n"
                       . "  font-family:arial,verdana,helvetica,sans-serif;\n"
                       . "  font-weight:bold;\n"
                       . "  font-size:10pt;\n"
                       . "  color:#000000;\n"
                       . "}\n"
                       . "</style>\n"
                       . "<table class='even bnone'><tr><td>" . _MYQUIZ_ZAMAN . ": ".$this->getVar('conditions') . _MYQUIZ_CDOWNM . "</td></tr></table>\n"
                       . "<table bgcolor='#fafafa' border='1' width='100%'>\n"
                       . "  <tr><td class='width100 center'><span id='theTime' class='timeClass'></span></td></tr>\n"
                       . "</table>\n";
                }

                // Afficher le formulaire de quizz
                $retVal .= "<form name='yolla' action='" . XOOPS_URL . "/modules/myquiz/index.php' method='post'>\n"
                         . "<input type='hidden' name='qid' value='{$quizzID}'>\n"
                         . "<input type='hidden' name='do' value='vote'>\n";
                if ((null !== $logname) && (null !== $adrs)) {
                    $retVal .= "<input type='hidden' name='logname' value='{$logname}'>\n"
                             . "<input type='hidden' name='adrs' value='{$adrs}'>\n";
                } else {
                    $retVal .= "<table class='even bnone'><tr><td class='center'>" . _MD_MYQUIZ_LOGNAME . ": <input type='text' name='logname'>\n"
                             . " " . _MD_MYQUIZ_EMAIL . ": <input type='text' name='adrs'></td></tr></table>\n"
                             . "<p>";
                }

                $num = 1;
                $descHandler  = $myquizHelper->getHandler('Desc');
                $descObjArray = $descHandler->getAll(new \Criteria('qid', $quizzID));
                foreach ($descObjArray as $descObj) {
                    $descImg = $descObj->getVar('image') ;
                    if (!empty($descImg)) {
                        $retVal .= "<table class='even bnone'><tr><td><img class='center bnone middle' src='" . $myquizHelper->url("assets/images/" . $descObj->getVar('image')) . "'><b>" . _MD_MYQUIZ_QUESTIONTITLE . " $num : " . $myts->xoopsCodeDecode($descObj->getVar('pollTitle', 's')) . "</b></td></tr></table>";
                    } else {
                        $retVal .= "<table class='even bnone'><tr><td><b>" . _MD_MYQUIZ_QUESTIONTITLE . " {$num} : " . $myts->xoopsCodeDecode($descObj->getVar('pollTitle' , 's')) ."</b></td></tr></table>";
                    }

                    if ($myquizHelper->isUserAdmin()){
                        $retVal .= "<table class='even bnone'><tr><td>[ <a href='" . $myquizHelper->url("admin/main.php?act=QuizzModifyQuestion&pidi=" . $descObj->getVar('pollID') ."&qid={$quizzID}") . "'>" . _MD_MYQUIZ_MODIFY . "</a> | "
                                 . "<a href='" . $myquizHelper->url("admin/main.php?act=QuizzDelQuestion&pidi=" . $descObj->getVar('pollID') . "&qid={$quizzID}") . "'>" . _DELETE . "</a> ]</td></tr></table>\n";
                    }

                    $descComment = $descObj->getVar('comment', 's');
                    if (!empty($descComment)) {
                        $retVal .= "<table class='even bnone'><tr><td>&nbsp;&nbsp;<i>" . $descObj->getVar('comment', 's') . "</i></td></tr></table>\n";
                    }

                    $retVal .= "<input type='hidden' name='pollID' value='" . $descObj->getVar('pollID') . "'>\n";
                    // ordered answer needed?
                    if (false === strpos($descObj->getVar('answer'), ",")) {
                        $dataHandler = $myquizHelper->getHandler('Data');
                        $criteria = new \CriteriaCompo(new \Criteria('pollID', $descObj->getVar('pollID')));
                        $criteria->add(new \Criteria('optionText', null, '<>'));
                        $criteria->setSort('voteID');
                        $dataArray = $dataHandler->getAll($criteria, array('optionText', 'voteID'), false);
                        $arrayText = array();
                        $retVal .= "&nbsp;&nbsp;<select name='voteQuizz[" . $descObj->getVar('pollID') . "][$i]'>";
                        foreach ($dataArray as $key => $dataItem) {
                            $retVal .= "<option name='rank' value='{$key}'$sel>" . $myts->htmlSpecialChars($dataItem['optionText']) . "</option>";
                        }
                        $retVal .= "</select> " . $myts->htmlSpecialChars($optionText) . "<br>\n";
                    } else {
                        $dataHandler = $myquizHelper->getHandler('Data');
                        $criteria = new \CriteriaCompo(new \Criteria('pollID', $descObj->getVar('pollID')));
                        $criteria->add(new \Criteria('voteID', '(1,2,3,4,5)', 'IN'));
                        $criteria->add(new \Criteria('optionText', null, '<>'));
                        $dataItemArray = $dataHandler->getAll($criteria, array('optionText'));

                        $retVal .= "<table class='even bnone'>\n";

                        $index = 0;
                        foreach ($dataItemArray as $dataItem) {
                            //@todo figure out which item should be checked - original code was broken
                            $checked = (1 == $index) ? " checked" : "";  // this sets 1st item as checked - should it?
                            ++$index;
                            $retVal .= "<table class='even bnone'><tr><td><input type='radio' name='voteQuizz[" . $descObj->getVar('pollID') . "' value='{$index}'$checked>" . $myts->xoopsCodeDecode($dataItem['optionText']) . "</td></tr>";
                        }
                    }
                    ++$num;
                    echo "</table>\n"
                       . "<br>\n";
                } // end poll loop

                $retVal .= "<table class='even bnone'><tr><td class='center'><input type='submit' class=button value='" . _SUBMIT . "'></td></tr></table></form>";

                // display the possibility for contributor to add question if needed
                if (1 == $this->getVar('contrib')) {
                    $retVal .= "<table class='even bnone'><tr><td class='center'><a href='" . $myquizHelper->url("index.php?qid={$quizzID}") . "&do=contrib'>[ " . _MYQUIZ_CONTRIBUTE . " ]</a></td></tr></table>";
                }
            } else {
//                include $myquizHelper->path("tek_question.php");
/************************************************/

                $descHandler  = $myquizHelper->getHandler('Desc');
                $descObjArray = $descHandler->getAll(new \Criteria('qid', $qid), array('pollID', 'pollTitle', 'voters', 'comment', 'answer', 'image'));

                $GLOBALS['xoopsOption']['template_main'] = 'myquiz_tek_question.tpl';
                include $GLOBALS['xoops']->path('header.php');

                $GLOBALS['xoopsTpl']->assign('conditions', $myts->displayTarea($conditions));
                $GLOBALS['xoopsTpl']->assign('secs', ($myts->displayTarea($conditions) + 2));
                $GLOBALS['xoopsTpl']->assign('nb', count($descObjArray));
                $GLOBALS['xoopsTpl']->assign('idx_link', $myquizHelper->url('index.php'));
                $GLOBALS['xoopsTpl']->assign('qid', $qid);
                $GLOBALS['xoopsTpl']->assign('mod_url', $myquizHelper->url());
                if ($xoopsUser){
                    $GLOBALS['xoopsTpl']->assign('logname', $logname);
                    $GLOBALS['xoopsTpl']->assign('adrs', $adrs);
                }

                $pollObjArray = array();
                $result = array();
                foreach ($descObjArray as $pollID => $descObj) {
                    $results[$pollID]['image']     = !empty($descObj->getVar('image')) ? $descObj->getVar('image') : '';
                    $results[$pollID]['pollTitle'] = $myts->xoopsCodeDecode($myts->htmlSpecialChars($pollTitle));
                    $results[$pollID]['comment']   = !empty($comment) ? $myts->displayTarea($comment) : null;
                    $dataHandler = $myquizHelper->getHandler('Data');
                    $criteria    = new \Criteria('pollID', $descObj->getVar('pollID'));
                    $criteria->setSort('voteID');
                    // ordered answer needed?
                    $array_text = array();
                    if (false === preg_match('/,/', $descObj->getVar('answer'))) {
                        $criteria->setOrder('ASC');
                        $pollObjArray = $dataHandler->getAll($criteria, array('optionText', 'voteID'));
                        // compute the list of answer for the combo box
                        foreach ($pollObjArray as $pollObj) {
                            $array_text[$pollObj->getVar('voteID')] = $pollObj->getVar('optionText');
                        }
                    } else {
                        for ($i = 1; $i <= 5; ++$i) {
                            if ('' != $descObj->getVar('optionText')) {
                                $array_text[$i] = $myts->xoopsCodeDecode($pollObj->getVar('optionText'));
                            }
                        }
                    }
                    $results[$pollID]['array_text'] = $array_text;
                }
                $GLOBALS['xoopsTpl']->assign('results', $results);
                include $GLOBALS['xoops']->path('footer.php');

/************************************************/
            }
        } else {
            $retVal = "<table class='even bnone'><tr><td class='center bold'>" . _MD_MYQUIZ_INACTIVE . "</td></tr></table>";

        }
        return $retVal;
    }
}
