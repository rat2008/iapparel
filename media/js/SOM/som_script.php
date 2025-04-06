
<script type="text/javascript">

$(document).ready(function(){
    var tabTitle = $( "#tab_title" ),
      tabContent = $( "#tab_content" ),
      tabTemplate = "<li><a href='#{href}'>#{label}</a> <span class='ui-icon-close' role='presentation'></span></li>",
      tabCounter = 2;
 
    var tabs = $( "#tabs" ).tabs();
 
    // modal dialog init: custom buttons and a "close" callback resetting the form inside
    var dialog = $( "#dialog" ).dialog({
      autoOpen: false,
      modal: true,
      buttons: {
        Add: function() {
          addTab();
          $( this ).dialog( "close" );
        },
        Cancel: function() {
          $( this ).dialog( "close" );
        }
      },
      close: function() {
        form[ 0 ].reset();
      }
    });
 
    // addTab form: calls addTab function on submit and closes the dialog
    var form = dialog.find( "form" ).submit(function( event ) {
      addTab();
      dialog.dialog( "close" );
      event.preventDefault();
    });
 
    // addTab button: just opens the dialog
    $( "#add_tab" ).button().click(function() {
        //dialog.dialog( "open" );
		addTab();
		tabs.tabs( "refresh" );
      });
	
	tabs.delegate( "span.ui-icon-close", "click", function() {
		var panelId = $( this ).closest( "li" ).remove().attr( "aria-controls" );
		$( "#" + panelId ).remove();
		//alert(panelId);
		tabs.tabs( "refresh" );
	});
	
  });

  function addTab(){
  
  	var count = document.getElementById("count").value;
  	var newID = parseInt(count) + 1;
	
  	//=================================Tab Header=============================================//
  
  	var ul = document.getElementById("myul");
  	var li = document.createElement("li");
  	li.innerHTML = "<a href='#tabs-"+newID+"'>Garment"+newID+" </a> <span class='ui-icon-close' ></span>";
  	//li.appendChild(document.createTextNode("Four"));
  	ul.appendChild(li);
	
  	//=================================Content=============================================//
	
  	var div = document.getElementById("tabs");
  	var newDiv = document.createElement("div");
  	newDiv.id = "tabs-"+newID+"";

		
  		var tb_header = "<table class='tb_header'><tr><td class='info_tab'>";
  		var tb_info = "<table class='tb_info'>";
		var tb_info_row00 ="<tr><td class='tab_label' width='12%'>Is default T&A:</td><td class='tab_short_2'>"+
										"<input type='checkbox' name='istna"+newID+"' id='istna"+newID+"' onClick='func2(&#39;"+newID+"&#39;)' ></td><td class='tab_label' width='12%'>Access T&A :</td><td class='tab_short_2'></td></tr>";

  		var tb_info_row0 ="<tr><td class='tab_label' width='10%'>inHouse :</td><td class='tab_short_2'><input type='checkbox' name='isIN1"+newID+"' disabled checked></td><td width='10%'>Garment Image:</td>"+
  													"<td width='15%'><input type='file' name='garment_picture-"+newID+"' id='imgInp2-"+newID+"'  class='file_img' /></td>"+
  													"<td class='picture' colspan='2' rowspan='4' style='width: 45% !important;>"+
  														"<div id='garmentpicture'>"+
  															"<span class='span_close' title='Remove image' onclick='funcRemoveImg(&#39;garmentPicture&#39;, &#39;"+newID+"&#39;)' ></span>"+
  															"<img id='gimgSrc-"+newID+"' class='imagePreview' alt=' &nbsp;&nbsp;No Image ' />"+
  														"</div>"+
  														"<span id='imgSpan'>(Max size: 30KB)</span>"+
  														"<input type='hidden' name='MAX_FILE_SIZE' value='2000000' />"+
  														"<input type='hidden' name='garment_Image_Exist-"+newID+"' id='garment_Image_Exist-"+newID+"' value='0' />"+
  													"</td></tr>";
  			var tb_info_row1 = "<tr><td class='tab_label' width='10%'>Style No : <input type='hidden' name='garmentID"+newID+"' value='none' /></td><td class='tab_short_2'><select name='ne_qdid"+newID+"' id='ne_qdid"+newID+"' class='select_medium_short3' required><?php
		
  				echo "<option value='0'>select NE</option>";
			$qdid_query = "(select q.refID as Orderno, q.QDID 
							from tblq_detail q where q.QDID=0)
							Union
							(select ne.Orderno, q.QDID from tblnewenquiry ne
							inner join tblq_detail q ON q.refID = ne.Orderno
							where q.del=0 and ne.statusID IN (4,8)
							and q.Status=0 and ne.seasonID=$o_seasonID 
							and ne.brandID=$o_brandID
							and ne.genderID=$o_genderID
							order by ne.Orderno DESC)";
  			 $re_qdid = $conn->query($qdid_query);
  			 while($Qrow = $re_qdid->fetch(PDO::FETCH_ASSOC)){
  			 		$ne_Orderno = $Qrow['Orderno'];
  			 		$QDID = $Qrow['QDID'];
				 echo "<option value='$QDID'>$ne_Orderno </option>";
  			}?></select>-<input type='text' name='style"+newID+"' value='123' class='txt_short' required /></td>";
  			var tb_info_row2 = "<td class='td_label' width='12%'>Garment Type:</td><td><select name='gmtType"+newID+"' id='gmtType"+newID+"' class='select_medium' required><?php

  				echo "<option value='0'>Select Garment Type</option>";
			$gmtType = "SELECT ID, Description FROM tblproducttype WHERE StatusID = 1 and Isgmttype=1 order by Description ASC";
  			 $re_gmtType = $conn->query($gmtType);
  			 while($rowType2 = $re_gmtType->fetch(PDO::FETCH_ASSOC)){
  			 		$ID = $rowType2['ID'];
  			 		$Description = $rowType2['Description'];
				 echo "<option value='$ID'>$Description</option>";
  			}?></select></td></tr>";						
  		var tb_info_row3="<tr><td width='12%'>Garment Feature</td><td colspan=3><select id='choose"+newID+"' name='chosen"+newID+"[]' data-placeholder='Choose...' class='chosen-select' style='width:100%;' multiple><?php
									
				$feature="select ID, Description from tblprodfeature where StatusID=1 order by Description";
				$re21 = $conn->query($feature);

				while($r21 = $re21->fetch(PDO::FETCH_ASSOC)){
				$f21 = $r21['ID'];
				$feature21= $r21['Description'];
					
				echo "<option value='$f21'>".$feature21."</option>";
				}?></select></td></tr>";
  		var tb_info_row4 = "<tr><td class='tab_label' width='12%'>Garment Remark:</td><td colspan=3><textarea name='content"+newID+"' class='txtarea_long' ></textarea></td></tr>";
  		var tb_info_end = "</table>";
  		var tb_header_end = "</td></tr></table>";
			
  	newDiv.innerHTML = tb_header+tb_info+tb_info_row00+tb_info_row0+tb_info_row1+tb_info_row2+tb_info_row3+tb_info_row4+tb_info_end+tb_header_end;
  	div.appendChild(newDiv);
  	$(".chosen-select").chosen({width:"100%"});
	
	
 		function readURL(input, imgname, tagid) {
			//alert("Run -- readurl (from som.js)");
			if (input.files && input.files[0]) {
				var reader = new FileReader();
				
				
				if (tagid == ""){
					reader.onload = function (e) {
						$('#'+imgname).attr('src', e.target.result);
					}
				
				}else{
					//alert("tab (from som.js) :"+tagid);
					reader.onload = function (e) {
						$('#'+imgname+"-"+tagid).attr('src', e.target.result);
					}
				}
				
				reader.readAsDataURL(input.files[0]);
			}
		}
		
		var countTab = document.getElementById("count").value;
		var countTab = parseFloat(countTab) +1;

		for (var x=2; x<=countTab; x++){

			(function (x) {
				
				//alert("tab no:"+x);
				var ele = document.getElementById("imgInp2-"+x);
				//alert(ele+" "+x);
				$("#imgInp2-"+x).change(function(){
				        //alert("from som.js :: imgInp2-"+x);
						readURL(this, "gimgSrc", x);
				});
			})(x);
		}
		
		//alert(parseFloat(countTab)+1);
	
	document.getElementById("count").value = newID;
	
  }

  
  Element.prototype.remove = function() {
    this.parentElement.removeChild(this);
  }
  
  NodeList.prototype.remove = HTMLCollection.prototype.remove = function() {
		for(var i = 0, len = this.length; i < len; i++) {
			if(this[i] && this[i].parentElement) {
				this[i].parentElement.removeChild(this[i]);
			}
		}
	}
	
 // Modified by Sheau Ling on 30 June 2017	
 //  function funcBuyerContact(id){
