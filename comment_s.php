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

include("../../mainfile.php");
include(XOOPS_ROOT_PATH."/header.php");
global $xoopsUser, $xoopsDB, $xoopsConfig, $xoopsTheme, $xoopsLogger;

$sqid=$_GET['qidir'];

$res = $xoopsDB->query("SELECT quizzTitle FROM ".$xoopsDB->prefix("myquiz_admin")." WHERE quizzID='$sqid'");
list($quizzTitle) = $xoopsDB->fetchRow($res);

$xoopsTpl->assign('quizTitle', $quizzTitle);
$xoopsTpl->assign('test', _MYQUIZ_MYQUIZ);

$xoopsOption['template_main'] = 'comments.html';
include(XOOPS_ROOT_PATH."/include/comment_view.php");  
include(XOOPS_ROOT_PATH."/footer.php");

?>