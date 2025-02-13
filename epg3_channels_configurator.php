<?php
ini_set('memory_limit','256M');   // 512 1024 ?

/*
nano  /etc/php/7.4/apache2/php.ini
memory_limit=256M
/etc/init.d/apache2 restart
*/


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);


date_default_timezone_set('Europe/Paris');

$array_channels_list = array();
$array_channels_list_temp = array();
$array_channels = array();

$url = 'xmltv_fr.xml';
$xml = simplexml_load_file($url);


foreach ( $xml->children() as $child0 ) 
        {
        if ( $child0->getName() == 'channel'  )
           {   
           array_push($array_channels_list_temp ,(string)$child0->attributes()->id);  
           
           //array_push($array_channels    ,"---");         
           array_push($array_channels    ,(string)$child0->attributes()->id);
           
           $temp_DisplayName = 'NoDisplayName';
           $temp_Icon = 'NoIcon';
           
           
           foreach ( $child0->children() as $child )  
                      {                         
                      if ( $child->getName() == 'display-name')     
                         {
                         $temp_DisplayName = (string)$child;                          
                         }   
                          
                      if ( $child->getName() == 'icon')
                         {
                         $temp_Icon = (string)$child->attributes()->src;                                           
                         }                
                      }  
           
           array_push($array_channels,$temp_DisplayName);  
           array_push($array_channels,$temp_Icon);   
           }
        }


/*
// methode 1
$file = 'epg3_channels_all.php';
file_put_contents($file, serialize($array_channels));
$array = unserialize(file_get_contents('epg3_channels_all.php'));
echo '<html>';
echo '<br><br><pre>';
print_r($array);
echo '</pre>'; 
*/

/*

echo '<br><br><pre>';
print_r($array_channels_list);
echo '</pre>';  

echo '<br><br><br><br><pre>';
print_r($array_channels);
echo '</pre>';   

echo '<br><br><br><br>end';
*/

echo '<html><head>';
echo '<script type="text/javascript">'; 
echo 'function AddOption(MyValue)';
echo '{';
echo 'const sel = document.getElementById("mychannels");';
echo 'const opt = document.createElement("option");';
echo 'opt.value = MyValue;';
echo 'opt.text = MyValue;';
echo 'sel.add(opt, null);';
echo 'opt.ondblclick = function() { this.remove(); }';  // alert(MyValue);  //opt.addEventListener("click", suppr, false); suppr is a function
echo '}';


echo 'function MoveUp()';
echo '{';
echo 'var sel   = document.getElementById(\'mychannels\');';
echo 'var index = sel.selectedIndex;';
echo 'if ( index > 0 )';
echo '   {';
echo '   var previousValue         =  sel.options[index-1].text;';
echo '   var selectedValue         =  sel.options[index].text;';
echo '   sel.options[index-1].text = selectedValue;';
echo '   sel.options[index].text   = previousValue;';
echo '   sel.selectedIndex = index-1;';
echo '   }';
echo '}';

echo 'function MoveDown()';
echo '{';
echo 'var sel   = document.getElementById(\'mychannels\');';
echo 'var index = sel.selectedIndex;';
echo 'if ( index < sel.options.length -1 )';
echo '   {';
echo '   var nextValue             =  sel.options[index+1].text;';
echo '   var selectedValue         =  sel.options[index].text;';
echo '   sel.options[index+1].text = selectedValue;';
echo '   sel.options[index].text   = nextValue;';
echo '   sel.selectedIndex = index+1;';
echo '   }';
echo '}';

echo 'function Save1()';
echo '{';
echo 'const temp = [];';
echo 'var sel   = document.getElementById(\'mychannels\');';
echo 'for ( i=0; i< sel.options.length; i++ )';
echo '   {';
echo '   temp.push(sel.options[i].text);';
echo '   }';
echo 'document.getElementById(\'myArray\').innerHTML = JSON.stringify(temp);';  //temp.toString();';
echo 'let posts = JSON.stringify(temp);';
echo 'const url = "epg3_channels_export.php";';
echo 'let xhr = new XMLHttpRequest();';
echo 'xhr.onreadystatechange = function() {
                                           if ( this.readyState == 4 && this.status == 200 ) 
                                               document.getElementById(\'fromphprequest\').innerHTML= this.responseText;                                           
                                           else if ( this.readyState == 4 ) 
                                               alert("xhr.status = "+xhr.status+" this.status = "+this.status);
                                           };';
echo 'xhr.onload = function () { console.log(xhr.status); 
                                 console.log(xhr.response.message); 
                                 document.getElementById(\'status_response\').innerHTML= xhr.status+" "+xhr.response;
                                 };';   
echo 'xhr.open(\'POST\', url, true);';
echo 'xhr.setRequestHeader(\'Content-type\', \'application/json; charset=UTF-8\');';
echo 'xhr.send(posts);';
echo '}';

