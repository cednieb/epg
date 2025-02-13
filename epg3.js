function mouse_position()
    {
    var e = window.event;
    var posX = e.clientX;
    var posY = e.clientY;
    document.Form1.posx.value = posX;
    document.Form1.posy.value = posY;
    var t = setTimeout(mouse_position,100);
    }


function divclick(divId,zchannel,zchannelimgurl,ztime,ztitle,zsubtitle,zdescription,zprogimgurl) 
    {
    // click prog -> infos foreground, click the same prog -> infos background, click infos -> background

    if ( document.getElementById("divforeground").getAttribute("class") == "foreground"+divId  )
       {
       document.getElementById("divforeground").style.visibility= "hidden";
       document.getElementById("divforeground").style.display= "none";  
       //document.getElementById("divforeground").classList.remove("foreground"+divId); 
       document.getElementById("divforeground").setAttribute("class","");
       }  
           
    else
       {
       document.getElementById("divforeground").style.visibility= "visible";
       document.getElementById("divforeground").style.display= "block";
    
       var htmlcode = '<div id="divforegroundchild" style="position:fixed;z-index:1002;border:5px solid #ff0000;background-color:#444444;max-height:1000px;width:500px;left:calc(100% - 600px);top:0;color:#ffffff;padding:10px" onclick="document.getElementById(\'divforeground\').style.visibility=\'hidden\';document.getElementById(\'divforeground\').style.display=\'none\'; document.getElementById(\'divforeground\').setAttribute(\'class\',\'\');"><center><br><h1><b>'+ztitle+'<a class="loupe" href="https://www.allocine.fr/rechercher/?q='+ztitle+'" target=_blank>&nbsp;&#128269;</a></b></h1><br><br><div style="width:60%;background-color:#aaaaaa;border:5px solid #ff0000;"><img height="75px" src="'+zchannelimgurl+'"></div><br><br><h2>'+ztime+'</h2><br><br><img height="150px" src="'+zprogimgurl+'"><br><br><h3>'+zdescription+'</h3></div>';
    
       document.getElementById("divforeground").innerHTML =htmlcode;       
       document.getElementById("divforeground").setAttribute("class","foreground"+divId); // not use classlist.add
       }   

    }
    

function myscroll(arrow,scrollx,y,totalseconds,left0,widthdivfirst,widthdivscroll) 
    {
    if ( y == 0 && arrow != 0 )  // scroll with divs
       {       
       window.scrollBy(parseInt(arrow)*parseInt(scrollx),0);  
       } 
    }