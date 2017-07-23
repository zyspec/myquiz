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

function b_scores_show() {
	$block = array();
	$myts =& MyTextSanitizer::getInstance();
	global $xoopsDB,$username, $score, $qid, $quizzTitle, $quizzID;


	$result = $xoopsDB->query("SELECT DISTINCT qid FROM ".$xoopsDB->prefix("myquiz_check"));
	while ($ligne1 = $xoopsDB->fetchArray($result)) {
		$objet = array();
		$result2 = $xoopsDB->query("SELECT quizzID, quizzTitle FROM ".$xoopsDB->prefix("myquiz_admin")." WHERE quizzID = ".$ligne1['qid']);
		$ligne3 = $xoopsDB->fetchArray($result2);

		$objet['idd'] = $ligne1['qid'];
		$objet['titre'] = $ligne3['quizzTitle'];

		$block['objets'][] = $objet;
	}
    $result = $xoopsDB->query("SELECT qid, username, score FROM ".$xoopsDB->prefix("myquiz_check")." ORDER BY score DESC,time DESC LIMIT 300 ");
	while ($ligne2 = $xoopsDB->fetchArray($result)) {
		$pers = array();
		$pers['ide'] = $ligne2['qid'];
		$pers['nom'] = $ligne2['username'];
		$pers['points'] = $ligne2['score'];

		$block['perss'][] = $pers;
	}	
	return $block;
}
?>