echo 'function Save2()';
echo '{';
echo 'const temp = [];';
echo 'var sel   = document.getElementById(\'infosChannels\');';
echo 'for ( i=0; i< sel.options.length; i++ )';
echo '   {';
echo '   temp.push(sel.options[i].text);';
echo '   }';
echo 'document.getElementById(\'myArray\').innerHTML = JSON.stringify(temp);';  //temp.toString();';
echo 'let posts = JSON.stringify(temp);';
echo 'const url = "epg3_channels_export2.php";';
echo 'let xhr = new XMLHttpRequest();';
echo 'xhr.onreadystatechange = function() {
                                           if ( this.readyState == 4 && this.status == 200 ) 
                                               document.getElementById(\'fromphprequest\').innerHTML= this.responseText;                                           
                                           else if ( this.readyState == 4 ) 
                                               alert("xhr.status = "+xhr.status+" this.status = "+this.status);
                                           };';
echo 'xhr.onload = function () { console.log(xhr.status); 
                                 console.log(xhr.response.message); 
                                 document.getElementById(\'status_response\').innerHTML= xhr.status+" "+xhr.response;
                                 };';   
echo 'xhr.open(\'POST\', url, true);';
echo 'xhr.setRequestHeader(\'Content-type\', \'application/json; charset=UTF-8\');';
echo 'xhr.send(posts);';
echo '}';

echo '</script>';
echo '</head>';

echo '<body>';
echo '<table cellspacing=100>';
echo '<tr>';

echo '<td>';
echo '<label for="channels-infos">Infos Channels:</label>';
echo '<select id="infosChannels" name="infosChannels" size="30">';
foreach($array_channels as $key => $value):
echo '<option value="'.$value.'" onclick="AddOption(\''.$value.'\')">'.$value.'</option>'; 
endforeach;
echo '</select>';
echo '<br><input type="button" value="Save2" onclick="Save2();">';

echo '<td>';
echo '<label for="channels-select">Add your channels:</label>';
echo '<select name="channels" size="30">';
foreach($array_channels_list_temp as $key => $value):
echo '<option value="'.$value.'" onclick="AddOption(\''.$value.'\')">'.$value.'</option>'; 
endforeach;
echo '</select>';

echo '<td>';
echo '<label for="mychannels-select">My channels:</label>';
echo '<select id="mychannels" name="mychannels" size="30">';
echo '</select>';
echo '<br><input type="button" value="Up" onclick="MoveUp()">';
echo '<br><br><input type="button" value="Down" onclick="MoveDown()">';
echo '<br><br><input type="button" value="Save my channels" onclick="Save1();">';

echo '<td>'; 
echo '<div id="myArray">nothing</div>';
echo '<div id="status_response">nothing</div>';
echo '<div id="fromphprequest">nothing</div>';

echo '</body></html>';


/*

//array_push($array_channels_list,"TF1.fr");
//array_push($array_channels,"13eRue.fr");
//array_push($array_channels,"13ème rue");
//array_push($array_channels,"https://focus.telerama.fr/500x500/0000/00/01/clear-2.png");
//array_push($array_channels,"6ter.fr");
//array_push($array_channels,"6ter");
//array_push($array_channels,"https://www.teleboy.ch/assets/stations/367/icon320_light.png?v2023_48_0");


  <channel id="13eRue.fr">
    <display-name>13ème rue</display-name>
    <icon src="https://focus.telerama.fr/500x500/0000/00/01/clear-2.png"/>
  </channel>  
  
  <programme start="20241126063000 +0100" stop="20241126070000 +0100" channel="01TV.fr">
    <title lang="fr">Culture IA</title>
    <desc lang="fr">Aucune description</desc>
    <category lang="fr">Divertissement</category>
    <icon src="http://static-cdn.tv.sfr.net/data/img/pl/2/0/9/8800902.jpg"/>
    <rating system="CSA">
      <value>Tout public</value>
    </rating>
  </programme>  
*/ 


/*
document.getElementById('mychannels').options.length
document.getElementById('mychannels').length

*/


/*
//echo '   sel.options[sel.selectedIndex].text="test"; ';  // document.getElementById('mychannels').selectedIndex.value;
echo '    ';                                  // document.getElementById('mychannels').options[0].text
*/


/*
echo '<td>';
echo '<input type="button" value="monter" 
onclick="
var selOption = document.getElementById(\'mychannels\').selectedIndex; 
selOption.insertBefore(selOption.prev());
">';
*/

/*
echo '<td>';
echo '<input type="button" value="monter" 
onclick="
var selOption = document.getElementById(\'mychannels\').selectedIndex; 
selOption.insertBefore(selOption.prev());
">';
*/

//<input type="button" onclick="var selOption = document.getElemetById('mychannels').selectedIndex; selOption.insertBefore(selOption.prev());">
//from.options[from.selectedIndex

//document.querySelector("#mychannels option").innerHTML;
//.children("option:selected")
 
//.querySelectorAll('option[value="frog"]');     
//use CSS selector select#animals > option[value="frog"]  
//document.getElementById("MyOption").options[0].innerHTML = "HTMLisNotAProgrammingL";
?>      