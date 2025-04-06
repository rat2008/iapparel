function CalcAccConsump(){
	
	var item = document.getElementById("totalItem").value;
	for(var i=1;i<=parseInt(item);i++){
		//var unit = document.getElementById("unit"+i);
		var byMethod = document.getElementById("by"+i).value;
		//var unitTxt = unit.options[unit.selectedIndex].text;
		
		var unitTxt = document.getElementById("unitText"+i).value;
		unitTxt = unitTxt.replace(/[^\d]/g, '');
		
		if(parseInt(byMethod)==1){
			
			funcShipmentCal(i);
			
		}else if(parseInt(byMethod)==2){
		
			funcRatioCal(i);
		
		}else{
		
		//var myString = 'abc123.8<blah>';
		//myString = myString.replace(/[^\d]/g, '');
		var unitValue = ((unitTxt=="") ? 1 : unitTxt );
		//alert(unitValue);
		
		var dozPcs = document.getElementById("type"+i).value;
		var all = document.getElementById("allSize"+i).value;
		var r2 = document.getElementById("garmentRow"+i).value;
		
		for(var g=1;g<=parseInt(r2);g++){
			var r3 = document.getElementById("count"+i+"-"+g).value;
			var qty = document.getElementById("garmentColorQty"+i+"-"+g).value;
			
			for(var c=1;c<=parseInt(r3);c++){
				var waste = document.getElementById("waste"+i+"-"+g+"-"+c).value;
				//var color = document.getElementById("Color"+i+"-"+g+"-"+c).value;
				var inv = document.getElementById("inv"+i+"-"+g+"-"+c).value;
				
				waste = (waste=="" ? 0 : waste);
				//alert(waste);
				//alert("-"+unitValue+"-")
				var consumption = ((parseFloat(all) * parseInt(qty)) / (parseInt(unitValue) * parseInt(dozPcs))) * ((parseFloat(waste) / 100) + 1) - parseFloat(inv);
				//alert(consumption);
				
				if(parseFloat(consumption)<0){ consumption = 0; }
				document.getElementById("consump"+i+"-"+g+"-"+c).value = consumption.toFixed(3);
				
			}//---end for loop (second level)---//
			
		}//---end for loop (second level)---//
		
		}
		
	}//---end for loop (first level)---//
}//---end consumption calculation---//

function funcEditMode(value){
	
	var res = value.split("-");
	var by = document.getElementById("by"+res[0]).value;
	
	if(parseInt(by)==0){ //---none---//
		document.getElementById("standard"+value).style.display = "none";
		document.getElementById("edit"+value).style.display = "inline";
		document.getElementById("btn_select"+value).style.display = "inline";
		document.getElementById("btn_edit"+value).style.display = "none";
		document.getElementById("mode"+value).value = "edit";
		
		funcResetInvQty(res[0],res[1],res[2],"none");
		
	}else if(parseInt(by)==1){ //---shipment---//
		document.getElementById("shipstandard"+value).style.display = "none";
		document.getElementById("shipedit"+value).style.display = "inline";
		document.getElementById("shipbtn_select"+value).style.display = "inline";
		document.getElementById("shipbtn_edit"+value).style.display = "none";
		document.getElementById("shipmode"+value).value = "edit";
		
		funcResetInvQty(res[0],res[1],res[2],"shipment");
		
	}else if(parseInt(by)==2){ //---ratio---//
		document.getElementById("ratiostandard"+value).style.display = "none";
		document.getElementById("ratioedit"+value).style.display = "inline";
		document.getElementById("ratiobtn_select"+value).style.display = "inline";
		document.getElementById("ratiobtn_edit"+value).style.display = "none";
		document.getElementById("ratiomode"+value).value = "edit";
		
		funcResetInvQty(res[0],res[1],res[2],"ratio");
		
	}
	
	
	//alert(res[0]+" "+res[1]+" "+res[2]);
	
	// if(parseInt(by)==0){//---none---//
		// funcResetInvQty(res[0],res[1],res[2],"none");
		
	// }else if(parseInt(by)==1){//---shipment---//
		// funcResetInvQty(res[0],res[1],res[2],"shipment");
		
	// }else if(parseInt(by)==2){//---ratio---//
		// funcResetInvQty(res[0],res[1],res[2],"ratio");
	// }
	
}

