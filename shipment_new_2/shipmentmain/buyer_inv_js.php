<script type="text/javascript">
		var arr_poselect = [<?= $selected_invpo; ?>]; // this arr store all the po selected
		var arr_poselect2 = []; // this arr store all the po selected, but will be empty after user has confirm the selection
		$(document).ready(function(){
			$(".select_chosen").chosen({
				search_contains:true
			});
			$(".datepicker").datepicker();
			$(".datepicker_short").datepicker();
			$( ".datetimepicker" ).datetimepicker({
				addSliderAccess: true,
				sliderAccessArgs: { touchonly: false }
			});

			if($(".datatable").length>0){
				$(".datatable").DataTable({
					"ordering":false
				});
			}

			$(".dropdown-toggle").dropdown();
			// form_checking();

		});
		
		function funcBack(url){
			var chk = confirm("Changes you made may not be saved");
			if(chk==true){
				window.location = url;
			}
		}
		
		function funcPropOut(url){
			var x = screen.width/2 - 800/2;
			var y = screen.height/2 - 800/2;

			window.open(""+url, "new", "height=800,width=800,left="+x+",top="+y);
		}
		
		function funcGetLatestQty(CI='', isPickNPack=''){
			var countPO = document.getElementById("countPO").value;
			var status = document.getElementById("status").value;
				
			for(var i=0;i<=parseInt(countPO);i++){
				var spID = document.getElementById("spID"+i);
				var use_ori_qty = document.getElementById("use_ori_qty"+i);
					
				if(spID!=null){
					var countColor = document.getElementById("countColor"+i);
					use_ori_qty.value = 1;
						
					for(var c=0;c<=parseInt(countColor.value);c++){
						var advise_qty = document.getElementById("advise_qty"+i+"-"+c);
						var color_qty  = document.getElementById("color_qty"+i+"-"+c);
						//console.log(advise_qty+" // "+color_qty+":"+i+"-"+c);
						if(advise_qty!=null && parseInt(status)==4){
							color_qty.value = advise_qty.value;
							
						}
							
					}//--- End Count Color ---//
					
					var check = $("#expand"+i).attr('class').indexOf('tr_nondisplay');
					var element  = document.getElementById("expand"+i);
					
					if(isPickNPack=="true"){
						use_ori_qty.value = 3;
					}
					
					console.log("check: "+check);
					if(parseInt(check)!=0){//not exist className
						ajaxLoadPackingList(i, CI, isPickNPack);
					}
					else{
						ajaxLoadPackingList(i, CI, isPickNPack);
					}
					
				}//--- End Check Isset spID ---//
			}//--- End Count PO ---//
				
			funcCalculateAll();
		}
		
		function funcGetLatestPrice(){
			var countPO = document.getElementById("countPO").value;
			var status = document.getElementById("status").value;
			
			for(var i=0;i<=parseInt(countPO);i++){
				var spID = document.getElementById("spID"+i);
				if(spID!=null){
					var countColor = document.getElementById("countColor"+i);
					// use_ori_qty.value = 1;
						
					for(var c=0;c<=parseInt(countColor.value);c++){
						var unit_price  = document.getElementById("unit_price"+i+"-"+c);
						var ori_poprice = document.getElementById("ori_poprice"+i+"-"+c);
						
						if(ori_poprice!=null){
							unit_price.value = ori_poprice.value;
						}
					}
				}
			}//--- End for ---//
			
			funcCalculateAll();
		}
		
		function funcGetLatestItemDescription(){
			$('.shipping_remark').each(function(index, tr){
				var this_id = this.id;
				
				$("#"+this_id).val($("#ori_"+this_id).val());
			});
		}
		
		function funcGetCIQty(){
			var countPO = document.getElementById("countPO").value;
			var status = document.getElementById("status").value;
			
			for(var i=0;i<=parseInt(countPO);i++){
				var spID = document.getElementById("spID"+i);
				var quota_cat    = document.getElementById("quota_cat"+i);
				var use_ori_qty = document.getElementById("use_ori_qty"+i);
				var gmt_count = document.getElementById("gmt_count"+i);
					
				if(spID!=null){
					var countColor = document.getElementById("countColor"+i);
					use_ori_qty.value = 2;
						
					for(var c=0;c<=parseInt(countColor.value);c++){
						var ci_qty = document.getElementById("ci_qty"+i+"-"+c);
						var color_qty  = document.getElementById("color_qty"+i+"-"+c);
						// var shipping_remark  = document.getElementById("shipping_remark"+i+"-"+c);
						// var ci_shipping_remark  = document.getElementById("ci_shipping_remark"+i+"-"+c);
						var class_description  = document.getElementById("class_description"+i+"-"+c);
						var ci_class_description  = document.getElementById("ci_class_description"+i+"-"+c);
						//console.log(advise_qty+" // "+color_qty+":"+i+"-"+c);
						if(ci_qty!=null && parseInt(status)==4){
							color_qty.value = ci_qty.value;
							// shipping_remark.value = ci_shipping_remark.value;
							class_description.value = ci_class_description.value;
						}
							
					}//--- End Count Color ---//
					
					for(var g=1;g<=parseInt(gmt_count.value);g++){
						var ci_quota_cat = document.getElementById("ci_quota_cat"+i+"-"+g);
						var ci_ht_code = document.getElementById("ci_ht_code"+i+"-"+g);
						var ci_shipping_remark = document.getElementById("ci_shipping_remark"+i+"-"+g);
						var ht_code = document.getElementById("ht_code"+i+"-"+g);
						var shipping_remark = document.getElementById("shipping_remark"+i+"-"+g);
						ht_code.value = ci_ht_code.value;
						shipping_remark.value = ci_shipping_remark.value;
						$("#quota_cat"+i+"-"+g).val(ci_quota_cat.value).trigger("chosen:updated");; 
					}
					
					var check = $("#expand"+i).attr('class').indexOf('tr_nondisplay');
					var element  = document.getElementById("expand"+i);
					
					if(parseInt(check)!=0){//not exist className
						ajaxLoadPackingList(i, "true");
					}
					
				}//--- End Check Isset spID ---//
			}//--- End Count PO ---//
			
			funcCalculateAll();
		}
		
		function form_checking(){
			var ponum = arr_poselect.length;
			// alert(ponum);
			// console.log(arr_poselect);
			if(ponum>0){
				var buyer = $("#buyer_select");
				var consignee = $("#consignee_select");

				var poissuer = $("#poissuer");
				var payer = $("#payer");
				var pol = $("#portloading");
				var shipmode = $("#shipmode");
				var tradeterm = $("#tradeterm");
				var paymentterm = $("#paymentterm");
				var portofdischarges = $("#portofdischarges");
				var buyerdestination = $("#buyerdestination");

				var status = $("#temp_status").val();

				if(status==8 || status==11){
					poissuer
					.attr("disabled", "true")
					.trigger("chosen:updated");

					payer
					.attr("disabled", "true")
					.trigger("chosen:updated");

					pol
					.attr("disabled", "true")
					.trigger("chosen:updated");

					shipmode
					.attr("disabled", "true")
					.trigger("chosen:updated");

					tradeterm
					.attr("disabled", "true")
					.trigger("chosen:updated");

					paymentterm
					.attr("disabled", "true")
					.trigger("chosen:updated");
					
					portofdischarges
					.attr("disabled", "true")
					.trigger("chosen:updated");
					
					buyerdestination
					.attr("disabled", "true")
					.trigger("chosen:updated");

					//$("#btn_add_inv").prop("disabled",true);
				}

				buyer
				.attr("disabled", "true")
				.trigger("chosen:updated");
				if($("#buyer_hidden").length>0){
					$("#buyer_hidden").val(buyer.val());
				}else{
					buyer.after("<input type='hidden' id='buyer_hidden' name='buyer' value='"+buyer.val()+"' />");	
				}
				

				// consignee
				// .attr("disabled", "true")
				// .trigger("chosen:updated");
				// if($("#consignee_hidden").length>0){
				// 	$("#consignee_hidden").val(consignee.val());
				// }else{
				// 	consignee.after("<input type='hidden' id='consignee_hidden' name='consignee' value='"+consignee.val()+"' />");
				// }

				

			}
			else{
				var buyer = $("#buyer_select");
				var consignee = $("#consignee_select");
				var buyer_hidden = $("#buyer_hidden");
				var consignee_hidden = $("#consignee_hidden");

				var poissuer = $("#poissuer");
				var payer = $("#payer");
				var pol = $("#portloading");
				var shipmode = $("#shipmode");
				var tradeterm = $("#tradeterm");
				var paymentterm = $("#paymentterm");
				var portofdischarges = $("#portofdischarges");
				var buyerdestination = $("#buyerdestination");

				poissuer
				.removeAttr("disabled")
				.trigger("chosen:updated"); 
				payer
				.removeAttr("disabled")
				.trigger("chosen:updated"); 
				pol
				.removeAttr("disabled")
				.trigger("chosen:updated"); 
				shipmode
				.removeAttr("disabled")
				.trigger("chosen:updated"); 
				tradeterm
				.removeAttr("disabled")
				.trigger("chosen:updated"); 
				paymentterm
				.removeAttr("disabled")
				.trigger("chosen:updated"); 

				buyer
				.removeAttr("disabled")
				.trigger("chosen:updated"); 
				buyer_hidden.remove();
				
				portofdischarges
				.removeAttr("disabled")
				.trigger("chosen:updated"); 
				buyer_hidden.remove();
				
				buyerdestination
				.removeAttr("disabled")
				.trigger("chosen:updated"); 
				buyer_hidden.remove();

				// consignee
				// .removeAttr("disabled")
				// .trigger("chosen:updated"); 
				// consignee_hidden.remove();
				
			}
		}

		function accordion(id,ele) {
		    
		    if (document.getElementById(id).style.maxHeight == "0px"){
		    	document.getElementById(id).style.maxHeight = document.getElementById(id).scrollHeight + "px";
		        document.getElementById(id).style.overflow=null;
		        ele.classList.remove("active");
		    } else {
		        document.getElementById(id).style.maxHeight = 0;
		        document.getElementById(id).style.overflow="hidden";
		        ele.classList.add("active");
		    } 
		}


		// ajax request to get consignee by buyerID
		function func_get_consignee(mode){

			var buyerID=$("#buyer_select").val();
			var poissuer=$("#poissuer").val();
			var payer=$("#payer").val();

			if(buyerID!=="" ){ //&& poissuer!=="" && payer!==""
				$.ajax({
					type: "POST", 
					url: "ajax_get_consignee.php",
					data: {
						buyerID: buyerID, 
						poissuer: poissuer,
						payer: payer,
						mode: mode <?php if(isset($ID)) echo ",conID: '$selectedConID'"; ?>
					}, 
					beforeSend: function(){
						//$("#btn_add_inv").attr("disabled", true);
					}, 
					success: function(output){
						var data = $.parseJSON(output);
						//alert(mode+" / "+data[0]);
						$("#consignee_select").html(data[0]);
						if(parseInt(mode)==0){
							$("#consignee_address").val("");
						}
						$("#consignee_select").trigger("chosen:updated");

						//$("#btn_add_inv").attr("disabled", "true");
						
						//console.log(data[2]);

						//============ shipmode ==============//
						// $("#txt_shipmode").val("");
						// $("#shipmode").val("");

						//============ paymentterm ==============//
						// $("#txt_paymentterm").val("");
						// $("#paymentterm").val("");

						//============ tradeterm ==============//
						// $("#txt_tradeterm").val("");
						// $("#tradeterm").val("");

						//============ loading port ==============//
						// $("#txt_portloading").val("");
						// $("#portloading").val("");
						// alert("data[1] : "+data[1]);
						if(data[1] != "-999"){
							//$("#consignee_select").trigger("change");
						}

					}
				});
			}
		}

		// ajax request to get consignee info by conID
		function func_consignee_info(conID){

			if(conID != "" || conID != undefined){
				if($("#temp_status").val() !== '8' || $("#temp_status").val() !== '11'){ 
					$("#btn_add_inv").attr("disabled", false);
				}
				
			}
			var consignee = $('#consignee_select').val();
			funcLoadIcon();
			
			var paymentterm = $('#paymentterm').val();
			///alert(consignee+" <<<< ");
			$.ajax({
				type: "POST", 
				url: "ajax_consignee_info.php",
				data: {conID: consignee, mode:"paymentterm", selected_id:""+paymentterm}, 
				success: function(output){
					//var data = $.parseJSON(output);
					//alert("Payment"+output);
					document.getElementById("html_payment").innerHTML = output;
					
					$(".select_chosen").chosen({search_contains:true});
					
					$("#buyermodal .modal-body").html("");
					form_checking();
					funcRemoveIcon();
				}
			});
			
			var buyerdestination = $('#buyerdestination').val();
			$.ajax({
				type: "POST", 
				url: "ajax_consignee_info.php",
				data: {conID: consignee, mode:"buyerdestination", selected_id:""+buyerdestination}, 
				success: function(output){
					//var data = $.parseJSON(output);
					
					document.getElementById("html_buyerdestination").innerHTML = output;
					
					$(".select_chosen").chosen({search_contains:true});
					
					$("#buyermodal .modal-body").html("");
					form_checking();
					funcRemoveIcon();
				}
			});
			
			var portofdischarges = $('#portofdischarges').val();
			$.ajax({
				type: "POST", 
				url: "ajax_consignee_info.php",
				data: {conID: consignee, mode:"portofdischarges", selected_id:""+portofdischarges}, 
				success: function(output){
					//var data = $.parseJSON(output);
					
					document.getElementById("html_portofdestination").innerHTML = output;
					
					$(".select_chosen").chosen({search_contains:true});
					
					$("#buyermodal .modal-body").html("");
					form_checking();
					funcRemoveIcon();
				}
			});
			
			var shipmode = $('#shipmode').val();
			$.ajax({
				type: "POST", 
				url: "ajax_consignee_info.php",
				data: {conID: consignee, mode:"shipmode", selected_id:""+shipmode}, 
				success: function(output){
					//var data = $.parseJSON(output);
					
					document.getElementById("html_shipmode").innerHTML = output;
					
					$(".select_chosen").chosen({search_contains:true});
					
					$("#buyermodal .modal-body").html("");
					form_checking();
					funcRemoveIcon();
				}
			});
			
			var tradeterm = $('#tradeterm').val();
			$.ajax({
				type: "POST", 
				url: "ajax_consignee_info.php",
				data: {conID: consignee, mode:"tradeterm", selected_id:""+tradeterm}, 
				success: function(output){
					//var data = $.parseJSON(output);
					
					document.getElementById("html_tradeterm").innerHTML = output;
					
					$(".select_chosen").chosen({search_contains:true});
					
					$("#buyermodal .modal-body").html("");
					form_checking();
					funcRemoveIcon();
				}
			});
			
			var portloading = $('#portloading').val();
			$.ajax({
				type: "POST", 
				url: "ajax_consignee_info.php",
				data: {conID: consignee, mode:"portloading", selected_id:""+portloading}, 
				success: function(output){
					//var data = $.parseJSON(output);
					
					document.getElementById("html_portloading").innerHTML = output;
					
					$(".select_chosen").chosen({search_contains:true});
					
					$("#buyermodal .modal-body").html("");
					form_checking();
					funcRemoveIcon();
				}
			});
			
			var payer = $('#payer').val(); 
			$.ajax({
				type: "POST", 
				url: "ajax_consignee_info.php",
				data: {conID: consignee, mode:"payer", selected_id:""+payer}, 
				success: function(output){
					//var data = $.parseJSON(output);
					
					document.getElementById("html_payer").innerHTML = output;
					
					$(".select_chosen").chosen({search_contains:true});
					
					$("#buyermodal .modal-body").html("");
					form_checking();
					funcRemoveIcon();
				}
			});
			
			var poissuer = $('#poissuer').val(); 
			$.ajax({
				type: "POST", 
				url: "ajax_consignee_info.php",
				data: {conID: consignee, mode:"poissuer", selected_id:""+poissuer}, 
				success: function(output){
					//var data = $.parseJSON(output);
					
					document.getElementById("html_poissuer").innerHTML = output;
					
					$(".select_chosen").chosen({search_contains:true});
					
					$("#buyermodal .modal-body").html("");
					form_checking();
					funcRemoveIcon();
				}
			});
			
			$.ajax({
				type: "POST", 
				url: "ajax_consignee_info.php",
				data: {conID: consignee, mode:"csn_address"}, 
				success: function(output){
					//var data = $.parseJSON(output);
					
					document.getElementById("consignee_address").value = output;
					funcRemoveIcon();
					
					var name = $("#consignee_select option:selected").text();
					var notify_party   = document.getElementById("notify_party");
					var notify_address = document.getElementById("notify_address");
					var ship_to        = document.getElementById("ship_to");
					var ship_address   = document.getElementById("ship_address");
					
					if(notify_party.value.trim()=="" && notify_address.value.trim()==""){
						// notify_party.value   = name;
						// notify_address.value = output;
					}
					
					if(ship_to.value.trim()=="" && ship_address.value.trim()==""){
						ship_to.value      = name;
						ship_address.value = output;
					}
					
				}
			});
			
			$.ajax({
				type: "POST", 
				url: "ajax_consignee_info.php",
				data: {conID: consignee, mode:"getNotifyPartyID"}, 
				success: function(output){
					// var notify_party   = document.getElementById("notify_party");
					// var notify_address = document.getElementById("notify_address");
					
					if(output!=""){ 
						document.getElementById("sel_notify_party").value = output;
						getAddress("notify_address", output); 
					}
					else{
						document.getElementById("sel_notify_party").value = 0;
						document.getElementById("notify_address").value = "";
					}
				}
			});
		}
		
		function getAddress(this_mode, id){
			//alert("A"+this_mode+"/"+id);
			$.ajax({
				type: "POST", 
				url: "ajax_consignee_info.php",
				data: {conID: id, mode:""+this_mode}, 
				success: function(output){
					//var data = $.parseJSON(output);
					// alert("B /"+this_mode+" / "+id);
					if(this_mode=="owner_address"){
						document.getElementById("issue_from_address").value = output;
					}
					else if(this_mode=="shipper_address"){
						document.getElementById("shipper_address").value = output;
					}
					else if(this_mode=="notify_address"){
						var sel = document.getElementById("sel_notify_party");
						if(sel.value!=""){
							var NotifyParty = sel.options[sel.selectedIndex].text;
							if(parseInt(sel.value)!=0){
								document.getElementById("notify_party").value = NotifyParty;
							}
							else{
								document.getElementById("notify_party").value = "";
							}
						}
						document.getElementById("notify_address").value = output;
						// alert(output);
					}
					
					funcRemoveIcon();
				}
			});
		}
		
		function funcAddCategory(){
			var max_number     = parseInt(document.getElementById("max_options").value);
			var max_options    = parseInt(document.getElementById("max_options").value) + 1;
			var invoice_no     = document.getElementById("invoice_no").value;
			var isBuyerPayment = document.getElementById("isBuyerPayment").value;
			var exist_n = 0;
			
			for(var n=0;n<=parseInt(max_number);n++){
				var cat_options = document.getElementById("cat_options"+n);
				if(cat_options!=null){
					exist_n = n;
				}
			}
			max_options = exist_n + 1;
			
			$.ajax({
				url: "ajax_custom.php",
				type: "POST", 
				data: {mode:"addInvCategory", this_option:max_options, this_inv:invoice_no, this_isBuyerPayment:isBuyerPayment}, 
				success: function(output){
					//var data = $.parseJSON(output);
					//alert(output);
					// console.log(data);
					$(".tb_inv tbody.tbody_inv_dt").append(output);
					
					// $( "<p>Test</p>" ).insertBefore( ".inner" );
					
					document.getElementById("max_options").value = max_options;
					// n = n + parseInt(arr_spID.length);
					// $("#countPO").val(n);
					$(".datepicker_short").datepicker();
					
				}
			});//*/
		}

		function func_get_buyerpo(options){
			arr_poselect3 = [];
			var buyerID   = $("#buyer_select").val();
			var conID     = $("#consignee_select").val();
			var ship_date = $("#shippeddate").val();
			var invID     = $("#invID").val();
			var isBuyerPayment = $("#isBuyerPayment").val();
			var BICID     = $("#BICID"+options).val(); 
			
			if(buyerID==""){
				alert("Buyer cannot be empty");
				return;
			}
			
			if(ship_date==""){
				alert("Vessel date cannot be empty");
				return;
			}
			
			funcLoadIcon();
			
			if(parseInt(isBuyerPayment)==0){
				var countPO = document.getElementById("countPO").value;
				for(var i=0;i<=parseInt(countPO);i++){
					var spID = document.getElementById("spID"+i);
					var this_options = document.getElementById("options"+i);
					if(spID!=null){
						if(parseInt(this_options.value)==parseInt(options)){
							arr_poselect3.push(spID.value);
						}
					}//--- End if sp not NULL ---//
				}//--- End For ---//
			}
			else{
				var countPO = document.getElementById("countPO").value;
				for(var i=0;i<=parseInt(countPO);i++){
					var spID    = document.getElementById("spID"+i);
					if(spID!=null){
						arr_poselect3.push(spID.value);
					}
				}
			}
			// console.log("ajax_table_buyerpo.php?conID="+conID+"&shippeddate="+ship_date+"&buyerID="+buyerID+"&invID="+invID+"&&isBuyerPayment="+isBuyerPayment+"&&options="+options+"&&BICID="+BICID);
			// alert("A");
			$("#buyermodal .modal-body").load("ajax_table_buyerpo.php?conID="+conID+"&shippeddate="+ship_date+"&buyerID="+buyerID+"&invID="+invID+"&&isBuyerPayment="+isBuyerPayment+"&&options="+options+"&&BICID="+BICID, {arr_poselect:arr_poselect3},function(){
				funcRemoveIcon();
			});
			$("#buyermodal").modal("show");
		}

		function func_close_modal(){
			arr_poselect2 = [];
			$("#buyermodal").modal("hide");
			$("#buyermodal .modal-body").html("");
			
		}

		function func_select_po(){
			var arr_spID = arr_poselect2;
			var invID = document.getElementById("invID").value;
			var invoice_no = document.getElementById("invoice_no").value;
			var options = document.getElementById("this_options").value;
			var BICID = document.getElementById("this_BICID").value;
			var isBuyerPayment = document.getElementById("isBuyerPayment").value;
			var countPO = document.getElementById("countPO").value;
			$("#allowconfirmstatus").val("1");
			$("#status_notice").html("");
			var n = parseInt(countPO);
			// if($("tr.invrow").length != 0){
				// n = Number($(".tb_inv tr.invrow:last").attr("data-invrownum"));
			// }
			// alert(arr_spID);
			// alert(arr_spID.length);
			
			$.ajax({
				url: "ajax_get_shipment.php",
				type: "POST", 
				data: {spID: arr_spID, this_n:n, this_invID:invID, this_isBuyerPayment:isBuyerPayment, 
						this_options:options, this_countPO:countPO, this_BICID:BICID, invoice_no:invoice_no}, 
				success: function(output){
					var data = $.parseJSON(output);
					//alert(data);
					// console.log(data);
					//$(".tb_inv tbody.tbody_inv_dt").append(data);
					
					var countPO = document.getElementById("countPO").value;
					var this_id = "";
					for(var i=0;i<=parseInt(countPO);i++){
						var ele = document.getElementById("options"+i);
						if(ele!=null){
							if(parseInt(ele.value)==options){
								this_id=i;
							}
						}
					}//--- End For ---//
					if(this_id==""){
						$("#category"+options).after(data);
					}
					else{
						$("#expand"+this_id).after(data);
					}
					n = n + parseInt(arr_spID.length);
					$("#countPO").val(n);
					
					$(".select_chosen").chosen();
					var temp_arr_poselect = arr_poselect.concat(arr_spID);
					arr_poselect2 = [];
					arr_poselect = temp_arr_poselect;
					form_checking();
					funcCalculateAll();
				}
			});//*/
			

			func_close_modal();
		}
		
		function funcReloadBuyerPOFromCommercial(){
			if(!confirm("Are you sure to reload Buyer PO from Commercial Invoice? Any changes cannot be recovered.")){
				return false;
			}
			var invID          = document.getElementById("invID").value;
			
			funcLoadIcon();
			$.ajax({
				url: "ajax_custom.php",
				type: "POST", 
				data: {mode:"reloadBuyerPOFromCommercial", this_invID:invID},
				success: function(output){
					//alert(output);
					location.reload();
				}
			});
		}
		
		function funcDeleteCategory(options){
			if(!confirm("Are you sure to delete? Any changes cannot be recovered.")){ //PO also will be removed from LC assignment draft.
				return false;
			}
			
			var countPO        = document.getElementById("countPO").value;
			var invID          = document.getElementById("invID").value;
			var isBuyerPayment = document.getElementById("isBuyerPayment").value;
			var BICID          = document.getElementById("BICID"+options).value;
			var arr_n = [];
			
			for(var n=0;n<=parseInt(countPO);n++){
				var this_options = document.getElementById("options"+n);
				if(this_options!=null){
					
					if(parseInt(this_options.value)==parseInt(options)){
						//arr_n[] = n;
						arr_n.push(n);
					}
				}
			}//--- End For ---//
			
			funcLoadIcon();
			$.ajax({
				url: "ajax_delete_invpo.php",
				type: "POST", 
				data: {mode:"delCategory", spID:"", this_invID:invID, this_isBuyerPayment:isBuyerPayment, this_options:options, this_BICID:BICID},
				success: function(){
					$("#category"+options).remove();
					for(var i=0;i<parseInt(arr_n.length);i++){
						var invrownum = arr_n[i];
						
						var row2 = document.getElementById("expand"+invrownum); // console.log(row2+" // "+invrownum);
							row2.parentNode.removeChild(row2);
							
						var spID = document.getElementById("spID"+invrownum).value;
						var charge_count = $("#count_spID"+spID).val();
						var row3 = document.getElementById("chargerow"+charge_count); //remove buyer po other charge row
							row3.parentNode.removeChild(row3);
							
						$(".tb_inv tr[data-invrownum='"+invrownum+"']").remove();// remove buyer po row
					}
					
					funcCalculateAll();
					funcRemoveIcon();
				}
			});
			
			
		}

		function func_delete_po(invrownum){
			var sp_BICID = document.getElementById("sp_BICID"+invrownum).value;

			if(!confirm("Are you sure to delete? Any changes cannot be recovered.")){ //PO also will be removed from LC assignment draft.
				return false;
			}

			var shipmentpriceID = $(".tb_inv tr[data-invrownum='"+invrownum+"'] #spID"+invrownum).val();
			var LCIID    = document.getElementById("LCIID"+invrownum).value;
			
			var invID    = document.getElementById("invID").value;
			var isBuyerPayment = document.getElementById("isBuyerPayment").value;
			var index = arr_poselect.indexOf(Number(shipmentpriceID));
			//console.log("delete => (shipmentpriceID, index)" + shipmentpriceID + ", "+ index);
		
			if (index > -1) {
				arr_poselect.splice(index, 1);
			}

			//remove buyer po related other charge
			var spID = $("#spID"+invrownum).val();
			var ele  = document.getElementById("count_spID"+spID);

			funcLoadIcon();
			$.ajax({
				url: "ajax_delete_invpo.php",
				type: "POST", 
				data: {spID: shipmentpriceID, mode:"delShipment", this_invID:invID, this_isBuyerPayment:isBuyerPayment, this_BICID:sp_BICID},
				success: function(){
					$(".tb_inv tr[data-invrownum='"+invrownum+"']").remove();// remove buyer po row
					
					var row2 = document.getElementById("expand"+invrownum);
						row2.parentNode.removeChild(row2);
						
					if(ele!==null){
						var charge_count = $("#count_spID"+spID).val();
						var row3 = document.getElementById("chargerow"+charge_count); //remove buyer po other charge row
							row3.parentNode.removeChild(row3);
					}
					
					funcCalculateAll();
					funcRemoveIcon();
					//form_checking();
					arr_poselect.remove(LCIID);
				}
			});
			
		}

		function finalcheck(conStatus){
			if(confirm("Save?")){
				$("#form").submit();
			}else{
				return false;
			}
		}

		function func_write_hidden(textbox,value){
			$("#"+textbox).val(value);
		}

		function calChargeAmt(percentage, count){
			var po_amt            = parseFloat($("#po_amt"+count).val());
			var percentage        = parseFloat($("#percentage"+count).val());
			var percentage        = (percentage==""? 0: percentage);
			var percentage        = parseFloat(percentage)/100;
			var percentage_credit = parseFloat($("#percentage_credit"+count).val());
			var percentage_credit = (percentage_credit==""? 0: percentage_credit);
			var percentage_credit = parseFloat(percentage_credit)/100;

			var charge_amt_deduct = (po_amt * percentage) * -1;
			var charge_amt_credit = po_amt * percentage_credit;
			
			var charge_amt = parseFloat(charge_amt_deduct) + parseFloat(charge_amt_credit);
			
			console.log(charge_amt_deduct+" / "+charge_amt_credit);
			$("#charge_amt"+count).val(charge_amt.toFixed(2));
		}
		
		function funcChangeCharge(value, count, type){
			if(parseInt(value)==-1){
				document.getElementById(type+""+count).style.display = "INLINE";
			}
			else{
				document.getElementById(type+""+count).style.display = "NONE";
			}
		}

		function enableChargeBox(count){
			var isChecked = $('#ableamtbox'+count).is(':checked');

			if(isChecked==true){
				$("#charge_amt"+count).val("0");
				$("#percentage"+count).val("0");
				$("#percentage_credit"+count).val("0");
				$("#charge_amt"+count).prop("readonly",false);
				$("#chargeID_credit"+count).prop("disabled",true);
				$("#chargeID_deduct"+count).prop("disabled",true);
				$("#purpose_credit"+count).prop("readonly",true);
				$("#purpose_deduct"+count).prop("readonly",true);
				$("#percentage"+count).prop("readonly",true);
				$("#percentage_credit"+count).prop("readonly",true);
			}else{
				$("#charge_amt"+count).val("0");
				$("#percentage"+count).val("0");
				$("#percentage_credit"+count).val("0");
				$("#charge_amt"+count).prop("readonly",true);
				$("#chargeID_credit"+count).prop("disabled",false);
				$("#chargeID_deduct"+count).prop("disabled",false);
				$("#purpose_credit"+count).prop("readonly",false);
				$("#purpose_deduct"+count).prop("readonly",false);
				$("#percentage"+count).prop("readonly",false);
				$("#percentage_credit"+count).prop("readonly",false);
			}
		}

		function addChargeRow(){
			var count    = parseInt($("#countCharge").val());
			var totalamt = parseFloat($("#total_amount").val());
			
			$.ajax({
				type: "POST", 
				url: "ajax_custom.php",
				data: {mode:"addOtherChargeRow", this_totalamt:""+totalamt, this_count:""+count}, 
				success: function(output){
					// alert(output);
					// $('#chargeTbody tr:last').after(output);
					$('#chargeTbody').append(output);
				}
			});

			// $('#chargeTbody tr:last').after('\
				// <tr id="chargerow'+count+'">\
					// <td>\
						// <input type="hidden" name="INVDID'+count+'>" value="">\
						// <input type="hidden" name="shipmentpriceID'+count+'" value="">\
						// <input type="hidden" name="buyerpo'+count+'" value="">\
						// <input type="hidden" id="po_amt'+count+'" name="po_amt'+count+'" value="'+totalamt+'">\
						// <button class="btn btn-danger btn-xs" onclick="removeChargeRow(\''+count+'\');"><span class="glyphicon glyphicon-trash"></span></button>\
					// </td>\
					// <td>\
						// <input type="text" style="width: 100%;" name="description'+count+'" class="txt_medium" value="" required>\
					// </td>\
					// <td><input type="number" step="any" name="percentage'+count+'" id="percentage'+count+'" class="txt_medium percentage_box" onkeyup="calChargeAmt(this.value,\''+count+'\');calTotalAmount();" value="0" ></td>\
					// <td></td>\
					// <td>\
						// <input type="number" step="any" name="charge_amt'+count+'" id="charge_amt'+count+'" class="txt_medium amt_box" onkeyup="calTotalAmount();" value="0" readonly>\
					// </td>\
					// <td>\
						// <input type="checkbox" onclick="enableChargeBox(\''+count+'\');" name="ableamtbox'+count+'" id="ableamtbox'+count+'">\
					// </td>\
				// </tr>\
				// ');

			count++;
			$("#countCharge").val(count);
		}

		function removeChargeRow(count){
			try {
				var chk = confirm("Are you sure to delete? \nThis action cannot be undone");
				if(chk==true){
					var INVDID = $("#INVDID"+count).val();
					var isBuyerPayment = document.getElementById("isBuyerPayment").value;
					
					funcLoadIcon(INVDID);
					$.ajax({
						url: "ajax_delete_invpo.php",
						type: "POST", 
						data: {spID: INVDID, mode:"delCharge", this_isBuyerPayment:isBuyerPayment},
						success: function(){
							var amt=parseFloat($("#charge_amt"+count).val());
							var totalamt=parseFloat($("#totalamount").val());

							var new_total=totalamt-amt;

							$("#totalamount").val(new_total.toFixed(2));

							var row2 = document.getElementById("chargerow"+count); 
								row2.parentNode.removeChild(row2);

							calTotalAmount();
							funcCalculateAll();
							
							funcRemoveIcon();
						}
					});
					
					
				}
			}catch(e){
				alert(e);
			}
		}

		function copyPercentage(){
			var percentage=$("#percentage0").val();
			var count_charge=parseInt($("#countCharge").val());

			for(var i=1;i<count_charge;i++){
				var ele=document.getElementById("percentage"+i);

				if(ele!==null){
					if(!$("#percentage"+i).is('[readonly]')){
						$("#percentage"+i).val(percentage);
						calChargeAmt(percentage,i);
					}
					
				}
			}

			// $('.percentage_box').each(function(){
			// 	if(!$(this).is('[readonly]')){
			// 		$(this).val(percentage);
			// 	}
			// });
		}

		function calTotalAmount(){
			var sum=0;
			var sum_poonly=0;

			$('.amt_box').each(function(){
				var value=0;
				if($(this).val()!==""){
					value=$(this).val()
				}
				sum+=parseFloat(value);
			});
			
			$('.po_amt').each(function(){
				var value=0;
				if($(this).val()!==""){
					value=$(this).val()
				}
				sum_poonly += parseFloat(value);
			});

			$("#totalamount").val(sum.toFixed(2)); console.log(sum+" <<<<<<< !!");
			var totalamt = sum_poonly.toFixed(2);
			
			var countCharge = document.getElementById("countCharge").value;
			
			for(var i=0;i<parseInt(countCharge);i++){
				var ableamtbox6       = document.getElementById("ableamtbox"+i);
				var percentage        = document.getElementById("percentage"+i);
				var percentage_credit = document.getElementById("percentage_credit"+i);
				var spID   = document.getElementById("shipmentpriceID"+i);
				var po_amt = document.getElementById("po_amt"+i);
				var charge_amt = document.getElementById("charge_amt"+i);
				
				if(spID!=null){
					//alert(spID.value+" / "+i+" = "+countCharge);
					if(spID.value==""){
						po_amt.value = totalamt;
						
						if(ableamtbox6.checked==false){
							var credit_amt = totalamt * (parseFloat(percentage_credit.value) / 100);
							var deduct_amt = totalamt * (parseFloat(percentage.value) / 100);
							
							var other_charges = 0 + parseFloat(credit_amt) - parseFloat(deduct_amt);
						
							charge_amt.value = other_charges.toFixed(2);
							console.log(charge_amt.value+" "+other_charges);
						}
					}
				}//-- End if element not null --//
			}//--- End For ---//
		}

		function checkForm(){
			$("#buyer_select").prop("disabled",false);

			var status         = $("#status").val();
			var temp_status    = $("#temp_status").val();
			var existInvoice   = $("#existInvoice").val();
			var buyer          = $("#buyer_select").val();
			var invoice_date   = $("#invoice_date").val();
			var poissue_date   = $("#poissue_date").val();
			var invoice_no     = $("#invoice_no").val();
			var poissuer       = $("#poissuer").val();
			var payer          = $("#payer").val();
			var isBuyerPayment = $("#isBuyerPayment").val();
			var consignee      = $("#consignee_select").val();
			var shipmode       = $("#shipmode").val();
			var portloading    = $("#portloading").val();
			var paymentterm    = $("#paymentterm").val();
			var tradeterm      = $("#tradeterm").val();
			var exfactory      = $("#exfactory").val();
			var cargocutoff    = $("#cargocutoff").val();
			var shippeddate    = $("#shippeddate").val();
			var vesselname     = $("#vesselname").val();

			var portdischarges=$("#portofdischarges").val();
			var buyerdestination=$("#buyerdestination").val();
			 
			//|| poissuer==""   || vesselname=="" || tradeterm=="0" || cargocutoff=="" || exfactory=="" || poissue_date==""

			if(invoice_date=="" || (payer=="0"&& parseInt(isBuyerPayment)==1) || invoice_no==""  || consignee==null || shipmode=="0"  || shippeddate==""  || paymentterm=="0" || buyer==""  || buyerdestination=="0"  || parseInt(existInvoice)>0 ){//|| portloading=="0" || portdischarges=="0"
				var notice = "Please fill in";
				if(buyer==""){
					//$("#buyer_select").css("border","2px solid red");
					$("#valid_buyer_select").html("* "+notice);
					console.log("Buyer");
				}
				else{
					$("#valid_buyer_select").html("* ");
				}

				if(invoice_date==""){
					//$("#invoice_date").css("border","2px solid red");
					$("#valid_invoice_date").html("* "+notice);
					console.log("Invoice Date");
				}
				else{
					$("#valid_invoice_date").html("* ");
				}

				if(invoice_no==""){
					//$("#invoice_no").css("border","2px solid red");
					$("#valid_invoice_no").html("* "+notice);
					console.log("Invoice");
				}
				else{
					$("#valid_invoice_no").html("* ");
				}
				
				if(parseInt(existInvoice)>0){
					$("#valid_invoice_no").html("* This Invoice no. is taken. Try another");
				}

				if(poissuer==""){
					//$("#poissuer").css("border","2px solid red");
					// $("#valid_poissuer").html("* "+notice);
					// console.log("Po Issuer");
				}

				if(payer=="0" && parseInt(isBuyerPayment)==1){
					$("#payer").css("border","2px solid red");
					$("#valid_payer").html("* "+notice);
					console.log("Payer");
				}

				if(consignee==null){
					//$("#consignee_select").css("border","2px solid red");
					$("#valid_consignee_select").html("* "+notice);
					console.log("Consignee");
				}
				else{
					$("#valid_consignee_select").html("* ");
				}

				if(paymentterm=="0"){
					//$("#paymentterm").css("border","2px solid red");
					$("#valid_paymentterm").html("* "+notice);
					console.log("Payment Term");
				}
				else{
					$("#valid_paymentterm").html("* ");
				}

				if(shipmode=="0"){
					//$("#shipmode").css("border","2px solid red");
					$("#valid_shipmode").html("* "+notice);
					console.log("Ship Mode");
				}
				else{
					$("#valid_shipmode").html("* ");
				}

				// if(portloading=="0"){ 
					// $("#valid_portloading").html("* "+notice);
					// console.log("Port Loading");
				// }
				// else{
					// $("#valid_portloading").html("* ");
				// }

				if(tradeterm=="0"){
					//$("#tradeterm").css("border","2px solid red");
					// $("#valid_tradeterm").html("* "+notice);
					// console.log("Trade Term");
				}
				else{
					$("#valid_tradeterm").html("* ");
				}

				if(exfactory==""){
					//$("#exfactory").css("border","2px solid red");
					// $("#valid_exfactory").html("* "+notice);
					// console.log("Ex Factory");
				}
				else{
					$("#valid_exfactory").html("* ");
				}

				if(cargocutoff==""){
					//$("#cargocutoff").css("border","2px solid red");
					// $("#valid_cargocutoff").html("* "+notice);
					// console.log("Cargo Cut Off");
				}
				else{
					$("#valid_cargocutoff").html("* ");
				}

				if(shippeddate==""){
					//$("#shippeddate").css("border","2px solid red");
					$("#valid_shippeddate").html("* "+notice);
					console.log("Ship Date");
				}
				else{
					$("#valid_shippeddate").html("* ");
				}

				if(vesselname==""){
					//$("#vesselname").css("border","2px solid red");
					// $("#valid_vesselname").html("* "+notice);
					// console.log("Vessel Name");
				}

				// if(portdischarges=="" || parseInt(portdischarges)==0){ 
					// $("#valid_portofdischarges").html("* "+notice);
					// console.log("Port of Discharges");
				// }
				// else{
					// $("#valid_portofdischarges").html("* ");
				// }

				if(buyerdestination=="" || parseInt(buyerdestination)==0){
					//$("#buyerdestination").css("border","2px solid red");
					$("#valid_buyerdestination").html("* "+notice);
					console.log("Buyer Destination");
				}
				else{
					$("#valid_buyerdestination").html("* ");
				}
				
				if(poissue_date==""){
					//$("#poissue_date").css("border","2px solid red");
					// $("#valid_poissue_date").html("* "+notice);
					// console.log("PO Issue");
				}
				else{
					$("#valid_poissue_date").html("* ");
				}

				alert("Kindly fill in all required field before save");
				return false;
			}
			else if((parseInt(status)==8 && parseInt(temp_status)!=8 && exfactory=="") || 
					(parseInt(status)==8 && parseInt(temp_status)!=8 && exfactory=="0000-00-00 00:00:00")){
				
				alert("Kindly fill in ex-factory date before save");
				return false;
			}
			else{
				document.getElementById("submitbtn").disabled = true;
				document.getElementById("submitbtn").value = "Saving..";
				return true;
			}

		}

		function clearred(id){
			$("#"+id).css("border","");
			var isBuyerPayment = document.getElementById("isBuyerPayment").value;
			if(id=="shippeddate" && parseInt(isBuyerPayment)==1){
				document.getElementById("invoice_date").value = document.getElementById("shippeddate").value;
			}
		}
		
		function funcCalculateAll(){ 
			var grand_amt = 0;
			var grand_qty = 0;
			var countPO = document.getElementById("countPO").value;
			for(var i=0;i<=parseInt(countPO);i++){
				var countColor = document.getElementById("countColor"+i);
				var spID = document.getElementById("spID"+i);
				var sp_amt = 0;
				if(countColor!=null){
					for(var c=0;c<=parseInt(countColor.value);c++){
						var qty_ele       = document.getElementById("color_qty"+i+"-"+c);
						var unitprice_ele = document.getElementById("unit_price"+i+"-"+c);
						var total_amt     = document.getElementById("total_amt"+i+"-"+c);
						
						if(qty_ele!=null){
							var total = parseInt(qty_ele.value) * parseFloat(unitprice_ele.value);
								total = total.toFixed(2);
							total_amt.value = total;
							grand_amt += parseFloat(total);
							grand_qty += parseInt(qty_ele.value);
							sp_amt += parseFloat(total);
						}
					}//--- End For C ---//
					
					funcCalSubTotalBuyerPO(spID.value, sp_amt);
					
				}//--- End Check CountColor is not NULL ---//
				
			}//--- End For I ---//
			
			document.getElementById("totalamount").value = grand_amt.toFixed(2);
			document.getElementById("total_amount").value = grand_amt.toFixed(2);
			document.getElementById("totalqty").value    = grand_qty.toFixed(0);
			
			var countCharge  = document.getElementById("countCharge").value;
			var total_amount = document.getElementById("total_amount").value;
			for(var other=0;other<=parseInt(countCharge);other++){
				var shipmentpriceID = document.getElementById("shipmentpriceID"+other);
				var po_amt = document.getElementById("po_amt"+other);
				var percentage = document.getElementById("percentage"+other);
				if(shipmentpriceID==null && po_amt!=null){
					console.log(" << "+other+" ["+countCharge+"]");
					po_amt.value = total_amount;
					calChargeAmt(percentage.value, other);
				}
			}
			
			calTotalAmount();
		}
		
		function funcCalSubTotalBuyerPO(spID, sp_amt){
			var countCharge = document.getElementById("countCharge");
			var count_spID = document.getElementById("count_spID"+spID);
			if(count_spID!=null){
				var this_po_amt = document.getElementById("po_amt"+count_spID.value);
				var this_percentage = document.getElementById("percentage"+count_spID.value);
				
				this_po_amt.value = sp_amt;
				calChargeAmt(this_percentage.value, count_spID.value);
			}
			
		}
		
		function funcSync(mode, id, this_c=1){
			var countColor = document.getElementById("countColor"+id);
			if(countColor!=null){
				var source = document.getElementById(mode+""+id+"-"+this_c);
				for(var c=this_c;c<parseInt(countColor.value);c++){
					var ele = document.getElementById(mode+""+id+"-"+c);
					
					if(ele!=null){
						ele.value = source.value;
					}
					
				}//--- End For CountColor ---//
			}//-- End if Count is not NULL --//
		}
		
		function checkInvoiceExist(){
			var invoice_no = document.getElementById("invoice_no").value;
			var invID      = document.getElementById("invID").value;
			var notice     = "";
			
			$.ajax({
				type: "POST", 
				url: "ajax_custom.php",
				data: {mode: "checkInvoiceID", invID:""+invID, invoice_no:""+invoice_no}, 
				success: function(output){
					//var data = $.parseJSON(output);
					// console.log("result: "+output);
					document.getElementById("existInvoice").value = output;
					if(parseInt(output)>0){
						notice =  "* This Invoice no. is taken, Try another";
					}
					else{
						notice = "*";
					}
					
					document.getElementById("valid_invoice_no").innerHTML = notice;
				}
			});
		}
		
		function funcToggleExpandCategory(options){
			var countPO = document.getElementById("countPO").value;
			
			for(var n=0;n<=parseInt(countPO);n++){
				var this_options = document.getElementById("options"+n);
				//alert(n+" / this:"+this_options+" / "+options+" ["+countPO+"]");
				if(this_options!=null){
					
					if(parseInt(this_options.value)==parseInt(options)){
						
						//var check = $("#po_row"+n).attr('class').indexOf('tr_nondisplay');
						var check = $(".tb_inv tr[data-invrownum='"+n+"']").attr('class').indexOf('tr_nondisplay');
						var icon_category  = document.getElementById("icon_category"+options);
						var element_po     = document.getElementById("po_row"+n);
						var element_expand = document.getElementById("expand"+n);
						
						// $(".tb_inv tr[data-invrownum='"+n+"']").remove();
						
						if(parseInt(check)>0){//exist className
							//element_po.classList.remove("tr_nondisplay");
							$(".tb_inv tr[data-invrownum='"+n+"']").removeClass("tr_nondisplay");
							icon_category.className = "glyphicon glyphicon-chevron-up";
						}
						else{
							//element_po.className = "tr_nondisplay";
							$(".tb_inv tr[data-invrownum='"+n+"']").addClass("tr_nondisplay");
							element_expand.className = "tr_nondisplay";
							icon_category.className = "glyphicon glyphicon-chevron-down";
						}
					}
					
				}//--- End If option NULL ---//*/
				
			}//--- End For ---//
			
		}
		
		//============================================================//
		//------------------------------------------------------------//
		//--------------- PACKING LIST DETAIL FUNCTION ---------------//
		//------------------------------------------------------------//
		//============================================================//
		function funcExpand(n){
			var check = $("#expand"+n).attr('class').indexOf('tr_nondisplay');
			var element  = document.getElementById("expand"+n);
			var use_ori_qty = document.getElementById("use_ori_qty"+n);
			
			
			if(parseInt(check)==0){//exist className
				element.classList.remove("tr_nondisplay");
				var CI = (parseInt(use_ori_qty.value)==2? "true":""); 
				var isPickNPack = (parseInt(use_ori_qty.value)==3? "true":""); 
				ajaxLoadPackingList(n, CI, isPickNPack, "", "true");
			}
			else{//no className
				element.className = "tr_nondisplay";
			}
		}
		
		function ajaxLoadPackingList(n, CI="", isPickNPack="", containerno="", isexpand=""){
			var isBuyerPayment = document.getElementById("isBuyerPayment");
			var invID       = document.getElementById("invID");
			var spID        = document.getElementById("spID"+n);
			var BICID       = document.getElementById("sp_BICID"+n);
			var use_ori_qty = document.getElementById("use_ori_qty"+n);
			var expand_td   = document.getElementById("expand_td"+n);
			var cat_options = document.getElementById("options"+n); 
			var invopt      = document.getElementById("invopt");
			
			if(isPickNPack=="true"){
				use_ori_qty.value = 3;
			}
			else if(containerno!=""){
				use_ori_qty.value = containerno;
			}
			
			// alert(cat_options+" "+"cat_options"+n);
			// alert(expand_td.innerHTML+" "+"cat_options"+n);
			
			if(expand_td.innerHTML=="" || parseInt(use_ori_qty.value)==1 
				|| parseInt(use_ori_qty.value)==2 || parseInt(use_ori_qty.value)==3 || containerno!=""){
				expand_td.innerHTML = "<img src='../../media/img/loading.gif' />";
				console.log("getPackingList / "+spID.value+" / n:"+n+" / "+use_ori_qty.value+" / invID:"+invID.value+" / IsBuyerPayment:"+isBuyerPayment.value+" / BICID:"+BICID.value+" / opt:"+cat_options.value+" / "+invopt.value+" / CI:"+CI+" / isPickNPack:"+isPickNPack+" / containerno: "+containerno+" / isexpand: "+isexpand+" / this_use_ori:"+use_ori_qty.value);//this_use_ori cat_options invopt
				$.ajax({
					type: "POST", 
					url: "ajax_custom.php",
					data: {mode: "getPackingList", spID:""+spID.value, this_n:n, this_use_ori:use_ori_qty.value, 
							this_invID:invID.value, this_isBuyerPayment:isBuyerPayment.value, this_BICID:BICID.value, isCI:CI, cat_options:cat_options.value, invopt:invopt.value, isPickNPack:isPickNPack, containerno:containerno, isexpand:isexpand}, 
					success: function(output){
						expand_td.innerHTML = output;
						$('.tt_large').tooltip({
								template: '<div class="tooltip" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner large"></div></div>'
							});
						$('[data-toggle="tooltip"]').tooltip(); 
						
						funcStartRearrange(n,1, CI);
					}
				});
			}//--- End If Check not yet loading ---//			
		}
		
		function funcAddCtnRow(n){
			var count_r = document.getElementById("count_pl_row"+n);
			var spID = document.getElementById("spID"+n);
			var new_r = parseInt(count_r.value) + 1;
			funcLoadIcon();
			$.ajax({
					type: "POST", 
					url: "ajax_custom.php",
					data: {mode: "getCtnRow", spID:""+spID.value, this_n:n, count_r:new_r}, 
					success: function(output){
						$( ""+output ).insertBefore( "#tr_last"+n );
						count_r.value = new_r;
						funcRemoveIcon();
						
						$('.tt_large').tooltip({
								template: '<div class="tooltip" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner large"></div></div>'
							});
						$('[data-toggle="tooltip"]').tooltip(); 
					}
				});
		}
		
		function funcRemoveCtnRow(n, c){
			var id = n+"-"+c;
			var chk = confirm("Are you sure to delete? Any changes cannot be recovered.");
		
			if(chk==true){
				var CIHID          = document.getElementById("CIHID"+id).value;
				var isBuyerPayment = document.getElementById("isBuyerPayment").value;
				funcLoadIcon();
				$.ajax({
					type: "POST", 
					url: "ajax_custom.php",
					data: {mode: "removeCtnRow", CIHID:""+CIHID, this_isBuyerPayment:isBuyerPayment}, 
					success: function(output){
						var row = document.getElementById("tr_ctn_row"+id);
							row.parentNode.removeChild(row);
						
						funcRemoveIcon();
						funcStartRearrange(n, 1, "remove");
					}
				});
			}
		}
		
		function funcCalOneBuyerPOTotalQty(n, from=''){
			var count_pl_row = document.getElementById("count_pl_row"+n);
			var arr_grp = [];
			
			for(var i=0;i<=parseInt(count_pl_row.value);i++){
				var arr_list_detail = document.getElementById("arr_list_detail"+n+"-"+i);
				var total_ctn = document.getElementById("total_ctn"+n+"-"+i);
				
				if(arr_list_detail!=null){
					if(arr_list_detail.value!=""){
						var arr_row = arr_list_detail.value.split("::^^");
						
						// console.log(arr_list_detail.value+" / "+arr_row.length+"<<< ");
						for(var r=0;r<parseInt(arr_row.length);r++){
							
							var arr_info = arr_row[r].split("**%%");
							var group_number = arr_info[0];
							var size_name    = arr_info[1];
							var qty          = arr_info[2];
							//var chk = arr_grp.includes("grp"+group_number);
							
							
							if(arr_grp["grp"+group_number]!==undefined){
								arr_grp["grp"+group_number] += (parseInt(qty) * parseInt(total_ctn.value));
								//console.log("------ exist / grp"+group_number);
							}
							else{
								arr_grp["grp"+group_number] = (parseInt(qty) * parseInt(total_ctn.value));
								//console.log("------ non-exist / grp"+group_number);
							}
							
							// console.log("------- "+arr_row[r]+"/  / "+arr_grp["grp"+group_number]);
							
						}
						
					}//--- End If arr_list_detail!="" ---//
				}//--- End If arr_list_detail!=null ---//
			}//--- End count_pl_row ---//
			
			var countColor = document.getElementById("countColor"+n);
			//alert(countColor.value);
			for(var i=0;i<parseInt(countColor.value);i++){
				var group_number = document.getElementById("group_number"+n+"-"+i);
				var color_qty    = document.getElementById("color_qty"+n+"-"+i);
				
				var total_qty   = (arr_grp["grp"+group_number.value]== undefined? 0: arr_grp["grp"+group_number.value]);
				color_qty.value = total_qty;
				//alert(total_qty+" / m:"+n+" / grp:"+group_number);
			}
			
			funcCalculateAll();
			
			if(from=="remove"){
				funcAjaxUpdateColorQty(n);
			}
		}
		
		function funcAjaxUpdateColorQty(n){
			var invID      = document.getElementById("invID");
			var spID       = document.getElementById("spID"+n);
			var countColor = document.getElementById("countColor"+n);
			var isBuyerPayment = document.getElementById("isBuyerPayment");
			var arr_value = [];
			for(var i=0;i<parseInt(countColor.value);i++){
				var unit_price   = document.getElementById("unit_price"+n+"-"+i);
				var group_number = document.getElementById("group_number"+n+"-"+i);
				var color_qty    = document.getElementById("color_qty"+n+"-"+i);
				
				arr_value.push(group_number.value+"::"+color_qty.value+"::"+unit_price.value);
			}
			
			if(parseInt(arr_value.length)>0){
				//alert(arr_value.toString());
				funcLoadIcon();
				$.ajax({
					type: "POST", 
					url: "ajax_custom.php",
					data: {mode:"updateColorQty", shipmentpriceID:""+spID.value, this_invID:invID.value, this_isBuyerPayment:isBuyerPayment.value, grp_qty:arr_value.toString()}, 
					success: function(output){
						funcRemoveIcon();
					}
				});
			}
		}
		
		function funcStartRearrange(n, c, from=''){
			var id = n+"-"+c;
			var use_ori_qty  = document.getElementById("use_ori_qty"+n);
			var count_pl_row = document.getElementById("count_pl_row"+n);
			var start = document.getElementById("start"+id);
			var end_num = document.getElementById("end_num"+id);
			var total_ctn = document.getElementById("total_ctn"+id);
			var total_qty_in_carton = document.getElementById("total_qty_in_carton"+id);
			var total_qty = document.getElementById("total_qty"+id);
			//console.log(end_num+" / "+start+" / "+total_ctn);
			if(start!=null){
				end_num.value = parseInt(start.value) + parseInt(total_ctn.value) - 1;
				total_qty.value = parseInt(total_ctn.value) * parseInt(total_qty_in_carton.value);
			}
			
			if(from!="true"){
				use_ori_qty.value = 0;
			}
			
			if(end_num!=null){
				var def_end = end_num.value;
				var def_start = parseInt(c) + 1;
				for(var i=def_start;i<=parseInt(count_pl_row.value);i++){
					var this_start = document.getElementById("start"+n+"-"+i);
					var this_end_num = document.getElementById("end_num"+n+"-"+i);
					var this_total_ctn = document.getElementById("total_ctn"+n+"-"+i);
					var this_total_qty_in_ctn = document.getElementById("total_qty_in_carton"+n+"-"+i);
					var this_total_qty = document.getElementById("total_qty"+n+"-"+i);
					
					if(this_start!=null){
						this_start.value     = parseInt(def_end) + 1;
						this_end_num.value   = parseInt(this_start.value) + parseInt(this_total_ctn.value) - 1;
						this_total_qty.value = parseInt(this_total_ctn.value) * parseInt(this_total_qty_in_ctn.value);
						//console.log("total_qty"+i+" <===== ");
						def_end = this_end_num.value;
					}
				}//--- For All Ctn Range ---//
			}
			funcCalOneBuyerPOTotalQty(n, from);
		}
		
		function funcEndRearrange(n, c){
			var id = n+"-"+c;
			var start = document.getElementById("start"+id);
			var end_num = document.getElementById("end_num"+id);
			var total_ctn = document.getElementById("total_ctn"+id);
			
			if(parseInt(end_num.value)<parseInt(start.value)){
				end_num.value = start.value;
			}
			
			var this_total_ctn = parseInt(end_num.value) - parseInt(start.value) + 1;
			total_ctn.value = this_total_ctn;
			funcStartRearrange(n, 1);
		}
		
		function funcSyncValue(n, c, element){
			var id = n+"-"+c;
			var count_pl_row = document.getElementById("count_pl_row"+n);
			var def_value = document.getElementById(element+""+id);
			var def_start = parseInt(c) + 1;
			
			for(var i=def_start;i<=parseInt(count_pl_row.value);i++){
				var this_value = document.getElementById(element+""+n+"-"+i);
				if(this_value!=null){
					this_value.value = def_value.value;
					
					if(element=="ext_length" || element=="ext_width" || element=="ext_height"){
						calCBM(n, i);
						// console.log(element+" ===>> "+i);
					}//--- if related ctn measurement, then calculate again CBM ---//
				}
			}//--- End For All Ctn Range ---//
			
		}
		
		function calCBM(n, c){
			var id = n+"-"+c;
			var ext_length = document.getElementById("ext_length"+id);
			var ext_width  = document.getElementById("ext_width"+id);
			var ext_height = document.getElementById("ext_height"+id);
			var total_CBM  = document.getElementById("total_CBM"+id);
			
			var this_CBM = parseFloat(ext_length.value)/100 * parseFloat(ext_width.value)/100 * parseFloat(ext_height.value)/100; 
			total_CBM.value = this_CBM.toFixed(3);
		}
		
		function funcLoadPackingListDetail(n, c){
			var spID            = document.getElementById("spID"+n).value;
			var BuyerPO         = document.getElementById("BuyerPO"+n).value;
			var arr_list_detail = document.getElementById("arr_list_detail"+n+"-"+c).value;
			var start           = document.getElementById("start"+n+"-"+c).value;
			var end_num         = document.getElementById("end_num"+n+"-"+c).value;
			
			funcLoadIcon();
			$("#buyermodal_packing .modal-body").load(
				"ajax_custom.php?spID="+spID, 
				{mode:"loadPackingDetail", pd_detail:arr_list_detail, this_n:n, this_c:c, this_start:start, this_end:end_num,
				this_buyerpo:BuyerPO},function(){
					
				document.getElementById("modal_title").innerHTML = "Carton Color Size Quantity Details";
				document.getElementById("modal_footer").innerHTML = "<button type='button' class='btn btn-success' onclick='funcConfirmQty("+n+","+c+")'>Confirm </button>";
				funcCalColorSizeQty();
				funcRemoveIcon();
				
				// $(".select_chosen").chosen({
					// search_contains:true
				// });
			});
			
			$("#buyermodal_packing").modal("show");
		}
		
		function func_close_modal_packing(){
			arr_poselect2 = [];
			$("#buyermodal_packing").modal("hide");
			$("#buyermodal_packing .modal-body").html("");
		}
		
		function funcConfirmQty(n, c){
			var csq_row = document.getElementById("csq_row");
			var str_pd_detail = "";
			var count = 0;
			var grand_qty = 0;
			var grand_nnw = 0;
			var grand_nw  = 0;
			var grand_gw  = 0;
			var id = n+"-"+c;
			
			for(var i=1;i<=parseInt(csq_row.value);i++){
				var csq_qty   = document.getElementById("csq_qty"+i);
				var colorsize = document.getElementById("colorsize"+i);
				var csq_nnw   = document.getElementById("csq_nnw"+i);
				var csq_nw    = document.getElementById("csq_nw"+i);
				var csq_gw    = document.getElementById("csq_gw"+i);
				
				if(csq_qty!=null){
					csq_qty = (csq_qty.value==""? 0: csq_qty.value);
					if(parseInt(count)==0){
						str_pd_detail = colorsize.value+"**%%"+csq_qty;
					}
					else{
						str_pd_detail += "::^^"+colorsize.value+"**%%"+csq_qty;
					}
					
					grand_qty += parseInt(csq_qty);
					grand_nnw += (parseInt(csq_qty) * parseFloat(csq_nnw.value));
					grand_nw += (parseInt(csq_qty) * parseFloat(csq_nw.value));
					grand_gw += (parseInt(csq_qty) * parseFloat(csq_gw.value));
					count++;
				}
			}//--- End For ---//
			
			document.getElementById("arr_list_detail"+id).value = str_pd_detail;
			document.getElementById("total_qty_in_carton"+id).value = grand_qty;
			document.getElementById("net_net_weight"+id).value = grand_nnw.toFixed(2);
			document.getElementById("net_weight"+id).value = grand_nw.toFixed(2);
			document.getElementById("gross_weight"+id).value = grand_gw.toFixed(2);
			
			func_close_modal_packing();
			funcStartRearrange(n, 1);
			
			getLatestToolTipDetails(id, str_pd_detail);
		}
		
		function getLatestToolTipDetails(id, str_pd_detail){
			var cih_spID = document.getElementById("cih_spID"+id);
			
			$.ajax({
					type: "POST", 
					url: "ajax_custom.php",
					data: {mode: "getTooltipHTML", spID:""+cih_spID.value, arr_list_detail:str_pd_detail}, 
					success: function(output){
						//alert(output);
						var this_ele = document.getElementById("tt_title"+id);
							this_ele.setAttribute('title',''+output);
						
						$(this_ele).tooltip('hide')
											  .attr('data-original-title', '')
											  .tooltip('fixTitle')
											  .tooltip('show'); 
					}
			});
			
			
		}
		
		function funcCalColorSizeQty(){
			var csq_row = document.getElementById("csq_row");
			var csq_total = document.getElementById("csq_total");
			var str_total = 0;
			
			for(var i=1;i<=parseInt(csq_row.value);i++){
				var csq_qty = document.getElementById("csq_qty"+i);
				
				if(csq_qty!=null){
					str_total += parseInt(csq_qty.value);
				}
			}//--- End For csq row ---//
			// console.log(csq_total.value+" <==")
			
			csq_total.value = str_total;
		}
		
		function funcAddCSQRow(){
			var csq_spID = document.getElementById("csq_spID");
			var csq_row = document.getElementById("csq_row");
			var new_r = parseInt(csq_row.value) + 1;
			funcLoadIcon();
			$.ajax({
					type: "POST", 
					url: "ajax_custom.php",
					data: {mode: "getCSQRow", spID:""+csq_spID.value, this_r:new_r}, 
					success: function(output){
						$( ""+output ).insertBefore( "#tr_lastcsq");
						csq_row.value = new_r;
						funcRemoveIcon();
					}
				});
			
		}
		
		function funcRemoveCSQRow(id){
			var chk = confirm("Confirm to remove?");
		
			if(chk==true){
				//var CIHID = document.getElementById("CIHID"+id).value;
				//funcLoadIcon();
				
				var row = document.getElementById("tr_csqrow"+id);
					row.parentNode.removeChild(row);
						
				funcCalColorSizeQty();
			}
		}
		
		function funcEditMode(id, n){
			var sel_display = document.getElementById(id+""+n).style.display;
			var txt_display = document.getElementById("txt_"+id+""+n).style.display;
			var this_ele = document.getElementById("btn_"+id+""+n);
			//alert(id+""+n+" // "+txt_display);
			if(txt_display=="none"){
				 document.getElementById("txt_"+id+""+n).style.display = "inline";
				 document.getElementById("td_"+id+""+n).style.display = "none";
				 document.getElementById("icon_"+id+""+n).className = "glyphicon glyphicon-remove";
				 document.getElementById("mode_"+id+""+n).value = "1";
				 //document.getElementById("btn_"+id+""+n).title = "";
				 this_ele.setAttribute('title','Go back select mode');
			}
			else{
				document.getElementById("txt_"+id+""+n).style.display = "none";
				 document.getElementById("td_"+id+""+n).style.display = "block";
				 document.getElementById("icon_"+id+""+n).className = "glyphicon glyphicon-edit";
				 document.getElementById("mode_"+id+""+n).value = "0";
				 this_ele.setAttribute('title','Edit Mode');
			}
						
			$(this_ele).tooltip('hide').attr('data-original-title', '')
									   .tooltip('fixTitle')
									   .tooltip('show'); 
		}
		
		function funcAddPaymentOpt(){
			var chk = confirm("Confirm add another option for payment invoice?");
			if(chk==true){
				funcLoadIcon();
				var from_invID = document.getElementById("from_invID");
				$.ajax({
						type: "POST", 
						url: "ajax_custom.php",
						data: {mode: "addPaymentOption", from_invID:""+from_invID.value}, 
						success: function(output){
							
							// alert(output);
							window.location = "buyer_inv.php?id="+output+"&&isBuyerPayment=true";
							funcRemoveIcon();
						}
					});
			}
		}
		
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
	  
	</script>