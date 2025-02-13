<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

//$varPost = json_decode($_POST['posts']);

$rawData = file_get_contents("php://input");
//echo '<br><br>'.count($varPost).'<br><br>';

$rawData = '<?php $array_channels=array'.str_replace("]", ")", str_replace("[", "(", $rawData)).';?>';

echo "etape1";
$myfile = fopen("epg3_channels_all.php", "w") or die("Unable to open file!");
echo "etape2";
fwrite($myfile, $rawData );
echo "etape3";
fclose($myfile);
?>