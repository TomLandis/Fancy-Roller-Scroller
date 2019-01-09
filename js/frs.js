
	window.onload = function() {
	var listo = document.getElementById("listOfThings");

var numOfItems = listo.children.length - 1;
let iter = 1;
	
function sliderOut() {
  jQuery("#inHere").animate(
    {
      opacity: 0,
      top: "-50px"
    },
    250,
    function() {
      document.getElementById("inHere").style.top = "50px";
      setTimeout(function() {changer();}, 10);
    }
  );
}
function sliderUp() {
  jQuery("#inHere").animate(
    {
      opacity: 1,
      top: "0px"
    },
    250,
    function() {
    }
  );
}
function changer() {
 
  let target = "thing" + iter;
  let targ = document.getElementById(target);
  document.getElementById("inHere").innerHTML = targ.innerHTML;

  sliderUp();

  setTimeout(function() {
    sliderOut();
    if (iter == numOfItems) {
      iter = 1;
    } else {
      iter++;
    }
  }, 1400);
}

changer();
}
