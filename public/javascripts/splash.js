window.onload = function(event) {
    var instructionButton = document.getElementById('instructions');
    var instructionPage=document.getElementById('instructionPage');
    var close=document.getElementById('close');
    instructionButton.onclick = function(){
        instructionPage.style.display="block"; 
    }
    close.onlick=function(){
        instructionPage.style.display="none";
    }
    window.onclick=function(event){
        if(event.target==instructionPage){
            instructionPage.style.display="none";
        }
    }
}