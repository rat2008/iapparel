<?php $datenow=date('Ymdhis'); ?>

<?php 
	// $http = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on'? "https://": "http://");
?>

<!-- Temporary Added $baseURL due to no variable set in LIVE on 19 JUL 2022 by Jasper -->
<?php 
	// $baseURL = "https://iapparelintl.apparelezi.com/";

	// $baseURL = "$http"."".$_SERVER['HTTP_HOST']."/";
	// $url_test = "$baseURL"."mediaNew/fontawesome-6.1.1/css/all.min.css";
	// $file_headers = @get_headers($url_test);

	// if(!$file_headers || $file_headers[0] == 'HTTP/1.1 404 Not Found') {
	//   $baseURL .= "iapparelintl/";
	// }
	  
	  // echo "$url_test / $baseURL / << ";
	  // print_r($file_headers);
	// $urlchk  = "media/fontawesome/css/all.min.css"; 
	// $urlchk1 = "../media/fontawesome/css/all.min.css"; 
	// $urlchk2 = "../../media/fontawesome/css/all.min.css"; 
	
	//retrieve path for library (updated by Lock - 2022-12-06)
	$baseURL = '';
	for($i=0;$i<=4;$i++){
		$count=1;
		$path1="";
		while($count<=$i){
			$path1.="../";

			$count++;
		}
		$urlchk  = $path1."mediaNew/fontawesome/css/all.min.css"; 

		if(file_exists($urlchk)){
			$baseURL = $path1;
			break;
		}
	}
	
	
	
?>

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- CSS -->
<!-- Fontawesome -->
<link rel="stylesheet" href="<?= $baseURL; ?>mediaNew/fontawesome-6.1.1/css/all.min.css">
<link rel="stylesheet" href="<?= $baseURL; ?>mediaNew/fontawesome/css/all.min.css">

<!-- Chosen Css-->
<!-- <link rel="stylesheet" href="<?= $baseURL; ?>mediaNew/chosen/chosen.css"/> -->
<link rel="stylesheet" href="<?= $baseURL; ?>mediaNew/chosen/chosen.bootstrap4.css"/>
<link rel="stylesheet" href="<?= $baseURL; ?>mediaNew/chosenImage/chosenImage.css"/>

<!-- JQuery UI Css -->
<link rel="stylesheet" href="<?= $baseURL; ?>mediaNew/css/jquery-ui.css"/>

<!-- Bootstrap Css-->
<link rel="stylesheet" href="<?= $baseURL; ?>mediaNew/bootstrap-4.6.0/css/bootstrap.min.css"/>
<!--<link rel="stylesheet" href="<?php echo $baseURL; ?>media/bootstrap-4.6.0/css/bootstrap.css"  />-->

<!-- Customize Css -->
<link rel="stylesheet" href="<?= $baseURL; ?>mediaNew/css/style.css?date=<?= $datenow; ?>"/>

<!-- Datatable with Bootstrap CSS -->
<link rel="stylesheet" href="<?= $baseURL; ?>mediaNew/DataTables/DataTables-1.10.25/css/dataTables.bootstrap4.min.css"/>
<link rel="stylesheet" href="<?= $baseURL; ?>mediaNew/DataTables/Responsive-2.2.9/css/responsive.bootstrap4.min.css"/>
<link rel="stylesheet" href="<?= $baseURL; ?>mediaNew/DataTables/DataTables-1.10.25/features/searchHighLight/css/dataTables.searchHighlight.min.css"/>

<!-- DevExtreme themes -->
<link rel="stylesheet" href="<?= $baseURL; ?>mediaNew/devexpress/css/dx.common.css">
<link rel="stylesheet" href="<?= $baseURL; ?>mediaNew/devexpress/css/dx.light.css">



<!-- JS -->
<!-- Jquery -->
<script src="<?= $baseURL; ?>media/js/misc.js?date=<?= $datenow; ?>"></script>
<script src="<?= $baseURL; ?>mediaNew/jquery-3.6.0.js"></script>
<!--<script src="<?= $baseURL; ?>mediaNew/jquery-3.5.1.slim.min.js"></script>-->

<!-- Chosen Jquery -->
<script src="<?= $baseURL; ?>mediaNew/chosen/chosen.jquery.min.js"></script>
<script src="<?= $baseURL; ?>mediaNew/chosenImage/chosenImage.jquery.js"></script>

<!-- JQuery UI JS -->
<script src="<?= $baseURL; ?>mediaNew/js/jquery-ui.min.js"></script>

