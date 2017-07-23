<?php
$modversion['name'] = _MYQUIZ_NAME;
$modversion['version'] = "2.0";
$modversion['description'] = _MYQUIZ_DESC;
$modversion['credits'] = "Modularized into XOOPS by Moumou ( http://www.frxoops.org/ )<BR> and Pascal Le Boustouller http://www.xoopsien.net";
$modversion['author'] = "Original Quizz 1.4.1 from xbee ( http://www.xbee.net/ )";
$modversion['help'] = "";
$modversion['license'] = "GPL see LICENSE";
$modversion['official'] = 1;
$modversion['image'] = "quiz_logo.gif";
$modversion['dirname'] = "myquiz";

// Admin things
$modversion['hasAdmin'] = 1;
$modversion['adminindex'] = "admin/index.php";
$modversion['adminmenu'] = "admin/menu.php";

// Menu
$modversion['hasMain'] = 1;

// Sql file (must contain sql generated by phpMyAdmin or phpPgAdmin)
// All tables should not have any prefix!
$modversion['sqlfile']['mysql'] = "sql/mysql.sql";
//$modversion['sqlfile']['postgresql'] = "sql/pgsql.sql";

// Tables created by sql file (without prefix!)
$modversion['tables'][0] = "myquiz_admin";
$modversion['tables'][1] = "myquiz_categories";
$modversion['tables'][2] = "myquiz_check";
$modversion['tables'][3] = "myquiz_data";
$modversion['tables'][4] = "myquiz_datacontrib";
$modversion['tables'][5] = "myquiz_desc";
$modversion['tables'][6] = "myquiz_descontrib";



?>