//   
// 	var xmlhttp;
// 
// 	if (window.XMLHttpRequest)
// 	  {// code for IE7+, Firefox, Chrome, Opera, Safari
// 	  xmlhttp=new XMLHttpRequest();
// 	  }
// 	else
// 	  {// code for IE6, IE5
// 	  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
// 	  }
// 	xmlhttp.onreadystatechange=function()
// 	  {
// 	  if (xmlhttp.readyState==4 && xmlhttp.status==200)
// 		{
// 		//document.getElementById("td_row").innerHTML=xmlhttp.responseText;
// 		//addOption = xmlhttp.responseText;
// 		//alert(xmlhttp.responseText);
// 		document.getElementById("td_contact").innerHTML = xmlhttp.responseText;
// 		
// 		}
// 	  }
// 	xmlhttp.open("GET","contact_list.php?id="+id,true);
// 	xmlhttp.send();
// 	
//   }
//   
  
  // Modified by Sheau Ling on 5 June 2015
  function funcManuCountry(value){
	var xmlhttp;
	//alert(value);
	
 	var x = document.getElementById("buyer").value;
	//alert(t);
	
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
		//document.getElementById("td_row").innerHTML=xmlhttp.responseText;
		//addOption = xmlhttp.responseText;
		//alert(xmlhttp.responseText);
		//var temp = "10#asa#asas";
		var temp = xmlhttp.responseText;
		//alert("jhjh "+temp);
		var sub= temp.substr(0, temp.indexOf('#')); 
		
		//alert(sub);
		var display = temp.substr(parseInt(sub.length)+1);
		//alert(sub+" "+sub.length+" "+display);
		document.getElementById("manFactoryDescribe").value = display;
		document.getElementById("manFactory").value = sub;
		
		}
	  }
	xmlhttp.open("GET","manufacture_country.php?id="+value+"&x="+x,true);
	xmlhttp.send();
  }
  
  function funcSubmit(){
	//var form = $( "#form_SOM" ).attr( "enctype", "multipart/form-data" );
	
	$.post('som_saving.php', $('#form_SOM').attr( "enctype", "multipart/form-data" ).attr( "encoding", "multipart/form-data" ).serialize());

	
	
	/*var url = "som_saving.php";
	var data = $('#form_SOM').serialize();//application/json
	
    $.ajax({
        'type': 'POST',
        'url': "som_saving.php",
        'contentType': "multipart/form-data",
        'data': $('#form_SOM').serialize(),
        success: function () {
                alert('Form Submitted!');
            },
        error: function(){
                alert("error in ajax form submission");
            }
    });//*/

	
	//alert("Saved");
  }
  
  function funcChangeRate(value){
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
		//document.getElementById("td_row").innerHTML=xmlhttp.responseText;
		//addOption = xmlhttp.responseText;
		//alert(xmlhttp.responseText);
		document.getElementById("txt_rate").value = xmlhttp.responseText;
		}
	  }
	xmlhttp.open("GET","rate_change.php?id="+value,true);
	xmlhttp.send();
  }
  
  function alertDisplay(value){

	$(function() {
				$('<div class=overlay></div><div class=modal><img src=../../icon/true.png width=14 height=14 /> '+value+'</div>')
					.insertBefore('#content_details_head')
					.delay(2000)
					.fadeOut(function() {
					$(this).remove(); 
					});
	});
  }
  
  function funcRemoveImg(picname, tagid){
      //alert("remove"+picname);
	  if (picname == "orderImage"){
			document.getElementById("imgSrc").src="";
			document.getElementById("imgInp").value="";
			document.getElementById("Image_Exist").value= 0;
	  }else if (picname == "garmentPicture"){
		    document.getElementById("gimgSrc-"+tagid).src="";
			document.getElementById("imgInp2-"+tagid).value="";
			document.getElementById("garment_Image_Exist-"+tagid).value= 0;
	  }
  }
  
  function funcPropOut(orderno , garment){

	window.open("csq/colorsize.php?orderno="+orderno+"&garment="+garment, "myWindow", "width="+screen.width+",height="+screen.height);
	//window.open("csq/colorsize.php?orderno="+orderno+"&garment="+garment, "Ratting", "width="+screen.width+", height="+screen.height+
		//		", directories=no, titlebar=no, location=no, left=auto, top=auto, toolbar=1, status=1");
  
  }
</script>
  