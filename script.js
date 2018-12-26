function hideInput() {
	document.getElementById("wrapper").style.display = "none";
}

function submitForm() {
    var x = document.getElementsByTagName("form");
    console.log(x.length())
    x[0].submit();
}

function removeClass() {
    var element = document.getElementById("kastleDiv");
    element.classList.remove("center");
}

function fadeIt() {
    document.getElementById("kastle").style.opacity = 1.0;
    var element = document.getElementById("kastleDiv");
    element.classList.add("animate-fading");
}

function noFade() {
    var element = document.getElementById("kastleDiv");
    element.classList.remove("animate-fading");
    document.getElementById("kastle").style.opacity = 1.0;
    document.getElementById("wrapper").style.display = "none";
}

function exportGeneral1() {
      $.ajax({
           type: "POST",
           url: 'xlsexport.php',
           data:{action:'exportgen1'},
           success:function(html) {
             alert("Document saved locally");
           }

      });
 }