function funcSelectMode(value){
	
	var res = value.split("-");
	var by = document.getElementById("by"+res[0]).value;
	
	if(parseInt(by)==0){ //---none---//
		document.getElementById("standard"+value).style.display = "inline";
		document.getElementById("edit"+value).style.display = "none";
		document.getElementById("btn_select"+value).style.display = "none";
		document.getElementById("btn_edit"+value).style.display = "inline";
		document.getElementById("mode"+value).value = "select";
		
	}else if(parseInt(by)==1){ //---shipment---//
		document.getElementById("shipstandard"+value).style.display = "inline";
		document.getElementById("shipedit"+value).style.display = "none";
		document.getElementById("shipbtn_select"+value).style.display = "none";
		document.getElementById("shipbtn_edit"+value).style.display = "inline";
		document.getElementById("shipmode"+value).value = "select";
		
	}else if(parseInt(by)==2){ //---ratio---//
		document.getElementById("ratiostandard"+value).style.display = "inline";
		document.getElementById("ratioedit"+value).style.display = "none";
		document.getElementById("ratiobtn_select"+value).style.display = "none";
		document.getElementById("ratiobtn_edit"+value).style.display = "inline";
		document.getElementById("ratiomode"+value).value = "select";
		
	}
}

function funcShipmentCal(i){
	var r2 = document.getElementById("shipRow"+i).value;
	
	for(var g=1;g<=parseInt(r2);g++){
		var r3 = document.getElementById("shipCount"+i+"-"+g).value;
		
		for(var f=1;f<=parseInt(r3);f++){
			var waste = document.getElementById("shipWaste"+i+"-"+g+"-"+f).value;
			var carton = document.getElementById("carton"+i+"-"+g+"-"+f).value;
			var inv = document.getElementById("shipInv"+i+"-"+g+"-"+f).value;
			
			waste = (waste=="" ? 0 : waste);
			var total = parseFloat(carton) * ((parseFloat(waste) / 100) + 1) - parseFloat(inv);
			
			if(parseFloat(total)<0){ total = 0; }
			document.getElementById("shipConsump"+i+"-"+g+"-"+f).value = total.toFixed(3);
		}
		//alert(r3);
	}//------end for loop------//
}

function funcRatioCal(i){
	var r2 = document.getElementById("ratioRow"+i).value;
	for(var g=1;g<=parseInt(r2);g++){
		var r3 = document.getElementById("ratioCount"+i+"-"+g).value;
		
		for(var f=1;f<=parseInt(r3);f++){
			var waste = document.getElementById("ratioWaste"+i+"-"+g+"-"+f).value;
			var ratioQty = document.getElementById("ratioQty"+i+"-"+g+"-"+f).value;
			var inv = document.getElementById("ratioInv"+i+"-"+g+"-"+f).value;
			
			waste = (waste=="" ? 0 : waste);
			var total = parseFloat(ratioQty) * ((parseFloat(waste) / 100) + 1) - parseFloat(inv);
			
			if(parseFloat(total)<0){ total = 0; }
			document.getElementById("ratioConsump"+i+"-"+g+"-"+f).value = total.toFixed(3);
		}
	}//------end for loop------//
}

