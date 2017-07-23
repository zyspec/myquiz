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

echo"
        <link rel='stylesheet' href='xdstyle.css' type='text/css' media='print, projection, screen'>
<body>
<div id='back'>
<div id='tabs'>
  <ul>
    <li><a href='kategoriler.php' title='"._MYQUIZ_KATEGORI."'><span>"._MYQUIZ_KATEGORI."</span></a></li>
    <li><a href='index.php?acti=QuizzAdd' title='"._MYQUIZ_NEW."'><span>"._MYQUIZ_NEW."</span></a></li>
    <li><a href='show_testler.php' title='"._MYQUIZ_SHOWTESTLER."'><span>"._MYQUIZ_SHOWTESTLER."</span></a></li>
	<li><a href='from_user.php' title='"._MYQUIZ_FROMUSER2."'><span>"._MYQUIZ_FROMUSER2."";
	$result = $xoopsDB->query("SELECT pollID, pollTitle, qid FROM ".$xoopsDB->prefix("myquiz_descontrib")." ORDER BY qid");
	
$say = "SELECT COUNT(pollID) FROM ".$xoopsDB->prefix("myquiz_descontrib"); 
	 
$resultsay = mysql_query($say) or die(mysql_error());

// Print out result
while($row = mysql_fetch_array($resultsay)){

	echo "(<font color='#FF0000'>". $row['COUNT(pollID)'] ."</font>)";
	}
	echo "</span></a></li>
	<li><a href='upload_soru.php' title='"._MYQUIZ_SORUYUKLE."'><span>"._MYQUIZ_SORUYUKLE."</span></a></li>
	<li><a href='import.php' title='"._MYQUIZ_SORUIMPORT."'><span>"._MYQUIZ_SORUIMPORT."</span></a></li>
	<li><a href='export.php' title='"._MYQUIZ_SORUEXPORT."'><span>"._MYQUIZ_SORUEXPORT."</span></a></li>
	<li><a href='help_me.php' title='"._MYQUIZ_YARDIM."'><span>"._MYQUIZ_YARDIM."</span></a></li>
   </ul>
</div>";	



?>