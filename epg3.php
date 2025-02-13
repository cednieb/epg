<?php
ini_set('memory_limit','256M');   // 512 1024 ?
/*nano  /etc/php/7.4/apache2/php.ini   memory_limit=256M   /etc/init.d/apache2 restart  */
/* ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL); error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED); */
date_default_timezone_set('Europe/Paris');
/////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
include ('epg3_channels.php');
include ('epg3_channels_all.php'); //$array_channels = unserialize(file_get_contents('epg3_channels_all.php'));
/////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
$zHtmlCode = "";
$zHtmlCode .= "\r\n"; $zHtmlCode .= '<html>'; 
$zHtmlCode .= "\r\n"; $zHtmlCode .= '<head>'; 
$zHtmlCode .= "\r\n"; $zHtmlCode .= '<META http-equiv=Content-Type content="text/html; charset=UTF-8">'; 
$zHtmlCode .= "\r\n"; $zHtmlCode .= '<meta name="viewport" content="height=device-height,width=device-width,initial-scale=0.55,maximum-scale=1,user-scalable=no"/>'; 
$zHtmlCode .= "\r\n"; $zHtmlCode .= '<link rel="stylesheet" href="epg3.css">'; 
$zHtmlCode .= "\r\n"; $zHtmlCode .= '<script type="text/javascript" src="epg3.js"></script>'; 
$zHtmlCode .= "\r\n"; $zHtmlCode .= '</head>'; 
$zHtmlCode .= "\r\n"; $zHtmlCode .= '<title>&nbsp;|&nbsp;EPG&nbsp;|&nbsp;</title>'; 
$zHtmlCode .= "\r\n"; $zHtmlCode .= '<link rel="icon" href="epg.jpg" />'; 
$zHtmlCode .= "\r\n"; $zHtmlCode .= '<body>'; 
$zHtmlCode .= "\r\n"; $zHtmlCode .= '<div id="verticalLineNow"></div>'; 

$url = 'xmltv_fr.xml';  // https://xmltvfr.fr/xmltv/xmltv_fr.zip
$array_channels_img = array();
$array_programmes_infos = array();
$array_programmes_infos_sorted = array();
$p = 0;
$searchnext = 0;

$lowest_timestamp = 4102441200; //   2100/01/01 00:00:00   
$lowest_time ="";

$highest_timestamp=0;        
$highest_time ="";

$xml = simplexml_load_file($url);

$replacearray = array("\"","'","<",">","...","%","\r","\n");
//$newreplacearray = array(" ",addslashes("'"),addslashes("<"),addslashes(">"),"",addslashes("%"));
$newreplacearray = " ";

$left0=10; // let some space on left

$h0= 52;  //22 let some space on top
$h1= 70;  //70 100  rows height 

$rr=-1;   // increment if the epg exists for the channel

$hClockLine= 22;  // clock line height
$nbRowsBeforeClockLine=12; // each time , a variable = $nbRowsBeforeClockLine, add a clock line
$nbofclocklines=0;  // nb of clock lines added  ( multiply by $hClockLine = new top value)

$widthdivfirst=80;   //80 100 width divfirst
$widthimgchannel=60;  // 80 width img in divfirst
$widthdivscroll=40;  //25 width of the div with function scroll onclick or onmouseover

$leftmenu = $left0+$widthdivfirst+$widthdivscroll; // for fixed menu
$soustractforwidthmenu = $leftmenu+$widthdivscroll;
$borderbottommenu=8; // 
$heightmenu = $h0-$hClockLine;// with border-bottom -> $heightmenu = $h0-$hClockLine-$borderbottommenu;

$secondperpixel = 10;  // default 10 -> 3600s = 360px  // don't forget:   00    00:30  "nameoftheday" 01:00

$later1 = 1;  // move 1 hour later
$later2 = 4;
$later3 = 12;
$laterkeyup = 2;

$earlier1 = -1;  // move 1 hour earlier
$earlier2 = -4;
$earlier3 = -12;
$earlierkeyup = -2;

