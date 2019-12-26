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
include_once $GLOBALS['xoops']->path('/class/tree.php');

class Tree extends \XoopsObjectTree
{
    /**
     * Class Constructor
     *
     * @param array \XoopsModules\Myquiz\Catagory objects
     * @param int   id of this Category
     * @param int   id of this category \ParentIterator
     * @param int   id of the tree root id
     * @return void
     */
    function __construct(&$objectArr, $myId, $parentId, $rootId = null)
    {
        parent::__construct($objectArr, $myId, $parentId, $rootId);
    }

    /**
     * Retrieve all child ids for given key
     *
     * @param  unknown $key
     * @return array all child ids of key in array
     */
    public function getAllChildIds($key)
    {
        $ret = array();
        $childKeys = $this->getAllChild($key);

        foreach ($childKeys as $ck => $val) {
            $ret[] = $ck;
            if (is_array($val)) {
                $ret = array_merge($ret, getAllChildIds($ck));
            }
        }

        return $ret;
    }
    /**
     * Make options for a array
     *
     * @param string $fieldName Name of the member variable from the
     *       node objects that should be used as the column.
     * @param string $prefix String to indent deeper levels
     * @param integer $key ID of the object to display as the root of the array
     * @return array
     */
    public function makeArrayTree($fieldName, $prefix = '-', $key = 0) {
        $ret = array();
        $this->_makeArrayTreeOptions($fieldName, $key, $ret, $prefix);
        return $ret;
    }

    /**
     * Make a array with options from the tree
     *
     * @param string $fieldName Name of the member variable from the
     *       node objects that should be used as the column.
     * @param integer $key ID of the object to display as the root of the array
     * @param string $prefix_orig String to indent deeper levels (origin)
     * @param string $prefix_curr String to indent deeper levels (current)
     *
     * @return void
     */
    public function _makeArrayTreeOptions($fieldName, $key, &$ret, $prefix_orig, $prefix_curr = '') {
        $tree = $this->getTree();
        if ($key > 0) {
            $value = $tree[$key]['obj']->getVar($this->myId);
            $ret[$value] = $prefix_curr . $tree[$key]['obj']->getVar($fieldName);
            $prefix_curr .= $prefix_orig;

        }
        if (isset($tree[$key]['child']) && !empty($tree[$key]['child'])) {
            foreach ($tree[$key]['child'] as $childkey) {
                $this->_makeArrayTreeOptions($fieldName, $childkey, $ret, $prefix_orig, $prefix_curr);
            }
        }
    }
}
