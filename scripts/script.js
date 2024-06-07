/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var intervalTimer;

window.onload = function(){
    intervalTimer = setInterval(setTime, 1000);
    // var isNeg = true;
    // if (offset < 0){
    //     isNeg = false;
    //     offset = offset * -1;
    // }
    // var offsetHours = offset/60
    // var offsetMinutes = offset % 60;
    // strOffset = (isNeg?"-":"") + pad(offsetHours,2) + ":" + pad(offsetMinutes,2);
    
}

function getXMLHTTPRequest(){
        var xh;
        try{
            xh = new XMLHttpRequest();
        }catch (error){
            try{
                xh = new ActiveXObject("Msxml2.XMLHTTP");
            }catch (error){
                try{
                    xh = new ActiveXObject("Microsoft.XMLHTTP");
                }catch (error){
                    alert("We ran into an error is your browser out of date?");
                    return false;
                }
            }
        }
        return xh;
}//end getXMLHTTP Request
function sendRequest(request, postVars, completionHandler = null){
     var xh = getXMLHTTPRequest();
    xh.onreadystatechange = function(){
        if (xh.readyState == 4){
            if (completionHandler){
                completionHandler(xh.responseText);
            }            
        }//end readystate
    }//end onreadystatechange
    postVars = "RQ=" + request + "&" + postVars;
    xh.open("POST", "./util/request.php", true);
    xh.setRequestHeader("Content-type", "application/x-www-form-urlencoded; charset=UTF-8");
    // xh.setRequestHeader("Content-length", "" + postVars.length);
    // xh.setRequestHeader("Connection", "close");
    
    xh.send(postVars);
}
function setTime(){
    var time = new Date();
    var strTime = time.toLocaleTimeString();
    document.getElementById("time").innerHTML = strTime;
}