<style>
div#load_screen{
	background: #fff;
	opacity: 0.8;
	position: fixed;
	z-index:100000;
	top: 0px;
	width: 100%;
	height: 100%;
}
	
div#load_screen > div#loading{
	color:#FFF;
	width:100%;
	height:100%;
	margin: auto;
}
</style>
<script>
	window.addEventListener("load", function(){
		var load_screen = document.getElementById("load_screen");
		
		if(load_screen!=null){
			document.body.removeChild(load_screen);
		}
		// $("#load_screen").remove();
	});
	
	//-- clear action: 0:none, 1:clear cache --//
	function funcBackPreviousLoading(url, cache_name, clear_action){
		url = escape(''+url);
		//alert(url+" / "+cache_name+" / "+clear_action);
		window.stop();
		document.getElementById("load_screen_btn_stop").style.display = "none";
		document.getElementById("load_screen_lbl").innerHTML = "Stopping Page In Progress...";
		$.ajax({
			type: "POST",
			url:'../includes/ajax_code_loading.php?cache_name='+cache_name+'&clear_action='+clear_action, 
			data:"url="+url,
			success : function(this_url) {
				window.location = this_url+"&reload_screen=true";
				//alert(this_url);
			}
		});
	}
	
	function funcRefreshLoading(){
		location.reload();
	}

	function funcLoadIcon(){
		$( "body" ).append( "<div id='load_screen'><div id='loading2' align='center'><img src='https://www.uat.apparelezi.com/includes/giphy-icon.gif'  /></div></div>" );
	}

	function funcRemoveIcon(){
		var load_screen = document.getElementById("load_screen");
		if(load_screen!=null){
			document.body.removeChild(load_screen);
		}
	}
</script>
<div id="load_screen">
	<div id="loading" align="center"><img src="https://www.uat.apparelezi.com/quotation/progressbar.gif" />
		<br/>
	<?php
		$load_screen_lbl = "";
		$lbl_display = $hdlang["stop_loading"];//-- Stop Loading & Back to Previous --//
		if($stop_in_url!="" && !(isset($_GET["reload_screen"]))){
			echo "<font color='black'><input type='button' class='btn btn-danger btn-sm' id='load_screen_btn_stop' value='$lbl_display' 
					onclick='funcBackPreviousLoading(&#39;$stop_in_url&#39;, &#39;$cache_name&#39;, &#39;$clear_action&#39;)' /></font>";
		}else if(isset($_GET["reload_screen"])){
			$load_screen_lbl = "Refreshing Page...";
		}
	?>
		<span id="load_screen_lbl" style="font-color:#000;font-weight:bold;font-size:18px" class="label label-default label-sm"><?php echo $load_screen_lbl; ?></span>
	</div>
</div>