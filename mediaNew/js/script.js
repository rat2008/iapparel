$(function () {

  // Tooltip function
  $('[data-toggle="tooltip"]').tooltip();
  
  // Chosen select function
  $(".chosen-select").chosen({width: "100%", search_contains: true});

  // Chosen select image function
  $(".chosen-select-image").chosenImage({width: "100%", disable_search_threshold: 10 });
  
  // Datepicker function
  $(".datepicker").datepicker({ dateFormat: 'yy-mm-dd',changeYear: true,changeMonth: true, });


  // Set dataTables search function
  $('.table-dt tfoot th').each( function () {
      var title = $(this).text();
      $(this).html( '<input type="text" class="form-control form-control-sm" placeholder="Search '+title+'" />' );
  });
  
  var pageLength = document.getElementById("pageLength");
  var this_length = 0;
  if(pageLength==null){
	  this_length = 25;
  }
  else{
	  this_length = parseInt(pageLength.value);
  }

  
  // dataTables control as class (table-dt)
  var dataTable = $(".table-dt").DataTable({
    responsive: true,
    searchHighlight: true,
    pageLength: this_length,
    initComplete: function () {
      // Apply the search
      this.api().columns().every( function () {
          var that = this;

          $( '.table-dt tfoot th input', this.footer() ).on( 'keyup change clear', function () {
              if ( that.search() !== this.value ) {
                that
                  .search( this.value )
                  .draw();
              }
          } );
      } );
    }

  });
  
  //added by ckwai on 20230926
  dataTable.columns().every( function () {
			var that = this;
			$( 'input', this.footer() ).on( 'keyup change', function () {
					that
					.search( this.value )
					.draw();
							
				});
		});


  // Left navigation out of frame open and close function
  $("#navCtrl").on("click", function() {

      if ($(window).width() < 768) {
        window.parent.$("#navLeft").removeClass("nav-left-ctrl");
        window.parent.$("#navLeft").toggleClass("nav-left-ctrl-sm");
        window.parent.$("#frameView").removeClass("frame-view-ctrl");
        window.parent.$("#frameView").toggleClass("frame-view-ctrl-sm");
      }else{
        window.parent.$("#navLeft").removeClass("nav-left-ctrl-sm");
        window.parent.$("#navLeft").toggleClass("nav-left-ctrl");
        window.parent.$("#frameView").removeClass("frame-view-ctrl-sm");
        window.parent.$("#frameView").toggleClass("frame-view-ctrl");
      }
  });

  // Add down arrow icon for collapse element which is open by default
  $(".collapse.show").each(function(){
    $(this).prev(".card-header").find(".fa").addClass("fa-angle-down").removeClass("fa-angle-right");
  });
  
  // Toggle right and down arrow icon on show hide of collapse element
  // $(".collapse").on('show.bs.collapse', function(){
  //   $(this).prev(".card-header").find(".fa").removeClass("fa-angle-right").addClass("fa-angle-down");
  // }).on('hide.bs.collapse', function(){
  //   $(this).prev(".card-header").find(".fa").removeClass("fa-angle-down").addClass("fa-angle-right");
  // });

  $("button[data-toggle='collapse']").on('click', function(){
    //added by Lock (2023-04-07)
    var target=$(this).data('target');
    if(!$(target).hasClass("collapsing")){ //prevent double click change to wrong icon
      var dataparent=$(target).attr('data-parent');

      if(dataparent===undefined){
        //without data-parent
        if($(target).hasClass("show")){
          $(this).find(".fa").removeClass("fa-angle-down").addClass("fa-angle-right");
        }else{
          $(this).find(".fa").removeClass("fa-angle-right").addClass("fa-angle-down");
        }
      }else{
        //with data-parent, icon need auto change to right when open other accordion
        $("button[data-toggle='collapse']").each( function () {
            var target2=$(this).data('target');
            var dataparent2=$(target2).attr('data-parent');

            if(dataparent2==dataparent){
              if(target==target2){
                //change clicked button icon
                if($(target2).hasClass("show")){
                  $(this).find(".fa").removeClass("fa-angle-down").addClass("fa-angle-right");
                }else{
                  $(this).find(".fa").removeClass("fa-angle-right").addClass("fa-angle-down");
                }
              }else{
                //change other icon back to default
                $(this).find(".fa").removeClass("fa-angle-down").addClass("fa-angle-right");
              }
            }
        });
      }
    }
    //end


    // if ($(this).find(".fa").hasClass("fa-angle-right") === true) {
    //   $(this).find(".fa").removeClass("fa-angle-right").addClass("fa-angle-down");
    //   // console.log("true");
    // }else{
    //   $(this).find(".fa").removeClass("fa-angle-down").addClass("fa-angle-right");
    //   // console.log("false");
    // }
  });
  
});

// Run clock timer function
function startTime() {
  var today = new Date();
  var h = today.getHours();
  var m = today.getMinutes();
  var s = today.getSeconds();
  m = checkTime(m);
  s = checkTime(s);
  document.getElementById('datetimenow').innerHTML = h + ":" + m + ":" + s;
  var t = setTimeout(startTime, 500);
}
function checkTime(i) {
  if (i < 10) {i = "0" + i};  // add zero in front of numbers < 10
  return i;
}


// Open factory list function
function scp_open_factory_list() {
    var x = document.getElementById("factory-list");
    x.style.display = "block";
}
// Close factory list function
function scp_close_factory_list() {
    var x = document.getElementById("factory-list");
    x.style.display = "none";
}


// Open task list function
function scp_open_task_list() {
    var x = document.getElementById("task-list");
    x.style.display = "block";
}
// Close task list function
function scp_close_task_list() {
    var x = document.getElementById("task-list");
    x.style.display = "none";
}


// Open language list function
function scp_open_lang_list() {
    var x = document.getElementById("lang-list");
    x.style.display = "block";
}
// Close language list function
function scp_close_lang_list() {
    var x = document.getElementById("lang-list");
    x.style.display = "none";
}
// Clear chosen multi select selected
function scp_clear_multichosenselected(id){
	// alert(id);
	$("#"+id).val("").trigger("chosen:updated");
}
// Show image as no image if not found
function imgError(image){
  image.onerror = "";
  image.src = "https://iapparelintl.apparelezi.com/mediaNew/img/noimage.png";
  return true;
}