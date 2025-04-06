
 /*
function addTable() {
      
    var myTableDiv = document.getElementById("myDynamicTable");
      
    var table = document.createElement('TABLE');
    table.border='1';
    
    var tableBody = document.createElement('TBODY');
    table.appendChild(tableBody);
	
	 var countRow = document.getElementById("dataTable").rows.length;
	 var countCol = document.getElementById('dataTable').rows[0].cells.length; //count columns
	 //alert(cols);
      
    for (var i=0; i<countRow; i++){
       var tr = document.createElement('TR');
	   tr.appendChild(document.createTextNode("Color "));
       tableBody.appendChild(tr);   
       
       for (var j=0; j<countCol; j++){
		   
           var td = document.createElement('TD');
           td.width='75';
           td.appendChild(document.createTextNode("Cell " + i + "," + j));
           tr.appendChild(td);
       }
    }
    myTableDiv.appendChild(table);
    
}*/

var generate = new Array();
//var colorRow = new Array();

function addTable(array, arrayLength) {
    //alert("shasja");
	if(document.getElementById("submit") != null){
		document.getElementById("submit").style.visibility="visible";
	}
	//create form
  /*theForm = document.createElement('form');
	theForm.action = '';
	theForm.setAttribute("class", "form");
	theForm.method = 'post';*/

    var myTableDiv = document.getElementById("myDynamicTable");
    document.getElementById("myDynamicTable").innerHTML = "";
	
	//count datatable's rows in number
    //var dataTable = document.getElementById("dataTable");
	//var rCount = dataTable.rows.length;
	
    
    var table = document.createElement('TABLE');
    table.border='0';
	table.id = 'tblGenerate';
	//theForm.appendChild(table);
    
    var tableBody = document.createElement('TBODY');
    table.appendChild(tableBody);
	//alert(document.getElementById("countColor"));
	//var countRow = document.getElementById("dataTable").rows.length; //this clause is count the row
	var countRow = document.getElementById("countColor").value;//retrieve the value from the hidden text (colorsize.php)
	//alert("test "+countRow);
	//alert(countRow);
	var countCol = parseInt(arrayLength); //count columns
	
	//alert(countCol);
	//==============================================================//
	//===============First Row (Label Size Name)====================//
	//==============================================================//
	var tr = document.createElement("TR");
	tr.id = "default";
	tableBody.appendChild(tr);
	
	var td = document.createElement("TH");
	td.innerHTML = "";
	tr.appendChild(td);
	
	var td = document.createElement("TH");
	td.innerHTML = "Color / Size Name";
	tr.appendChild(td);
	
	if(document.getElementById("countSizeName")!=null){
		var countSizeName = document.getElementById("countSizeName").value;
	}else{
		var countSizeName = 0;
	}
	//alert(countSizeName);
	
	var statusSize = document.getElementById("sizeStatus").value;
	
	//if(parseInt(countSizeName)==0){
	if(statusSize=="change" || statusSize=="new"){
		document.getElementById("countSizeName").value = countCol;
		//alert(countCol);
		for(var i=0;i<countCol;i++){
			
			var td = document.createElement("TH");
			td.innerHTML = ""+array[i];
			hiddenSize = document.createElement("input");
			hiddenSize.type = "hidden";
			hiddenSize.setAttribute("id", "sizeNameC-"+i);
			hiddenSize.setAttribute("name", "sizeNameC-"+i);
			hiddenSize.setAttribute("value", ""+array[i]);
			td.appendChild(hiddenSize);
			
			tr.appendChild(td);
		}
	}else{
		//alert("fsd");
		for(var i=1;i<=parseInt(countSizeName);i++){
			
			var mySize = document.getElementById("sizeName"+i).value;
			//alert(mySize);
			var td = document.createElement("TH");
			td.innerHTML = mySize;
			hiddenSize = document.createElement("input");
			hiddenSize.type = "hidden";
			hiddenSize.setAttribute("id", "sizeName-"+i);
			hiddenSize.setAttribute("name", "sizeName-"+i);
			hiddenSize.setAttribute("value", mySize);
			td.appendChild(hiddenSize);
			
			tr.appendChild(td);
		}
	}
	
	
	var td = document.createElement("TH");
	td.innerHTML = "Total Qty";
	tr.appendChild(td);
	
	//===============End First Row (Label Size Name)====================//
	//==================================================================//
	
	//alert(countRow);

	document.getElementById("countGeneColor").value = countRow;
	//alert("here");
    for (var i=1; i<=parseInt(countRow); i++){
		
		var check = document.getElementById("selectColor-"+i);
		//alert(" selectColor "+i);
		if(check!=null){//check element exist
		
		var colorID = document.getElementById("selectColorID-"+i).value;
		//var checkArray = generate.indexOf(colorID);
		
			var tr = document.createElement("TR");
			tr.id = "tbGen"+i;
			//alert("gen");
			tableBody.appendChild(tr);
			
			var td = document.createElement('TD');
			var btn = document.createElement("BUTTON");
			var value = document.createTextNode("Delete");
			btn.setAttribute("id", "btn_id");
			btn.setAttribute("class", "deletebtn_class");
			btn.setAttribute("width", "250px");
			btn.setAttribute("onclick","dele("+i+")");
			btn.appendChild(value);
			td.appendChild(btn);
			tr.appendChild(td);
			
			var td = document.createElement('TD');
			//var strUser = s.options[s.selectedIndex].text;
			var s = document.getElementById("selectColor-"+i).innerHTML;
			
			
			
			
			//var strUser = s.options[s.selectedIndex].text;
			//alert("--"+strUser);
			td.appendChild(document.createTextNode(s));
			tr.appendChild(td);	
			
			if(statusSize=="change" || statusSize=="new") {
				
				for (var j=0; j<countCol; j++){
			   
				   var td = document.createElement('TD');
				   td.width='75';
				   var text = document.createElement("input");
				   text.type = "number";
				   text.setAttribute("id", "text_id"+i+"-"+j);
				   text.setAttribute("name", "text_id"+i+"-"+j);
				   //text.value = i+""+j;
				   
				   /*//count each row for create class name 
				   for (var c=0; c<countRow; c++){
						text.setAttribute("class", "amountRow"+c);
				   }//*/
				   
				   text.setAttribute("min", "0");
				   text.setAttribute("max", "10000");
				   text.setAttribute("onkeyup", "total("+i+","+countCol+")");
				   //text.setAttribute("onchange", "total("+i+","+countCol+")");
				   text.setAttribute("onclick", "total("+i+","+countCol+")");
				  // text.setAttribute("value", i+""+j);
				   
				   td.appendChild(text);
				   tr.appendChild(td);
			   
				}  // end for loop (td)
			
			}else{ //----status exist----//
				
				for(var j=0;j<parseInt(countSizeName);j++){
			   
				   var td = document.createElement('TD');
				   td.width='75';
				   var text = document.createElement("input");
				   text.type = "number";
				   text.setAttribute("id", "text_id"+i+"-"+j);
				   text.setAttribute("name", "text_id"+i+"-"+j);
				   //text.value = i+""+j;
				   
				   text.setAttribute("min", "0");
				   text.setAttribute("max", "10000");
				   text.setAttribute("onkeyup", "total("+i+","+countSizeName+")");
				   //text.setAttribute("onchange", "total("+i+","+countCol+")");
				   text.setAttribute("onclick", "total("+i+","+countSizeName+")");
				  // text.setAttribute("value", i+""+j);
				   
				   td.appendChild(text);
				   tr.appendChild(td);
			   
				}  // end for loop (td)
			
			}
		
		
	   
	   var td = document.createElement('TD');
       td.width='75';
	   
       var totalText = document.createElement("label");
		   //totalText.type = "text";
		   totalText.setAttribute("id", "totalText_id"+i);
		   totalText.setAttribute("class", "totalText");   
	   td.appendChild(totalText);
	   tr.appendChild(td);
	   
	   }//--end check element exist
	   
    }// end for loop (tr)

    var tr = document.createElement("TR");
	tableBody.appendChild(tr);
	
    myTableDiv.appendChild(table);
	  
	//count row and column in dynamic data (big table)
	document.getElementById("column").value = countSizeName; 
	document.getElementById("row").value = parseFloat(countRow) +1;
	
}
//==============================================================================================================//
//================================================end add table=================================================//
//==============================================================================================================//

// calculate the total amount different size and color
function total (rowID, countColumn) {
    //alert(rowID+" "+countColumn);
	document.getElementById("row").value = parseFloat(rowID);
    document.getElementById("column").value = parseFloat(countColumn);

	//alert(rowID+" "+countColumn);
	var total = 0;
	var id;
	var currentqty;

		for (var i=0; i<countColumn; i++){
			//document.getElementById("totalText_id"+i).value = parseFloat(value);
			 id = "text_id"+rowID+"-"+i;
			 //alert(id);
			 //alert(parseInt(document.getElementById(id).value));
			 currentqty = (document.getElementById(id).value == "" ? 0 : parseInt(document.getElementById(id).value) ) //if else (iif)
			 //alert(currentqty);
			 total = total +  currentqty;
		}
		//alert("finish");
		document.getElementById("totalText_id"+rowID).innerHTML = total;
}
getItems();



function deleteGRows(obj) {
	alert(obj);
   try {
		var row = document.getElementById("r"+obj);
		row.parentNode.removeChild(row);
		
	}catch(e){
		//alert(e);
	}
    
}
 
function load() {
    
    console.log("Page load finished");
 
}