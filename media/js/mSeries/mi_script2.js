
//============================Select & Issued Material (Step 1)=================================//
var issued = new Array();
var arr_remark = new Array();
var arr_order = new Array();
var issuedNumber = new Array(); //Associative Arrays
var issuedUnit = new Array(); //Associative Arrays

$(document).ready(function() {
	//alert("Test");
	$('#tb_detail').dataTable({
		 dom: 'T<"clear">lfrtip',
                tableTools: {
                    "sSwfPath": "../../media/swf/copy_csv_xls_pdf.swf",
                    "aButtons": [
                        {
                            "sExtends": "csv",
                            "sButtonText": "Export Excel",
                            "sMessage": "material_wh",
                            "sTitle": "material_wh",
                            "oSelectorOpts": { filter: 'applied', order: 'current' }
                        }
                    ]
                },//*/
		"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]]
	});
	
	var table = $('#tb_detail').DataTable();
	table.columns().eq( 0 ).each( function ( colIdx ) {
		$('input', table.column( colIdx ).header()).on( 'keyup change', function () {
			table
				.column( colIdx )
				.search( this.value )
				.draw();
			});
		});
	
	 
    $('#tb_detail tbody').on( 'click', 'tr', function (){
        $(this).toggleClass('selected');
		var trid = $(this).closest('tr').attr('id');
		//alert(trid);
		
		var status = $('#c'+trid).is(':checked');
		var value =  $("#c"+trid).val();
		var issue =  $("#issue"+trid).val();
		var trf_unit =  $("#trf_unit"+trid).val();
		var remark_ele = document.getElementById("remarks"+trid);
		var order_ele = document.getElementById("select_order"+trid);
		
	
		if(status==false){
			$("#c"+trid).prop('checked', true);
			issued.push(value);
			//alert(value);
			issuedNumber[value] = issue;
			issuedUnit[value] = trf_unit;
			funcCalculator();
			//alert(issuedNumber[value]);
			if(remark_ele!=null){
				arr_remark[value] = remark_ele.value;
			}
			if(order_ele!=null){
				arr_order[value] = order_ele.value;
			}
			$("#this_chk"+trid).prop('checked', true);
			
		}else{	
			$("#c"+trid).prop('checked', false);
			issued.remove(value);
			funcCalculator();
			
			$("#this_chk"+trid).prop('checked', false);
		}	
    });
	
	Array.prototype.remove = function() {
    var what, a = arguments, L = a.length, ax;
    while (L && this.length) {
        what = a[--L];
        while ((ax = this.indexOf(what)) !== -1) {
            this.splice(ax, 1);
        }
    }
    return this;
	};
 
    $('#btn_issued').click( function () {
        //alert( table.rows('.selected').data().length +' row(s) selected' );
		//alert(issued.toString());
		//alert(issuedNumber.toString());
		
		funcFormSubmit("mi_issuing.php","Issued");
		
    });
	
	$('#btn_return').click( function () {
		funcFormSubmit("mi_returning.php","Return");
	});
	$('#btn_transfer').click( function () {
		var from = document.getElementById("from");
			str_from = (from==null? "": from.value);
		//alert("test");
		//alert(issued.toString());
		//alert(issuedNumber.toString());
		funcFormSubmit("transfering.php?from="+str_from,"Transfer");
	});
	
	$('#btn_transferA').click( function () {
		var from = document.getElementById("from");
			str_from = (from==null? "": from.value);
		//alert("test");
		//alert(issued.toString());
		//alert(issuedNumber.toString());
		funcFormSubmit("transfering.php?from="+str_from,"Transfer");
	});
	
	$('#btn_warehouse_receive').click( function () {
		//alert("test");
		//alert(issued.toString());
		//alert(issuedNumber.toString());
		funcFormSubmit("wh_receiving.php","Receive");
	});
	
	
	$('#btn_request').click( function () {
		//alert("test");
		//alert(issued.toString());
		//alert(issuedNumber.toString());
		funcFormSubmit("requesting.php","Request");
	});
	$('#btn_requestAcc').click( function () {
		//alert("test");
		//alert(issued.toString());
		//alert(issuedNumber.toString());
		funcFormSubmit("acc_requesting.php","Request");
	});
});

