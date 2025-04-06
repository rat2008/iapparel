
$(document).ready(function() {

	var table=$('#man').DataTable({
	"iDisplayLength": 10,
    "aLengthMenu" : [10,20,50,"All"]
    });

    // Apply the filter
   table.columns().eq( 0 ).each( function ( colIdx ) {
        $( 'input', table.column( colIdx ).header()).on( 'keyup change', function () {
            table
                .column( colIdx )
                .search( this.value )
                .draw();
        } );
    } );
} );

 $(function ()
    {

      // Apply a class on mouse over and remove it on mouse out.
      $('#list tr').hover(function ()
      {
        $(this).toggleClass('Highlight');
      });
  
      // Assign a click handler that grabs the URL 
      // from the first cell and redirects the user.
//       $('#list tr').click(function ()
//       {
//         location.href = $(this).find('td a').attr('href');
//       });

    });
    