$now0 = strtotime(date('Y-m-d H:i:s'));

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// parse the tv shows
$tempchannel ='';
foreach ( $xml->children() as $child0 ) 
        {
        if ( $child0->getName() == 'programme' && in_array($child0->attributes()->channel,$array_channels_list) )
           {   
           $date_start = $child0->attributes()->start;
           $date_stop = $child0->attributes()->stop;
           
           $start  = substr($date_start,0,4);
           $start .= '-'.substr($date_start,4,2);
           $start .= '-'.substr($date_start,6,2);
           $start .= ' '.substr($date_start,8,2);
           $start .= ':'.substr($date_start,10,2);
           $start .= ':'.substr($date_start,12,2);
                     
           $stop  = substr($date_stop,0,4);         
           $stop .= '-'.substr($date_stop,4,2);
           $stop .= '-'.substr($date_stop,6,2);
           $stop .= ' '.substr($date_stop,8,2);
           $stop .= ':'.substr($date_stop,10,2);
           $stop .= ':'.substr($date_stop,12,2);  
          
           if ( strtotime($start) > $now0 + 5*12*3600 )    // nb * 12 hours
              $searchnext = 0;
              
          
           // add the following tv shows to the current channel        
           if ( $searchnext != 0 )           
           if ( $array_programmes_infos[$p-1][0] == $child0->attributes()->channel )           
              {
              $majindex = 10*$searchnext;              
              $find_title = 0;
              $find_subtitle = 0;
              $find_desc = 0;
              $find_icon = 0;    
              
              $array_programmes_infos[$p-1][$majindex+3] = substr($start,11,8).' - '.substr($stop,11,8); 
              $array_programmes_infos[$p-1][$majindex+8] = strtotime($start); 
              $array_programmes_infos[$p-1][$majindex+9] = strtotime($stop); 
              
              foreach ( $child0->children() as $child )  
                      {   
                      if ( $child->getName() == 'title')     
                         {
                         $array_programmes_infos[$p-1][$majindex+4] = (string)$child; 
                         $find_title=1;
                         }                       
                      
                      if ( $child->getName() == 'sub-title') 
                         {
                         $array_programmes_infos[$p-1][$majindex+5] = (string)$child; 
                         $find_subtitle=1;
                         } 
                      
                      if ( $child->getName() == 'desc')      
                         {
                         $array_programmes_infos[$p-1][$majindex+6] = (string)$child; 
                         $find_desc=1;
                         } 
                         
                      if ( $child->getName() == 'icon')
                        {
                        $array_programmes_infos[$p-1][$majindex+7] = (string)$child->attributes()->src ;
                        $find_icon=1;                        
                        }  
                      
                     }                    
                     
              if ( $find_title   == 0 ) $array_programmes_infos[$p-1][$majindex+4] = "";
              if ( $find_subtitle == 0 ) $array_programmes_infos[$p-1][$majindex+5] = "";
              if ( $find_desc    == 0 ) $array_programmes_infos[$p-1][$majindex+6] = "";
              if ( $find_icon    == 0 ) $array_programmes_infos[$p-1][$majindex+7] = "";  
              
              if ( strtotime($stop) > $highest_timestamp ) 
                 {
                 $highest_timestamp = strtotime($stop);   
                 $highest_time = $date_stop;                  
                 }  
             
              $searchnext++;              
              }            
           
           // begin to retrieve the infos for the first or next channel
           if ( 
                // get the current tv show 
                ( 
                $tempchannel != $child0->attributes()->channel  
                && 
                //strtotime($start) <= strtotime("now") && strtotime($stop) >= strtotime("now")  
                strtotime($start) <= $now0  && strtotime($stop) >= $now0  
                )     
                          
                ||           
           
                // sometimes ther is no info for the current time, we have to search later in the time 
                (
                $tempchannel != $child0->attributes()->channel  
                && 
                //strtotime($start) >= strtotime("now")  
                strtotime($start) >= $now0                   
                &&
                $array_programmes_infos[$p-1][0] != $child0->attributes()->channel 
                )             
              )   
                                  
              {   
              $tempchannel = $child0->attributes()->channel;               
              $find_title = 0;
              $find_subtitle = 0;
              $find_desc = 0;
              $find_icon = 0;  
              $channelIndex = array_search($child0->attributes()->channel, $array_channels);
              
              $array_programmes_infos[$p][0] = (string)$child0->attributes()->channel;
              $array_programmes_infos[$p][1] = (string)$array_channels[$channelIndex+1]; 
              $array_programmes_infos[$p][2] = (string)$array_channels[$channelIndex+2]; 
              $array_programmes_infos[$p][3] = substr($start,11,8).' - '.substr($stop,11,8);                  
              
              //define the lowest time but not before -1h30 (5400s) or other
              if ( strtotime($stop) - strtotime($start) < 5400 )          
              if ( strtotime($start) < $lowest_timestamp ) 
                 {
                 $lowest_timestamp = strtotime($start);   
                 $lowest_time = $date_start;                  
                 }  
                               
              $array_programmes_infos[$p][8] = strtotime($start); 
              $array_programmes_infos[$p][9] = strtotime($stop);                    
              
              foreach ( $child0->children() as $child )  
                      {   
                      if ( $child->getName() == 'title')     
                         {
                         $array_programmes_infos[$p][4] = (string)$child; 
                         $find_title=1;
                         }                       
                      
                      if ( $child->getName() == 'sub-title') 
                         {
                         $array_programmes_infos[$p][5] = (string)$child; 
                         $find_subtitle=1;
                         } 
                      
                      if ( $child->getName() == 'desc')      
                         {
                         $array_programmes_infos[$p][6] = (string)$child; 
                         $find_desc=1;
                         } 
                         
                      if ( $child->getName() == 'icon')
                        {
                        $array_programmes_infos[$p][7] = (string)$child->attributes()->src;
                        $find_icon=1;
                        }  
                     }                    
                     
              if ( $find_title   == 0 ) $array_programmes_infos[$p][4] = "";
              if ( $find_subtitle == 0 ) $array_programmes_infos[$p][5] = "";
              if ( $find_desc    == 0 ) $array_programmes_infos[$p][6] = "";
              if ( $find_icon    == 0 ) $array_programmes_infos[$p][7] = "";  
                          
              
              $p++; 
              $searchnext =1;                  
              }  
                                            
           }        
        } 
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// sort the tv shows with the same order of the channel list
for ( $l=0; $l<count($array_channels_list); $l++ )
    for ( $k=0; $k<count($array_programmes_infos); $k++ )
        if ( $array_channels_list[$l] == $array_programmes_infos[$k][0] ) 
           $array_programmes_infos_sorted[$l] = $array_programmes_infos[$k];        

for ( $k=0; $k<count($array_programmes_infos_sorted); $k++ )
    if ( !$array_programmes_infos_sorted[$k][0] )
       unset($array_programmes_infos_sorted[$k]);