<!-- Bootstrap Jquery -->
<script src="<?= $baseURL; ?>mediaNew/bootstrap-4.6.0/js/popper.min.js"></script>
<script src="<?= $baseURL; ?>mediaNew/bootstrap-4.6.0/js/bootstrap.bundle.min.js"></script>
<script src="<?= $baseURL; ?>mediaNew/bootstrap-4.6.0/js/bootstrap-4-autocomplete.min.js"></script>

<!-- Date Time Picker added by ckwai on 20240825 -->
<script src="<?= $baseURL; ?>media/js/jquery-ui-timepicker-addon.js?date="></script>
<script src="<?= $baseURL; ?>media/js/jquery-ui-sliderAccess.js?date="></script>

<!-- Customize Script -->
<!--<script src="<?= $baseURL; ?>media/js/script.js?date=20220415B"></script>-->

<!-- Handheld Scanner Detection -->
<script type="text/javascript" src="<?= $baseURL; ?>mediaNew/jQuery-Scanner-Detection-master/jquery.scannerdetection.js"></script>

<!-- Customize Script -->
<script src="<?= $baseURL; ?>mediaNew/js/script.js?date=<?= $datenow; ?>"></script>

<!-- Datatable with Bootstrap JS -->
<script src="<?= $baseURL; ?>mediaNew/DataTables/DataTables-1.10.25/js/jquery.dataTables.min.js"></script>
<script src="<?= $baseURL; ?>mediaNew/DataTables/DataTables-1.10.25/js/dataTables.bootstrap4.min.js"></script>
<script src="<?= $baseURL; ?>mediaNew/DataTables/Responsive-2.2.9/js/dataTables.responsive.min.js"></script>
<script src="<?= $baseURL; ?>mediaNew/DataTables/Responsive-2.2.9/js/responsive.bootstrap4.min.js"></script>
<script src="<?= $baseURL; ?>mediaNew/DataTables/DataTables-1.10.25/features/searchHighLight/js/jquery.highlight.js"></script>
<script src="<?= $baseURL; ?>mediaNew/DataTables/DataTables-1.10.25/features/searchHighLight/js/dataTables.searchHighlight.min.js"></script>

<!-- DevExtreme library -->
<!-- A CDN link -->
<!-- or a local script -->
<script type="text/javascript" src="<?= $baseURL; ?>mediaNew/devexpress/js/jszip.min.js"></script>
<script type="text/javascript" src="<?= $baseURL; ?>mediaNew/devexpress/js/dx.all.js"></script>

<!-- Reference either Knockout or AngularJS, if you do -->
<script type="text/javascript" src="<?= $baseURL; ?>mediaNew/devexpress/js/knockout-latest.js"></script>
<script type="text/javascript" src="<?= $baseURL; ?>mediaNew/devexpress/js/angular.js"></script>
<script type="text/javascript" src="<?= $baseURL; ?>media/webcamjs-master/webcam.min.js"></script>

<script type="text/javascript" src="<?= $baseURL; ?>media/js/dev_img_enlarge.js"></script>
<!-- <script type="text/javascript" src="<?= $baseURL; ?>mediaNew/jquery.ui.js"></script> -->

<style>
.font_12{
	font-size:12px;
}
.chk_20{
	width:20px;
	height:20px;
}
.lbl_hide150{
	width: 150px;
	white-space: nowrap;
	overflow: hidden;
	text-overflow: ellipsis;
}
.txt_50{
	width:50px;
}
.txt_100{
	width:100px;
}
.txt_200{
	width:200px;
}
.txt_300{
	width:300px;
}
.alias {cursor: alias;}
.all-scroll {cursor: all-scroll;}
.auto {cursor: auto;}
.cell {cursor: cell;}
.col-resize {cursor: col-resize;}
.context-menu {cursor: context-menu;}
.copy {cursor: copy;}
.crosshair {cursor: crosshair;}
.default {cursor: default;}
.e-resize {cursor: e-resize;}
.ew-resize {cursor: ew-resize;}
.grab {cursor: -webkit-grab; cursor: grab;}
.grabbing {cursor: -webkit-grabbing; cursor: grabbing;}
.help {cursor: help;}
.move {cursor: move;}
.n-resize {cursor: n-resize;}
.ne-resize {cursor: ne-resize;}
.nesw-resize {cursor: nesw-resize;}
.ns-resize {cursor: ns-resize;}
.nw-resize {cursor: nw-resize;}
.nwse-resize {cursor: nwse-resize;}
.no-drop {cursor: no-drop;}
.none {cursor: none;}
.not-allowed {cursor: not-allowed;}
.pointer {cursor: pointer;}
.progress {cursor: progress;}
.row-resize {cursor: row-resize;}
.s-resize {cursor: s-resize;}
.se-resize {cursor: se-resize;}
.sw-resize {cursor: sw-resize;}
.text {cursor: text;}
.url {cursor: url(myBall.cur),auto;}
.w-resize {cursor: w-resize;}
.wait {cursor: wait;}
.zoom-in {cursor: zoom-in;}
.zoom-out {cursor: zoom-out;}
tfoot {
	display: table-header-group;
}
</style>
<script>
$(function () {
	 $( ".datetimepicker" ).datetimepicker({
		 dateFormat: 'yy-mm-dd',
		 addSliderAccess: true,
		 sliderAccessArgs: { touchonly: false }
	  });
});