function funcDeleteAB(){
	var mmidArray = new Array();
	var count = 0;
	//alert("remove");
	$("input:checked").each(function() {
			var id = $(this).val();
			
			var sub = id.substring(0, 6);
			if(sub=="delete"){
				var deleteRow = id.substring(6);
				//alert(deleteRow);
				var apid = $("#apid"+deleteRow).val();
				//var subMMID = mmidCheck.substring(0, 6);
				//alert(apid);
				//if(subMMID=="exists"){
					//var mmid = mmidCheck.substring(6);
				mmidArray.push(apid);
				//}
				
				try{			
					var delRow = document.getElementById(deleteRow);
					//delRow.parentNode.removeChild(delRow);
						
					var delRow2 = document.getElementById("m-detail"+deleteRow);
					//delRow2.parentNode.removeChild(delRow2);
					
				}catch(e){
					alert(e);
				}
				count++;
			}
	});
	
	//alert(mmidArray);
	if(mmidArray.length > 0 || count>0){
		var r = confirm("Items selected will be permanent removed, are you confirm to remove?");
		
		if (r == true) {
			var orderno = document.getElementById("orderno").value;
			$.post('deleteAB.php?APID='+mmidArray+'&orderno='+orderno);
			//window.location = "deleteAB.php?APID="+mmidArray+"&orderno="+orderno;
			
			$("input:checked").each(function() {
					var id = $(this).val();
					//alert("--->"+id);
					
					var sub = id.substring(0, 6);
					if(sub=="delete"){
						var deleteRow = id.substring(6);
						//alert(deleteRow);
						var apid = $("#apid"+deleteRow).val();
						
						
						try{			
							var delRow = document.getElementById(deleteRow);
							delRow.parentNode.removeChild(delRow);
								
							var delRow2 = document.getElementById("m-detail"+deleteRow);
							delRow2.parentNode.removeChild(delRow2);
							
						}catch(e){
							alert(e);
						}
						count++;
					}
					
			});
			alertDisplay("Removed");
		}//---End user confirm remove---//
	}
	
	if(count>0){
		//alertDisplay("Removed");
	}
	
	
	
	/*//===================temporary=============================//
	
	var theForm, newInput1, newInput2, newInput3, check = true;
		theForm = document.createElement('form');
				theForm.action = "_test_deleteMB.php?MMID="+mmidArray;
				theForm.method = 'post';
	
	document.getElementById('hidden_form_container').appendChild(theForm);
	// ...and submit it
	theForm.submit();//*/

}

function funcByMethod(row, value){
	//alert(row+" "+value);
	//document.getElementById("expandRow"+row).innerHTML = "";

	
	switch(parseInt(value)) {
		case 0: //---None---//
			//alert(0);
			document.getElementById("item"+row).style.display = "inline-block";
			document.getElementById("shipment"+row).style.display = "none";
			document.getElementById("ratio"+row).style.display = "none";
			//document.getElementById("item"+row).width = "100%";
			break;
		case 1: //---By Shipment---//
			//alert(1);
			document.getElementById("shipment"+row).style.display = "inline-block";
			document.getElementById("item"+row).style.display = "none";
			document.getElementById("ratio"+row).style.display = "none";
			break;
		case 2: //---By Ratio---//
			//alert(2);
			document.getElementById("shipment"+row).style.display = "none";
			document.getElementById("item"+row).style.display = "none";
			document.getElementById("ratio"+row).style.display = "inline-block";
			break;
	}//---end switch---//
}

function submitBTN(){
	var purchase = 0;
	//var status = document.getElementById("statusID").value;
	var checkPurchase = document.getElementById("purchaseCheck").value;
	
	var item = document.getElementById("totalItem").value;
	for(var i=1;i<=parseInt(item);i++){
		var status = document.getElementById("statusPurchase"+i).value;
		if(parseInt(status)==8){
			purchase++;
		}
	}
	//alert(checkPurchase);
	var eError = 0;
	if(parseInt(checkPurchase)==1){
		//alert("here");
		var check = funcToPurchase();
	
		if(check==false){
			//return false;
			eError++;
		}else{
			CalcAccConsump();
			//return true;
		}
		
		var countCheck = funcToPurchaseCount();
		
		if(parseInt(countCheck)==0){
			eError++;
		}
		
		if(eError>0){
			return false;
		}else{
			return true;
		}
		
		
		
	}else if(purchase>0){
		
		var check = finalCheck2();
		if(check==false){
			return false;
		}else{
			CalcAccConsump();
			return true;
		}
		
	}else{
		//alert("hihi");
		return true;
		
	}
	
	
	//alert(check);
	/*if(parseInt(status)==8){
		var check = finalCheck();
		if(check==false){
			return false; 
		}else{
			CalcAccConsump();
			return true;
		}
	}else{
		CalcAccConsump();
		return true;
	}//*/
	
	//return false;
}

function funcPurchase(){
	document.getElementById("purchaseCheck").value = 1;
}
function funcToPurchaseCount(){
	var cc = 0;
	$("input:checked").each(function() {
		var id = $(this).val();
		var sub = id.substring(0, 6);
		
		if(sub=="delete"){
			cc++;
		}
	});
	return cc;
}
function funcToPurchase(){
	//document.getElementById("statusID").value = 8;
	var error = 0;
	$("input:checked").each(function() {
			var id = $(this).val();
			//alert("--->"+id);
			
			var sub = id.substring(0, 6);
			if(sub=="delete"){
				var row = id.substring(6);
				//alert(row);
				
				var check = finalCheck(row);
				//alert(check);
				if(check==false){
					error++;
					return;
				}else{
					document.getElementById("statusPurchase"+row).value = 8;
				}
				
			}
	});
	
	if(error>0){
		return false;
	}else{
		return true;
	}
	
}

