function date_time(id)
{
        date = new Date;
        year = date.getFullYear();
        month = date.getMonth();
        months = new Array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'July', 'Aug', 'Sept', 'Oct', 'Nov', 'Dec');
        d = date.getDate();
        day = date.getDay();
        days = new Array('Sun', 'Mon', 'Tues', 'Wed', 'Thur', 'Fri', 'Sat');
        h = date.getHours();
        if(h<10)
        {
                h = "0"+h;
        }
        m = date.getMinutes();
        if(m<10)
        {
                m = "0"+m;
        }
        s = date.getSeconds();
        if(s<10)
        {
                s = "0"+s;
        }
        result = ''+days[day]+', '+months[month]+' '+d+'  @  '+h+':'+m+':'+s;
       
       	//result = ''+days[day]+', '+d+'-'+months[month]+'-'+year+'    '+h+':'+m+':'+s;
        document.getElementById(id).innerHTML = result;
        setTimeout('date_time("'+id+'");','1000');
        return true;
}


// var myVar=setInterval(function(){myTimer()},1000);

// function myTimer() {
//     var d = new Date();
//  
//     //return result;
//     document.getElementById("date_time").innerHTML = d.toLocaleTimeString();
//     
//     
//     return (d.toLocaleTimeString());
//     //document.getElementById("date_time").innerHTML = "ABC";
// }
// 
function loadXMLDoc()
{
var xmlhttp;

alert("abc");
if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
xmlhttp.onreadystatechange=function()
  {
  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
    // document.getElementById("myDiv").innerHTML=xmlhttp.responseText;
    }
  }
xmlhttp.open("GET","timer_ajax.php",true);
xmlhttp.send();
}