$temp_sorted = array_values($array_programmes_infos_sorted);
$array_programmes_infos_sorted = $temp_sorted;
           
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
// time to place our html elements
//$zHtmlCode .= "\r\n"; $zHtmlCode .= '<div id="menu" style="border-bottom:'.$borderbottommenu.'px solid #000000;height:'.$heightmenu.'px;width:calc(100% - '.$soustractforwidthmenu.'px);left:'.$leftmenu.'px;">';
$zHtmlCode .= "\r\n"; $zHtmlCode .= '<div id="menu" style="height:'.$heightmenu.'px;width:calc(100% - '.$soustractforwidthmenu.'px);left:'.$leftmenu.'px;">';
$zHtmlCode .= "\r\n"; $zHtmlCode .= '    <div onmouseover="autoscroll();">Now</div>';
$zHtmlCode .= "\r\n"; $zHtmlCode .= '    <div onmouseover="autoscroll21H();">21H</div>';
$zHtmlCode .= "\r\n"; $zHtmlCode .= '    <div>Generate 1</div>';
$zHtmlCode .= "\r\n"; $zHtmlCode .= '    <div>Generate 2</div>';
$zHtmlCode .= "\r\n"; $zHtmlCode .= '</div>';

$zHtmlCode .= "\r\n"; $zHtmlCode .= '<div id="divforeground" style="display:none;visibility:hidden;"></div>'; // div to display showtv infos
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// write the function for the time 
// begin at lowesttime, search the next full hour and make div, repeat with +3600seconds ..
function timeline($y)
       { 
       // need access to global variables
       $lowest_time     = $GLOBALS['lowest_time'];           
       $lowest_timestamp  = $GLOBALS['lowest_timestamp']; 
       $highest_time    = $GLOBALS['highest_time']; 
       $highest_timestamp = $GLOBALS['highest_timestamp']; 
       
       $widthdivscroll   = $GLOBALS['widthdivscroll'];  
       $widthdivfirst    = $GLOBALS['widthdivfirst'];
       
       $hClockLine      = $GLOBALS['hClockLine'];  
       $left0         = $GLOBALS['left0'];  
       $h0         = $GLOBALS['h0'];      
        
       $secondperpixel   = $GLOBALS['secondperpixel'];  
                  
       $localtime_assoc = localtime(time(), true);   
       
       $day_nb = $localtime_assoc['tm_wday'];//https://www.php.net/manual/en/function.localtime.php true for associative array and day of the week, 0 (Sun) to 6 (Sat) 
       $frenchdays = array('Dim','Lun','Mar','Mer','Jeu','Ven','Sam');// array('Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi');
       
       $GLOBALS['zHtmlCode'] .=  '<div onmouseover="autoscroll();"  style="position:absolute;z-index:1001;background-color:black;top:'.$y.'px;left:'.$left0.'px;width:'.$widthdivfirst.'px;height:'.$hClockLine.'px;"></div>'; 

       if ( $y ==0 ) $y = $h0-$hClockLine;


       // first clock
       $firsclockwidth = ((60-(int)substr($lowest_time,10,2))*60)/$secondperpixel;// (60 minutes - x minute) * 60 -> a part of 1 hour       
       $nexthour = (int)substr($lowest_time,8,2) +1;  
             
       if ($nexthour == 24) 
          $nexthour=0;
       
       $newleft0  = $left0+$widthdivfirst+$widthdivscroll;// left marge + iconchannel width + divscroll width  
       
       if ( $firsclockwidth > 40 )
          $GLOBALS['zHtmlCode'] .=  '<div class="clock0" style="top:'.$y.'px;left:'.$newleft0.'px;width:'.$firsclockwidth .'px;height:'.$hClockLine.'px;">'.$frenchdays[$day_nb].' '.$nexthour.':</div>'; 
       else    
          $GLOBALS['zHtmlCode'] .=  '<div class="clock0" style="top:'.$y.'px;left:'.$newleft0.'px;width:'.$firsclockwidth .'px;height:'.$hClockLine.'px;">&nbsp;</div>'; 
       
       // next clocks
       $clocklength = floor(($highest_timestamp-$lowest_timestamp)/3600);// find the number of hours for the next loop
       for ($h=0; $h<$clocklength; $h++)
           {              
           $nexthour++;
           $newleft = $left0+$widthdivfirst+$widthdivscroll+$firsclockwidth+3600*$h/$secondperpixel;// 360px = 1 hour
                      
           $previoushour = $nexthour-1;
           if ($previoushour == -1 )
              $previoushour =23;
              
           if ($previoushour < 10 ) 
              $previoushour = '0'.$previoushour;          
           
           if ($nexthour == 24) 
              {
              $nexthour=0;  
              $day_nb++; // 24h = 1 day   
              if ( $day_nb == 7 ) $day_nb = 0;       
              }
               
           if ($nexthour < 10)  
              $nexthour = '0'.$nexthour;   
              
           $widthonehour = 3600/$secondperpixel;
           $GLOBALS['zHtmlCode'] .= '<div class="clock" style="top:'.$y.'px;left:'.$newleft.'px;width:'.$widthonehour.'px;height:'.$hClockLine.'px;">';   // 360px = 1 hour
           $GLOBALS['zHtmlCode'] .= '<span class="spanleft">00</span>';// span minutes
           $GLOBALS['zHtmlCode'] .= '<span class="spanmiddle">'.$previoushour.':30</span>';// span hour + 30 minutes    
           $GLOBALS['zHtmlCode'] .= '<span class="spanright">'.$frenchdays[$day_nb].' '.$nexthour.':</span></div>';// span hour
           }  
       }
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