function finalCheck(i){
	//var item = document.getElementById("totalItem").value;
	//for(var i=1;i<=parseInt(item);i++){
		
		var unit = document.getElementById("unit"+i).value;
		//alert(unit);
		
		if(unit==""){
			alert("Please Select Unit");
			document.getElementById("unit"+i).className = "select_medium_error";
			return false;
		}else{
			document.getElementById("unit"+i).className = "select_medium";
		}
		
		var byMethod = document.getElementById("by"+i).value;
		
		//---------------------------By None----------------------------//
		if(parseInt(byMethod)==0){
			var allSize = document.getElementById("allSize"+i).value;
			if(allSize=="" || parseInt(allSize)==0){
				alert("Field cannot be empty or zero");
				document.getElementById("allSize"+i).className = "error_short";
				return false;
			}else{
				document.getElementById("allSize"+i).className = "txt_short";
			}
			
			var r2 = document.getElementById("garmentRow"+i).value;
			
			for(var g=1;g<=parseInt(r2);g++){	
				var r3 = document.getElementById("count"+i+"-"+g).value;
			
				for(var c=1;c<=parseInt(r3);c++){
					var waste = document.getElementById("waste"+i+"-"+g+"-"+c).value;
					var fabColor = document.getElementById("Color"+i+"-"+g+"-"+c).value;
					//alert("check");
					// if(myTrim(waste)!=""){		
						// document.getElementById("waste"+i+"-"+g+"-"+c).className = "txt_short";
					// }else{
						// validateShowDetail("m-detail"+i);
						// alert("Invalid wastage percentage");
						// document.getElementById("waste"+i+"-"+g+"-"+c).className = "error_short";
						// document.getElementById("waste"+item+"-"+garment+"-"+fab).focus();
						// return false;
					// }
					
					if(parseInt(fabColor)==0){
						validateShowDetail("m-detail"+i);
						alert("Please select a standard of accessory");
						document.getElementById("Color"+i+"-"+g+"-"+c).className = "select_long_error";
						return false;
					}else{
						document.getElementById("Color"+i+"-"+g+"-"+c).className = "select_long";
					}
					//return false;
				}//------end for loop (third level)------//
			
			}//-----end for loop (second level)------//
			
		//---------------------------By Shipment----------------------------//
		}else if(parseInt(byMethod)==1){
			
			var r2 = document.getElementById("shipRow"+i).value;
			for(var g=1;g<=parseInt(r2);g++){
				
				var r3 = document.getElementById("shipCount"+i+"-"+g).value;
				for(var c=1;c<=parseInt(r3);c++){
					var waste = document.getElementById("shipWaste"+i+"-"+g+"-"+c).value;
					var fabColor = document.getElementById("shipColor"+i+"-"+g+"-"+c).value;
					
					if(parseInt(fabColor)==0){
						validateShowDetail("m-detail"+i);
						alert("Please select a standard of accessory");
						document.getElementById("shipColor"+i+"-"+g+"-"+c).className = "select_long_error";
						return false;
					}else{
						document.getElementById("shipColor"+i+"-"+g+"-"+c).className = "select_long";
					}
					
					// if(myTrim(waste)!=""){		
						// document.getElementById("shipWaste"+i+"-"+g+"-"+c).className = "txt_short";
					// }else{
						// validateShowDetail("m-detail"+i);
						// alert("Invalid wastage percentage");
						// document.getElementById("shipWaste"+i+"-"+g+"-"+c).className = "error_short";
						// //document.getElementById("waste"+item+"-"+garment+"-"+fab).focus();
						// return false;
					// }
				
				}//------end for loop (third level)------//
			
			}//------end for loop (second level)------//
		
		//---------------------------By Ratio----------------------------//
		}else if(parseInt(byMethod)==2){
			
			var r2 = document.getElementById("ratioRow"+i).value;
			for(var g=1;g<=parseInt(r2);g++){
				
				var r3 = document.getElementById("ratioCount"+i+"-"+g).value;
				for(var c=1;c<=parseInt(r3);c++){
					var waste = document.getElementById("ratioWaste"+i+"-"+g+"-"+c).value;
					var fabColor = document.getElementById("ratioColor"+i+"-"+g+"-"+c).value;
					
					if(parseInt(fabColor)==0){
						validateShowDetail("m-detail"+i);
						alert("Please select a standard of accessory");
						document.getElementById("ratioColor"+i+"-"+g+"-"+c).className = "select_long_error";
						return false;
					}else{
						document.getElementById("ratioColor"+i+"-"+g+"-"+c).className = "select_long";
					}
					
					// if(myTrim(waste)!=""){		
						// document.getElementById("ratioWaste"+i+"-"+g+"-"+c).className = "txt_short";
					// }else{
						// validateShowDetail("m-detail"+i);
						// alert("Invalid wastage percentage");
						// document.getElementById("ratioWaste"+i+"-"+g+"-"+c).className = "error_short";
						// //document.getElementById("waste"+item+"-"+garment+"-"+fab).focus();
						// return false;
					// }
				
				}//------end for loop (third level)------//
			
			}//------end for loop (second level)------//
			
			
		}
	
	//}//------end for loop------//
	
	
	return true;
}

