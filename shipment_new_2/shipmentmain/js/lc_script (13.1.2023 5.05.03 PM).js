var arr_selectdetail=[];

//open close content
function funcToggleContent(id){
	var class_name = document.getElementById("icon_"+id).className;
	var lastFour = class_name.substr(class_name.length - 4);
	// alert(class_name+" // "+lastFour+"//"+id);
	if(lastFour=="down"){
		document.getElementById("icon_"+id).className = "glyphicon glyphicon-chevron-up";
		document.getElementById("collapse_"+id).className = "panel-collapse";
	}
	else{
		document.getElementById("icon_"+id).className = "glyphicon glyphicon-chevron-down";
		document.getElementById("collapse_"+id).className = "panel-collapse collapse";
	}
}

function checkPOExist(id){
	var btn_trash = document.getElementById("btn_trash"+id);
	var count_po  = document.getElementById("count_detail"+id);
	var po = 0;
	
	if(count_po!=null){
		for(var i=1;i<=parseInt(count_po.value);i++){
			var sp = document.getElementById("shipmentpriceID_"+id+"-"+i);
			// alert(sp+" "+id+"-"+i+" = "+count_po.value);
			if(sp!=null){
				po++;
			}
		}
	}
	
	if(po==0){
		btn_trash.style.display = "";
	}
	else{
		btn_trash.style.display = "NONE";
	}
}

function funcRemoveInfo(id,LCIID=''){
	try {
		var chk = confirm("Are you sure to delete? \nThis action cannot be undone");
		if(chk==true){

			if(LCIID==''){
				LCIID=$("#LCIID"+id).val();
			}
			
			$.ajax({
				url:"lc_ajax_custom.php",
				method:"post",
				data:{
					module:"removeInfo",
					LCIID:LCIID
				},
				success:function(data){
					console.log(data);

					var split=id.split("_");
					var infohead_id=split[0];
					var inforow_id=split[1];

					if(data=="success"){
						var row2 = document.getElementById("infohead_"+infohead_id); 
							row2.parentNode.removeChild(row2);
						
						var row2 = document.getElementById("collapse_"+infohead_id); 
							row2.parentNode.removeChild(row2);
					}
					else if(data=="remove row"){
						if(inforow_id=="1"){
							$(".td_data_row"+id).html("");
							$("#removeBtn"+id).css("display","none");
						}else{
							var row2 = document.getElementById("infoRow"+id); 
							row2.parentNode.removeChild(row2);
						}

						//check if nothing in info then delete the whole row
						var countDate = $("#countDate"+infohead_id).val()
						var haveRow=0;
						for(var i=1;i<=parseInt(countDate);i++){
							var ele=document.getElementById("lcdate"+infohead_id+"_"+i);

							if(ele!==null){
								haveRow=1;
							}
						}

						if(haveRow==0){
							var row2 = document.getElementById("infohead_"+infohead_id); 
								row2.parentNode.removeChild(row2);
							
							var row2 = document.getElementById("collapse_"+infohead_id); 
								row2.parentNode.removeChild(row2);
						}
						calPOAmt(infohead_id); console.log("calPOAmt"+infohead_id);
						
					}//--- End Remove Row ---//
				}
			})
			
		}
	}catch(e){
		alert(e);
	}
}

function funcRemoveInfoSection(id){
	var chk = confirm("Are you sure to delete?\nThis action cannot be undone");
	
	if(chk==true){
		funcLoadIcon();
		var LCHID = document.getElementById("LCHID").value;
		var seq   = document.getElementById("seq"+id).value;
		
		$.ajax({
				url:"lc_ajax_custom.php",
				method:"post",
				data:{
					module:"removeInfoSection",
					LCHID:LCHID,
					seq:seq
				},
				success:function(data){
					
					var infohead = document.getElementById("infohead_"+id);
						infohead.parentNode.removeChild(infohead);
					var collapse = document.getElementById("collapse_"+id);	
						collapse.parentNode.removeChild(collapse);
						
					funcRemoveIcon();
				}
		
		});
	}
}