timeline(0); // first clock

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// add the epg
// 0 "urlchannel" 1 channel  2 imgchannel  3 time from/to  4 title  5 subtitle  6 description  7 imgprog 8 start  9 stop
// 13 time from/to 14 title  15 subtitle  16 description  17 imgprog 18 start  19 stop
// 23 time from/to 24 title  ...
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$divId = 0;
for ( $r=0; $r<count($array_programmes_infos_sorted,0); $r++ )  
    {   
    $rr++;  //  progs found, increment rr    
           
    if ( $rr%$nbRowsBeforeClockLine == 0 && $rr != 0) $nbofclocklines++;  // if rr = some value (use modulo), we increment to add a clock line   
    
    $top = $h0 +$h1*$rr +$hClockLine*$nbofclocklines; // top = h0 + h1*nbchannels + hclock*nbclockline
    
    if ($rr%$nbRowsBeforeClockLine == 0 && $rr != 0) timeline($top-$hClockLine); //
                     
    $totalwidth=$left0;             

    $zHtmlCode .= "\r\n"; $zHtmlCode .= '<div class="divfirst" style="top:'.$top.'px;left:'.$totalwidth.'px;width:'.$widthdivfirst.'px;height:'.$h1.'px">';  
    $zHtmlCode .= "\r\n"; $zHtmlCode .= '<img style="margin:0 auto;" width="'.$widthimgchannel.'px" src="'.$array_programmes_infos_sorted[$r][2].'" title="'.$array_programmes_infos_sorted[$r][1].'"></div>'; 
           
    $totalwidth += $widthdivfirst;  
    $totalwidth += $widthdivscroll;   
    
    $width=0;
    if ( $array_programmes_infos_sorted[$r][8] - $lowest_timestamp > 0 ) // add a gray box
       {
       $width = (int)(  ( $array_programmes_infos_sorted[$r][8] - $lowest_timestamp )/$secondperpixel );
       $zHtmlCode .= "\r\n"; $zHtmlCode .= '<div class="divblank" style="top:'.$top.'px;left:'.$totalwidth.'px;width:'.$width.'px;height:'.$h1.'px">';
       //$zHtmlCode .= "\r\n"; $zHtmlCode .= $width;  
       $zHtmlCode .= "\r\n"; $zHtmlCode .= '</div>';   
       $totalwidth += $width;  
       }   
    
       
    if ( $array_programmes_infos_sorted[$r][8] - $lowest_timestamp < 0 ) // reduce the first tv shows
       $array_programmes_infos_sorted[$r][8] = $lowest_timestamp;
    
    $nbtvshows = (count($array_programmes_infos_sorted[$r])-10)/7+1; // -10 +1 because the [0->9] for the first tv show
    
    for ( $l=0; $l<$nbtvshows; $l++)  // 1-> +10  2-> +20  3-> +30 ...
        {  
        $divId += 1;
        $width = (int)(   ($array_programmes_infos_sorted[$r][$l*10+9]-$array_programmes_infos_sorted[$r][$l*10+8]) /$secondperpixel  ); 
        $left = $left0+$widthdivfirst+$widthdivscroll+($array_programmes_infos_sorted[$r][$l*10+8] - $lowest_timestamp)/$secondperpixel; 
        
        if ( $width >= 150 )
           {
           $zHtmlCode .= "\r\n"; $zHtmlCode .= '<div id="prog'.$divId.'" class="divprog" style="top:'.$top.'px;left:'.$left.'px;width:'.$width.'px;height:'.$h1.'px" ';          

           $zHtmlCode .= "\r\n"; $zHtmlCode .= 'onclick="divclick(   
            '.$divId.'
           ,\''.str_replace($replacearray,$newreplacearray,$array_programmes_infos_sorted[$r][1]).'\'
           ,\''.$array_programmes_infos_sorted[$r][2].'\'
           ,\''.str_replace($replacearray,$newreplacearray,$array_programmes_infos_sorted[$r][$l*10+3]).'\'
           ,\''.str_replace($replacearray,$newreplacearray,$array_programmes_infos_sorted[$r][$l*10+4]).'\'
           ,\''.str_replace($replacearray,$newreplacearray,$array_programmes_infos_sorted[$r][$l*10+5]).'\'
           ,\''.str_replace($replacearray,$newreplacearray,$array_programmes_infos_sorted[$r][$l*10+6]).'\'
           ,\''.$array_programmes_infos_sorted[$r][$l*10+7].'\'
           );"';
            
           // function divclick(divId,zchannel,zchannelimgurl,ztime,ztitle,zsubtitle,zdescription,zprogimgurl)  
                      

           
          //no mouseout because the divforeground can be over the mouse and never be displayed -> change the position of the divforeground
          //$zHtmlCode .= "\r\n"; $zHtmlCode .= 'onmouseout="document.getElementById(\'divforeground\').style.visibility=\'hidden\';  
          //document.getElementById(\'divforeground\').style.display=\'none\';" 
          
      
          $zHtmlCode .= "\r\n"; $zHtmlCode .= ' title=" 
          &#10;'.str_replace($replacearray,$newreplacearray,$array_programmes_infos_sorted[$r][$l*10+3]).'
          &#10;'.str_replace($replacearray,$newreplacearray,$array_programmes_infos_sorted[$r][$l*10+4]).'
          &#10;'.str_replace($replacearray,$newreplacearray,$array_programmes_infos_sorted[$r][$l*10+5]).'
          &#10;'.str_replace($replacearray,$newreplacearray,$array_programmes_infos_sorted[$r][$l*10+6]).'"';  
        
          
          $zHtmlCode .= "\r\n"; $zHtmlCode .= '>';  
          $zHtmlCode .= "\r\n"; $zHtmlCode .= '<p>'.$array_programmes_infos_sorted[$r][$l*10+3].'</p>';            
          $zHtmlCode .= "\r\n"; $zHtmlCode .= '<p>'.$array_programmes_infos_sorted[$r][$l*10+4].'</p>';            
          //$zHtmlCode .= "\r\n"; $zHtmlCode .= '<p>'.$array_programmes_infos_sorted[$r][$l*10+5].'</p>';    
          $zHtmlCode .= "\r\n"; $zHtmlCode .= '</div>';    
          }  
          
       else 
          {
          $zHtmlCode .= "\r\n"; $zHtmlCode .= '<div class="divprog" id="prog'.$divId.'" class="divprog" style="top:'.$top.'px;left:'.$left.'px;width:'.$width.'px;height:'.$h1.'px" ';
          
          // onmouseover
          $zHtmlCode .= "\r\n"; $zHtmlCode .= 'onclick="divclick(    
           '.$divId.'
          ,\''.str_replace($replacearray,$newreplacearray,$array_programmes_infos_sorted[$r][1]).'\'
          ,\''.$array_programmes_infos_sorted[$r][2].'\'
          ,\''.str_replace($replacearray,$newreplacearray,$array_programmes_infos_sorted[$r][$l*10+3]).'\'  
          ,\''.str_replace($replacearray,$newreplacearray,$array_programmes_infos_sorted[$r][$l*10+4]).'\'  
          ,\''.str_replace($replacearray,$newreplacearray,$array_programmes_infos_sorted[$r][$l*10+5]).'\'  
          ,\''.str_replace($replacearray,$newreplacearray,$array_programmes_infos_sorted[$r][$l*10+6]).'\'  
          ,\''.$array_programmes_infos_sorted[$r][$l*10+7].'\'                         
          );"';   
                  
          $zHtmlCode .= "\r\n"; $zHtmlCode .= ' title=" 
          &#10;'.str_replace($replacearray,$newreplacearray,$array_programmes_infos_sorted[$r][$l*10+3]).'
          &#10;'.str_replace($replacearray,$newreplacearray,$array_programmes_infos_sorted[$r][$l*10+4]).'
          &#10;'.str_replace($replacearray,$newreplacearray,$array_programmes_infos_sorted[$r][$l*10+5]).'
          &#10;'.str_replace($replacearray,$newreplacearray,$array_programmes_infos_sorted[$r][$l*10+6]).' 
          "></div>';             
            
          //$zHtmlCode .= "\r\n"; $zHtmlCode .= '><span class="tooltiptext">123456</span></div>';   
          }
          
        $totalwidth += $width;
        }              
    
    }  

