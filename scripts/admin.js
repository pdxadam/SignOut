/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var prev = window.onload;

window.onload = function(){
    if(prev){
        prev();
    }
    assignHandlers();
}
function toggleHideButton(){
    var hideButton = document.getElementById("hideButton");
    var chkHide = document.getElementById("chkHideNames");
    hideButton.innerHTML = chkHide.checked?"Hide names":"Show Names";
    
}
function assignHandlers(){
    var signoutRows = document.getElementsByClassName("pastSignouts");
    
    for(var s of signoutRows){
        s.addEventListener("click", openCover);
    }
    var tabs = document.getElementsByClassName("tabTitle");
    for (var t of tabs){
        t.addEventListener("click", swapTab);
    }
}
function closeCover(){
    var el = document.getElementsByClassName("cover")[0];
    el.style.opacity = 0;
    el.style.pointerEvents = "none";
}
function openCover(){
    var pk = this.dataset.pk;
    sendRequest("getSignout", "s="+pk, function(response){
        if (response.startsWith("Error")){
            return;
        }
        console.log(response);
        var entry = JSON.parse(response)[0];
        console.log(entry);
        document.getElementById("editHeader").innerHTML = "Edit entry for " + entry['name'];
        document.getElementById("adjustName").value = entry['name'];
        var outDate = getDateString(new Date(entry['outTime']));
        var outTime = getTimeString(new Date(entry['outTime']));
        document.getElementById("startDate").value = outDate;
        document.getElementById("startTime").value = outTime;
        console.log(new Date(entry['inTime']));
        var inTime = new Date(entry['inTime']);        
        inDateStr = getDateString(inTime);     
        
        inTimeStr = getTimeString(inTime);

        document.getElementById("endDate").value = inDateStr;
        document.getElementById("endTime").value = inTimeStr;
        document.getElementById("cover").style.opacity = 1;
        document.getElementById("cover").style.pointerEvents = "all";
       document.getElementById("notes").value = entry['notes'];
       document.getElementById("pk").value = entry['pkSignOut'];
           
       
    });
    
}
function getDateString(fromDate){
    var inMonth = fromDate.getMonth() + 1;
   
        if (inMonth < 10){
            inMonth = "0" + inMonth;
   
        }
        var inDate = fromDate.getDate();
        if (inDate < 10){
            inDate = "0" + inDate;
        }
        var inDateStr = fromDate.getFullYear() + "-" + inMonth + "-" + inDate;
        
        return inDateStr;
}
function getTimeString(fromTime){
    var inHours = fromTime.getHours();
        if (inHours < 10){
            inHours = "0" + inHours;
        }
        var inMinutes = fromTime.getMinutes();
        if (inMinutes < 10){
            inMinutes = "0" + inMinutes;
        }
        var inSeconds = fromTime.getSeconds();
        if (inSeconds < 10){
            inSeconds = "0" + inSeconds;
        }
        
        var newTimeString = inHours + ":" + inMinutes + ":" + inSeconds;
        return newTimeString;
}
function deleteEntry(){
    if(confirm("Are you sure you want to delete this entry?")){
        
        var pk = "s=" + document.getElementById("pk").value;
        sendRequest("delEntry", "s=" + pk, function(response){
            alert(response);
           location.reload(true); 
        });
    }
}
function swapTab(){
    var target = this.dataset.target;
    var tabs = document.getElementsByClassName("tab");
    for (var t of tabs){
        if (t.id != target){
            t.classList.add("hidden");
        }
        else{
            t.classList.remove("hidden");
        }
    }
}
function checkMatch(){
    console.log("test");
    var submit = document.getElementById("submitPassChange");
    var pw = document.getElementById("newPassword");
    var c = document.getElementById("confirm");
    if (pw.value !== c.value){
        c.setCustomValidity("Passwords don't match");
        submit.setAttribute("disabled", true)
        console.log("no match");
        
    }
    else{
        console.log("match");
        c.setCustomValidity("");
        submit.removeAttribute("disabled");
    }

}