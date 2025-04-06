function funcAllSize(value, id){
	//document.getElementById("size"+id).value = value;
	var by = document.getElementById("by"+id).value;
	var getCount = 0;

	switch(parseInt(by)){
		case 0: getCount = 10;break; //--- By Order ---//
		case 1: getCount = 20;break; //--- By Color ---//
		case 2: getCount = document.getElementById("bysize"+id).value;break; //--- By Size/Color ---//
		case 3: getCount = document.getElementById("bysize"+id).value;break; //--- By Size only ---//
		case 4: getCount = 30;break; //--- By Shipment ---//
		case 5: getCount = 3;break; //--- By Ratio Pack ---//
		case 6: getCount = 30;break; //--- By Pick List ---//
	}
	
	//------ By Size ------//
	if(parseInt(by)==2 || parseInt(by)==3){ 
		for(var i=1;i<=parseInt(getCount);i++){
			document.getElementById("size"+id+"-"+i).value = value;
		}
	}
	//------ By Style ------//
	/*else if(parseInt(by)==3){
		//alert(getCount)
		for(var i=1;i<=parseInt(getCount);i++){ //---- Count Style ----//
			var sizeCount = document.getElementById("sizeCount"+id+"-"+i).value;
			//alert("-"+sizeCount);
			for(var c=1;c<=parseInt(sizeCount);c++){ //---- Count Size ----//
				document.getElementById("size"+id+"-"+i+"-"+c).value = value;
			}
		}
	}*/
	//------ By Ratio Pack ------//
	else if(parseInt(by)==5){
		for(i=1;i<=parseInt(getCount);i++){ //---- Count Ratio Method ----//
			document.getElementById("size"+id+"-"+i).value = value;
		}
	}
}

function finalcheckAB(){
	$("#ab_form :input").prop("disabled", false);
}
function finalcheckAP(){
	$("#ap_form :input").prop("disabled", false);
}

function funcEdit(value){
	var res = value.split("-");
	var by = document.getElementById("by"+res[0]).value;
	
	//if(parseInt(by)==0){ //--- By Order ---//
		document.getElementById("displaySel"+value).style.display = "none";
		document.getElementById("displayEdit"+value).style.display = "inline";
		document.getElementById("btn_select"+value).style.display = "inline";
		document.getElementById("btn_edit"+value).style.display = "none";
		document.getElementById("mode"+value).value = "edit";
		
	//}
}

function funcSelect(value){
	var res = value.split("-");
	var by = document.getElementById("by"+res[0]).value;
	
	//if(parseInt(by)==0){ //--- By Order ---//
		document.getElementById("displaySel"+value).style.display = "inline";
		document.getElementById("displayEdit"+value).style.display = "none";
		document.getElementById("btn_select"+value).style.display = "none";
		document.getElementById("btn_edit"+value).style.display = "inline";
		document.getElementById("mode"+value).value = "select";
	//}
}

function funcAutoComplete(id, value){
	$("#"+id).autocomplete({
		source: "ajax_search.php?searchBy="+value,
		minLength: 1
	});
}

function funcDuplicate(id, txt){
	var byMethod = document.getElementById("by"+id).value;
	var countColor = document.getElementById("countColor").value;
	
	//alert(id+" "+txt);
	switch(parseInt(byMethod)){
		case 1: 
				var oriStandard = document.getElementById("txt_standard"+id).value;
				var oriColor = document.getElementById("txt_color"+id).value;
				for(var c=1;c<=parseInt(countColor);c++){
					//var ele = document.getElementById("txt_standardAcc"+id+"-"+c); alert(ele+" "+c);
					if(txt=="standard"){
						document.getElementById("txt_standardAcc"+id+"-"+c).value = oriStandard;
					}else{
						document.getElementById("txt_colorAcc"+id+"-"+c).value = oriColor;
					}
				}
	}
}

