var issuedID = new Array();
	var issuedName = new Array(); //Associative Arrays
	var issuedNumber = new Array(); //Associative Arrays
	var rejectNumber = new Array(); //Associative Arrays
	var focNumber = new Array(); //Associative Arrays
	var issuedAction = new Array(); //Associative Arrays
	$(document).ready(function() {
		var ele = document.getElementById("tb_detail"); 
		
		if(ele!=null){
		$('#tb_detail').dataTable();
		var table = $('#tb_detail').DataTable();
		
		//table.fnFilter('8063');
		
		// Apply the filter
		
			table.columns().eq( 0 ).each( function ( colIdx ){
			$( 'input', table.column( colIdx ).header()).on( 'keyup change', function () {
				
				table
					.column( colIdx )
					.search( this.value )
					.draw();
				} );
			});

		
		$('#tb_detail tbody').on('click', 'tr', function (){
			$(this).toggleClass('selected');
			var trid = $(this).closest('tr').attr('id');
			//alert(trid);
			
			var status = $('#c'+trid).is(':checked');
			var value =  $("#c"+trid).val();
			var issue =  $("#issue"+trid).val();
			var reject =  $("#reject"+trid).val();
			var foc =  $("#foc"+trid).val();
			//var action =  $("#action"+trid).val();
			//alert(action);
			//alert(status+" "+value+" "+issue);
			
			if(status==false){
				$("#c"+trid).prop('checked', true);
				issuedID.push(trid);
				issuedName[trid] = value;
				issuedNumber[trid] = issue;
				rejectNumber[trid] = reject;
				focNumber[trid] = foc;
				//issuedAction[trid] = action;
				
				$("#chkbox"+trid).prop('checked', true);
			}else{	
				$("#c"+trid).prop('checked', false);
				issuedID.remove(trid);
				
				$("#chkbox"+trid).prop('checked', false);
			}
			
			//alert(rejectNumber.toString());
		
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
		
		$('#btn_receive').click( function () {
			funcFormSubmit("ar_receiving.php");
		});
		$('#btn_issued').click( function () {
        //alert( table.rows('.selected').data().length +' row(s) selected' );
		//alert(issuedID.toString());
		//alert(issuedName.toString());
		
		
			funcFormSubmit("ai_issuing.php");
		});
		$('#btn_return').click( function () {
			funcFormSubmit("ai_returning.php");
		});
		
		$('#btn_transfer').click( function () {
			funcFormSubmit("acc_transfering.php");
		});
		
		$('#btn_wh_receive').click( function () {
			funcFormSubmit("acc_wh_receiving.php");
		});
		
		var btn_issued2 = document.getElementById("btn_issued2"); 
		$('#btn_issued2').click(function () {
			funcFormSubmit("ai_issuing.php");
		});
		$('#btn_issued2A').click(function () {
			funcFormSubmit("ai_issuing.php");
		});
		
		}
	
	});//*/
	
function funcSelectAll(){
		$('#tb_detail tbody tr').each(function() {
			$(this).toggleClass('selected');
			var trid = $(this).closest('tr').attr('id');
			//alert(trid);
			
			var status = $('#c'+trid).is(':checked');
			var value =  $("#c"+trid).val();
			var issue =  $("#issue"+trid).val();
			var reject =  $("#reject"+trid).val();
			var foc =  $("#foc"+trid).val();
			//var action =  $("#action"+trid).val();
			//alert(action);
			//alert(status+" "+value+" "+issue);
			
			if(status==false){
				$("#c"+trid).prop('checked', true);
				issuedID.push(trid);
				issuedName[trid] = value;
				issuedNumber[trid] = issue;
				rejectNumber[trid] = reject;
				focNumber[trid] = foc;
				//issuedAction[trid] = action;
			}else{	
				$("#c"+trid).prop('checked', false);
				issuedID.remove(trid);
			}
		});
	}
	
function funcUpdate(id){
	var c = document.getElementById("c"+id).value;
	var issue = document.getElementById("issue"+id).value;
	var reject = document.getElementById("reject"+id).value;
	//alert(id+" -- "+issue+" -- "+reject+" -- "+c);
	issuedName[id] = c;
	issuedNumber[id] = issue;
	rejectNumber[id] = reject;
	focNumber[id] = foc;
}
	
function funcFormSubmit(url){ 
		var count = 0;
		if(issuedID.length==0){
			alert("Please select at least one accessories to receive");
		}else{
			
			var theForm, newInput1, newInput2, newInput3, newInput4, check = true;
			theForm = document.createElement('form');
					theForm.action = url;
					theForm.method = 'post';
			
			for(var i=0;i<issuedID.length;i++){
				var id = issuedID[i];
				var name = issuedName[id];
				var receiveQty = parseFloat(issuedNumber[id]);
				var rejectQty = parseFloat(rejectNumber[id]);
				var focQty = parseFloat(focNumber[id]);
				//alert(name+" ====== "+receiveQty);
				if (isNaN(receiveQty)){
					//alert(issueQty);
					check = false;
				}
				count++;
				newInput1 = document.createElement('input');
					newInput1.type = 'hidden';
					newInput1.name = 'IDName'+count;
					newInput1.value = name;
					theForm.appendChild(newInput1);
					
				newInput2 = document.createElement('input');
					newInput2.type = 'hidden';
					newInput2.name = 'receivedQty'+count;
					newInput2.value = receiveQty;
					theForm.appendChild(newInput2);//*/
				//alert(rejectQty);
				newInput2 = document.createElement('input');
					newInput2.type = 'hidden';
					newInput2.name = 'rejectQty'+count;
					newInput2.value = rejectQty;
					theForm.appendChild(newInput2);//*/
				
				newInput2 = document.createElement('input');
					newInput2.type = 'hidden';
					newInput2.name = 'focQty'+count;
					newInput2.value = focQty;
					theForm.appendChild(newInput2);//*/
					
					//alert("rejectQty"+count+" - "+rejectQty);

			}//---end for loop---//
			
			if(url=="ai_issuing.php"){
				newInput7 = document.createElement('input');
				newInput7.type = 'hidden';
				newInput7.name = 'issuefactory';
				newInput7.value = document.getElementById("issuefactory").value;
				theForm.appendChild(newInput7);
			}
			
			newInput3 = document.createElement('input');
				newInput3.type = 'hidden';
				newInput3.name = 'countRow';
				newInput3.value = issuedID.length;
				theForm.appendChild(newInput3);
				
			if(document.getElementById("issueTo") != null){
				var issue = document.getElementById("issueTo").value;
					
				newInput4 = document.createElement('input');
					newInput4.type = 'hidden';
					newInput4.name = 'issueTo';
					newInput4.value = issue;
					theForm.appendChild(newInput4);
			}
				
			if(check==true){
				document.getElementById('hidden_form_container').appendChild(theForm);
				// ...and submit it
				theForm.submit();//
			}else{
				alert("Accessories quantity can't be empty");
			}//*/
			
		}
}//---end funcFormSubmit---//
	
function funcValidQty(value,id,max,name){
	
	//alert(max);
	if(value.trim()!=""){
		if (!isNaN(value)) 
		{
			var issue = parseFloat(value);
			var maxValue = parseFloat(max);
			//alert(issue+" > "+maxValue);
			if(issue>maxValue){
				document.getElementById(id).value = maxValue;
			}
			else{
				document.getElementById(id).value = issue;
			}
			//document.getElementById(id).value = issue.toFixed(2);
			//alert(issue);*/
		}

	}else{
		document.getElementById(id).value = "0";
	}
	
	var myIssue = document.getElementById(id).value;
	var this_id = id.substring(5);
	issuedNumber[this_id] = myIssue;
	//alert(this_id+" // "+myIssue);
	//alert(issuedNumber);
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
			
			document.getElementById('qty'+id).innerHTML = qty.toFixed(2);
			document.getElementById("issuedQty"+id).value = qty.toFixed(2);
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
			//alert(n);
		}//end for loop
		//alert("hihi");
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
	
//--------------------Add On Item (Refer PO)--------------------------------------------//
function funcAddOn2(id){
	if(document.getElementById(id).checked){
		
		var table = document.getElementById("referPO");
		//var row = table.insertRow(4);
		//row.id = "AddOn";
		//var cell1 = row.insertCell(0);
		
		table.innerHTML = "<b>Refer PO#</b> <input type='text' name='referPO' id='referPO' class='txt_long' /> <input type='button' value='Find Refer' class='contact' onclick='submitRefer(referPO.value);' /><br/><br/><div id='referItem2'></div>";
		
	}else{
		try{			
			
			var div = document.getElementById("referPO");
			div.innerHTML = "";
					
		}catch(e){
			alert(e);
		}
	}
}

function submitRefer(value){
	//alert(value);
	var xmlhttp;
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
    document.getElementById("referItem2").innerHTML=xmlhttp.responseText;
    }
}
xmlhttp.open("GET","refer_ar.php?mpohid="+value,true);
xmlhttp.send();
	
}