function funcRemoveDetail(count_info, shipmentpriceID, LCDID){
	try {
		var chk = confirm("Are you sure to delete? \nThis action cannot be undone");
		if(chk==true){

			$.ajax({
				url:"lc_ajax_custom.php",
				method:"post",
				data:{
					module:"removeDetail",
					LCDID:LCDID
				},
				success:function(data){
					console.log(data);
					if(data=="success"){
						//remove from hidden text box
						var selected=$("#selectedDetail"+count_info).val();
						arr_selectdetail=selected.split(",");

						// var PID=$("PID_"+count_info+"-"+count_detail).val();
						// var garmentID=$("garmentID_"+count_info+"-"+count_detail).val();
						// var colorID=$("colorID_"+count_info+"-"+count_detail).val();
						// var size_name=$("sizename_"+count_info+"-"+count_detail).val();

						var data=shipmentpriceID;

						arr_selectdetail.splice($.inArray(data, arr_selectdetail),1);
						var str_selectdetail=arr_selectdetail.join(",");

						$("#selectedDetail"+count_info).val(str_selectdetail);

						//calculate total
						var totalprice=$("#lcprice"+count_info).html();
						var totalpo=$("#totalpo"+count_info).html();

						var sum = 0;
						var count = 0;
						$('.lcamt_'+shipmentpriceID).each(function(){
						    sum += parseFloat($(this).text()); 
						    count++;
						});

						var remove_amt=sum;
						
						var this_amt = parseFloat(totalprice)-parseFloat(remove_amt)
						this_amt = Math.round((this_amt + Number.EPSILON) * 100) / 100
						
						$("#lcprice"+count_info).html(this_amt);
						$("#lcprice_txt"+count_info).val(this_amt);
						$("#totalpo"+count_info).html(parseFloat(totalpo)-count);

						//remove row
						$('.rows_'+shipmentpriceID).each(function(){
							this.parentNode.removeChild(this);
						});

						//enable buyer select box
						var detailRows_class = document.getElementsByClassName('detailRows');
						
						if (detailRows_class.length == 0) {
						    $("#select_buyer").prop("disabled",false).trigger("chosen:updated");
						}
						
						checkPOExist(count_info);
					}

					calPOAmt(count_info);
				}
			});
		}
	}catch(e){
		alert(e);
	}
}

function addInfo(){
	var num = document.getElementById("countInfo").value;
	num++;
	funcLoadIcon();
	
	$.ajax({
		type:"post",
		url:"lc_ajax_custom.php",
		data:"module=addInfo&this_i="+num+"&details=",
		success:function(data){
			// alert(data);
			//$(data).insertBefore("#cont_lasttr"+i);
			//funcRemoveIcon();

			$(data).insertBefore("#last_detail");
			document.getElementById('countInfo').value = num;
			$(".chosen-select").chosen({width:"100%",search_contains:true});
			funcRemoveIcon();

			//$(".datepicker").datepicker(); //lcdate1_1
			$("#lcdate"+num+"_1").datepicker({ 
				dateFormat: 'yy-mm-dd',
				onSelect: function(selectedDate){
					var arr_date = selectedDate.split("-");
					var rMin = new Date(arr_date[0], parseInt(arr_date[1])-1, arr_date[2]);
					$('#expireddate'+num+"_1").datepicker("option","minDate",rMin);
					var chk = $('#expireddate'+num+"_1").val();
					if(chk==""){
						$('#expireddate'+num+"_1").datepicker("setDate",rMin);
					}
					
				}
			}); //expireddate1_1
			$("#expireddate"+num+"_1").datepicker(); //expireddate1_1
		}
	});
}

