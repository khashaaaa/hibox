
function SetLocation(BCC){ 
  document.getElementById('GeoCurr').action='index.php?p=set_currency&c=' + BCC; 
  document.getElementById('GeoCurr').submit();  
}
function ShowLocation(){
   var can=true;
   if (document.getElementById('money').style.visibility=='hidden')  {
	  document.getElementById('money').style.visibility='visible';
	  can=false;
   }
   if ((document.getElementById('money').style.visibility=='visible') && (can==true))  {
	  document.getElementById('money').style.visibility='hidden';   
   }
   
}
