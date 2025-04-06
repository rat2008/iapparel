/*$(document).ready(function () {
    $('.calculate').on('click', function() {
        $('.tb_detail tr').each(function() {
            var proQty = $(this).find('input.proQty').val();
            var sizeQty1 = $(this).find('input.sizeQty1').val();
			var sizeQty2 = $(this).find('input.sizeQty2').val();
			var sizeQty3 = $(this).find('input.sizeQty3').val();
			var sizeQty4 = $(this).find('input.sizeQty4').val();
			
			alert(sizeQty1+" "+sizeQty2+" "+sizeQty3+" "+sizeQty4);

            var total = ( parseFloat(sizeQty1) + parseFloat(sizeQty2) + parseFloat(sizeQty3) + parseFloat(sizeQty4));
            $(this).find('input.proQty').val(total);
			
			var proQtySec = (parseFloat(total)* parseFloat(total));
			$(this).find('input.consump').val(proQtySec);

			var purchaseQty = ( parseFloat(proQtySec) -35 );
			$(this).find('input.total').val(purchaseQty);
        }); //END .each
        return false;
    }); // END click 
});*/

function calConsumption(){
	
	var countMB = document.getElementById("countMB").value;
	
	for (var i = 1; i <= countMB; i++) { 
		
		var countSize = document.getElementById("countSize"+i).value;
		//alert(countSize);
		
		var ProdQty = 0.000;
		for (var c = 1; c <= countSize; c++) { 
			var size = document.getElementById("sizeQty"+i+"-"+c).value;
			var ProdQty = parseFloat(ProdQty) + parseFloat(size);
			var prodQty_ = document.getElementById("proQty-"+i).value;
			var pQ =  parseFloat(ProdQty) * parseFloat(prodQty_);
			
			//alert(size);
		}//end for loop
		// ProQty * wastage = consumption
		//alert(ProdQty.toFixed(4));
		
			
		
		document.getElementById("h-proQty-"+i).value = pQ.toFixed(2); //----------product quantity
		
		var rowCount = document.getElementById("countRow"+i).value;
		//alert("rowCount"+i);
		for (var row = 1; row <= rowCount; row++) { 
			
			var consumption = 0.000;
			var pQ2 = document.getElementById("h-proQty-"+i).value
			var wastage = document.getElementById("wastage"+i+"-"+row).value;
			consumption = ((parseFloat(pQ) * parseFloat(wastage)) /100) + parseFloat(pQ2);		
			
			var aQuality = 0.000;
			aQuality = parseFloat(consumption) - 35;
			
			document.getElementById("consump-"+i+"-"+row).value = consumption.toFixed(2); //---------consumption total
			document.getElementById("total-"+i+"-"+row).value = aQuality.toFixed(2); //----------A.Quality*/
			document.getElementById("purchaseQty-"+i+"-"+row).value = aQuality.toFixed(2); 
			//alert ("purchaseQty-"+i+"-"+row);
			
			//calculate purchaseqty in lbs
			//alert (i);
			var all = document.getElementById("all-"+i).value;
			//alert (all);
			var max_weig_yarn = document.getElementById("max_weig_yarn-"+i).htmlFor;
			var total_lbs1 = (parseFloat(all) * (parseFloat(max_weig_yarn) / 1000)) * parseFloat(wastage)/100;
			var total_lbs2 = (((parseFloat(all) * (parseFloat(max_weig_yarn) / 1000)) + parseFloat(total_lbs1)) * 2.2046) * parseFloat(prodQty_) ;
			document.getElementById("purchaseQty_lbs-"+i+"-"+row).value = total_lbs2.toFixed(2);
			var test = document.getElementById("purchaseQty_lbs-"+i+"-"+row).value ;
			//alert (test);
			
			/*if (document.getElementById("convert1").disabled === true)
			{
			alert("dsad");
			}*/
		}

	}//end for loop

}