function finalCheck2(){
	var item = document.getElementById("totalItem").value;
	for(var i=1;i<=parseInt(item);i++){
		
		var statusPurchase = document.getElementById("statusPurchase"+i).value;
		if(parseInt(statusPurchase)==8){
		
		var unit = document.getElementById("unit"+i).value;
		//alert(unit);
		
		if(unit==""){
			alert("Please Select Unit");
			document.getElementById("unit"+i).className = "select_medium_error";
			return false;
		}else{
			document.getElementById("unit"+i).className = "select_medium";
		}
		
		var byMethod = document.getElementById("by"+i).value;
		
		//---------------------------By None----------------------------//
		if(parseInt(byMethod)==0){
			var allSize = document.getElementById("allSize"+i).value;
			if(allSize=="" || parseInt(allSize)==0){
				alert("Field cannot be empty or zero");
				document.getElementById("allSize"+i).className = "error_short";
				return false;
			}else{
				document.getElementById("allSize"+i).className = "txt_short";
			}
			
			var r2 = document.getElementById("garmentRow"+i).value;
			
			for(var g=1;g<=parseInt(r2);g++){	
				var r3 = document.getElementById("count"+i+"-"+g).value;
			
				for(var c=1;c<=parseInt(r3);c++){
					var waste = document.getElementById("waste"+i+"-"+g+"-"+c).value;
					var fabColor = document.getElementById("Color"+i+"-"+g+"-"+c).value;
					var mode = document.getElementById("mode"+i+"-"+g+"-"+c).value;
					var txt_standard = document.getElementById("txt_standard"+i+"-"+g+"-"+c).value;
					//alert("check");
					// if(myTrim(waste)!=""){		
						// document.getElementById("waste"+i+"-"+g+"-"+c).className = "txt_short";
					// }else{
						// validateShowDetail("m-detail"+i);
						// alert("Invalid wastage percentage");
						// document.getElementById("waste"+i+"-"+g+"-"+c).className = "error_short";
						// //document.getElementById("waste"+item+"-"+garment+"-"+fab).focus();
						// return false;
					// }
					
					if(parseInt(fabColor)==0 && mode=="select"){
						validateShowDetail("m-detail"+i);
						alert("Please select a standard of accessory");
						document.getElementById("Color"+i+"-"+g+"-"+c).className = "select_long_error";
						return false;
					}else{
						document.getElementById("Color"+i+"-"+g+"-"+c).className = "select_long";
					}
					
					if(mode=="edit" && txt_standard==""){
						validateShowDetail("m-detail"+i);
						alert("Please enter standard name");
						document.getElementById("txt_standard"+i+"-"+g+"-"+c).className = "txt_md_short_error"; //---txt_md_short---//
						return false;
					}else{
						document.getElementById("txt_standard"+i+"-"+g+"-"+c).className = "txt_md_short"; //---txt_md_short---//
					}
					//return false;
				}//------end for loop (third level)------//
			
			}//-----end for loop (second level)------//
			
		//---------------------------By Shipment----------------------------//
		}else if(parseInt(byMethod)==1){
			
			var r2 = document.getElementById("shipRow"+i).value;
			for(var g=1;g<=parseInt(r2);g++){
				
				var r3 = document.getElementById("shipCount"+i+"-"+g).value;
				for(var c=1;c<=parseInt(r3);c++){
					var waste = document.getElementById("shipWaste"+i+"-"+g+"-"+c).value;
					var fabColor = document.getElementById("shipColor"+i+"-"+g+"-"+c).value;
					
					if(parseInt(fabColor)==0){
						validateShowDetail("m-detail"+i);
						alert("Please select a standard of accessory");
						document.getElementById("shipColor"+i+"-"+g+"-"+c).className = "select_long_error";
						return false;
					}else{
						document.getElementById("shipColor"+i+"-"+g+"-"+c).className = "select_long";
					}
					
					// if(myTrim(waste)!=""){		
						// document.getElementById("shipWaste"+i+"-"+g+"-"+c).className = "txt_short";
					// }else{
						// validateShowDetail("m-detail"+i);
						// alert("Invalid wastage percentage");
						// document.getElementById("shipWaste"+i+"-"+g+"-"+c).className = "error_short";
						// //document.getElementById("waste"+item+"-"+garment+"-"+fab).focus();
						// return false;
					// }
				
				}//------end for loop (third level)------//
			
			}//------end for loop (second level)------//
		
		//---------------------------By Ratio----------------------------//
		}else if(parseInt(byMethod)==2){
			
			var r2 = document.getElementById("ratioRow"+i).value;
			for(var g=1;g<=parseInt(r2);g++){
				
				var r3 = document.getElementById("ratioCount"+i+"-"+g).value;
				for(var c=1;c<=parseInt(r3);c++){
					var waste = document.getElementById("ratioWaste"+i+"-"+g+"-"+c).value;
					var fabColor = document.getElementById("ratioColor"+i+"-"+g+"-"+c).value;
					
					if(parseInt(fabColor)==0){
						validateShowDetail("m-detail"+i);
						alert("Please select a standard of accessory");
						document.getElementById("ratioColor"+i+"-"+g+"-"+c).className = "select_long_error";
						return false;
					}else{
						document.getElementById("ratioColor"+i+"-"+g+"-"+c).className = "select_long";
					}
					
					// if(myTrim(waste)!=""){		
						// document.getElementById("ratioWaste"+i+"-"+g+"-"+c).className = "txt_short";
					// }else{
						// validateShowDetail("m-detail"+i);
						// alert("Invalid wastage percentage");
						// document.getElementById("ratioWaste"+i+"-"+g+"-"+c).className = "error_short";
						// //document.getElementById("waste"+item+"-"+garment+"-"+fab).focus();
						// return false;
					// }
				
				}//------end for loop (third level)------//
			
			}//------end for loop (second level)------//
			
			
		}
		
		}//------end check status = 8 (To Purchase item)------//
	}//------end for loop------//
	
	
	return true;
}