function formchecking(){
	var lcform    = $("#lcform");
	var lcnumber  = $("#txt_lcnumber").val();
	var buyer     = $("#select_buyer").val();
	var LCHID     = $("#LCHID").val();
	var countInfo = $("#countInfo").val();
	var error     = 0;
	var txt_error = "";
	
	var chk=confirm("Are You Sure You Want Submit?");
	
	if(chk==true){
		
		//--- Added by ckwai on 2021-01-25 to validate limit amount cannot zero value ---//
		for(var id=1;id<=parseInt(countInfo);id++){
			var countDate = document.getElementById("countDate"+id);
			if(countDate!=null){
				for(var c=1;c<=parseInt(countDate.value);c++){
					var limit_amt = document.getElementById("limit_amt"+id+"_"+c);
					var lcdate = document.getElementById("lcdate"+id+"_"+c);
					if(limit_amt!=null){
						// if(parseFloat(limit_amt.value)==0){
							// error++;
							// limit_amt.style.border = "1px solid red";
							// window.location = "#limit_amt"+id+"_"+c;
							// txt_error = "LC limit amount cannot zero value";
						// }
						// else 
						if(lcdate.value==""){
							error++;
							lcdate.style.border = "1px solid red";
							window.location = "#lcdate"+id+"_"+c;
							txt_error = "LC date cannot be empty";
						}
					}
				}//--- End For ---//
			}//--- Check CountDate not NULL ---//
		}//--- End For ---//
		
		if(parseInt(error)==0){
			$.ajax({
				url:"lc_ajax_custom.php",
				method:"post",
				data:{
					module:"formcheck",
					lcnumber:lcnumber
				},
				success:function(data){
					
					if(data=="approve"){
						if(lcnumber=="" || buyer==""){
							if(lcnumber==""){
								$("#txt_lcnumber").css("border","2px solid red");
								
							}
							if(buyer==""){
								$("#select_buyer_chosen").css("border","2px solid red");
								$("#select_buyer_chosen").css("border-radius","5px");
							}
						}else{
							lcform.submit();
						}
					}
					else{
						if(LCHID!==""){
							lcform.submit();
						}else{
							$("#txt_lcnumber").css("border","2px solid red");
							alert("This LC No. already exist in system");
						}
					}
				}
			});
		}
		else{
			alert(""+txt_error);
		}
		//--- End Count Error ---//
		
	}//--- End Confirm Chk ---//

}

function funcAddDetail(LCIID,count){
	funcLoadIcon();
	var buyer=$("#select_buyer").val();

	arr_selectdetail=[];
	var selected=$("#selectedDetail"+count).val();
	arr_selectdetail=selected.split(",");
	
	$.ajax({
		url:"lc_ajax_custom.php",
		method:"post",
		data:{
			module:"addDetail",
			buyer:buyer,
			LCIID:LCIID,
			count:count,
			selected:selected
		},
		success:function(data){
			$("#modalBox").html(data);
			$('#addDetailModal').modal(); 

			//datatable
			var table = $('#seldetailTable').DataTable({
				"lengthMenu": [10, 25, 50, 100],
				"pageLength": 10
			});
 
		    $('#seldetailTable tbody').on( 'click', 'tr', function () {
		        $(this).toggleClass('selected');
		    });
		    funcRemoveIcon();
		}
	})
}

function funcSelectDetail(shipmentpriceID){
	var data=shipmentpriceID;
	// alert(data+" / "+arr_selectdetail);
	if(arr_selectdetail.includes(data)){
		var itemtoRemove = data;

   		arr_selectdetail.splice($.inArray(itemtoRemove, arr_selectdetail),1);

	}else{
		arr_selectdetail.push(data);
	}
	
	// alert(arr_selectdetail);
}

function funcSubmitDetail(count,LCIID){
	funcLoadIcon();
	var str_selectdetail=arr_selectdetail.join(",");
	var buyer=$("#select_buyer").val();
	// alert(str_selectdetail);
	$.ajax({
		url:"lc_ajax_custom.php",
		method:"post",
		data:{
			module:"confirmAddDetail",
			count:count,
			LCIID:LCIID,
			buyer:buyer,
			details:str_selectdetail
		},
		success:function(data){
			$("#detailTbody"+count).html(data);
			
			$('#addDetailModal').modal('toggle');

			$("#selectedDetail"+count).val(str_selectdetail);

			if($("#collapse_"+count).hasClass("collapse")){
				funcToggleContent(count);
			}
			
			calPOAmt(count);
			funcRemoveIcon();
			var btn_trash = document.getElementById("btn_trash"+count); 
			btn_trash.style.display = "NONE";
			
			$(".datepicker").datepicker({ 
				dateFormat: 'yy-mm-dd'
			});

			calculate_total_NG_qty_all();
		}
	});
}

function funcChangeTxtBuyer(value){
	$("#txt_buyer").val(value);
}

function funcChangeBrand(value){
	funcLoadIcon();
	
	$.ajax({
		url:"lc_ajax_custom.php",
		method:"post",
		data:{
			module:"getBrand",
			buyerid:value
		},
		success:function(data){
			funcRemoveIcon();
			
			$("#brandID").html(data).trigger("chosen:updated");;
			
		}
	});
}

