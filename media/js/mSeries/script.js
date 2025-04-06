// Code goes here

    function showDetail(selector){
      // console.log("selector = "+ selector);      
      if($(selector).hasClass("hiddenRow")){
      	$(selector).removeClass("hiddenRow");
      }else {
      	$(selector).addClass("hiddenRow");
      }
      
    };
	
	//function convertWeightNew_yds(){
		//alert("here!");
		// var yds = document.getElementById("yds"+row+"-no"+lot+"-"+receive).value;
		// var maxGM = document.getElementById("maxGM"+row).value;
		// var extWidth = document.getElementById("extWidth"+row).value;
		// var multiplier = document.getElementById("multiplier"+row).value;
		// var basicUnit = document.getElementById("basicUnit"+row).value;
		
		// alert(yds+" "+maxGM+" "+extWidth+" "+multiplier+" "+basicUnit);
		
	//}
	
	function convertWeight(row, lot, receive){
	
		//alert(row+" - "+lot+" - "+receive);
		var lds = document.getElementById("lbs"+row+"-no"+lot+"-"+receive).value;
		var unit = document.getElementById("unit"+row+"-no"+lot+"-"+receive).value;
		
		if(lds.trim()!=""){
		
			if (!isNaN(lds)) 
			{
				//alert(row+" - "+lot+" - "+receive+" ==>"+lds);
				var kg = parseFloat(lds) * 0.45359237;
				var yds = parseFloat(lds) * parseFloat(unit);
				document.getElementById("kg"+row+"-no"+lot+"-"+receive).value = kg.toFixed(2);
				document.getElementById("yds"+row+"-no"+lot+"-"+receive).value = yds.toFixed(2);
			}

		}else{
			document.getElementById("lbs"+row+"-no"+lot+"-"+receive).value = "";
			document.getElementById("kg"+row+"-no"+lot+"-"+receive).value = "";
			document.getElementById("yds"+row+"-no"+lot+"-"+receive).value = "";
		}
	}

	function convertWeightKgToLbs(row, lot, receive){
	
		//alert(row+" - "+lot+" - "+receive);
		var kg = document.getElementById("kg"+row+"-no"+lot+"-"+receive).value;
		var unit = document.getElementById("unit"+row+"-no"+lot+"-"+receive).value;
		
		if(kg.trim()!=""){
		
			if (!isNaN(kg)) 
			{
				//alert(row+" - "+lot+" - "+receive+" ==>"+lds);
				var lds = parseFloat(kg) / 0.45359237;
				var yds = parseFloat(lds) * parseFloat(unit);
				document.getElementById("lbs"+row+"-no"+lot+"-"+receive).value = lds.toFixed(2);
				document.getElementById("yds"+row+"-no"+lot+"-"+receive).value = yds.toFixed(2);
			}

		}else{
			document.getElementById("lbs"+row+"-no"+lot+"-"+receive).value = "";
			document.getElementById("kg"+row+"-no"+lot+"-"+receive).value = "";
			document.getElementById("yds"+row+"-no"+lot+"-"+receive).value = "";
		}
	}

	function convertWeightFromYds(row, lot, receive){
	
		//alert(row+" - "+lot+" - "+receive);
		var yds = document.getElementById("yds"+row+"-no"+lot+"-"+receive).value;
		var unit = document.getElementById("unit"+row+"-no"+lot+"-"+receive).value;
		
		if(yds.trim()!=""){
		
			if (!isNaN(yds)) 
			{
				//alert(row+" - "+lot+" - "+receive+" ==>"+lds);
				var lbs = parseFloat(yds) / parseFloat(unit);
				var kg = parseFloat(lbs) * 0.45359237;
				document.getElementById("lbs"+row+"-no"+lot+"-"+receive).value = lbs.toFixed(2);
				document.getElementById("kg"+row+"-no"+lot+"-"+receive).value = kg.toFixed(2);
			}

		}else{
			document.getElementById("lbs"+row+"-no"+lot+"-"+receive).value = "";
			document.getElementById("kg"+row+"-no"+lot+"-"+receive).value = "";
			document.getElementById("yds"+row+"-no"+lot+"-"+receive).value = "";
		}
	}

	function convertWeightUnit(row, lot, receive){
	
		//alert(row+" - "+lot+" - "+receive);
		var lbs = document.getElementById("lbs"+row+"-no"+lot+"-"+receive).value;
		var unit = document.getElementById("unit"+row+"-no"+lot+"-"+receive).value;
		
		if(unit.trim()!=""){
		
			if (!isNaN(unit)&&lbs.trim()!="") 
			{
				//alert(row+" - "+lot+" - "+receive+" ==>"+lds);
				var yds = parseFloat(lbs) * parseFloat(unit);
				document.getElementById("yds"+row+"-no"+lot+"-"+receive).value = yds.toFixed(2);
			}

		}else{
				var defaultUnit = 1.78;
				//document.getElementById("lbs"+row+"-no"+lot+"-"+receive).value = "";
				document.getElementById("unit"+row+"-no"+lot+"-"+receive).value = "";
				if(!isNaN(lbs)){
					var yds = parseFloat(lbs) * 1.78;
					document.getElementById("yds"+row+"-no"+lot+"-"+receive).value = yds.toFixed(2);
				}

		}
	}//end function

	

