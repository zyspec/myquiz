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
 * @author    XOOPS Module Development Team
 * @copyright Copyright (c) 2001-2020 {@link https://xoops.org XOOPS Project}
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GNU Public License
 * @since     4.10
 */

/**
 * Class Configurator
 */
class Configurator
{
    public $name;
    public $paths           = [];
    public $uploadFolders   = [];
    public $copyBlankFiles  = [];
    public $copyTestFolders = [];
    public $templateFolders = [];
    public $oldFiles        = [];
    public $oldFolders      = [];
    public $renameTables    = [];
    public $modCopyright;
    /**
     * Configurator constructor.
     */
    public function __construct()
    {
        $moduleDirName      = basename(dirname(dirname(__DIR__)));
        $moduleDirNameUpper = mb_strtoupper($moduleDirName);
        require_once dirname(dirname(__DIR__)) . '/include/config.php';
        $config = getConfig();
        $this->name            = $config->name;
        $this->paths           = $config->paths;
        $this->uploadFolders   = $config->uploadFolders;
        $this->copyBlankFiles  = $config->copyBlankFiles;
        $this->copyTestFolders = $config->copyTestFolders;
        $this->templateFolders = $config->templateFolders;
        $this->oldFiles        = $config->oldFiles;
        $this->oldFolders      = $config->oldFolders;
        $this->renameTables    = $config->renameTables;
        $this->modCopyright    = $config->modCopyright;
    }
}