function clearred(box){
	$("#"+box).css("border","");
}

function calPOAmt(count_info){
	var sum=0;
	// var limit=parseFloat($("#limit_amt"+count_info).val());
	var countDet      = parseInt($("#count_detail"+count_info).val());
	var maxDate       = parseInt($("#countDate"+count_info).val());
	var arr_poamt     = [];
	var arr_poamtship = [];
	var arr_shipdate  = [];
	var arr_count     = [];
	for(var i=1;i<countDet;i++){
		var amt=0;
		var shipdate=$("#shipdate"+count_info+"-"+i).html();
		if($("#po_amt_"+count_info+"-"+i).val() !==""){
			amt=parseFloat($("#po_amt_"+count_info+"-"+i).val());
		}
		// alert(shipdate);
		arr_poamt.push(amt);
		arr_shipdate.push(shipdate);
		arr_count.push(count_info+"-"+i);
	}

	
	var count_valid_addon=0;
	for(var i=1;i<=maxDate;i++){
		var expired_ele = document.getElementById("expireddate"+count_info+"_"+i);
		
		if(expired_ele!=null){
			count_valid_addon++;
		}
	}
	
	var sum=0;
	var count_po=0;
	var count_current_addon=0;
	for(var i=1;i<=maxDate;i++){
		var str_lcdate  = $("#lcdate"+count_info+"_"+i).val();
		var expireddate = $("#expireddate"+count_info+"_"+i).val();
		var expired_ele = document.getElementById("expireddate"+count_info+"_"+i);
		var limit       = parseFloat($("#limit_amt"+count_info+"_"+i).val());
		expireddate     = new Date(expireddate);
		lcdate = new Date(str_lcdate);
		
		
		if(str_lcdate!="" && expired_ele!=null){
			sum      = 0;
			count_po = 0;
			count_current_addon++;
			
			for(var j=0;j<arr_poamt.length;j++){
				var shipdate=new Date(arr_shipdate[j]);
				var amt=parseFloat(arr_poamt[j]);
				// console.log(" "+i+"  ("+shipdate+">="+lcdate+" && "+shipdate+"<="+expireddate+") && ("+sum+"+"+amt+")<="+limit+"");
				if((shipdate>=lcdate && shipdate<=expireddate) && (sum+amt)<=limit){ //if the date is not expired and not over the limit
					if(amt>0){
						sum+=amt;
						count_po++;
					}

					arr_poamt[j]=0;
				}
				else if((shipdate>=lcdate && shipdate<=expireddate) && count_current_addon==count_valid_addon){ //if the date is not expired, add in to last topup eventhough is over the limit && i==maxDate
					if(amt>0){
						sum+=amt;
						count_po++;
					}

					arr_poamt[j]=0;
				}

				$("#lcprice"+count_info+"_"+i).html(sum.toFixed(2));
				$("#lcprice_txt"+count_info+"_"+i).val(sum.toFixed(2));
				$("#totalpo"+count_info+"_"+i).html(count_po);
			

				// calDeviation(count_info,i);
				
			}//--- End For PO Amt ---//

			
			//double check with lower requirement
			var lcprice=parseFloat($("#lcprice"+count_info+"_"+i).html());
			var totalpo=parseInt($("#totalpo"+count_info+"_"+i).html());
			sum=0;
			count_po=0;
			for(var j=0;j<arr_poamt.length;j++){
				var shipdate=new Date(arr_shipdate[j]);
				var amt=parseFloat(arr_poamt[j]);

				if((shipdate>=lcdate && shipdate<=expireddate) && amt>0 && count_current_addon==count_valid_addon ){ //if the date is not expired and not over the limit && i==maxDate
					if(amt>0){
						sum+=amt;
						count_po++;
					}

					arr_poamt[j]=0;

					sum+=lcprice;
					count_po+=totalpo;

					$("#lcprice"+count_info+"_"+i).html(sum.toFixed(2));
					$("#lcprice_txt"+count_info+"_"+i).val(sum.toFixed(2));
					$("#totalpo"+count_info+"_"+i).html(count_po);

					// calDeviation(count_info,i);
				}
			}

			calDeviation(count_info,i);
		}//-- End if lcdate not empty --//
		
	}//-- End For count date --//

	//$(".detailRows").css("background-color","");

	//highlight row if the row has error
	for(var i=0;i<arr_count.length;i++){
		if(arr_poamt[i]!==0){
			//if(count_info=="1"){
				//alert(arr_poamt[i]+" / "+arr_count[i]);
			//}
			
			$("#detailRow_"+arr_count[i]).css("background-color","orange");
		}
		else{
			$("#detailRow_"+arr_count[i]).css("background-color","");
		}
	}
	
	calAllPODeviation(count_info);
		
	// var count_date=1;
	// for(var i=1;i<countDet;i++){
	// 	var amt=0;
	// 	if($("#po_amt_"+count_info+"-"+i).val() !==""){
	// 		amt=parseFloat($("#po_amt_"+count_info+"-"+i).val());
	// 	}

	// 	var limit=$("#limit_amt"+count_info+"_"+count_date).val();
	// 	var expireddate=$("#expireddate"+count_info+"_"+count_date).val();
	// 	var shipdate=$("#shipdate"+count_info+"_"+i).html();
		
	// 	if((sum+amt)<=limit || count_date==maxDate){
	// 		sum+=amt;

	// 		if(count_date==maxDate || i==(countDet-1)){

	// 			$("#lcprice"+count_info+"_"+count_date).html(sum.toFixed(2));
	// 			$("#lcprice_txt"+count_info+"_"+count_date).val(sum.toFixed(2));

	// 			calDeviation(count_info,count_date);
	// 		}
	// 	}else{

	// 		$("#lcprice"+count_info+"_"+count_date).html(sum.toFixed(2));
	// 		$("#lcprice_txt"+count_info+"_"+count_date).val(sum.toFixed(2));

	// 		calDeviation(count_info,count_date);

	// 		sum=amt;
	// 		count_date++;

	// 		if(i==(countDet-1)){

	// 			$("#lcprice"+count_info+"_"+count_date).html(sum.toFixed(2));
	// 			$("#lcprice_txt"+count_info+"_"+count_date).val(sum.toFixed(2));

	// 			calDeviation(count_info,count_date);
	// 		}
	// 	}
	// }

	// $("#lcprice"+count_info).html(sum);
	// $("#lcprice_txt"+count_info).val(sum);

}

