//============================Select & Issued Material (Step 1)=================================//
$(document).ready(function() {
	
	$('#tb_detail').dataTable();
	/*$('#tb_detail').dataTable( {
		"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]]
	});//*/
	
	var table = $('#tb_detail').DataTable();
	
	table.columns().eq( 0 ).each( function ( colIdx ) {
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
		// alert(trid);
		
		funcSelected(trid);	
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
 
    
});


function funcSelectAll(){
	$("#tb_detail tbody tr").each(function( index ) {
		$(this).toggleClass('selected');
		var trid = $(this).closest('tr').attr('id');
		funcSelected(trid);
	});
}

function funcSelected(trid){
	var status = $('#c'+trid).is(':checked');
	var value =  $("#c"+trid).val();
		
	var element = document.getElementById("checkboxGrp");
		
	if(status==false){
			$("#c"+trid).prop('checked', true);
			var newInput3 = document.createElement('input');
				newInput3.type = 'hidden';
				newInput3.name = 'checkbox[]';
				newInput3.id = 'check'+value;
				newInput3.value = value;
			element.appendChild(newInput3);
			
			$("#chkbox"+trid).prop('checked', true);
			
	}
	else{	
			$("#c"+trid).prop('checked', false);
			var child = document.getElementById("check"+value);
			element.removeChild(child);
			
			$("#chkbox"+trid).prop('checked', false);
	}	
}