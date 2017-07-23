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

function rand_show() {
	$block = array();
	$myts =& MyTextSanitizer::getInstance();

	global $xoopsDB,$quizzID, $voteID, $quizzTitle, $active, $pollTitle, $pollID, $optionText;
	
	$result = $xoopsDB->query("SELECT pollTitle, pollID, answer FROM ".$xoopsDB->prefix("myquiz_desc")." ORDER BY rand() LIMIT 0,1");
   
	while ($ligne = $xoopsDB->fetchArray($result)) {
		$objet = array();	
		$objet['idre'] = $ligne['pollID'];     
		$objet['titre'] = $ligne['pollTitle'];   
		$objet['ans'] = $ligne['answer'];     
		$block['objets'][] = $objet;		
	}
		$result = $xoopsDB->query("SELECT optionText, pollID, voteID FROM ".$xoopsDB->prefix("myquiz_data")." WHERE optionText != '' ORDER BY voteID");
		while ($ligne2 = $xoopsDB->fetchArray($result)) {
		$cvp = array();	
		$array_text[$voteID] = $optionText;
		$cvp['vid'] = $ligne2['voteID'];
		$cvp['pid'] = $ligne2['pollID'];     
		$cvp['cvpp'] = $ligne2['optionText'];        
		$block['ccvp'][] = $cvp;
				}
				
	return $block;
}
?>