function funcSelectAll(){
	$('#tb_detail tbody tr').each(function() {
	 $(this).toggleClass('selected');
		var trid = $(this).closest('tr').attr('id');
		//alert(trid);
		
		var status = $('#c'+trid).is(':checked');
		var value =  $("#c"+trid).val();
		var issue =  $("#issue"+trid).val();
		var remark_ele = document.getElementById("remarks"+trid);
		var order_ele = document.getElementById("select_order"+trid);
		
	
		if(status==false){
			$("#c"+trid).prop('checked', true);
			issued.push(value);
			//alert(value);
			issuedNumber[value] = issue;
			funcCalculator();
			//alert(issuedNumber[value]);
			if(remark_ele!=null){
				arr_remark[value] = remark_ele.value;
			}
			if(order_ele!=null){
				arr_order[value] = order_ele.value;
			}
			
		}else{	
			$("#c"+trid).prop('checked', false);
			issued.remove(value);
			funcCalculator();
			
		}	
	});
}

function funcCalculator(){
	var eleChkSel = document.getElementById("str_selected");
	var total = 0;
	for(var i=0; i<issued.length;i++){
		//alert(issuedNumber[issued[i]]);
		total += parseFloat(issuedNumber[issued[i]]);
	}
	if(eleChkSel!=null){
		var ans =  parseFloat(total);
		eleChkSel.value = ans.toFixed(3);
	}
}

function funcFormSubmit(directory, value){
	//alert(issued.length);
	var count = 0;
	var issueTo = document.getElementById("issueTo").value;
	var screenID_ele   = document.getElementById("screenID");
	var trimtypeID_ele = document.getElementById("trimtypeID");
	//alert(issueTo);
	
	if(issued.length==0){
		
		alert("Please select at least one material");
		
	}else{
	
		var theForm, newInput1, newInput2, newInput3, newInput4, newInput5, newInput6, newInput7, newInput8, newInput9, newInput10, newInput11, check = true;
		theForm = document.createElement('form');
				theForm.action = directory;
				theForm.method = 'post';
		
		for(var i=0;i<issued.length;i++){
			
			var id = issued[i];
			//alert(id);
			var remark="";
			var this_order="";
			if(directory=="wh_receiving.php"){
				remark = arr_remark[id];
				this_order = arr_order[id];
			}
			if(directory=="transfering.php"){
				this_order = arr_order[id];
			}
			
			var issueQty = parseFloat(issuedNumber[id]);
			
			if (isNaN(issueQty)){
				//alert(issueQty);
				check = false;
			}
			count++;

			newInput1 = document.createElement('input');
				newInput1.type = 'hidden';
				newInput1.name = 'MRDIID'+count;
				newInput1.value = id;
				theForm.appendChild(newInput1);
			
			newInput2 = document.createElement('input');
				newInput2.type = 'hidden';
				newInput2.name = 'issuedQty'+count;
				newInput2.value = issueQty.toFixed(3);
				theForm.appendChild(newInput2);
				
			if(value=="Transfer"){
				newInput2 = document.createElement('input');
				newInput2.type = 'hidden';
				newInput2.name = 'trf_unit'+count;
				newInput2.value = issuedUnit[id];
				theForm.appendChild(newInput2);
			}
			
			
			newInput7 = document.createElement('input');
				newInput7.type = 'hidden';
				newInput7.name = 'remark'+count;
				newInput7.value = remark;
				theForm.appendChild(newInput7);
				
			newInput8 = document.createElement('input');
				newInput8.type = 'hidden';
				newInput8.name = 'this_order'+count;
				newInput8.value = this_order;
				theForm.appendChild(newInput8);
		}//------end for loop------//
		
		
		if(value=="Request"){
			var POHID = $("#POHID").val();
			newInput11 = document.createElement('input');
			newInput11.type = 'hidden';
			newInput11.name = 'POHID';
			newInput11.value = POHID;
			theForm.appendChild(newInput11);
		}
		
		newInput3 = document.createElement('input');
				newInput3.type = 'hidden';
				newInput3.name = 'countRow';
				newInput3.value = issued.length;
				theForm.appendChild(newInput3);
				
		newInput4 = document.createElement('input');
				newInput4.type = 'hidden';
				newInput4.name = 'issueTo';
				newInput4.value = issueTo;
				theForm.appendChild(newInput4);
				
		var ele_wh = document.getElementById("select_warehouseID");
		//alert(ele_wh);
		if(ele_wh!=null){
			newInput5 = document.createElement('input');
					newInput5.type = 'hidden';
					newInput5.name = 'selected_warehouseID';
					newInput5.value = ele_wh.value;
					theForm.appendChild(newInput5);
					
			newInput6 = document.createElement('input');
					newInput6.type = 'hidden';
					newInput6.name = 'selected_warehouse_str';
					newInput6.value = ele_wh.options[ele_wh.selectedIndex].text;
					theForm.appendChild(newInput6);
		}
		
		if(screenID_ele!=null && trimtypeID_ele!=null){
			newInput9 = document.createElement('input');
					newInput9.type = 'hidden';
					newInput9.name = 'screenID';
					newInput9.value = screenID_ele.value;
					theForm.appendChild(newInput9);
					
			newInput10 = document.createElement('input');
					newInput10.type = 'hidden';
					newInput10.name = 'trimtypeID';
					newInput10.value = trimtypeID_ele.value;
					theForm.appendChild(newInput10);
		}
		
		if(check==true){
			document.getElementById('hidden_form_container').appendChild(theForm);
			// ...and submit it
			theForm.submit();//
		}else{
			alert(value+" quantity can't be empty");
		}
	
	}//end if*/
	
}

