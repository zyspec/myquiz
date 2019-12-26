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

defined('XOOPS_ROOT_PATH') || exit('Restricted access');

/**
 * Category Object Handler
 *
 */
class CategoryHandler extends \XoopsPersistableObjectHandler
{
    /**
     * Class constructor
     *
     * @param \XoopsDatabase $db
     * @return void
     */
    public function __construct(&$db = null)
    {
        parent::__construct($db, 'myquiz_categories', Category::class, 'cid');
    }

    /**
     *
     * @param int $ustid
     * @param int $depth
     * @param int $presel
     * @param array $exclude
     * @return string
     */
    public function lists($ustid, $depth, $presel = null, $exclude = array())
    {
        ++$depth;
        $catObjArray = $this->getAll(new \Criteria('ustid', (int)$ustid), array('cid', 'ustid', 'name'));
        $retString = '';
        foreach ($catObjArray as $cid => $catObj) {
            if (!in_array($cid, $exclude)) { // skip if this cat is in excluded from list
                $preChar = (1 === $depth) ? "#" : "-";
                $sel = ($presel == $cid) ? ' selected' : '';
                $retString = "<option value=\"{$cid}\"{$sel}>" . str_repeat($preChar, $depth) . " " . $catObj->getVar('name') . "</option>";

                //recursively get cats
                $s2 = $this->getCount(new \Criteria('ustid', $cid));
                if ($s2 > 0) {
                    $retString .= $this->lists((int)$cid, (int)$depth, (int)$presel, (array)$exclude);
                }
            }
        }
        return $retString;
    }

    /**
     * Compare 2 trees by a field in the tree
     *
     * @param \XoopsObjectTree $a
     * @param \XoopsObjectTree $b
     * @param string|int $sortField field to sort on (typically a title or name field)
     * @return boolean|number {@see strcmp()}
     */
    private function treeCmp($a, $b, $sortField)
    {
        $retVal = false;
        if (is_object($a) && is_object($b)) {
            $retVal = strcmp($a->getVar($sortField), $b->getVar($sortField));
        }

        return $retVal;
    }

    /**
     *
     * @param \XoopsObjectTree $tree
     * @param string|int $sortField field to sort on (typically a title or name field)
     * @param string sortType valid types are 'f' (field) or 'k' (key) - case insensitive
     * @return object sorted \XoopsModules\Myquiz\Tree object
     */
    public function sortTree(\XoopsObjectTree $tree, $sortField = null, $sortType = 'k')
    {
        $sortType = in_array($sortType, array('k', 'K', 'f', 'F')) ? mb_strtolower($sortType) : 'k';
        if ('f' === $sortType) {
            // @todo make sure 'sortField' is a valid field in one of the tree objects
            uasort($tree, '$this->treeCmp');
        } else {
            uksort($tree, '$this->treeCmp');
        }
        return $tree;
    }
}