$widthdivfake = $left0+$widthdivfirst;
// fake div on the left to hide the progs/clocks when scroll
$zHtmlCode .= "\r\n"; $zHtmlCode .= '<div  style="background-color:#000000;position:fixed;left:0px;width:'.$widthdivfake.'px;top:0px;height:100%;z-index:996;"></div>';     
//onmouseover="autoscroll();"

// add several divs with colored arrows to scroll horizontaly 0.5X, 1X or 2X
$leftdivsidearrow = $left0+$widthdivfirst;
$hour_in_pixel = 3600/$secondperpixel;

// left divs to scroll horizontaly
$zHtmlCode .= "\r\n"; $zHtmlCode .= '<div class="divsidearrow" style="left:'.$leftdivsidearrow.'px;width:'.$widthdivscroll.'px;">'; 
$zHtmlCode .= "\r\n"; $zHtmlCode .= '   <div class="arrowsempty"></div>';  
$zHtmlCode .= "\r\n"; $zHtmlCode .= '   <div class="arrowstwo_left"
         onmouseover="myscroll('.$earlier3.','.$hour_in_pixel.', 0,'.$highest_timestamp-$lowest_timestamp.','.$left0.','.$widthdivfirst.','. $widthdivscroll.');"
         onclick=    "myscroll('.$earlier3.','.$hour_in_pixel.', 0,'.$highest_timestamp-$lowest_timestamp.','.$left0.','.$widthdivfirst.','. $widthdivscroll.');" 
         ></div>'; 
$zHtmlCode .= "\r\n"; $zHtmlCode .= '   <div class="arrowsempty"></div>';  
$zHtmlCode .= "\r\n"; $zHtmlCode .= '   <div class="arrowsone_left"
         onmouseover="myscroll('.$earlier2.','.$hour_in_pixel.', 0,'.$highest_timestamp-$lowest_timestamp.','.$left0.','.$widthdivfirst.','. $widthdivscroll.');"
         onclick=    "myscroll('.$earlier2.','.$hour_in_pixel.', 0,'.$highest_timestamp-$lowest_timestamp.','.$left0.','.$widthdivfirst.','. $widthdivscroll.');" 
         ></div>'; 
$zHtmlCode .= "\r\n"; $zHtmlCode .= '   <div class="arrowsempty"></div>';      
$zHtmlCode .= "\r\n"; $zHtmlCode .= '   <div class="arrowshalf_left"
         onmouseover="myscroll('.$earlier1.','.$hour_in_pixel.', 0,'.$highest_timestamp-$lowest_timestamp.','.$left0.','.$widthdivfirst.','. $widthdivscroll.');"
         onclick=    "myscroll('.$earlier1.','.$hour_in_pixel.', 0,'.$highest_timestamp-$lowest_timestamp.','.$left0.','.$widthdivfirst.','. $widthdivscroll.');" 
         ></div>';
