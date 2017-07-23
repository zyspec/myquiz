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

include_once("admin_header.php");
xoops_cp_header();
include ("admin_menutab.php");
$upload_dir = "".XOOPS_ROOT_PATH."/modules/myquiz/import/";

      if(isset($_POST['submit'])){
      $numfilesuploaded = $_POST['numuploads'];
      $count = 1;
	  
 
          while ($count <= $numfilesuploaded)
          {
                  $conname = "new_file".$count;

                  $filetype = $_FILES[$conname]['type'];
                 
                  $filename = $_FILES[$conname]['name'];
                  if ($filename != '')
                  {
				  $limitedext = array(".csv",".txt");
				  $ext = strrchr($filename,'.');
                    if (in_array(strtolower($ext),$limitedext))
                    {
                        $maxfilesize = $_POST['maxsize'];
                        $filesize = $_FILES[$conname]['size'];
                        if($filesize <= $maxfilesize )
                        {
                              $randomdigit = rand(0000,9999);
                             $filename = str_replace(' ', '_', $filename);
                              $newfilename = $randomdigit."_".$filename;
                              $source = $_FILES[$conname]['tmp_name'];
                              $target = $upload_dir.$newfilename;
                              move_uploaded_file($source, $target);
                              echo $count." File uploaded | ";
                      
                        
                        }
                        else
                        {
                            echo $count." File is too big! 100kb limit! |";
                        
                        }
                    }
                    else
                    {
                        echo " The file is not a supported type |";
                    }
                  }
          $count = $count + 1;
          }
      
      }
?>
<html>
<?php
    $numuploads = 3;
    $count = 1;
?>

<center><b>csv,txt File Upload</b></center>
<center>
<form action="<?php echo $_server['php-self'];  ?>" method="post" enctype="multipart/form-data" id="something" class="uniForm">
<table class='table-cev'><tr><td align="center">
<?php
      while ($count <= $numuploads)
      {

?>
      <input name="new_file<?php echo $count; ?>" id="new_file<?php echo $count; ?>" size="30" type="file" class="fileUpload" /><br />
      <?php
            $count = $count + 1;
      }
?>
      <input type = "hidden" name="maxsize" value = "102400">
       <input type = "hidden" name="numuploads" value = "<?php echo $numuploads; ?>">
      <br />
      <button name="submit" type="submit" class="submitButton">Upload Files</button>

</form><br /></center></td></tr></table>
</html>
<?php
echo "<br /><font color='#993300'>"._MYQUIZ_CHMOD."</font>";
xoops_cp_footer();
?>