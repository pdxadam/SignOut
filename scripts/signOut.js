/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var prev = window.onload;
window.onload = function(){
    if (prev){
        prev();
    }
    assignHandlers();
    document.getElementById("default").focus();
    try{
        var offset = new this.Date().getTimezoneOffset();
        this.document.getElementById("tzOffset").value = offset;
        //This didn't work on the pi -- I had to put in a literal number.
    }
    catch(error){
        
    }
}
function signIn(){
    var len = this.id.length;    
    var signin = this.id.substring(7, len);
    var params = "s=" + signin;
    var el = this;
    
    sendRequest("signin", params, function(response){
       if (response == "TRUE"){
           var container = el.parentElement;
           el.parentElement.removeChild(el);
           if (document.getElementsByClassName("signOut").length == 0){
               document.getElementById("signOutHeader").innerHTML = "No current signouts";
           }
           else{
               document.getElementById("signOutHeader").innerHTML = "Click your name to sign back in";
           
           }
           
       }
       document.getElementById("default").focus();
    });
}
function assignHandlers(){
    var signOuts = document.getElementsByClassName("signOut");
    for(var each of signOuts){
        each.addEventListener("click", signIn);
        
    }
}