$zHtmlCode .= "\r\n"; $zHtmlCode .= '   <div class="arrowsempty"></div>';  
$zHtmlCode .= "\r\n"; $zHtmlCode .= '   <div class="arrowsone_left"
         onmouseover="myscroll('.$earlier2.','.$hour_in_pixel.', 0,'.$highest_timestamp-$lowest_timestamp.','.$left0.','.$widthdivfirst.','. $widthdivscroll.');"
         onclick=    "myscroll('.$earlier2.','.$hour_in_pixel.', 0,'.$highest_timestamp-$lowest_timestamp.','.$left0.','.$widthdivfirst.','. $widthdivscroll.');" 
         ></div>'; 
$zHtmlCode .= "\r\n"; $zHtmlCode .= '   <div class="arrowsempty"></div>';  
$zHtmlCode .= "\r\n"; $zHtmlCode .= '   <div class="arrowstwo_left"
         onmouseover="myscroll('.$earlier3.','.$hour_in_pixel.', 0,'.$highest_timestamp-$lowest_timestamp.','.$left0.','.$widthdivfirst.','. $widthdivscroll.');"
         onclick=    "myscroll('.$earlier3.','.$hour_in_pixel.', 0,'.$highest_timestamp-$lowest_timestamp.','.$left0.','.$widthdivfirst.','. $widthdivscroll.');" 
         ></div>'; 
$zHtmlCode .= "\r\n"; $zHtmlCode .= '   <div class="arrowsempty"></div>';    
$zHtmlCode .= "\r\n"; $zHtmlCode .= '</div>'; 
    
//right divs to scroll horizontaly
$zHtmlCode .= "\r\n"; $zHtmlCode .= '<div class="divsidearrow" style="right:0px; width:'.$widthdivscroll.'px;">';   
$zHtmlCode .= "\r\n"; $zHtmlCode .= '   <div class="arrowsempty"></div>';  
$zHtmlCode .= "\r\n"; $zHtmlCode .= '   <div class="arrowstwo_right"
         onmouseover="myscroll('.$later3.','.$hour_in_pixel.', 0,'.$highest_timestamp-$lowest_timestamp.','.$left0.','.$widthdivfirst.','. $widthdivscroll.');"
         onclick=    "myscroll('.$later3.','.$hour_in_pixel.', 0,'.$highest_timestamp-$lowest_timestamp.','.$left0.','.$widthdivfirst.','. $widthdivscroll.');" 
         ></div>';         
$zHtmlCode .= "\r\n"; $zHtmlCode .= '   <div class="arrowsempty"></div>';  
$zHtmlCode .= "\r\n"; $zHtmlCode .= '   <div class="arrowsone_right"
         onmouseover="myscroll('.$later2.','.$hour_in_pixel.', 0,'.$highest_timestamp-$lowest_timestamp.','.$left0.','.$widthdivfirst.','. $widthdivscroll.');"
         onclick=    "myscroll('.$later2.','.$hour_in_pixel.', 0,'.$highest_timestamp-$lowest_timestamp.','.$left0.','.$widthdivfirst.','. $widthdivscroll.');" 
         ></div>'; 
$zHtmlCode .= "\r\n"; $zHtmlCode .= '   <div class="arrowsempty"></div>';      
$zHtmlCode .= "\r\n"; $zHtmlCode .= '   <div class="arrowshalf_right"
         onmouseover="myscroll('.$later1.','.$hour_in_pixel.', 0,'.$highest_timestamp-$lowest_timestamp.','.$left0.','.$widthdivfirst.','. $widthdivscroll.');"
         onclick=    "myscroll('.$later1.','.$hour_in_pixel.', 0,'.$highest_timestamp-$lowest_timestamp.','.$left0.','.$widthdivfirst.','. $widthdivscroll.');" 
         ></div>'; 
$zHtmlCode .= "\r\n"; $zHtmlCode .= '   <div class="arrowsempty"></div>';  
$zHtmlCode .= "\r\n"; $zHtmlCode .= '   <div class="arrowsone_right"
         onmouseover="myscroll('.$later2.','.$hour_in_pixel.', 0,'.$highest_timestamp-$lowest_timestamp.','.$left0.','.$widthdivfirst.','. $widthdivscroll.');"
         onclick=    "myscroll('.$later2.','.$hour_in_pixel.', 0,'.$highest_timestamp-$lowest_timestamp.','.$left0.','.$widthdivfirst.','. $widthdivscroll.');" 
         ></div>'; 
$zHtmlCode .= "\r\n"; $zHtmlCode .= '   <div class="arrowsempty"></div>';  
$zHtmlCode .= "\r\n"; $zHtmlCode .= '   <div class="arrowstwo_right"
         onmouseover="myscroll('.$later3.','.$hour_in_pixel.', 0,'.$highest_timestamp-$lowest_timestamp.','.$left0.','.$widthdivfirst.','. $widthdivscroll.');"
         onclick=    "myscroll('.$later3.','.$hour_in_pixel.', 0,'.$highest_timestamp-$lowest_timestamp.','.$left0.','.$widthdivfirst.','. $widthdivscroll.');" 
         ></div>'; 
$zHtmlCode .= "\r\n"; $zHtmlCode .= '   <div class="arrowsempty"></div>';    
$zHtmlCode .= "\r\n"; $zHtmlCode .= '</div>';   

// Javascript declare some variables, modify divsidearrow style, add listener for scroll/scrollend, timer 15 minutes move to now
$zHtmlCode .= "\r\n"; $zHtmlCode .= '<script type="text/javascript">'; 

$zHtmlCode .= "\r\n"; $zHtmlCode .= 'var time0 = '.$now0.';'; // timestamp in s
$zHtmlCode .= "\r\n"; $zHtmlCode .= 'var secondperpixel = '.$secondperpixel.';';