function funcByMethod(id, value){
	var table = document.getElementById("tb_detail");
	var row = document.getElementById("row"+id).rowIndex;
	var countColor = document.getElementById("countColor").value;
	var orderno = document.getElementById("orderno").value;
	var contentID = document.getElementById("content"+id).value;
	var sizeValue = document.getElementById("size"+id).value;
	var res = contentID.split(":");
	var amid = res[0];

	//------ By Order ------//
	if(parseInt(value)==0){
		funcDelRow(id);
		funcMergeCol(row, id, value);
		document.getElementById("displayMainColor"+id).style.display = "none";
	}
	//------ By Color ------//
	else if(parseInt(value)==1){
		funcDelRow(id);
		var countColor = document.getElementById("countColor").value;
		var ele = document.getElementById("combine"+id);
		document.getElementById("displayMainColor"+id).style.display = "inline";
		
		if(ele != null){
			for(var i=1;i<parseInt(countColor);i++){
				// first mark cell with id="c1"
				REDIPS.table.mark(true, 'combine'+id);
				// and then split marked cell in table2
				REDIPS.table.split('h', 'tb_detail');
			}//---- End For Loop ----//
		}
		
		var cc = 1;
		for(var i=11;i<parseInt(countColor)+11;i++){
			var col = table.rows[row].cells[i];
			var color = document.getElementById("mColorName"+cc).value;
			
			table.rows[row].cells[i].innerHTML = "Loading...";
			table.rows[row].cells[i].className = "detail";
			table.rows[row].cells[i].id = "combine"+id+"-"+cc;
			table.rows[row].cells[i].title = color;
			
			ajax_standard(col, id, cc);
			
			cc++;
		}//---- End For Loop ----//
	}
	//------ By Size ------//
	else if(parseInt(value)==2){
		document.getElementById("displayMainColor"+id).style.display = "none";
		funcDelRow(id);
		funcMergeCol(row, id, value);
		var link = "ajax_bysize.php?id="+id+"&countColor="+countColor+"&order="+orderno+"&amid="+amid+"&sizeValue="+sizeValue;
		ajax_addContent(id, link);
		//alert($row);
	}
	//------- By Styling ------//
	else if(parseInt(value)==3){
		funcDelRow(id);
		funcMergeCol(row, id, value);
		//var link = "ajax_bystyle.php?id="+id+"&countColor="+countColor+"&order="+orderno+"&amid="+amid+"&sizeValue="+sizeValue;
		var link = "ajax_sizeonly.php?id="+id+"&countColor="+countColor+"&order="+orderno+"&amid="+amid+"&sizeValue="+sizeValue;
		ajax_addContent(id, link);
	}
	//-------- By Shipment Ratio ------//
	else if(parseInt(value)==5){
		document.getElementById("displayMainColor"+id).style.display = "none";
		funcDelRow(id);
		funcMergeCol(row, id, value);
		var link = "ajax_byshipment.php?id="+id+"&countColor="+countColor+"&order="+orderno+"&amid="+amid+"&sizeValue="+sizeValue;
		ajax_addContent(id, link);
	}
	//-------- By Order Pick List ------//
	else if(parseInt(value)==6){
		funcDelRow(id);
		funcMergeCol(row, id, value);
		document.getElementById("displayMainColor"+id).style.display = "none";
	}
}

function ajax_standard(col, id, cc){
	var contentID = document.getElementById("content"+id).value;
	var by = document.getElementById("by"+id).value;
	var res = contentID.split(":");
	var amid = res[0];
	var oriStandard = (parseInt(cc)==0) ? "NONE": document.getElementById("txt_standard"+id).value;
	var oriColor = (parseInt(cc)==0) ? "": document.getElementById("txt_color"+id).value;
	var myid = (parseInt(cc)==0) ? id : id+"-"+cc;
	//alert(amid);
	
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
			col.innerHTML = xmlhttp.responseText;
			document.getElementById("txt_standardAcc"+myid).value = oriStandard;
			document.getElementById("txt_colorAcc"+myid).value = oriColor;
			checkContentValue(myid, contentID, by);
		}
	 }
	xmlhttp.open("GET","ajax_standard.php?amid="+amid+"&id="+id+"&cc="+cc+"&oriColor="+oriColor+"&oriStandard="+oriStandard,true);
	xmlhttp.send();
	
}