function calDeviation(count_info,count_date){
	var limit=parseFloat($("#limit_amt"+count_info+"_"+count_date).val());
	var poamt=parseFloat($("#lcprice_txt"+count_info+"_"+count_date).val());
	// alert("#limit_amt"+count_info+"_"+count_date);
	if((limit!==0 && poamt!==0) && $("#limit_amt"+count_info+"_"+count_date).val()!==""){
		var dev=(poamt/limit)*100;
		dev+=0.00000000001;
		
		console.log(dev+" <<< ");
		
		dev=dev.toFixed(2);

		$("#deviation"+count_info+"_"+count_date).html(dev+" %");

		if(dev>100){
			$("#deviation"+count_info+"_"+count_date).css("color","red");
		}
		else if(dev<100){
			$("#deviation"+count_info+"_"+count_date).css("color","orange");
		}
		else{
			$("#deviation"+count_info+"_"+count_date).css("color","green");
		}
	}else{
		// $("#deviation"+count_info+"_"+count_date).html("-");
		// $("#deviation"+count_info+"_"+count_date).css("color","black");

		if(limit==0 || $("#limit_amt"+count_info+"_"+count_date).val()==""){
			$("#deviation"+count_info+"_"+count_date).html("N/A");
			$("#deviation"+count_info+"_"+count_date).css("color","red");
		}else{
			$("#deviation"+count_info+"_"+count_date).html("0%");
			$("#deviation"+count_info+"_"+count_date).css("color","red");
		}
	}
	
	
	
	
}

