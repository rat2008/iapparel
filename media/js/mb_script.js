function showDetail(selector){
      // console.log("selector = "+ selector);      
      if($(selector).hasClass("hiddenRow")){
      	$(selector).removeClass("hiddenRow");
      }else {
      	$(selector).addClass("hiddenRow");
      }
      
};

function validateShowDetail(selector){
	document.getElementById(selector).className = "";
}


function funcUnit(value){
	
	var item = document.getElementById("totalItem").value;
	//alert(item);
	
	for(var i=1;i<=parseInt(item);i++){
		
		if(value=="doz"){
			document.getElementById("type"+i).value = "12";
		}else{
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

function funcAllSize(value, row){
	//alert(value+" - "+row);
	var sizeRow = document.getElementById("sizeCount"+row).value;
	
	for(var i=1;i<=parseInt(sizeRow);i++){
		document.getElementById("size"+row+"-"+i).value = value;
	}
	
}
function funcToPurchase(){
	var check = finalCheck();
	if(check==true){
		
		calConsumption();
		$.post('_test_saveMB.php?status=purchase', $('#form_MB').serialize());
		//$.post('_test_toPurchase.php', $('#form_MB').serialize());
		
		//document.getElementById("btn_save").disabled = true;
		//document.getElementById("btn_load").disabled = true;
		document.getElementById("btn_purchase").disabled = true;
		//document.getElementById("btn_remove").disabled = true;
		document.getElementById("txt_status").value = "Confirm";
		//alert("");
		alertDisplay("Confirmed to purchase");
	}
}

//------------------------------------------------------------------------//
//-----------------FINAL CHECK BEFORE TO PURCHASE-------------------------//
//------------------------------------------------------------------------//
function finalCheck(){
	var totalItem = document.getElementById("totalItem").value;
	
	//--------------------First level for loop----------------------------//
	for(var item=1;item<=parseInt(totalItem);item++){
		//alert("--->"+item);
		var elementExists = document.getElementById("type"+item);
		if(elementExists!=null){
			var allSize = document.getElementById("allSize"+item).value;
			var position = document.getElementById("position"+item).value;
			var totalGarmentColor = document.getElementById("garmentRow"+item).value;
			
			if(position==""){
				alert("Please select position");
				//document.getElementById("position"+item).focus();
				document.getElementById("position"+item).className = "select_medium_error";
				
				return false;
			}else{
				document.getElementById("position"+item).className = "select_medium";
			}
			
			if(myTrim(allSize)!=""){		
				if(parseFloat(allSize)==0){
					alert("Field cannot be zero");
					document.getElementById("allSize"+item).className = "error_short";
					return false;
				}else{
					document.getElementById("allSize"+item).className = "txt_short";
				}
				
			}else{
				alert("Invalid number");
				document.getElementById("allSize"+item).className = "error_short";
				return false;
			}
		
			//--------------------Second level for loop----------------------------//
			for(var garment=1;garment<=parseInt(totalGarmentColor);garment++){
				
				var totalFabColor = document.getElementById("count"+item+"-"+garment).value;
				
				//--------------------Third level for loop----------------------------//
				for(var fab=1;fab<=parseInt(totalFabColor);fab++){
				
					var wastage = document.getElementById("waste"+item+"-"+garment+"-"+fab).value;
					var fabColor = document.getElementById("fabColor"+item+"-"+garment+"-"+fab).value;
					var pcode = document.getElementById("pcode"+item+"-"+garment+"-"+fab).value;
					
					//alert(wastage+" | "+fabColor+" | "+pcode);
					if(myTrim(wastage)!="" || fabColor=="0"){		
						document.getElementById("waste"+item+"-"+garment+"-"+fab).className = "txt_short";
					}else{
						alert("Invalid wastage percentage");
						document.getElementById("waste"+item+"-"+garment+"-"+fab).className = "error_short";
						validateShowDetail("m-detail"+item);
						//document.getElementById("waste"+item+"-"+garment+"-"+fab).focus();
						return false;
					}
					
					if(fabColor==""){
						alert("Please select a fabric color");
						document.getElementById("fabColor"+item+"-"+garment+"-"+fab).className = "select_medium_error";
						validateShowDetail("m-detail"+item);
						return false;
					}else{
						document.getElementById("fabColor"+item+"-"+garment+"-"+fab).className = "select_medium";
					}
					
					if(pcode == "" && fabColor != "0"){
						alert("Please fill in PCode");
						document.getElementById("pcode"+item+"-"+garment+"-"+fab).className = "error_short";
						validateShowDetail("m-detail"+item);
						return false
					}else{
						document.getElementById("pcode"+item+"-"+garment+"-"+fab).className = "txt_short";
					}
					
					
				
				}//---end for loop (third level)---//
			}//---end for loop (second level)---//
		}//---end if valid exists---//
	}//---end for loop (first level)---//
	return true;
}

//------------------------------------------------------------------------//
//----------------------CALCULATE CONSUMPTION-----------------------------//
//------------------------------------------------------------------------//

function calConsumption(){
	var totalItem = document.getElementById("totalItem").value;
	
	//--------------------First level for loop----------------------------//
	for(var item=1;item<=parseInt(totalItem);item++){
		//alert("--->"+item);
		
		var elementExists = document.getElementById("type"+item);
		if(elementExists!=null){
			
			var unit = document.getElementById("type"+item).value;//---pcs or doz---//
			var allSize = document.getElementById("allSize"+item).value;
			var totalGarmentColor = document.getElementById("garmentRow"+item).value;
			
			var maxGM = document.getElementById("maxGM"+item).value;
			var maxWidth = document.getElementById("maxWidth"+item).value;
			var multiplier = document.getElementById("multiplier"+item).value;
			
			//alert(unit+" "+allSize+" "+totalGarmentColor);
			
			//--------------------Second level for loop----------------------------//
			for(var garment=1;garment<=parseInt(totalGarmentColor);garment++){
				
				var garmentQty = document.getElementById("garmentColorQty"+item+"-"+garment).value;
				var totalFabColor = document.getElementById("count"+item+"-"+garment).value;
				
				//alert(garmentQty+" "+totalFabColor);
				
				//--------------------Third level for loop----------------------------//
				for(var fab=1;fab<=parseInt(totalFabColor);fab++){
					var consumptionYDS = 0;
					var consumptionLBS = 0;
					var wastage = document.getElementById("waste"+item+"-"+garment+"-"+fab).value;
					
					//---formula consumption yds---//
					consumptionYDS = parseInt(garmentQty) * parseFloat(allSize) * (parseFloat(wastage)/100 + 1) / parseInt(unit);
					//alert(garmentQty+" * "+unit+" * "+allSize+" * "+parseFloat(wastage)/100+" = "+consumptionYDS);
					
					//---formula consumption lbs---//
					consumptionLBS = parseFloat(consumptionYDS.toFixed(2)) * ((parseFloat(maxGM) * parseFloat(maxWidth) * 0.0232 * parseInt(multiplier)) * 2.2046/1000 );
					
					document.getElementById("yds"+item+"-"+garment+"-"+fab).value = consumptionYDS.toFixed(2);
					document.getElementById("lbs"+item+"-"+garment+"-"+fab).value = consumptionLBS.toFixed(2);
				
				}//---end for loop (third level)---//	
			
			}//---end for loop (second level)---//
		}//---end if valid exists---//
	}//---end for loop (first level)---//
	
}

function funcDelete(){
	
	var mmidArray = new Array();
	var count = 0;
	//alert("remove");
	$("input:checked").each(function() {
			var id = $(this).val();
			//alert(id);
			
			var sub = id.substring(0, 6);
			if(sub=="delete"){
				var deleteRow = id.substring(6);
				var mmidCheck = $("#cCheck"+deleteRow).val();
				var subMMID = mmidCheck.substring(0, 6);
				
				if(subMMID=="exists"){
					var mmid =mmidCheck.substring(6);
					mmidArray.push(mmid);
				}
				
				try{			
					var delRow = document.getElementById(deleteRow);
					delRow.parentNode.removeChild(delRow);
						
					var delRow2 = document.getElementById("m-detail"+deleteRow);
					delRow2.parentNode.removeChild(delRow2);
					
				}catch(e){
					alert(e);
				}
			
			}
			count++;
	});
	
	//alert(mmidArray.length);
	if(mmidArray.length > 0){
		var orderno = document.getElementById("orderno").value;
		$.post('_test_deleteMB.php?MMID='+mmidArray+'&orderno='+orderno);
	}
	
	if(count>0){
		alertDisplay("Removed");
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

function funcSave(){

	var totalItem = document.getElementById("totalItem").value;
	var statusID = document.getElementById("statusID").value;
	var checkStatus = true;
	
	
	
	for(var item=1;item<=parseInt(totalItem);item++){
		
		var elementExists = document.getElementById("cCheck"+item);
		if(elementExists!=null){
			//alert("exist");
			var id = document.getElementById("cCheck"+item).value;
			var check = id.substring(0,6);
			if(check!="exists"){
				document.getElementById("cCheck"+item).value = "exists"+id;
			}
		}
	}//*/
	
	if(statusID=="8"){
		checkStatus = finalCheck();
	}
	//alert("debug -->"+checkStatus);
	if(checkStatus==true){
		////$("#form_MB").ajaxSubmit({url: '_test_SaveMB.php', type: 'post'})
		calConsumption();
		$.post('_test_saveMB.php', $('#form_MB').serialize());
		alertDisplay("Saved");
		////window.location.reload();
	}
}//---end function save---//

function funcGetPCode(value, i, g, c){
	var pcode = $("option:selected", value).attr("class");
	document.getElementById("pcode"+i+"-"+g+"-"+c).value = pcode;

	if(pcode!=""){
		document.getElementById("pcode"+i+"-"+g+"-"+c).readOnly = true;
 	}else{
		document.getElementById("pcode"+i+"-"+g+"-"+c).readOnly = false;
	}
	
	//alert(pcode);
	
}


function myTrim(x) {
    return x.replace(/^\s+|\s+$/gm,'');
}

function alertDisplay(value){

	$(function() {
				$('<div class=overlay></div><div class=modal><img src=../media/icon/true.png width=14 height=14 /> '+value+'</div>')
					.insertBefore('#content_details_head')
					.delay(2000)
					.fadeOut(function() {
					$(this).remove(); 
					});
				});
	
}