<!---- Master Carton Calculator javascript & Css ---->
<script type="text/javascript">		
	function func_display_lg(url){
		// alert(url);
		$("#opsviewdiv001").html('<span class="glyphicon glyphicon-remove btn_close_lg fa fa-lg fa-times" '+
								'onclick="document.getElementById(&#39;opsviewdiv002&#39;).innerHTML=&#39;&#39;;'+
								'document.getElementById(&#39;opsviewdiv001&#39;).innerHTML=&#39;&#39;;"></span>'+
						'<object type="text/html" data="'+url+'" class="objops_lg" />'); 
										/*'<span class="glyphicon glyphicon-remove btn_close" '+
										'onclick="document.getElementById(&#39;opsviewdiv2&#39;).innerHTML=&#39;&#39;;'+
										'document.getElementById(&#39;opsviewdiv&#39;).innerHTML=&#39;&#39;"></span>'+*/
		$("#opsviewdiv002").html('<div class="bg_cover"></div>');
	}

	function func_display_lg_reload(url){
		// alert(url);
		$("#opsviewdiv001").html('<span class="glyphicon glyphicon-remove btn_close_lg fa fa-lg fa-times" '+
								'onclick="document.getElementById(&#39;opsviewdiv002&#39;).innerHTML=&#39;&#39;;'+
								'document.getElementById(&#39;opsviewdiv001&#39;).innerHTML=&#39;&#39;;location.reload();"></span>'+
						'<object type="text/html" data="'+url+'" class="objops_lg" />'); 
										/*'<span class="glyphicon glyphicon-remove btn_close" '+
										'onclick="document.getElementById(&#39;opsviewdiv2&#39;).innerHTML=&#39;&#39;;'+
										'document.getElementById(&#39;opsviewdiv&#39;).innerHTML=&#39;&#39;"></span>'+*/
		$("#opsviewdiv002").html('<div class="bg_cover"></div>');
	}
	
	function func_display_small(url){ 
		$("#opsviewdiv001").html('<span class="glyphicon glyphicon-remove btn_close_small fa fa-lg fa-times" '+
								'onclick="document.getElementById(&#39;opsviewdiv002&#39;).innerHTML=&#39;&#39;;'+
								'document.getElementById(&#39;opsviewdiv001&#39;).innerHTML=&#39;&#39;;"></span>'+
						'<object type="text/html" data="'+url+'" class="objops_small">'); 
										/*'<span class="glyphicon glyphicon-remove btn_close" '+
										'onclick="document.getElementById(&#39;opsviewdiv2&#39;).innerHTML=&#39;&#39;;'+
										'document.getElementById(&#39;opsviewdiv&#39;).innerHTML=&#39;&#39;"></span>'+*/
		$("#opsviewdiv002").html('<div class="bg_cover"></div>');
	}
	
	function func_display_xsmall(url){//for qr scan use
		$("#opsviewdiv001").html('<span class="glyphicon glyphicon-remove btn_close_xsmall fa fa-lg fa-times" '+
								'onclick="document.getElementById(&#39;opsviewdiv002&#39;).innerHTML=&#39;&#39;;'+
								'document.getElementById(&#39;opsviewdiv001&#39;).innerHTML=&#39;&#39;;"></span>'+
						'<object type="text/html" data="'+url+'" class="objops_xsmall">'); 
										/*'<span class="glyphicon glyphicon-remove btn_close" '+
										'onclick="document.getElementById(&#39;opsviewdiv2&#39;).innerHTML=&#39;&#39;;'+
										'document.getElementById(&#39;opsviewdiv&#39;).innerHTML=&#39;&#39;"></span>'+*/
		$("#opsviewdiv002").html('<div class="bg_cover"></div>');
	}

	function func_display_lg_reload(url){
		$("#opsviewdiv001").html('<span class="glyphicon glyphicon-remove btn_close_lg fa fa-lg fa-times" '+
								'onclick="document.getElementById(&#39;opsviewdiv002&#39;).innerHTML=&#39;&#39;;'+
								'document.getElementById(&#39;opsviewdiv001&#39;).innerHTML=&#39;&#39;;location.reload();">X</span>'+
						'<object type="text/html" data="'+url+'" class="objops_lg">'); 
										// '<span class="glyphicon glyphicon-remove btn_close" '+
										// 'onclick="document.getElementById(&#39;opsviewdiv2&#39;).innerHTML=&#39;&#39;;'+
										// 'document.getElementById(&#39;opsviewdiv&#39;).innerHTML=&#39;&#39;"></span>'+
		$("#opsviewdiv002").html('<div class="bg_cover"></div>');
	}
</script>
<style>
	.objops{
		background: #fff;
		opacity: 1;
		position: fixed;
		z-index:100000;
		top: 20px;
		left:25%;
		width: 60%;
		height: 90%;
	}
	.objops_lg{
		background: #fff;
		opacity: 1;
		position: fixed;
		z-index:100000;
		top: 20px;
		left:5%;
		width: 90%;
		height: 95%;
		overflow-x:scroll;
		border-radius:10px;
		border:6px solid #888888;
	}
	.bg_cover{
		background: #bdbdbd;
		opacity: 0.6;
		position: fixed;
		z-index:80000;
		top: 0px;
		left:0%;
		width: 100%;
		height: 100%;
	}
	
	.btn_close{
		z-index:52000;
		position: fixed;
		margin-top:40px;
		left:82%;
		cursor:pointer;
		opacity:0.5;
		font-size:16px;
		font-weight:bold;
	}
	.btn_close:hover, btn_close_small:hover{
		opacity:1;
	}
	.btn_close_lg:hover{
		color:#bdbdbd;
	}
	.btn_close_lg{ 
		position: fixed;
		margin-top:25px;
		left:93%;
		cursor:pointer;
		opacity:1;
		font-size:16px;
		font-weight:bold;
		text-align:center;
		color:#fff;
		background-color:#000;
		width:25px;
		height:25px;
		border:2px solid #bdbdbd;
		border-radius:25px;
		padding:3px;
		z-index:520000;
	}
	
	.objops_small{
		background: #fff;
		opacity: 1;
		position: fixed;
		z-index:100000;
		top: 20px;
		left:25%;
		width: 50%;
		height: 70%;
		border-radius:10px;
		border:6px solid #888888;
	}
	
	.btn_close_small{
		z-index:620000;
		position: fixed;
		margin-top:43px;
		left:73%;
		cursor:pointer;
		opacity:1;
		font-size:16px;
		font-weight:bold;
		text-align:center;
		color:#fff;
		background-color:#000;
		width:25px;
		height:25px;
		border:2px solid #bdbdbd;
		border-radius:25px;
		padding:3px;
	}
	
	.objops_xsmall{
		background: #fff;
		opacity: 1;
		position: fixed;
		z-index:100000;
		top: 20px;
		left:25px;
		width: 450px;
		height: 65%;
	}
	
	.btn_close_xsmall{
		z-index:520000;
		position: fixed;
		margin-top:30px;
		left:460px;
		cursor:pointer;
		opacity:1;
		font-size:16px;
		font-weight:bold;
		text-align:center;
		color:#fff;
		background-color:#000;
		width:25px;
		height:25px;
		border:2px solid #bdbdbd;
		border-radius:25px;
		padding:3px;
	}
	</style>
	
<!---- Carton Calculator Div Used ---->
<div id="opsviewdiv001" ></div>
<div id="opsviewdiv002" ></div>