function validateShowDetail(selector){
	document.getElementById(selector).className = "";
}//

function funcGetPCode_Acc(value, i, g, c, method){
	//alert(method);
	var ascid = $("option:selected", value).attr("value");
	funcResetInvQty(i,g,c,method);
	//alert(pcode+" "+i+" "+g+" "+c);
	//alert(ascid);
	if(parseInt(ascid)!=0){
	
	var xmlhttp;
		//alert(MPDID)
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
			//document.getElementById("myDiv").innerHTML=xmlhttp.responseText;
			if(method=="ratio"){
				document.getElementById("ratioInvQty"+i+"-"+g+"-"+c).innerHTML = "["+xmlhttp.responseText+"]";
			}else if(method=="shipment"){
				document.getElementById("shipmentInvQty"+i+"-"+g+"-"+c).innerHTML = "["+xmlhttp.responseText+"]";
			}else{
				document.getElementById("invQty"+i+"-"+g+"-"+c).innerHTML = "["+xmlhttp.responseText+"]";
			}
		}
	 }
	xmlhttp.open("GET","getQtyASCID.php?id="+ascid,true);
	xmlhttp.send();
	
	}else{
		if(method=="ratio"){
			document.getElementById("ratioInvQty"+i+"-"+g+"-"+c).innerHTML = "[0]";
		}else if(method=="shipment"){
			document.getElementById("shipmentInvQty"+i+"-"+g+"-"+c).innerHTML = "[0]";
		}else{
			document.getElementById("invQty"+i+"-"+g+"-"+c).innerHTML = "[0]";
		}
	}
	
}

