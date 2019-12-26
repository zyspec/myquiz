<?php

namespace \XoopsModules\Myquiz;

/*
 MyQuiz Zip Class Definition

 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */
/**
 * Module:  myQuiz
 *
 * @package   \XoopsModule\Myquiz\admin
 * @license   https://www.fsf.org/copyleft/gpl.html GNU public license
 * @copyright https://xoops.org 2001-2019 &copy; XOOPS Project
 * @author    XOOPS Module Development Team
 */

/**
 *
 * PhpZip Class Definition
 *
 */
class PhpZip
{
    var $datasec      = array();
    var $ctrl_dir     = array();
    var $eof_ctrl_dir = "\x50\x4b\x05\x06\x00\x00\x00\x00";
    var $old_offset   = 0;

    /**
     *
     * @param string $dir
     * @param string $zipfilename
     * @return int
     */
    function Zip($dir, $zipfilename)
    {
        $ret = false;
        if (@function_exists('gzcompress')) {
            $curdir = getcwd();
            if (is_array($dir)) {
                $filelist = $dir;
            } else {
                $filelist = $this->getFileList($dir);
            }

            if (!empty($dir) && !is_array($dir) && file_exists($dir)) {
                chdir($dir);
            } else {
                chdir($curdir);
            }

            if (0 < count($filelist)) {
                foreach ($filelist as $filename) {
                    if (is_file($filename)) {
                        $fd = fopen ($filename, "r");
                        $content = fread ($fd, filesize ($filename));
                        fclose ($fd);

                        if (is_array($dir)) {
                            $filename = basename($filename);
                        }
                        $this->addFile($content, $filename);
                    }
                }
                $out = $this->file();

                chdir($curdir);
                $fp = fopen($zipfilename, "w");
                fwrite($fp, $out, strlen($out));
                fclose($fp);
            }
            $ret = true;
        }

        return $ret;
    }

    /**
     *
     * @param string $dir
     * @return string|array
     */
    function getFileList($dir)
    {
        if (file_exists($dir)) {
            $args = func_get_args();
            $pref = $args[1];
            $dh   = opendir($dir);

            while($files = readdir($dh)) {
                if (('.' !== $files) && ('..' !== $files)) {
                    if (is_dir($dir.$files)) {
                        $curdir = getcwd();
                        chdir($dir.$files);
                        $file = array_merge($file, $this->getFileList('', "{$pref}{$files}/"));
                        chdir($curdir);
                    } else {
                        $file[] = $pref . $files;
                    }
                }
            }
            closedir($dh);
        }
        return $file;
    }

    /**
     *
     * @param int $unixtime
     * @return boolean
     */
    function unix2DosTime($unixtime = 0)
    {
        $timearray = ($unixtime == 0) ? getdate() : getdate($unixtime);

        if ($timearray['year'] < 1980) {
            $timearray['year']    = 1980;
            $timearray['mon']     = 1;
            $timearray['mday']    = 1;
            $timearray['hours']   = 0;
            $timearray['minutes'] = 0;
            $timearray['seconds'] = 0;
        } // end if

        return (($timearray['year'] - 1980) << 25) | ($timearray['mon'] << 21) | ($timearray['mday'] << 16) |
               ($timearray['hours'] << 11) | ($timearray['minutes'] << 5) | ($timearray['seconds'] >> 1);
    } // end of the 'unix2DosTime()' method

    /**
     *
     * @param unknown $data
     * @param unknown $name
     * @param number $time
     */
    function addFile($data, $name, $time = 0)
    {
        $name     = str_replace('\\', '/', $name);

        $dtime    = dechex($this->unix2DosTime($time));
        $hexdtime = '\x' . $dtime[6] . $dtime[7]
        . '\x' . $dtime[4] . $dtime[5]
        . '\x' . $dtime[2] . $dtime[3]
        . '\x' . $dtime[0] . $dtime[1];
        eval('$hexdtime = "' . $hexdtime . '";');

        $fr   = "\x50\x4b\x03\x04";
        $fr   .= "\x14\x00";            // ver needed to extract
        $fr   .= "\x00\x00";            // gen purpose bit flag
        $fr   .= "\x08\x00";            // compression method
        $fr   .= $hexdtime;             // last mod time and date
        $unc_len = strlen($data);
        $crc     = crc32($data);
        $zdata   = gzcompress($data);
        $c_len   = strlen($zdata);
        $zdata   = substr(substr($zdata, 0, strlen($zdata) - 4), 2); // fix crc bug
        $fr      .= pack('V', $crc);             // crc32
        $fr      .= pack('V', $c_len);           // compressed filesize
        $fr      .= pack('V', $unc_len);         // uncompressed filesize
        $fr      .= pack('v', strlen($name));    // length of filename
        $fr      .= pack('v', 0);                // extra field length
        $fr      .= $name;
        $fr .= $zdata;
        $fr .= pack('V', $crc);                 // crc32
        $fr .= pack('V', $c_len);               // compressed filesize
        $fr .= pack('V', $unc_len);             // uncompressed filesize
        $this -> datasec[] = $fr;
        $new_offset        = strlen(implode('', $this->datasec));
        $cdrec = "\x50\x4b\x01\x02";
        $cdrec .= "\x00\x00";                // version made by
        $cdrec .= "\x14\x00";                // version needed to extract
        $cdrec .= "\x00\x00";                // gen purpose bit flag
        $cdrec .= "\x08\x00";                // compression method
        $cdrec .= $hexdtime;                 // last mod time & date
        $cdrec .= pack('V', $crc);           // crc32
        $cdrec .= pack('V', $c_len);         // compressed filesize
        $cdrec .= pack('V', $unc_len);       // uncompressed filesize
        $cdrec .= pack('v', strlen($name)); // length of filename
        $cdrec .= pack('v', 0);             // extra field length
        $cdrec .= pack('v', 0);             // file comment length
        $cdrec .= pack('v', 0);             // disk number start
        $cdrec .= pack('v', 0);             // internal file attributes
        $cdrec .= pack('V', 32);            // external file attributes - 'archive' bit set
        $cdrec .= pack('V', $this -> old_offset); // relative offset of local header
        $this -> old_offset = $new_offset;
        $cdrec .= $name;
        $this -> ctrl_dir[] = $cdrec;
    } // end of the 'addFile()' method

    /**
     *
     * @return string
     */
    function file()
    {
        $data    = implode('', $this -> datasec);
        $ctrldir = implode('', $this -> ctrl_dir);

        return $data
             . $ctrldir
             . $this -> eof_ctrl_dir
             . pack('v', sizeof($this -> ctrl_dir))  // total # of entries "on this disk"
             . pack('v', sizeof($this -> ctrl_dir))  // total # of entries overall
             . pack('V', strlen($ctrldir))           // size of central dir
             . pack('V', strlen($data))              // offset to start of central dir
             . "\x00\x00";                           // .zip file comment length
    } // end of the 'file()' method
}