function ajax_addContent(id, link){
	var countColor = document.getElementById("countColor").value;
	var countSize = document.getElementById("countSize").value;
	var orderno = document.getElementById("orderno").value;
	var by = document.getElementById("by"+id).value;
	var contentID = document.getElementById("content"+id).value;
	var res = contentID.split(":");
	var amid = res[0];
	
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
			//col.innerHTML = xmlhttp.responseText;
			//var newContent = "<tr><td colspan='7' class='detail_left'>new td</td></tr>";
			var newContent = xmlhttp.responseText;
			$(""+newContent).insertAfter("#row"+id);
			
			for(var s=1;s<=parseInt(countSize);s++){
				var eleColor = document.getElementById("txt_colorAcc"+id+"-"+s);
				funcAutoComplete("txt_standardAcc"+id+"-"+s, "standard");
				checkContentValue(id+"-"+s, contentID, by);
				
				if(eleColor != null){
					funcAutoComplete("txt_colorAcc"+id+"-"+s, "color");
					checkContentValue(id+"-"+s, contentID, by);
				}
				
				for(var c=1;c<=parseInt(countColor);c++){
					var ele = document.getElementById("txt_colorAcc"+id+"-"+s+"-"+c);
					//alert(ele+" "+id+"-"+s+"-"+c);
					if(ele != null){
						funcAutoComplete("txt_colorAcc"+id+"-"+s+"-"+c, "color");
						checkContentValue(id+"-"+s+"-"+c, contentID, by);
						var colorName = document.getElementById("mColorName"+c).value;
						document.getElementById("txt_colorAcc"+id+"-"+s+"-"+c).value = colorName;
					}
				}//--- End For Loop Count Color ---//
			}//--- End For Loop Count Size ---//
			
			var by = document.getElementById("by"+id).value;
			if(parseInt(by)==5){
				funcSetRowSpan(id);
			}
		}
	}
	xmlhttp.open("GET",""+link,true);
	xmlhttp.send();

}

function funcSetRowSpan(id){
	var rowNum = document.getElementById("countRatio"+id).value;
	document.getElementById("tdratio"+id).rowSpan = rowNum;
	//alert(rowNum);
}

function funcDelRow(id){
	$('table tr.rowExtra'+id).remove();
	
	// try{			
		// var delRow = document.getElementById("rowExtra"+id);
		// delRow.parentNode.removeChild(delRow);
					
	// }catch(e){
		// alert(e);
	// }
	
}

function funcMergeCol(row, id, value){
	var table = document.getElementById("tb_detail");
	var innerContent = (parseInt(value)==0) ? "Loading..." : "";
	row = document.getElementById("row"+id).rowIndex;
		
	// mark cells for merging (cells should be marked in a sequence)
	var countColor = document.getElementById("countColor").value;
		var start = 11;
		var cc = 0;
		var end = parseInt(start) + parseInt(countColor);
		
		for(var i=start;i<=parseInt(end);i++){
			REDIPS.table.mark(true, 'tb_detail', row, i);
		}
		//REDIPS.table.mark(true, 'tb_detail', 2, 3);
		//REDIPS.table.mark(true, 'tb_detail', 2, 4);
		// merge cells:
		// 'h' - horizontally
		// true - clear mark after merging
		// 'table1' - table id
		REDIPS.table.merge('h', true, 'tb_detail');
		var col = table.rows[row].cells[start];
				  table.rows[row].cells[start].innerHTML = innerContent;
				  //table.rows[row].cells[start].className = "detail text-center";
				  table.rows[row].cells[start].id = "combine"+id; 
	
	//------ If Method is Order Or Pick List Order ------//
	if(parseInt(value)==0 || parseInt(value)==6){
		ajax_standard(col, id, cc);
	}
}

