var select = document.getElementById('selectPeople');


function Disableload(){
  for (let i = 1; i <= 20; i++) {
    let plid = 'pl' + i;
    if(i<=(Number(select.value))){
    	document.getElementById(plid).removeAttribute("disabled");
    }else{
    	document.getElementById(plid).setAttribute("disabled", true);
    }
  }
}

select.onchange = function(){
  Disableload();
}


window.onload = function(){
  Disableload();
}

$("#reset").on("click", function()  {
	alert('Aaa');
}