function funcSearchStyleNo(){
		
	var date = document.getElementById("receivedDate").value;
	var style = document.getElementById("search_styleNo").value;
	
	//alert(date+" ---- "+style);
	var theForm, newInput1, newInput2;
	theForm = document.createElement('form');
		theForm.action = 'index.php?action=edit';
		theForm.method = 'post';
		
	newInput1 = document.createElement('input');
		newInput1.type = 'hidden';
		newInput1.name = 'dateReceive';
		newInput1.value = date;
		theForm.appendChild(newInput1);
	
	if(style!=""){
		
		newInput2 = document.createElement('input');
		newInput2.type = 'hidden';
		newInput2.name = 'styleNo';
		newInput2.value = style;
	 
		theForm.appendChild(newInput2);

	}
	
	document.getElementById('hidden_form_container').appendChild(theForm);
		// ...and submit it
		theForm.submit();
}

function funcDelRow(rowID){
	
	try {
		//alert("sas");
		var row = document.getElementById("m-detail"+rowID);
		if(row!=null){
			row.parentNode.removeChild(row);
		}
		
		var row2 = document.getElementById("row"+rowID);
		if(row2!=null){
			row2.parentNode.removeChild(row2);
		}
		
	}catch(e){
		alert(e);
	}
}

function funcDelAllLot(rowID){
	var countLot = document.getElementById("countLot"+rowID).value;
	var chk = confirm("Confirm to delete all?");
	var arr_MRDIID = [];
	if(chk==true){
		for(var lotID=1;lotID<=parseInt(countLot);lotID++){
			var mrdiid_ele  = document.getElementById("MRDIID"+rowID+"-"+lotID);
			var lot_ele     = document.getElementById("lot"+rowID+"-"+lotID);
			var lotline_ele = document.getElementById("lot"+rowID+"-line"+lotID);
			if(lot_ele!=null){
				lot_ele.parentNode.removeChild(lot_ele);
			}
			if(lotline_ele!=null){
				lotline_ele.parentNode.removeChild(lotline_ele);
			}
			if(mrdiid_ele!=null){
				arr_MRDIID.push(mrdiid_ele.value);
			}
		}//--- End For ---//
		
		if(parseInt(arr_MRDIID.length)>0){
			ajaxRemove(arr_MRDIID.toString());
		}
	}
}

function funcDelLot(rowID, lotID){ 
	
	try {
		var total = document.getElementById("countLotForJava"+rowID).value;
		var count = parseInt(total);
		var mrdiid_ele = document.getElementById("MRDIID"+rowID+"-"+lotID);
		var chk = confirm("Are you confirm to delete? This action cannot be undone.");
		
		if(chk==true){
			if(count==1){
				funcDelRow(rowID);	
			}
			else{
				var row = document.getElementById("lot"+rowID+"-"+lotID);
				if(row!=null){
					row.parentNode.removeChild(row);
				}
			
				var row2 = document.getElementById("lot"+rowID+"-line"+lotID);
				if(row2!=null){
					row2.parentNode.removeChild(row2);
				}
			}
			count--;
			
			if(count!=0){
				document.getElementById("countLotForJava"+rowID).value = count;
			}
			
			if(mrdiid_ele!=null){
				funcLoadIcon();
				ajaxRemove(mrdiid_ele.value);
			}
		}
		
	}catch(e){
		alert(e);
	}
	
}

function ajaxRemove(MRDIID){
	$.ajax({
		type: "GET",
		url: "ajax_delete.php?MRDIID=" + MRDIID,
		success: function(result2){
			funcRemoveIcon();
			alert("Delete Successful");
		},

		});
}

function funcLoadIcon(){
	$( "body" ).append( "<div id='load_screen'><div id='loading2' align='center'><img src='http://www.uat.apparelezi.com/includes/giphy-icon.gif'  /></div></div>" );
}
function funcRemoveIcon(){
	var load_screen = document.getElementById("load_screen");
	if(load_screen!=null){
		document.body.removeChild(load_screen);
	}
}

function funcCheckLot(value){
	
	var countLot = document.getElementById("countLot"+value).value;
	
	//alert(countLot);
}
/*========================================Function of Material Receive Refer===========================================*/

function submitRefer(value){
	//alert(value);
	var xmlhttp;
	document.getElementById("referItem2").innerHTML = "<img src='../../media/img/loading.gif' />"; //<img src='../../media/img/loading.gif' />
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
	//alert();
    document.getElementById("referItem2").innerHTML=xmlhttp.responseText;
    }
}
xmlhttp.open("GET","refer_get.php?mpohid="+value,true);
xmlhttp.send();
	
}

function funcAddOn(id){
	if(document.getElementById(id).checked){
		
		var table = document.getElementById("contact-form");
		var row = table.insertRow(4);
		row.id = "AddOn";
		var cell1 = row.insertCell(0);
			cell1.innerHTML = "<b>Refer PO#</b>  ";
			cell1.style.textAlign = "right";
		var cell1 = row.insertCell(1);
			cell1.innerHTML = "<input type='text' name='referPO' id='referPO' class='txt_long' /> "+
								"<input type='button' value='Find Refer' class='contact' onclick='submitRefer(referPO.value);' />";
		
		
		var row2 = table.insertRow(5);
		row2.id = "referItemRow";
		var cell1 = row2.insertCell(0);
		cell1.innerHTML = "";
		var cell1 = row2.insertCell(1);
		cell1.innerHTML = "<div id='referItem2'></div>";
		
	}else{
		try{			
			var delRow = document.getElementById("AddOn");
			delRow.parentNode.removeChild(delRow);
			
			var delRow = document.getElementById("referItemRow");
			delRow.parentNode.removeChild(delRow);
					
		}catch(e){
			alert(e);
		}
	}
}