function funcGetUnit(id, value){ 
	var by = document.getElementById("by"+id).value;
	
	//------ By Order ------//
	if(parseInt(by)==0){
		funcByMethod(id, 0);
		
	//------ By Color ------//
	}else if(parseInt(by)==1){
		funcByMethod(id, 1);
		
	//------ By Size ------//
	}else if(parseInt(by)==2){
		funcDelRow(id);
		funcByMethod(id, 2);
		
	//------ By Style ------//
	}else if(parseInt(by)==3){
		funcDelRow(id);
		funcByMethod(id, 3);
	
	//------ By Ratio ------//
	}else if(parseInt(by)==5){
		funcDelRow(id);
		funcByMethod(id, 5);
	}
	//alert(value);
	var res = value.split(":");
	if(parseInt(value)!="0"){
		document.getElementById("displayUnit"+id).innerHTML = res[2];
		document.getElementById("unit"+id).value = res[1];
		document.getElementById("unitText"+id).value = res[2];
	}
}

function checkContentValue(id, value, by){
	var countColor = document.getElementById("countColor").value;
	var check =true;
	if(parseInt(value)!=0){
		check = false;
	}
	//alert(check+" "+by);
	switch(parseInt(by)){
		case 0: //---- By Order Method ----// 
			var ele = document.getElementById("txt_standardAcc"+id);
			//alert(ele+" txt_standardAcc"+id);
			document.getElementById("txt_standardAcc"+id).readOnly = check;
			document.getElementById("txt_colorAcc"+id).readOnly = check;
			
			if(check==true){
				document.getElementById("txt_standardAcc"+id).value = "NONE";
				document.getElementById("txt_colorAcc"+id).value = "";
			}break;
		
		case 1: //---- By Color Method ----//
			var res = id.split("-");
			document.getElementById("txt_standard"+res[0]).readOnly = check;
			document.getElementById("txt_color"+res[0]).readOnly = check;
			if(check==true){
				document.getElementById("txt_standard"+res[0]).value="NONE";
				document.getElementById("txt_color"+res[0]).value="";
				document.getElementById("txt_standardAcc"+id).value="NONE";
				document.getElementById("txt_colorAcc"+id).value="";
			}
			break;
		
		case 2: //---- By Size Method ----//
			//alert(id);
			var eleStandard = document.getElementById("txt_standardAcc"+id);
			var eleColor = document.getElementById("txt_colorAcc"+id);
			
			if(eleStandard != null){
				document.getElementById("txt_standardAcc"+id).readOnly = check;
			}
			if(eleColor != null){
				document.getElementById("txt_colorAcc"+id).readOnly = check;
			}
			
			if(check==true){
				if(eleStandard != null){
					document.getElementById("txt_standardAcc"+id).value = "";
				}
				if(eleColor != null){
					document.getElementById("txt_colorAcc"+id).value = "";
				}
			}
			break;
			
		case 5: //---- By Ratio Method ----//
			var eleStandard = document.getElementById("txt_standardAcc"+id);
			var eleColor = document.getElementById("txt_colorAcc"+id);
			
			if(eleStandard != null){
				document.getElementById("txt_standardAcc"+id).readOnly = check;
			}
			if(eleColor != null){
				document.getElementById("txt_colorAcc"+id).readOnly = check;
			}
			
			if(check==true){
				if(eleStandard != null){
					document.getElementById("txt_standardAcc"+id).value = "";
				}
				if(eleColor != null){
					document.getElementById("txt_colorAcc"+id).value = "";
				}
			}
			break;
		case 6: //---- By Pick List Method ----// 
			var ele = document.getElementById("txt_standardAcc"+id);
			//alert(ele+" txt_standardAcc"+id);
			document.getElementById("txt_standardAcc"+id).readOnly = check;
			document.getElementById("txt_colorAcc"+id).readOnly = check;
			
			if(check==true){
				document.getElementById("txt_standardAcc"+id).value = "NONE";
				document.getElementById("txt_colorAcc"+id).value = "";
			}break;
	}
}

function submitAB(){
	//finalcheckMP();
	//calConsumption();
	document.getElementById("btn_submit").disabled = true;
	finalcheckAB();
	document.ab_form.submit();
}

function funcbuyerPO(value, id){
	var orderno = document.getElementById("orderno").value;
	
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
			var newContent = document.getElementById("PODisplay"+id);
			newContent.innerHTML = xmlhttp.responseText;
		}
	 }
	xmlhttp.open("GET","ajax_buyerPO.php?orderno="+orderno+"&style="+value+"&id="+id,true);
	xmlhttp.send();
}

