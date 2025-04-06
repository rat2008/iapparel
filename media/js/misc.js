function funcLoadIcon(){
	$( "body" ).append( "<div id='load_screen'><div id='loading2' align='center'><img src='http://www.uat.apparelezi.com/includes/giphy-icon.gif'  /></div></div>" );
}

function funcRemoveIcon(){
	var load_screen = document.getElementById("load_screen");
	if(load_screen!=null){
		document.body.removeChild(load_screen);
	}
}

function funcChangeOther(id, value){
	if(parseInt(value)==0){
		$('#txt_'+id).attr('readonly', false);
	}
	else{
		$('#txt_'+id).attr('readonly', true);
	}	
}

function funcWinOpen(url){
	window.open(""+url, "Ratting", "width=1150, height=570, location=yes");
}

function funcClearChosenSelect(id){
	$("#"+id).val('').trigger("chosen:updated");
}

function alertDisplay(value, type){
	var icon = "glyphicon-remove";
			//var value = "Item";
	var str_status = "";
	var className = "alert-danger";
			
	switch(type){
		case "remove": 
			icon = "glyphicon-remove";
			className = "alert-danger";
			str_status = "removed";
			value = "Item";
			break;
		case "saved": 
			icon = "glyphicon-ok";
			className = "alert-success";
			str_status = "saved";
			break;
		case "updated": 
			icon = "glyphicon-ok";
			className = "alert-success";
			str_status = "updated";
			break;
		case "approved": 
			icon = "glyphicon-ok";
			className = "alert-success";
			str_status = "approved";
			break;
		case "request BOM": 
			icon = "glyphicon-ok";
			className = "alert-success";
			str_status = "request BOM";
			break;
		case "request sourcing": 
			icon = "glyphicon-ok";
			className = "alert-success";
			str_status = "request sourcing";
			break;
		case "to purchase": 
			icon = "glyphicon-ok";
			className = "alert-warning";
			str_status = "to purchase";
			break;
		case "verified": 
			icon = "glyphicon-ok";
			className = "alert-warning";
			str_status = "verified";
			break;
		}
			
	$(function() {
		$("<div class='alert "+className+"' id='notice_display'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>"+
			 "<span class='glyphicon "+icon+"'></span> "+value+" has been "+str_status+".</div>")
			 .insertBefore('.panel-heading')
			 .delay(2000)
			 .fadeOut(function() {
				$(this).remove(); 
			 });
	});
}

function funcAlertDisplay(type, value){
	var icon = "glyphicon-remove fas fa-trash";
			//var value = "Item";
	var className = "alert-danger";
			
	switch(type){
		case "remove": 
			icon = "glyphicon-remove fas fa-trash";
			className = "alert-danger";
			// value = "Item";
			break;
		case "save": 
			icon = "glyphicon-ok";
			className = "alert-success";
			// value = "Item";
			break;
		}
			
	$(function() {
		$("<div class='alert "+className+"' id='notice_display'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>"+
			 "<span class='glyphicon "+icon+"'></span> "+value+"</div>")
			 .insertBefore('.panel-heading')
			 .delay(2000)
			 .fadeOut(function() {
				$(this).remove(); 
			 });
	});
}


function togglerSideBar(){
	var ele = window.parent.document.getElementById("main_frame");

			  if (ele.cols!="15%,*"){
				ele.cols = "15%,*";
			  }
			  else{ 
				ele.cols = "0%,*";
			  }
}