function funcAutoComplete(id, value){
	$("#"+id).autocomplete({
		source: "../accessory/ajax_search.php?searchBy="+value,
		minLength: 1
	});
}

function alertDisplayDiv(id="panel_heading", type, value){
	var icon = "fa-trash";
				//var value = "Item";
	var str_status = "";
	var className = "alert-danger";
				
	switch(type){
		case "remove": 
			icon = "fa-trash";
			className = "alert-danger";
			str_status = "removed";
			// value = "Fabric has been";
			break;
		case "saved":
			icon = "fa-check";
			className = "alert-success";
			str_status = "update successful";
			// value = "Fabric has been"; 
			break;
		case "error":
			icon = "fa-times";
			className = "alert-danger";
			break;
		case "warning":
			icon = "fa-check";
			className = "alert-warning";
			break;
		}
				
	$(function() {
		$("<div class='alert "+className+"' id='notice_display'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>"+
			 "<span class='fas "+icon+"'></span> "+value+".</div>")
			 .insertBefore('#'+id)
			 .delay(2000)
			 .fadeOut(function() {
				$(this).remove(); 
			 });
	});
}

function toggleSelect(classname){
	var chk = $("#"+classname).prop('checked');
	
	$('.'+classname).each(function(index, tr){
		// var midid = $(this).val();
		$(this).prop('checked', chk);
		// console.log(this.id+" / "+chk);
	});
	
}

function duplicateAll(classname){
	var thisvalue = $("#"+classname).val();
	
	$('.'+classname).each(function(index, tr){
		$(this).val(thisvalue);
	});
}

function toggleExpand(classname){
	if($("#"+classname).hasClass("d-none")){
		$("#"+classname).removeClass("d-none");
		$("#icon-"+classname).attr('class', 'fas fa-chevron-up');
	}
	else{
		$("#"+classname).addClass("d-none");
		$("#icon-"+classname).attr('class', 'fas fa-chevron-down');
	}
}

function toggleExpandByBatch(classname){
	var this_class = $("#icon-"+classname).attr('class');
	
	if(this_class=="fas fa-chevron-up"){
		$('.tr-'+classname).each(function(index, tr){
			$(this).hide();
		});
		$("#icon-"+classname).attr('class', 'fas fa-chevron-down');
	}
	else{
		$('.tr-'+classname).each(function(index, tr){
			$(this).show();
		});
		$("#icon-"+classname).attr('class', 'fas fa-chevron-up');
	}
}

function expandAll(classname){
	$('.'+classname).each(function(index, tr){
		var id = $(this).val();
		
		$("#"+id).removeClass("d-none");
		$("#icon-"+id).attr('class', 'fas fa-chevron-up');
		
	});
}

function collapseAll(classname){
	$('.'+classname).each(function(index, tr){
		var id = $(this).val();
		
		$("#"+id).addClass("d-none");
		$("#icon-"+id).attr('class', 'fas fa-chevron-down');
		
	});
}

function funcLoadCBM(this_id){
	funcLoadIcon();
	
	var value = $("#"+this_id).val();
	
	$.ajax({
		type: "POST", 
		url: "../ajax/ajax_misc.php",
		data: {
			value: value,
			mode: "loadCargoCBM"	
		},
		success: function(output){
			// alert(output+" / id: "+id+" / value: "+value+" / ");
			$("#txt_"+this_id).val(output);
			funcRemoveIcon();
		}
	});
}

function funcLoadOtherType(this_id){
	var value = $("#"+this_id).val();
	
	if(parseInt(value)==-1){
		$("#div_"+this_id).attr("class","input-group-prepend");
		$("#div_"+this_id).show();
	}
	else{
		$("#div_"+this_id).hide();
	}
}

</script>

<?php 
if($acctid==1){
	// echo $baseURL." <<< ";
}

?>