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
 * @author    Mamba <mambax7@gmail.com>
 * @author    ZySpec <zyspec@yahoo.com>
 * @author    XOOPS Module Development Team
 * @copyright Copyright (c) 2001-2020 {@link https://xoops.org XOOPS Project}
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GNU Public License
 * @since     4.10
 */

use \XoopsModules\MyQuiz\Helper;

 /**
  * \XoopsModules\Myquiz\Utility
  *
  * Static utility class to provide common functionality
  *
  */
class Utility
{
    use Common\VersionChecks;   //checkVerXoops, checkVerPhp Traits
    use Common\ServerStats;     // getServerStats Trait
    use Common\FilesManagement; // Files Management Trait

    /**
     * Check Other element setting
     *
     * Checks to see if there's anything in the 'Other' setting
     *
     * @param string $key
     * @param int $id
     * @param string|bool returns 'Other' string or false if nothing set or on error
     *
     * @todo refactor code to eliminate use of 'global $err' to track errors
     *
     * @global array $err - used to keep error messages
     * @global array $_POST
     *
     * @return bool|string false on error | string for 'other' element
     */
    public static function checkOther($key, $id, $caption) {
        $id = (int)$id;
        if (!preg_match('/\{OTHER\|+[0-9]+\}/', $key)) {
            return false;
        } else {
            $myts = \MyTextSanitizer::getInstance();
            if (!empty($_POST['other']['ele_' . $id])) {
                return _MD_XFORMS_OPT_OTHER . $myts->htmlSpecialChars($_POST['other']['ele_' . $id]);
            } else {
                global $err;
                $err[] = sprintf(_MD_XFORMS_ERR_REQ, $myts->htmlSpecialChars($caption));
            }
        }
        return false;
    }

    /**
     * Decode HTML entities
     *
     * function used for smarty output filter of csv files
     *
     * @param string $tpl_output
     *
     * @return string filtered to decode HTML entities
     */
    public static function undoHtmlEntities($tpl_output) {
        return html_entity_decode($tpl_output);
    }

    /**
     * Callback function to convert item to integer
     *
     * Allows use of PHP array_walk to also preserve keys
     *
     * @param string|int $item
     *
     * @return int
     */
    public static function intArray(&$item) {
        $item = (int)$item;
    }

    /**
     * Quiz List Function
     *
     * @param int $ustid
     * @param int $depth
     *
     * @return void
     */
    //function listele($ustid,$depth)
    public static function lists(integer $ustid, integer $depth)
    {
        /** @var \XoopsModules\Myquiz\Helper $myquizHelper */
        $myquizHelper = Helper::getInstance();
        /** @var \XoopsModules\Myquiz\AdminHandler = $adminHandler */
        $adminHandler = $myquizHelper->getHandler('Admin');
        /** @var \XoopsModules\Myquiz\CategoryHandler = $catHandler */
        $catHandler   = $myquizHelper->getHandler('Category');
        $criteria     = new \Criteria('ustid', $ustid);
        $catObjArray  = $catHandler->getAll($criteria);

        ++$depth;
        /** @var \XoopsModules\Myquiz\Category $catObj */
        foreach($catObjArray as $cid => $catObj) {
            $criteria = new \CriteriaCompo(new \Criteria('cid', $cid));
            if (!$myquizHelper->isUserAdmin()) {
                $criteria->add(new \Criteria('active', 1));
            }
            $listCount = $adminHandler->getCount($criteria);
            if (1 == $depth) {
                echo "<tr class=\"outer\"><th class=\"center\">\n";
                if (!empty($catObj->getVar('image'))) {
                    echo "  <a href=\"" . $myquizHelper->url("index.php?cid={$cid}") . "\"><img src=\"" . $myquizHelper->url("assets/images/" . $catObj->getVar('image')) . "\" class=\"bnone\" alt=\"" . $catObj->getVar('name') . "\"></a>\n";
                }
                echo "</th><th class=\"width100\"><a href=\"" . $myquizHelper->url("index.php?cid={$cid}") . "\"><img src=\"" . $myquizHelper->url("assets/images/cvp.png") . "\" class=\"bnone\"><span style=\"bold\">" . $catObj->getVar('name') . "</span></a><br>" . $catObj->getVar('comment') . "</th><th class=\"center\"> ($listCount)</th></tr>";
            } else {
                echo "<tr class=\"even\"><td class=\"center\">";
                if (!empty($catObj->getVar('image'))) {
                    echo "<a href=\"" . $myquizHelper->url("index.php?cid={$cid}") . "\"><img src=\"" . $myquizHelper->url("assets/images/" . $catObj->getVar('image')) ."\" class=\"bnone\" alt=\"" . $catObj->getVar('name') ."\"></a>";
                }
                echo "</td><td>";
                echo str_repeat("-", $depth) . " &nbsp<a href=\"" . $myquizHelper->url("index.php?cid={$cid}") . "\"><strong>" . $catObj->getVar('name') . "</strong></a><br>" . $catObj->getVar('comment');
                echo "</td><td class=\"center\" style=\"color:#CC0000\">({$listCount})</td>";
                echo "</tr>";
            }

            $subCatCount = $catHandler->getCount(new \Criteria('ustid', $cid));
            if ($subCatCount > 0) {
                $this->lists($cid, $depth);
            }
        }
    }
}