function cal2(){
	
	var countMB = document.getElementById("countMB").value;
	
	for (var i = 1; i <= countMB; i++) { 
		
		var countSize = document.getElementById("countSize"+i).value;
			//get lbs
			

		var prodQty_ = document.getElementById("proQty-"+i).value;
					var all = document.getElementById("all-"+i).value;
			var max_weig_yarn = document.getElementById("max_weig_yarn-"+i).htmlFor;	
			//alert(size);
		


	
		var rowCount = document.getElementById("countRow"+i).value;
		alert(countMB);
		//alert("rowCount"+i);
		for (var row = 1; row <= rowCount; row++) { 
				
			var wastage = document.getElementById("wastage"+i+"-"+row).value;
			//calculate purchaseqty in lbs
			var total_lbs1 = (parseFloat(all) * (parseFloat(max_weig_yarn) / 1000)) * parseFloat(wastage)/100;
			var total_lbs2 = (((parseFloat(all) * (parseFloat(max_weig_yarn) / 1000)) + parseFloat(total_lbs1)) * 2.2046) * parseFloat(prodQty_) ;
			document.getElementById("purchaseQty_lbs-"+i+"-"+row).value = total_lbs2.toFixed(2);
			//alert (total_lbs2);
			//var test = document.getElementById("purchaseQty_lbs-"+i+"-"+row).value;
	
			
			//var test = 1;
			//document.getElementById("purchaseQty_lbs-5-1").value = parseFloat(test);
		}

	}//end for loop

}


//]]>  


/*function calc(A,B,C,D,E,SUM,CONSUMP,A_QUA) {
  var one = (document.getElementById(A).value -0);
  var two = (document.getElementById(B).value -0);
  var three = (document.getElementById(C).value -0);
  var four = (document.getElementById(D).value -0);  
  document.getElementById(E).value = one + two + three + four;
  
  var prodQua = (document.getElementById(E).value -0);
  document.getElementById(SUM).value = prodQua ;//- 35;
  
  var consump = (document.getElementById(SUM).value -0);
  document.getElementById(CONSUMP).value = prodQua*(one + two + three + four);
  
  var a_quality = (document.getElementById(CONSUMP).value -0);
  document.getElementById(A_QUA).value = a_quality - 35;
}

//function convert() {
// var consump = (document.getElementById("result").value -0);
//  var results = parseFloat(consump)/ 12;
//  document.getElementById("result").value = results;	
  
  //document.getElementById("result").value = document.getElementById("result").value + 12;
  
//}*/

   function OnClickDoz (choice) {
   var countMB = document.getElementById("countMB").value;

   for (var i = 1; i <= countMB; i++)  
   {
   var pQ = document.getElementById("proQty-"+i).value;
   if(choice == 'A')
   {   
   document.getElementById("proQty-"+i).value  = parseFloat(pQ) / 12;
   document.getElementById("convert1").disabled = true;
   document.getElementById("convert2").disabled = false;

   }
   }
		document.getElementById("unit").value="doz";
   
   }
   
   
   function OnClickPC (choice) {
   var countMB = document.getElementById("countMB").value;

   for (var i = 1; i <= countMB; i++)  
   {
   var pQ = document.getElementById("proQty-"+i).value;
   if(choice == 'B')
   {   
   document.getElementById("proQty-"+i).value = parseFloat(pQ) * 12;
   document.getElementById("convert2").disabled = true;
   document.getElementById("convert1").disabled = false;	
   }
   }
   document.getElementById("unit").value="pcs";
   }


   
 function convertWeight(row, lot, receive){
	
	//alert(row+" - "+lot+" - "+receive);
	var all = document.getElementById("all-"+row).value;
     //alert(row);
	 
	var countSize = document.getElementById("countSize"+row).value;
    //alert(countSize);
	
   for (var c = 1; c <= countSize; c++) { 
   {
   document.getElementById("sizeQty"+row+"-"+c).value = parseFloat(all);
   //alert(size);
   }
   }
	 
		
}