function funcResetInvQty(i,g,c,method){
	//alert(mmcid);
	//var valid = document.getElementById("inv"+i+"-"+g+"-"+c);
	//alert("non-exist "+valid);
	var APDID = "";
	
	//var check = confirm("Are you confirm to reset inventory request?");
	
	//if(check==true){
		if(method=="ratio"){
			document.getElementById("ratioInv"+i+"-"+g+"-"+c).value = "0.000";
			document.getElementById("ratioASCID"+i+"-"+g+"-"+c).value = "";
			document.getElementById("ratioRequestItem"+i+"-"+g+"-"+c).value = "";
			document.getElementById("ratioRequestQty"+i+"-"+g+"-"+c).value = "";
			
			if(document.getElementById("APDID_Ratio"+i+"-"+g+"-"+c) != null){
				APDID = document.getElementById("APDID_Ratio"+i+"-"+g+"-"+c).value;
			}
			
		}else if (method=="shipment"){
			document.getElementById("shipInv"+i+"-"+g+"-"+c).value = "0.000";
			document.getElementById("shipmentASCID"+i+"-"+g+"-"+c).value = "";
			document.getElementById("shipmentRequestItem"+i+"-"+g+"-"+c).value = "";
			document.getElementById("shipmentRequestQty"+i+"-"+g+"-"+c).value = "";
			
			if(document.getElementById("APDID_Ship"+i+"-"+g+"-"+c) != null){
				APDID = document.getElementById("APDID_Ship"+i+"-"+g+"-"+c).value;
			}
			
		}else{
			document.getElementById("inv"+i+"-"+g+"-"+c).value = "0.000";
			document.getElementById("ASCID"+i+"-"+g+"-"+c).value = "";
			document.getElementById("requestItem"+i+"-"+g+"-"+c).value = "";
			document.getElementById("requestQty"+i+"-"+g+"-"+c).value = "";
			
			if(document.getElementById("APDID_All"+i+"-"+g+"-"+c) != null){
				APDID = document.getElementById("APDID_All"+i+"-"+g+"-"+c).value;
			}
		}
		deleteTransfer(APDID)
		//alert("here"+APDID);
	//}
	
}

function deleteTransfer(APDID){
	var xmlhttp;
		//alert(MPDID)
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
			//document.getElementById("myDiv").innerHTML=xmlhttp.responseText;
		}
	 }
	xmlhttp.open("GET","delTransfer.php?id="+APDID,true);
	xmlhttp.send();
}

function funcRequest_Acc(i1, i2, i3, orderno, method){
	//alert("here");
	var ascid,mode;
	
	if(method=="ratio"){
		ascid = document.getElementById("ratioColor"+i1+"-"+i2+"-"+i3).value;
		mode = document.getElementById("ratiomode"+i1+"-"+i2+"-"+i3).value;
	}else if(method=="shipment"){
		ascid = document.getElementById("shipColor"+i1+"-"+i2+"-"+i3).value; 
		mode = document.getElementById("shipmode"+i1+"-"+i2+"-"+i3).value;
	}else{
		ascid = document.getElementById("Color"+i1+"-"+i2+"-"+i3).value;
		mode = document.getElementById("mode"+i1+"-"+i2+"-"+i3).value;
	}
	//alert(i1+" "+i2+" "+i3+" "+orderno+" -->"+ascid);
	
	if(mode=="edit"){
		ascid = 0;
	}
	
	window.open("acc_request.php?orderno="+orderno+"&i1="+i1+"&i2="+i2+"&i3="+i3+"&ascid="+ascid+"&method="+method, "Ratting", "width="+screen.width+", height="+screen.height+", left=150, top=200, toolbar=1, status=1,");

}

function funcUnitAcc(value){
	var isLock;
	var item = document.getElementById("totalItem").value;
	//alert(value);
	
	for(var i=1;i<=parseInt(item);i++){
		isLock = document.getElementById("isLock"+i).value;
		
		if(value=="doz" && parseInt(isLock)==0){
			document.getElementById("type"+i).value = "12";
		}else if(value=="pcs" && parseInt(isLock)==0){
			document.getElementById("type"+i).value = "1";
		}
		
	}
	if(value == "doz"){
		document.getElementById("doz").disabled = true;
		document.getElementById("pcs").disabled = false;
	}else{
		document.getElementById("pcs").disabled = true;
		document.getElementById("doz").disabled = false;
	}
}