function funcKeyRemark(id, value){
	arr_remark[id] = value;
}

function funcValidQty(value,id,max,i){
	var name = document.getElementById("c"+i).value;
	//alert(max);
	if(value.trim()!=""){
		if (!isNaN(value)) 
		{
			var issue = parseFloat(value);
			var maxValue = parseFloat(max);
			
			if(issue>maxValue){
				document.getElementById(id).value = maxValue;
			}
			//document.getElementById(id).value = issue.toFixed(2);
			//alert(issue);*/
		}

	}else{
		document.getElementById(id).value = "";
	}
	
	var myIssue = document.getElementById(id).value;
	issuedNumber[name] = myIssue;
	//alert(issuedNumber[name]+" "+name);
	funcCalculator();
}

function funcDecimalTwo(value,id,name){
	
	var newValue = parseFloat(value);
	//alert(newValue);
	
	if(newValue==0){
		newValue = 0.01;
	}
	
	document.getElementById(id).value = newValue.toFixed(3);
	issuedNumber[name] = newValue;
}

/*===============================Confirmation Issuing Step (Step 2)===================================*/

//------------------Make All Checkbox become checked/uncheck-------------------------//
	function funcCheckAll(){
			
		var check = document.getElementsByName("check[]");
		//alert(check.length);
		
		for(var n=1;n<=check.length;n++){
			var element = document.getElementById(n);
			
			if (document.getElementById('checkHead').checked){
				document.getElementById("c"+n).checked = true;
				element.className = "selected";
				
			}else{
				document.getElementById("c"+n).checked = false;
				element.className = "";
			}
		 
		}//end for loop
		
		document.getElementById("total").innerHTML = (document.getElementById('checkHead').checked?check.length:0);
		
	}
	//------------------Make Single Checkbox become checked/uncheck-------------------------//
	function funcCheckSingle(id){
		//alert(id);
		var element = document.getElementById(id);
		var total = document.getElementById("total").innerHTML;
		var editedTotal = 0;
		
		if(document.getElementById("c"+id).checked){
			element.className = "selected";
			editedTotal = parseInt(total) + 1;
		}else{
			element.className = "";
			editedTotal = parseInt(total) - 1;
		}
		
		document.getElementById("total").innerHTML = editedTotal;
	}
	
	//------------------User edit issued qty mode-------------------------//
	function funcEditMode(id){
		document.getElementById("qty_unit"+id).style.display = "none";
		document.getElementById("unit"+id).style.display = "inline";
		document.getElementById('issuedQty'+id).type = 'number';
		document.getElementById('issuedQty'+id).focus();
		var input = document.getElementById('issuedQty'+id);
		var inputValue = document.getElementById('issuedQty'+id).value;//-----original input value as Default------//
		
		input.onblur = funcOnBlur;
		input.onkeyup = funcValidQtyConfirm;
		//alert(id);
		
		//------------------Change to display mode-------------------------//
		function funcOnBlur(){
			//alert("asas"+id);
			var v = document.getElementById("issuedQty"+id).value;
			var qty = parseFloat(v);
			
			if(v==""||qty==0){qty=parseFloat(inputValue);}//------check if textbox is empty, use default input value.------//
			
			
			document.getElementById("qty_unit"+id).style.display = "inline";
			document.getElementById("unit"+id).style.display = "none";
			document.getElementById('issuedQty'+id).type = 'hidden';
			
			document.getElementById('qty'+id).innerHTML = qty.toFixed(3);
			document.getElementById("issuedQty"+id).value = qty.toFixed(3);
		}
		
		//------------------Valid Issued Qty amount-------------------------//
		function funcValidQtyConfirm(){ 
			var value = document.getElementById('issuedQty'+id).value
			var max = document.getElementById('max'+id).value
			//alert("asas");
			if(value.trim()!=""){
				if (!isNaN(value)) 
				{
					var issue = parseFloat(value);
					var maxValue = parseFloat(max);
					
					if(issue>maxValue){
						document.getElementById('issuedQty'+id).value = maxValue;
		
					}
					//document.getElementById(id).value = issue.toFixed(2);
					//alert(issue);*/
				}

			}else{
				document.getElementById('issuedQty'+id).value = "";
			}
		}
	}
