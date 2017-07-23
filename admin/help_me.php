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

include_once 'admin_header.php';
xoops_cp_header();
include ("admin_menutab.php");
?>
<style type="text/css">
<!--
.style1 {color: #000066}
.style2 {color: #FF0000}
-->
</style>

<div align="center"><strong>Myquiz v.4</strong>
</div>
<p>Help Page</p>
<p><strong><u>General help</u></strong></p>
<p>*Before you prepare a quiz ,firstly <strong>you must have a category.</strong><br />
  <span class="style2">*If you want to add image to a question only use image name which is located in your images folder "/myquiz/images/myimage.jpg&quot;<br />
  <strong>(so write = myimage.jpg)</strong></span><br />
*The questions that users send are removable. That means : after you save the user questions, you can delete them from User Questions page.It wont delete it from quiz.<br />
*You can export and then download quiz questions. So you will easily add or have all quiz questions.
<p><br />
  <strong><u>1-CSV and TXT Files</u></strong></p>
<p>Example:</p>
<p><em>which one is the last month of the year ?<strong>,</strong>October<strong>,</strong>January<strong>,</strong>December<strong>,</strong>November<strong>,</strong>April,3<strong>,</strong>You are wrong December is the last one </em><br /><br />
  As you read, you must write:<br />
question,option1,option2,option3,option4,option5,correct answer id(1-2-3-4 or 5),if the answer is wrong show this.</p>
<p>*So we have 8 variables.<br /><br />
  *You must use comma(,) between this terms.<br /><br />
  *You cant use extra (,)(')(;) &quot;for example: you cant use a comma(,) in question&quot;
	<br />
&nbsp;&nbsp;&nbsp;&nbsp;# Instead of it ; Use<b>
=&gt;<br />
/c/ &nbsp;&nbsp; &nbsp;for comma (,) <br />
/s/&nbsp;&nbsp; &nbsp;for semicolon(;) <br />
\' &nbsp;&nbsp; &nbsp; &nbsp;for apostrophe(')</b></p>
<p><br />
  <strong><u>2-BBcodes</u></strong> :<br />
  *You can use bbcodes in both answers end questions.<br />
  <br />
  
  <span class="style1">Available bbcode Examples:</span><br />
  [img]myimage.jpg[/img] <br />
  [list][*]firsr row[*]second row[/list]<br />
  [b]bold write[/b] <br />
  [u]underline[/u] <br />
  [i]write italic[/i] <br />
  [color=#660066]color me[/color] <br />
  [color=blue]color me[/color] <br />
  [size=14px]font size[/size] <br />
  [code]coded write[/code] <br />
  [quote]quate this text[/quote] 
</p>
</p>
<p>&nbsp;</p>
<center>&copy; by metemet <br />
www.xoops-tr.com</center>
<? xoops_cp_footer(); ?>