function calAllPODeviation(count_info){ //-- Calculate Total Limit, PO Amt, Deviation, PO number, unassigned PO by ckwai on 2021-01-25 --//
	var count_detail = parseInt($("#count_detail"+count_info).val());
	var maxDate      = parseInt($("#countDate"+count_info).val());
	var grand_limit_amt = 0;
	var grand_lcprice   = 0;
	var grand_totalpo   = 0;
	var grand_allpo     = 0;
	var grand_allpoamt  = 0;
	
	for(var i=1;i<count_detail;i++){
		var shipdate = document.getElementById("shipdate"+count_info+"-"+i);
		var po_amt   = document.getElementById("po_amt_"+count_info+"-"+i);
		console.log(shipdate+" / "+i);
		if(shipdate!=null){
			grand_allpo++;
			
			var this_po_amt = (po_amt.value==""? 0: po_amt.value);
			grand_allpoamt += parseFloat(this_po_amt);
		}
	}
	
	for(var i=1;i<=maxDate;i++){
		var limit_amt = document.getElementById("limit_amt"+count_info+"_"+i);
		var lcprice   = document.getElementById("lcprice"+count_info+"_"+i);
		var totalpo   = document.getElementById("totalpo"+count_info+"_"+i);
		if(limit_amt!=null){
			grand_limit_amt += parseFloat(limit_amt.value); 
			grand_lcprice   += parseFloat(lcprice.innerHTML); 
			grand_totalpo   += parseFloat(totalpo.innerHTML); 
		}
	}//--- End For ---//
	
	var grand_unassign    = grand_allpo - grand_totalpo;
	var grand_unassignamt = grand_allpoamt - grand_lcprice;
	
	grand_allpoamt = Math.round((grand_allpoamt + Number.EPSILON) * 100) / 100;
	
	document.getElementById("limit_amt"+count_info).innerHTML = grand_limit_amt;
	document.getElementById("lcprice"+count_info).innerHTML = grand_allpoamt;
	document.getElementById("totalpo"+count_info).innerHTML = grand_allpo;
	document.getElementById("totalunassignpo"+count_info).innerHTML = grand_unassign;
	document.getElementById("unassignlcprice"+count_info).innerHTML = grand_unassignamt.toFixed(2);
	
	
	var dev = (grand_allpoamt / (grand_limit_amt)) * 100;
		dev += 0.00000000001;
		dev = dev.toFixed(2);
	
	document.getElementById("deviation"+count_info).innerHTML = dev+" %";
	
	if(dev>100){
		$("#deviation"+count_info).css("color","red");
	}
	else if(dev<100){
		$("#deviation"+count_info).css("color","orange");
	}
	else{
		$("#deviation"+count_info).css("color","green");
	}
	
}

function topupInfo(count_info){ 
	var arr_count=count_info.split("_");
	var count=arr_count[0];
	var new_lc_type = parseInt(document.getElementById("last_lc_type"+count).value) + 1;
	var num = document.getElementById("countDate"+count).value;
	num++;
	
	// alert(count+"-"+num);

	funcLoadIcon();

	$.ajax({
		url:"lc_ajax_custom.php",
		method:"post",
		data:{
			module:"topupInfo",
			num:num,
			count:count,
			lc_type:new_lc_type
		},
		success:function(data){
			$("#infoTable_"+count+" tbody").append(data);
			document.getElementById('countDate'+count).value = num;
			$(".chosen-select").chosen({width:"100%",search_contains:true});
			funcRemoveIcon();

			// $(".datepicker").datepicker();
			$("#lcdate"+count+"_"+num).datepicker({ 
				dateFormat: 'yy-mm-dd',
				onSelect: function(selectedDate){
					var arr_date = selectedDate.split("-");
					var rMin = new Date(arr_date[0], parseInt(arr_date[1])-1, arr_date[2]);
					$('#expireddate'+count+"_"+num).datepicker("option","minDate",rMin);
					var chk = $('#expireddate'+count+"_"+num).val();
					if(chk==""){
						$('#expireddate'+count+"_"+num).datepicker("setDate",rMin);
					}
					
				}
			}); //expireddate1_1
			$("#expireddate"+count+"_"+num).datepicker(); //expireddate1_1
			document.getElementById("last_lc_type"+count).value = new_lc_type;
			
			calPOAmt(count);
		}
	})
}

function funcLoadIcon(){
	//var str = document.getElementById("notice_loadicon").value;
	$( "body" ).append( "<div id='load_screen'><div id='loading2' align='center'><img src='http://www.uat.apparelezi.com/includes/giphy-icon.gif'  />"+
							"<br/><font style='font-size:14px;font-weight:bold'>Loading...</font></div></div>" );
}

function funcRemoveIcon(){
	var load_screen = document.getElementById("load_screen");
	if(load_screen!=null){
		document.body.removeChild(load_screen);
	}
	//document.getElementById("notice_loadicon").value = "";
}