var issuedID = new Array();
	var issuedName = new Array(); //Associative Arrays
	var issuedNumber = new Array(); //Associative Arrays
	$(document).ready(function() {
		$('#tb_detail').dataTable();
		
		var table = $('#tb_detail').DataTable();
		
		// Apply the filter
		table.columns().eq( 0 ).each( function ( colIdx ) {
		$( 'input', table.column( colIdx ).header()).on( 'keyup change', function () {
			table
				.column( colIdx )
				.search( this.value )
				.draw();
			});
		});
		
		$('#tb_detail tbody').on('click', 'tr', function (){
        $(this).toggleClass('selected');
		var trid = $(this).closest('tr').attr('id');
		//alert(trid);
		
		var status = $('#c'+trid).is(':checked');
		var value =  $("#c"+trid).val();
		//var issue =  $("#issue"+trid).val();
		
		//alert(status+" "+value);
		
		if(status==false){
			$("#c"+trid).prop('checked', true);
			issuedID.push(value);
			//issuedName[trid] = value;
			//issuedNumber[trid] = issue;
		}else{	
			$("#c"+trid).prop('checked', false);
			issuedID.remove(value);
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
		
		$('#btn_delete').click(function (){
			funcFormSubmit("ms_removing.php");
			//alert(issuedID.toString());
		});
		$('#btn_submit').click( function () {
        //alert( table.rows('.selected').data().length +' row(s) selected' );
		//alert(issuedID.toString());
		funcFormSubmit("ms_loading.php");
		//alert(issuedName.toString());
		//alert(issuedNumber.toString());
		
			//funcFormSubmit("ai_issuing.php");
		});
		
	
	});//*/
	
function funcFormSubmit(url){
		var count = 0;
		//alert("test");
		if(issuedID.length==0){
			alert("Please select at least one material");
		}else{
		
			var shelfID = document.getElementById("shelfID").value;
			var shelf = document.getElementById("shelf").value;
			var row = document.getElementById("row").value;
			var col = document.getElementById("col").value;
			var store = document.getElementById("store").value;
			var location = document.getElementById("location").value;
			var factory = document.getElementById("factory").value;
			

			// alert(factory);
			var theForm, newInput1, newInput2, newInput3, newInput4, check = true;
			theForm = document.createElement('form');
					theForm.action = url+"?store="+store+"&location="+location+"&factory="+factory;
					theForm.method = 'post';
			
			for(var i=0;i<issuedID.length;i++){
				
				var id = issuedID[i];
				//var name = issuedName[id];
				//var receiveQty = issuedNumber[id];
				//alert(name+" ====== "+receiveQty);
				
				count++;
				newInput1 = document.createElement('input');
					newInput1.type = 'hidden';
					newInput1.name = 'IDName'+count;
					newInput1.value = id;
					theForm.appendChild(newInput1);
					
				/*newInput2 = document.createElement('input');
					newInput2.type = 'hidden';
					newInput2.name = 'receivedQty'+count;
					newInput2.value = receiveQty;
					theForm.appendChild(newInput2);//*/

			}//---end for loop---//
			
			newInput2 = document.createElement('input');
				newInput2.type = 'hidden';
				newInput2.name = 'shelfID';
				newInput2.value = shelfID;
				theForm.appendChild(newInput2);
			
			newInput3 = document.createElement('input');
				newInput3.type = 'hidden';
				newInput3.name = 'countRow';
				newInput3.value = issuedID.length;
				theForm.appendChild(newInput3);
				
			newInput4 = document.createElement('input');
				newInput4.type = 'hidden';
				newInput4.name = 'shelf';
				newInput4.value = shelf;
				theForm.appendChild(newInput4);
			
			newInput5 = document.createElement('input');
				newInput5.type = 'hidden';
				newInput5.name = 'row';
				newInput5.value = row;
				theForm.appendChild(newInput5);
			
			newInput6 = document.createElement('input');
				newInput6.type = 'hidden';
				newInput6.name = 'col';
				newInput6.value = col;
				theForm.appendChild(newInput6);
				
			if(check==true){
				document.getElementById('hidden_form_container').appendChild(theForm);
				// ...and submit it
				theForm.submit();//
			}else{
				alert("Accessories quantity can't be empty");
			}//*/
			
		}
}//---end funcFormSubmit---//