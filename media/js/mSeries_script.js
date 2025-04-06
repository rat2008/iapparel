// Code goes here

    function showDetail(selector){
      // console.log("selector = "+ selector);      
      if($(selector).hasClass("hiddenRow")){
      	$(selector).removeClass("hiddenRow");
      }else {
      	$(selector).addClass("hiddenRow");
      }
      
    };
	
	
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

	function searchDate(){
		var date = document.getElementById("receivedDate").value;
	
		 var theForm, newInput1;
		 theForm = document.createElement('form');
		 theForm.action = 'index.php?action=edit';
		 theForm.method = 'post';
		 
		 newInput1 = document.createElement('input');
		 newInput1.type = 'hidden';
		 newInput1.name = 'dateReceive';
		 newInput1.value = date;
		 
		 theForm.appendChild(newInput1);
		 
		 document.getElementById('hidden_form_container').appendChild(theForm);
		// ...and submit it
		theForm.submit();
		
		//alert('search: '+date);
	}

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
		
		var row = document.getElementById("m-detail"+rowID);
		row.parentNode.removeChild(row);
		
		var row2 = document.getElementById("row"+rowID);
		row2.parentNode.removeChild(row2);
		
	}catch(e){
		alert(e);
	}
}

function funcDelLot(rowID, lotID){
	
	try {
		
		var total = document.getElementById("countLotForJava"+rowID).value;
		//alert(count);
		var count = parseInt(total);
		
		if(count==1){
			funcDelRow(rowID);
			
		}else{
		
		var row = document.getElementById("lot"+rowID+"-"+lotID);
		row.parentNode.removeChild(row);
		
		var row2 = document.getElementById("lot"+rowID+"-line"+lotID);
		row2.parentNode.removeChild(row2);
		
		}
		count--;
		
		if(count!=0){
			document.getElementById("countLotForJava"+rowID).value = count;
		}
		
	}catch(e){
		alert(e);
	}
	
}

function funcCheckLot(value){
	
	var countLot = document.getElementById("countLot"+value).value;
	
	//alert(countLot);
}
/*========================================Function of Material Issue===========================================*/




