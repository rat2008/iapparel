
//============================Select & Issued Material (Step 1)=================================//
var issued = new Array();
var issuedNumber = new Array(); //Associative Arrays

$(document).ready(function() {
	
	//===============================================================//
	//===============================================================//
	//===============================================================//
	
	/*$('#tb_detail').dataTable( {
		"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]]
	});*/
	
	//var table = $('#tb_detail').DataTable();
	
	/*// Apply the filter
		table.columns().eq( 0 ).each( function ( colIdx ) {
			$( 'input', table.column( colIdx ).footer() ).on( 'keyup change', function () {
				table
					.column( colIdx )
					.search( this.value )
					.draw();
			});
		});//*/
	
    $('#tb_detail tbody').on('click', 'tr', function (){
        $(this).toggleClass('selected');
		var trid = $(this).closest('tr').attr('id');
		//alert(trid);
		
		var status = $('#c'+trid).is(':checked');
		var value =  $("#c"+trid).val();
		var issue =  $("#issue"+trid).val();
		//alert(issue);//
		
		if(status==false){
			$("#c"+trid).prop('checked', true);
			issued.push(value);
			issuedNumber[value] = issue;
			funcCalculator();
		}else{	
			$("#c"+trid).prop('checked', false);
			issued.remove(value);
			funcCalculator();
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
});

function selectOne(trid){
	var status = $('#c'+trid).is(':checked');
	var value =  $("#c"+trid).val();
	var issue =  $("#issue"+trid).val();
		//alert(issue);//
		
	if(status==false){
		$("#c"+trid).prop('checked', true);
		issued.push(value);
		issuedNumber[value] = issue;
		funcCalculator();
	}else{	
		$("#c"+trid).prop('checked', false);
		issued.remove(value);
		funcCalculator();
	}
}

function funcCalculator(){
	var eleChkReq = document.getElementById("str_required");
	var eleChkSel = document.getElementById("str_selected");
	var total = 0;
	for(var i=0; i<issued.length;i++){
		//alert(issuedNumber[issued[i]]);
		total += parseFloat(issuedNumber[issued[i]]);
	}
	if(eleChkReq!=null){
		var ans = parseFloat(eleChkReq.value) - parseFloat(total);
		eleChkSel.value = ans.toFixed(3);
	}
}

function funcFormSubmit(directory, value){
	//alert(issued.length);
	var count = 0;
	var issueTo = document.getElementById("issueTo").value;
	//alert(issueTo);
	
	if(issued.length==0){
		alert("Please select at least one material");
	}else{
		var theForm, newInput1, newInput2, newInput3, check = true;
		theForm = document.createElement('form');
				theForm.action = directory;
				theForm.method = 'post';
		
		for(var i=0;i<issued.length;i++){
			
			var id = issued[i];
			//alert(id);
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
		}//------end for loop------//
		
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
				
		if(directory=="mi_issuing.php"){
			newInput4 = document.createElement('input');
				newInput4.type = 'hidden';
				newInput4.name = 'this_orderno';
				newInput4.value = document.getElementById("search_orderno").value;
				theForm.appendChild(newInput4);
				
			newInput5 = document.createElement('input');
				newInput5.type = 'hidden';
				newInput5.name = 'this_formID';
				newInput5.value = document.getElementById("formID").value;
				theForm.appendChild(newInput5);
				
			newInput6 = document.createElement('input');
				newInput6.type = 'hidden';
				newInput6.name = 'trolley_number';
				newInput6.value = document.getElementById("trolley_number").value;
				theForm.appendChild(newInput6);
			
			newInput7 = document.createElement('input');
				newInput7.type = 'hidden';
				newInput7.name = 'issuefactory';
				newInput7.value = document.getElementById("issuefactory").value;
				theForm.appendChild(newInput7);
		}
		else if(directory=="mv_moving.php"){
			newInput4 = document.createElement('input');
				newInput4.type = 'hidden';
				newInput4.name = 'this_mpohid';
				newInput4.value = document.getElementById("search_mpohid").value;
				theForm.appendChild(newInput4);
				
			newInput5 = document.createElement('input');
				newInput5.type = 'hidden';
				newInput5.name = 'this_orderno';
				newInput5.value = document.getElementById("search_orderno").value;
				theForm.appendChild(newInput5);
		}
		
		if(check==true){
			document.getElementById('hidden_form_container').appendChild(theForm);
			//alert(issued.length+"<--check");
			// ...and submit it
			theForm.submit();//
		}else{
			alert(value+" quantity can't be empty");
		}
	
	}//end if*/
	
}
function funcValidQty(value,id,max,name){
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
	
	funcCalculator();
	//alert(issuedNumber[name]);
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
