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

class Descontrib extends \XoopsObject
{
    /**
     * Class Constructor
     */
    function __construct()
    {
        parent::__construct();
        $this->initVar('pollID', XOBJ_DTYPE_INT, null, false);
        $this->initVar('pollTitle', XOBJ_DTYPE_TXTBOX, null, true, 255);
        $this->initVar('timeStamp', XOBJ_DTYPE_TIMESTAMP, 0, false);
        $this->initVar('voters', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('qid', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('answer', XOBJ_DTYPE_TXTAREA, null, false);
        $this->initVar('coef', XOBJ_DTYPE_INT, 1, false);
        $this->initVar('good', XOBJ_DTYPE_OTHER, null, false);
        $this->initVar('bad', XOBJ_DTYPE_OTHER, null, false);
        $this->initVar('comment', XOBJ_DTYPE_OTHER, null, false);
        $this->initVar('image', XOBJ_DTYPE_TXTBOX, null, true, 255);
    }

    /**
     * Magic method to return formatted object title
     *
     * @return string
     */
    function __toString()
    {
        return trim($this->getVar('pollTitle'));
    }
}
