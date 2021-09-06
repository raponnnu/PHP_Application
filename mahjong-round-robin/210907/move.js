let elements = document.getElementsByName('show');
let len = elements.length;
let checkValue = '';

function Disableload(){
	for (let i = 0; i < len; i++){
	if (elements.item(i).checked){
        	checkValue = elements.item(i).value;
		}
  	}
    if(checkValue == 'yes'){
    	document.getElementById("vp").disabled = true;
    	document.getElementById("vp_c").disabled = true;
    }else{
    	document.getElementById("vp").disabled = false;
    	document.getElementById("vp_c").disabled = false;
    }

}



window.onload = function(){
  	Disableload();
}