$zHtmlCode .= "\r\n"; $zHtmlCode .= 'function autoscroll()';  // scroll the windows to now
$zHtmlCode .= "\r\n"; $zHtmlCode .= '   {';
// V1
//$zHtmlCode .= "\r\n"; $zHtmlCode .= '   var myscrollX = window.scrollX;';     
//$zHtmlCode .= "\r\n"; $zHtmlCode .= '   var timedelta = Date.now()/1000 - time0;';  // timestamp in ms / 1000 - time in s
//$zHtmlCode .= "\r\n"; $zHtmlCode .= '   var nbpixels = timedelta/secondperpixel-myscrollX;';
//$zHtmlCode .= "\r\n"; $zHtmlCode .= '   window.scrollTo(nbpixels,0);'; // move to a specific position (now)
$zHtmlCode .= "\r\n"; $zHtmlCode .= '   var myscrollX = window.scrollX;';   
$zHtmlCode .= "\r\n"; $zHtmlCode .= '   var myscrollY = window.scrollY;';     
$zHtmlCode .= "\r\n"; $zHtmlCode .= '   var timedelta = Date.now()/1000 - time0;';  // timestamp in ms / 1000 - time in s
$zHtmlCode .= "\r\n"; $zHtmlCode .= '   var nbpixels = timedelta/secondperpixel;';
$zHtmlCode .= "\r\n"; $zHtmlCode .= '   window.scrollTo(nbpixels,myscrollY);'; // move to a specific position (now)

$zHtmlCode .= "\r\n"; $zHtmlCode .= '   var lowest_timestamp = '.$lowest_timestamp.';'; 
$zHtmlCode .= "\r\n"; $zHtmlCode .= '   var left0 = '.$left0.';'; 
$zHtmlCode .= "\r\n"; $zHtmlCode .= '   var widthdivfirst = '.$widthdivfirst.';'; 
$zHtmlCode .= "\r\n"; $zHtmlCode .= '   var widthdivscroll = '.$widthdivscroll.';'; 
$zHtmlCode .= "\r\n"; $zHtmlCode .='    var temp = ( ( (Date.now()/1000)- lowest_timestamp)/secondperpixel)+widthdivfirst+widthdivscroll;'; // +left0
$zHtmlCode .= "\r\n"; $zHtmlCode .= '   document.getElementById("verticalLineNow").style.left = temp;';

// find document height https://stackoverflow.com/a/14744331
$zHtmlCode .= "\r\n"; $zHtmlCode .= '   var B = document.body, H = document.documentElement, Height;';
$zHtmlCode .= "\r\n"; $zHtmlCode .= '   if (typeof document.height !== \'undefined\')';
$zHtmlCode .= "\r\n"; $zHtmlCode .= '      {'; 
$zHtmlCode .= "\r\n"; $zHtmlCode .= '      Height = document.height'; 
$zHtmlCode .= "\r\n"; $zHtmlCode .= '      } '; // For webkit browsers
$zHtmlCode .= "\r\n"; $zHtmlCode .= '   else'; 
$zHtmlCode .= "\r\n"; $zHtmlCode .= '      {'; 
$zHtmlCode .= "\r\n"; $zHtmlCode .= '      Height = Math.max( B.scrollHeight, B.offsetHeight,H.clientHeight, H.scrollHeight, H.offsetHeight );';
$zHtmlCode .= "\r\n"; $zHtmlCode .= '      }';
$zHtmlCode .= "\r\n"; $zHtmlCode .= '   document.getElementById("verticalLineNow").style.height = Height+\'px\';';

//$zHtmlCode .= "\r\n"; $zHtmlCode .='    console.log("myscrollX= "+myscrollX );';
//$zHtmlCode .= "\r\n"; $zHtmlCode .='    console.log("nbpixels= "+nbpixels );';
//$zHtmlCode .= "\r\n"; $zHtmlCode .='    console.log("Date.now()= "+Date.now() );';
//$zHtmlCode .= "\r\n"; $zHtmlCode .='    console.log("lowest_timestamp= "+lowest_timestamp );';
//$zHtmlCode .= "\r\n"; $zHtmlCode .='    console.log("temp = "+ temp );';
$zHtmlCode .= "\r\n"; $zHtmlCode .= '   }';

$zHtmlCode .= "\r\n"; $zHtmlCode .= 'function autoscroll21H()';  // scroll the windows to now
$zHtmlCode .= "\r\n"; $zHtmlCode .= '   {';
$zHtmlCode .= "\r\n"; $zHtmlCode .= '   var myscrollY = window.scrollY;'; 
$zHtmlCode .= "\r\n"; $zHtmlCode .= '   const nonow = new Date(Date.now());';    
$zHtmlCode .= "\r\n"; $zHtmlCode .= '   var timedelta = nonow.setHours(20,30,00,00)/1000 - time0;';  // timestamp in ms / 1000 - time in s
$zHtmlCode .= "\r\n"; $zHtmlCode .= '   var nbpixels = timedelta/secondperpixel;';
$zHtmlCode .= "\r\n"; $zHtmlCode .= '   window.scrollTo(nbpixels,myscrollY);'; // move to a specific position (now)
$zHtmlCode .= "\r\n"; $zHtmlCode .= '   }';


$zHtmlCode .= "\r\n"; $zHtmlCode .= 'setInterval(autoscroll, 900000);'; // delay in ms 900.000 = 15 minutes

$zHtmlCode .= "\r\n"; $zHtmlCode .= 'var lastKnownScrollPositionY = window.scrollY;';    

