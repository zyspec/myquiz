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

class Category extends \XoopsObject
{
    /**
     * constructor
     */
    function __construct()
    {
        parent::__construct();
        $this->initVar('cid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('ustid', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('name', XOBJ_DTYPE_TXTBOX, null, true, 50);
        $this->initVar('comment', XOBJ_DTYPE_TXTAREA, null, false);
        $this->initVar('image', XOBJ_DTYPE_TXTBOX, null, false, 50);
    }

    function __toString()
    {
        return trim($this->getVar('name'));
    }
}
