function checkFabColor(id, value){
	//alert(id+" "+value);
	var res = value.substring(0,1);
	
	if(res=="A"){
		document.getElementById("pcode"+id).style.display = "none";
	}else if(res=="0"){
		document.getElementById("pcode"+id).style.display = "none";
	}else{
		document.getElementById("pcode"+id).style.display = "inline";
		document.getElementById("pcode"+id).value = "";
	}
	
}

function syncColor(num){
	//var check = document.getElementById("colorCheck"+num).checked;
	var total =  document.getElementById("totalItem").value;
	//var defaultValue = document.getElementById("fabColor1-"+num);
	var e = document.getElementById("fabColor1-"+num);
	var strUser = e.options[e.selectedIndex].className;
	var res = strUser.split(",");
	//alert(res+" ");
	
	if(res!=""){
		for(var i=2;i<=parseInt(total);i++){
			document.getElementById("fabColor"+i+"-"+num).value = res[0];
			document.getElementById("pcode"+i+"-"+num).style.display = "inline";
			document.getElementById("pcode"+i+"-"+num).value = res[1];
		}
	}

	
}

function finalcheckMB(){
	$("#mb_form :input").prop("disabled", false);
}

function finalcheckMP(){
	$("#mp_form :input").prop("disabled", false);
}

function submitMB(){
	alert("1");
	// var check = fm_finalcheck();
	// finalcheckMB(); //alert("2");
	// document.mb_form.submit();
}

function submitMP(){
	finalcheckMP();
	calConsumption();
	document.mp_form.submit();
}

function submitNDeleteMB(){
	var inputs = document.getElementsByTagName("input");
	var delCheck = 0;
		for(var i = 0; i < inputs.length; i++) {
			if(inputs[i].type == "checkbox") {
				
				var id = inputs[i].id;
				var str = id.substring(0,5);
				
				if(str=="check"){
					var check = inputs[i].checked;
					delCheck = (check==true ? ++delCheck : delCheck );
					//alert(check+" "+id);
				}
			}  
		}
	if(delCheck>0){	
		document.getElementById("submitStatus").value = "delete";
		document.mb_form.submit();
	}else{
		alert("Please select at least 1 item");
	}
}

function submitMP_purchase(value){
	var inputs = document.getElementsByTagName("input");
	var delCheck = 0;
		for(var i = 0; i < inputs.length; i++) {
			if(inputs[i].type == "checkbox") {
				
				var id = inputs[i].id;
				var str = id.substring(0,7);
				
				if(str=="MPcheck"){
					var check = inputs[i].checked;
					delCheck = (check==true ? ++delCheck : delCheck );
					//alert(check+" "+id);
				}
			}  
		}
	if(delCheck>0){	
		document.getElementById("submitStatusMP").value = value;
		document.mp_form.submit();
	}else{
		alert("Please select at least 1 item");
	}
}

