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

function b_myquiz_show() {
	$block = array();
	$myts =& MyTextSanitizer::getInstance();

	global $xoopsDB,$quizzID, $quizzTitle, $active;
	$result = $xoopsDB->query("SELECT quizzID, quizzTitle, active FROM ".$xoopsDB->prefix("myquiz_admin")." WHERE active='1'");

	while ($ligne = $xoopsDB->fetchArray($result)) {
		$objet = array();
	    $objet['idt'] = $ligne['quizzID'];
		$objet['titre'] = $ligne['quizzTitle'];
        
		$block['objets'][] = $objet;
	}

	return $block;
}
?>