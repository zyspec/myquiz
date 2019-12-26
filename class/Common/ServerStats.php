<?php

namespace XoopsModules\Myquiz\Common;

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
 * @author    Mamba <mambax7@gmail.com>
 * @author    XOOPS Module Development Team
 * @copyright Copyright (c) 2001-2020 {@link https://xoops.org XOOPS Project}
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GNU Public License
 * @since     4.10
 */

trait ServerStats
{
    /**
     * serverStats()
     *
     * @return string
     */
    public static function getServerStats()
    {
        $moduleDirName      = basename(dirname(dirname(__DIR__)));
        $moduleDirNameUpper = mb_strtoupper($moduleDirName);
        xoops_loadLanguage('common', $moduleDirName);
        $html = "<fieldset><legend style='font-weight: bold; color: #900;'>" . constant('CO_' . $moduleDirNameUpper . '_IMAGEINFO') . "</legend>\n"
              . "<div style='padding: 8px;'>\n"
              . '<div>' . constant('CO_' . $moduleDirNameUpper . '_SPHPINI') . "</div>\n";
        $gdlib = function_exists('gd_info') ? '<span style="color: #008000;">' . constant('CO_' . $moduleDirNameUpper . '_GDON') . '</span>' : '<span style="color: red;">' . constant('CO_' . $moduleDirNameUpper . '_GDOFF') . '</span>';
        $html .= "<ul>\n"
               . '<li>' . constant('CO_' . $moduleDirNameUpper . '_GDLIBSTATUS') . $gdlib . "</li>\n";
        if (function_exists('gd_info')) {
            if (true === ($gdlib = gd_info())) {
                $html .= '<li>' . constant('CO_' . $moduleDirNameUpper . '_GDLIBVERSION') . '<b>' . $gdlib['GD Version'] . '</b></li>';
            }
        }
        $downloads = ini_get('file_uploads') ? '<span style="color: green;">' . constant('CO_' . $moduleDirNameUpper . '_ON') . '</span>' : '<span style="color: #ff0000;">' . constant('CO_' . $moduleDirNameUpper . '_OFF') . '</span>';
        $html .= '<li>' . constant('CO_' . $moduleDirNameUpper . '_SERVERUPLOADSTATUS') . $downloads . "</li>\n"
               . '<li>' . constant('CO_' . $moduleDirNameUpper . '_MAXUPLOADSIZE') . ' <b><span style="color: #0000ff;">' . ini_get('upload_max_filesize') . "</span></b></li>\n"
               . '<li>' . constant('CO_' . $moduleDirNameUpper . '_MAXPOSTSIZE') . ' <b><span style="color: #0000ff;">' . ini_get('post_max_size') . "</span></b></li>\n"
               . '<li>' . constant('CO_' . $moduleDirNameUpper . '_MEMORYLIMIT') . ' <b><span style="color: #0000ff;">' . ini_get('memory_limit') . "</span></b></li>\n"
               . "</ul>\n"
               . "<ul>\n"
               . '<li>' . constant('CO_' . $moduleDirNameUpper . '_SERVERPATH') . ' <b>' . XOOPS_ROOT_PATH . "</b></li>\n"
               . "</ul>\n"
               . "<br>\n"
               . constant('CO_' . $moduleDirNameUpper . '_UPLOADPATHDSC') . "\n"
               . '</div>'
               . '</fieldset><br>';
        return $html;
    }
}