function calConsumption(){
	var count = document.getElementById("total").value;
	
	for(var i=1;i<=parseInt(count);i++){
		var countSub = document.getElementById("countSub"+i).value;
		for(var s=1;s<=parseInt(countSub);s++){
			
			var maxGM = document.getElementById("maxGM"+i+"-"+s).value;
			var external = document.getElementById("external"+i+"-"+s).value;
			var multiplier = document.getElementById("multiplier"+i+"-"+s).value;
			var allSize = document.getElementById("allSize"+i+"-"+s).value;
			var dozPcs = document.getElementById("dozPcs"+i+"-"+s).value;
			var garmentQty = document.getElementById("garmentQty"+i+"-"+s).value;
			var inv = document.getElementById("inv"+i+"-"+s).value;
			var wastage = document.getElementById("wastage"+i+"-"+s).value;
			var basic = document.getElementById("basic"+i+"-"+s).value;
			var consumCount = document.getElementById("consumCount"+i+"-"+s).value;
			
			var consumYDSInner = 0;
			var consumYDS = 0; 
			var consumLBS = 0;
			
			for(var t=1;t<=parseInt(consumCount);t++){ //alert(i+"-"+s+"-"+t);
				var gmtQty = document.getElementById("garmentQty"+i+"-"+s+"-"+t).value; //---garment qty---//
				var consumQty = document.getElementById("consumQty"+i+"-"+s+"-"+t).value; //---all size---//
				
				wastage = (wastage==""? 0: wastage);
				
				//if(parseInt(basic)!=64){
					consumYDSInner = (parseFloat(gmtQty) * parseFloat(consumQty) * (parseFloat(wastage)/100 + 1) / parseFloat(dozPcs));
				//}
				//alert(consumYDSInner+" "+i+"-"+s+"-"+t);
				//var consumLBSInner = parseFloat(consumYDSInner.toFixed(2)) * ((parseFloat(maxGM) * parseFloat(external) * 0.0232 * parseFloat(multiplier)) * 2.2046/1000 );
				
				consumYDS += parseFloat(consumYDSInner);
				//consumLBS += consumLBSInner;
			}
			
			var oneYDSequalLBS = parseFloat(maxGM) * parseFloat(external) * 0.0232 * parseFloat(multiplier) * 2.2046/1000;
			var invYDS = parseFloat(inv) / parseFloat(oneYDSequalLBS);
			
			consumYDS = (parseInt(basic)!=64) ? consumYDS - parseFloat(invYDS) : parseInt(consumYDS) - parseInt(inv);
			consumLBS = parseFloat(consumYDS.toFixed(2)) * ((parseFloat(maxGM) * parseFloat(external) * 0.0232 * parseFloat(multiplier)) * 2.2046/1000 );
			//alert(consumYDS+" "+consumLBS);
			// var consumYDS = (parseFloat(garmentQty) * parseFloat(allSize) * (parseFloat(wastage)/100 + 1) / parseFloat(dozPcs)) - invYDS;
			// var consumLBS = parseFloat(consumYDS.toFixed(2)) * ((parseFloat(maxGM) * parseFloat(external) * 0.0232 * parseFloat(multiplier)) * 2.2046/1000 );
			
			var ansYDS = consumYDS.toFixed(2);
			var ansLBS = consumLBS.toFixed(2);
			var displayLBS = ansLBS;
			
			if(parseInt(basic)==64){
				ansYDS = consumYDS.toFixed(0);
				ansLBS = 0;
				displayLBS = "--";
			}
			
			document.getElementById("qtyYDS"+i+"-"+s).value = ansYDS;
			document.getElementById("qtyLBS"+i+"-"+s).value = ansLBS;
			
			document.getElementById("displayYDS"+i+"-"+s).innerHTML = ansYDS;
			document.getElementById("displayLBS"+i+"-"+s).innerHTML = displayLBS;
			
			//alert(consumYDS.toFixed(2)+" - "+consumLBS.toFixed(2));
			
		}
	}
	
}

function funcResetRequest2(i1,i2){
	//alert(i1+i2+i3);
	document.getElementById("requestItem"+i1+"-"+i2).value = "";
	document.getElementById("requestQty"+i1+"-"+i2).value = "";
	document.getElementById("inv"+i1+"-"+i2).value = "0.0000";//*/
	document.getElementById("inv"+i1+"-"+i2).style.border="1px solid #bdbdbd";
	
	var checkMPDID = document.getElementById("MPDID"+i1+"-"+i2);
	if(checkMPDID!=null){
		//alert(checkMPDID);
		var MPDID = document.getElementById("MPDID"+i1+"-"+i2).value;
		deleteTransfer(MPDID);
	}	
}

function funcRequest2(i1, i2, orderno){
	//alert("aasa"+i1+" "+i2+" ");
	var i3 = 0;
	//alert(mmcid);
	var mmcid = document.getElementById("MMCID"+i1+"-"+i2).value;
	window.open("_test_request.php?orderno="+orderno+"&i1="+i1+"&i2="+i2+"&i3="+i3+"&mmcid="+mmcid, "Ratting", "width="+screen.width+", height="+screen.height+", left=150, top=200, toolbar=1, status=1,");
}

function checkDozPcs(){
	var firstValue = document.getElementById("type1").value;
	
	if(parseInt(firstValue)==1){ 
		document.getElementById("doz").disabled = false; 
		document.getElementById("pcs").disabled = true; 
	}else{ 
		document.getElementById("doz").disabled = true; 
		document.getElementById("pcs").disabled = false;
		//alert(document.getElementById("pcs").disabled);
		
	}
}

function fm_finalcheck(){
	var error = 0;
	var totalCount = document.getElementById("totalItem").value;
	for(var i=1;i<=parseInt(totalCount);i++){
		var position = document.getElementById("position"+i).value;
		alert(position);
	}
}

// function checkAllSynColor(){
	
	// var inputTags = document.body.getElementsByTagName('input'); 
	// var checkboxCount = 0;
	// for (var i=0, length = inputTags.length; i<length; i++) {
		// if (inputTags[i].type == 'checkbox') {
			
			// var id = inputTags[i].id;
			// var str = id.substring(0,10);
			
			// if(str=="colorCheck"){
				// alert(inputTags[i].id);
				// checkboxCount++;
			// }
		// }
	// }
	
// }