function calConsumption(){
	var rowCount = document.getElementById("rowCount").value;
	var total = 0;
	
	for(var i=1;i<=parseInt(rowCount);i++){
		var subRow = document.getElementById("subRowCount"+i).value;
		var allQty = document.getElementById("allQty"+i).value;
		var dozPcs = document.getElementById("dozPcs"+i).value;
		
		for(var c=1;c<=parseInt(subRow);c++){
			var qty = document.getElementById("qty"+i+"-"+c).value;
			var wastage = document.getElementById("wastage"+i+"-"+c).value;
			var inv = document.getElementById("inv"+i+"-"+c).value;
			var unit = document.getElementById("unit"+i+"-"+c).value;
			var consum = document.getElementById("consum_All"+i+"-"+c).value;
			var unit_int = unit.replace ( /[^\d.]/g, '' );
			var num_unit = (unit_int=="")? 1: 1;
			//alert(num_unit);
			
			if(parseFloat(qty) >= 0){
				total = (((parseFloat(qty) * parseFloat(consum) * ((parseFloat(wastage)/100)+1)) / parseInt(dozPcs)) / parseInt(num_unit)) - parseFloat(inv);
			}else{
				total = 0;
			}
			
			if(parseInt(i)>8){
				//alert(total+" - "+c+" | "+qty+":"+allQty+":"+wastage+":"+dozPcs+":"+num_unit);
			}
			//alert(total);
			
			document.getElementById("displayConsum"+i+"-"+c).innerHTML = total.toFixed(4);
			document.getElementById("consum"+i+"-"+c).value = total.toFixed(4);
			
		}//------ End For Loop (Inner) ------//
	}//------ End For Loop (Outer) ------//
}

function submitAP(){
	document.getElementById("btn_submit_plan").disabled = true;
	finalcheckAP();
	calConsumption();
	document.ap_form.submit();
}

function submitAP_purchase(value){
	var inputs = document.getElementsByTagName("input");
	var delCheck = 0;
		for(var i = 0; i < inputs.length; i++) {
			if(inputs[i].type == "checkbox") {
				
				var id = inputs[i].id;
				var str = id.substring(0,7);
				
				if(str=="mycheck"){
					var check = inputs[i].checked;
					delCheck = (check==true ? ++delCheck : delCheck );
					//alert(check+" "+id);
				}
			}  
		}
	if(delCheck>0){	
		document.getElementById("submitStatusAP").value = value;
		submitAP();
		//document.mp_form.submit();
	}else{
		alert("Please select at least 1 item");
	}
}

function submitNDeleteAB(){
	var inputs = document.getElementsByTagName("input");
	var delCheck = 0;
		//alert(inputs.length);
		for(var i = 0; i < inputs.length; i++) {
			if(inputs[i].type == "checkbox") {
				
				var id = inputs[i].id;
				var str = id.substring(0,6);
				if(str=="delete"){
					var check = inputs[i].checked;
					delCheck = (check==true ? ++delCheck : delCheck );
					//alert(check+" "+id);
				}
			}  
		}
	if(delCheck>0){	
		document.getElementById("submitStatus").value = "delete";
		//document.mb_form.submit();
		submitAB();
	}else{
		alert("Please select at least 1 item");
	}
}

function funcSyncWastage(){
	var rowCount = document.getElementById("rowCount").value;
	var wastage = document.getElementById("wastage").value;
	
	for(var i=1;i<=parseInt(rowCount);i++){
		var subRow = document.getElementById("subRowCount"+i).value;
		for(var c=1;c<=parseInt(subRow);c++){
			var ele = document.getElementById("wastage"+i+"-"+c);
			if(ele != null){
				var check = document.getElementById("wastage"+i+"-"+c).disabled;
				
				if(check == false){
					document.getElementById("wastage"+i+"-"+c).value = wastage;
				}
			}
		}//------ For Loop (Inner) ------//
	}//------ For Loop (Outer) ------//
}