$zHtmlCode .= "\r\n"; $zHtmlCode .= 'var clHeight = document.getElementsByTagName(\'html\')[0].clientHeight;';

$zHtmlCode .= "\r\n"; $zHtmlCode .= 'var divside = document.getElementsByClassName(\'divsidearrow\');';  // set the height with client height not 100% because fixed
$zHtmlCode .= "\r\n"; $zHtmlCode .= 'for ( var i=0; i<divside.length; i++ ) divside[i].style.height=clHeight+\'px\';';

$zHtmlCode .= "\r\n"; $zHtmlCode .= 'document.addEventListener("scrollend", (event) => {'; // listen scroll   end
$zHtmlCode .= "\r\n"; $zHtmlCode .= 'lastKnownScrollPositionX = window.scrollX;';   
$zHtmlCode .= "\r\n"; $zHtmlCode .= 'myscroll(0,lastKnownScrollPositionX,0,'.$highest_timestamp-$lowest_timestamp.','.$left0.','.$widthdivfirst.','.$widthdivscroll.' );';  
$zHtmlCode .= "\r\n"; $zHtmlCode .= 'var lefttemp = lastKnownScrollPositionX + '.$left0.';';    
$zHtmlCode .= "\r\n"; $zHtmlCode .= 'var icons = document.getElementsByClassName(\'divfirst\');';
$zHtmlCode .= "\r\n"; $zHtmlCode .= 'for ( var i=0; i<icons.length; i++ ) {icons[i].style.display="block";icons[i].style.left = lefttemp+"px";}';
$zHtmlCode .= "\r\n"; $zHtmlCode .= '});'; 

$zHtmlCode .= "\r\n"; $zHtmlCode .= 'document.addEventListener("scroll", (event) => {';  // listen scroll
$zHtmlCode .= "\r\n"; $zHtmlCode .= 'if ( lastKnownScrollPositionY == window.scrollY)';  // mean scroll horizontaly
$zHtmlCode .= "\r\n"; $zHtmlCode .= '{'; 
$zHtmlCode .= "\r\n"; $zHtmlCode .= 'var icons = document.getElementsByClassName(\'divfirst\');';
$zHtmlCode .= "\r\n"; $zHtmlCode .= 'for ( var i=0; i<icons.length; i++ ) icons[i].style.display="none";';
$zHtmlCode .= "\r\n"; $zHtmlCode .= '}'; 
$zHtmlCode .= "\r\n"; $zHtmlCode .= 'else lastKnownScrollPositionY = window.scrollY;';
$zHtmlCode .= "\r\n"; $zHtmlCode .= '});'; 

$zHtmlCode .= "\r\n"; $zHtmlCode .= 'window.addEventListener("resize", (event) => {';  // listen resize
$zHtmlCode .= "\r\n"; $zHtmlCode .=  'window.scrollTo(window.scrollX,window.scrollY);';
$zHtmlCode .= "\r\n"; $zHtmlCode .= '});'; 

$zHtmlCode .= "\r\n"; $zHtmlCode .= 'window.addEventListener("keyup", (event) => {';  // listen keyup arrow keys
$zHtmlCode .= "\r\n"; $zHtmlCode .=  'if ( event.code === "ArrowLeft")  myscroll('.$earlierkeyup .','.$hour_in_pixel.', 0,'.$highest_timestamp-$lowest_timestamp.','.$left0.','.$widthdivfirst.','. $widthdivscroll.'); ';
$zHtmlCode .= "\r\n"; $zHtmlCode .=  'if ( event.code === "ArrowRight")  myscroll('.$laterkeyup .','.$hour_in_pixel.', 0,'.$highest_timestamp-$lowest_timestamp.','.$left0.','.$widthdivfirst.','. $widthdivscroll.'); ';
$zHtmlCode .= "\r\n"; $zHtmlCode .=  'if ( event.code === "ArrowUp")     window.scrollBy( 0, -clHeight*3/4);';  
$zHtmlCode .= "\r\n"; $zHtmlCode .=  'if ( event.code === "ArrowDown")   window.scrollBy( 0, clHeight*3/4); ';  
$zHtmlCode .= "\r\n"; $zHtmlCode .= '});'; 

$zHtmlCode .= "\r\n"; $zHtmlCode .= 'let wheelEventEndTimeout = null;';  // listen mousee wheel
$zHtmlCode .= "\r\n"; $zHtmlCode .= 'window.addEventListener("wheel", (event) => {'; 
$zHtmlCode .= "\r\n"; $zHtmlCode .= 'document.getElementsByTagName(\'body\')[0].style.overflowY = "visible";';   
$zHtmlCode .= "\r\n"; $zHtmlCode .= 'clearTimeout(wheelEventEndTimeout);'; 
$zHtmlCode .= "\r\n"; $zHtmlCode .= 'wheelEventEndTimeout = setTimeout(() => { document.body.style.overflowY = "hidden"; }, 2000);'; 
$zHtmlCode .= "\r\n"; $zHtmlCode .= '});'; 

$zHtmlCode .= "\r\n"; $zHtmlCode .= 'window.addEventListener(\'load\', (event) => { autoscroll(); });'; //console.log(\'page is fully loaded\');

$zHtmlCode .= "\r\n"; $zHtmlCode .= '</script>'; 

$zHtmlCode .= "\r\n"; $zHtmlCode .= '</body>'; 
$zHtmlCode .= "\r\n"; $zHtmlCode .= '</html>'; 

$myfile = fopen("epg3.html", "w") or die("Unable to open file!");
fwrite($myfile, $zHtmlCode );
fclose($myfile);
?>