function funcSyncCur(value){
	var rowCount = document.getElementById("rowCount").value;
	for(var i=1;i<=parseInt(rowCount);i++){
		var subRow = document.getElementById("subRowCount"+i).value;
		for(var c=1;c<=parseInt(subRow);c++){
			var ele = document.getElementById("currency"+i+"-"+c);
			if(ele != null){
				var check = document.getElementById("currency"+i+"-"+c).disabled;
				if(check == false){
					$("#currency"+i+"-"+c).val(value);
				}
			}
		}//------ For Loop (Inner) ------//
	}//------ For Loop (Outer) ------//
}

function funcSyncPrice(id, row){
	var rowCount = document.getElementById("subRowCount"+row).value;
	var unitprice = document.getElementById("unitprice"+id).value;
	for(var cc=1;cc<=parseInt(rowCount);cc++){
		var check = document.getElementById("unitprice"+row+"-"+cc).disabled;
		if(check == false){
			document.getElementById("unitprice"+row+"-"+cc).value = unitprice;
		}
	}
}

function funcPlanRowSpan(id, value){
	document.getElementById("a"+id).rowSpan = value;
	document.getElementById("b"+id).rowSpan = value;
	document.getElementById("c"+id).rowSpan = value;
	document.getElementById("d"+id).rowSpan = value;
	document.getElementById("e"+id).rowSpan = value;
	document.getElementById("f"+id).rowSpan = value;
}

function funcSyncColor(name, myid, id, c){
	var colorDef = document.getElementById(name+""+myid).value;
	var sizeCount = document.getElementById("bysize"+id).value;
	
	for(var ss=1;ss<=parseInt(sizeCount);ss++){
		var eleCheck = document.getElementById(name+""+id+"-"+ss+"-"+c);
		
		if(eleCheck != null){
			var check = document.getElementById(name+""+id+"-"+ss+"-"+c).disabled;
			if(check == false){
				document.getElementById(name+""+id+"-"+ss+"-"+c).value = colorDef;
			}
		}
		
		var eleCheck2 = document.getElementById(name+""+id+"-"+ss);
		if(eleCheck2 != null){
			var check = document.getElementById(name+""+id+"-"+ss).disabled;
			if(check == false){
				document.getElementById(name+""+id+"-"+ss).value = colorDef;
			}
		}
	}
}

function funcRequest_Acc(i1, i2, orderno){
	//alert("here");
	var ascid,mode;
	ascid = document.getElementById("ASCID"+i1+"-"+i2).value;
	//mode = document.getElementById("ratiomode"+i1+"-"+i2).value;
	//alert(i1+" "+i2+" "+i3+" "+orderno+" -->"+ascid);
	// if(mode=="edit"){
		// ascid = 0;
	// }
	
	window.open("acc_request.php?orderno="+orderno+"&i1="+i1+"&i2="+i2+"&ascid="+ascid, "Ratting", "width="+screen.width+", height="+screen.height+", left=150, top=200, toolbar=1, status=1,");
}

function funcResetInvQty(i,g){
	var APDID = "";

	document.getElementById("inv"+i+"-"+g).value = "0.000";
	//document.getElementById("ASCID"+i+"-"+g).value = "";
	document.getElementById("requestItem"+i+"-"+g).value = "";
	document.getElementById("requestQty"+i+"-"+g).value = "";
			
	APDID = document.getElementById("myAPDID"+i+"-"+g).value;
	
	deleteTransfer(APDID);
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

function alertDisplay(value, status){
	var className = (status == "saved")? "alert-success":"alert-danger";
	var icon = (status == "saved")? "glyphicon-ok":"glyphicon-remove";
	icon = (status == "to purchase")? "glyphicon-ok" : icon; 
	className = (status == "to purchase")? "alert-warning" : className; 

	$(function() {
		$("<div class='alert "+className+"' id='notice_display'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>"+
		  "<span class='glyphicon "+icon+"'></span> "+value+" has been "+status+".</div>")
			.insertBefore('.panel-heading')
			.delay(2000)
			.fadeOut(function() {
				$(this).remove(); 
			});
	});
}