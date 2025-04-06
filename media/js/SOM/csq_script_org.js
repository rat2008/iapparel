function myFunction() {
		
		var x = document.getElementById("selectSize").value;
		//document.getElementById("showsize").innerHTML = "Selected size: " + x;
		var status = document.getElementById("sizeStatus").value;
		
		if(x=="empty" && status!="new"){
			document.getElementById("sizeStatus").value = "exist";
		}else if(x!="empty" && status!="new"){
			document.getElementById("sizeStatus").value = "change";
		}
		
		var xmlhttp;  

		if (window.XMLHttpRequest)    //retrieve text or notice drop down list
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
			
			//funcSplitSize(xmlhttp.responseText, "fromSelect",0);//responseText = get all data from the sizeNameList.php
			//funcSplitSize(xmlhttp.responseText, "toSelect",0);  //start is the position such as from is 2, then to only can select after 2.
			selectValue = xmlhttp.responseText;
			//alert(selectValue);
			selectRange(selectValue, "toSelectRange");
			}
		  }
		xmlhttp.open("GET","sizeNameList.php?id="+x,true);  //retrieve x from sizeNameList
		xmlhttp.send();
		
}

var myArray2 = new Array();	
function selectRange(value, selectID){	
	var res = value.split(",");
	myArray2 = res;
	
	var status = funcClearOption(selectID);
		
	if(status==true){
		var x = document.getElementById(selectID);
		
		for(var i=0;i<res.length;i++){
			//alert(myArray[i]);
			var option = document.createElement("option");
			option.text = myArray2[i];
			option.value = i;
			option.selected = true;
			x.add(option);
					
			//alert(i);
		}//------end for loop------//
	}//------end if------//
}
	
var myArray = new Array();
function funcSplitSize(value, selectID, start){
		
		var res = value.split(",");
		myArray = res;
		
		var status = funcClearOption(selectID);
		
		if(status==true){
			var x = document.getElementById(selectID);
			
			for(var i=start;i<res.length;i++){
				//alert(myArray[i]);
				var option = document.createElement("option");
				option.text = myArray[i];
				option.value = i;
				x.add(option);
				
				//alert(i);
			}
		}
	}
	
function funcClearOption(selectID){
		var select = document.getElementById(selectID);
		var length = select.options.length;
		//alert(length);
		for (i = 0; i < length; i++) {
		   select.remove(0);
		}
		return true;
	}

function myFunctionSelected(){   // after myFunctionSelected complete then pass to funcSplitSize
	var select = document.getElementById("fromSelect");  //select tag 
	//alert(select.length);
	//alert(selectValue);
	var start = 0;
	
	//if((select.length-1)==select.selectedIndex){  //get option position
		start = select.selectedIndex;
	/*}else{
		start = select.selectedIndex + 1;  //
	}//*/
	//alert(start);
	funcSplitSize(selectValue,"toSelect",start); // POS

}

var checkColorArray = new Array();

function generate(tableID){
	var countRow = document.getElementById("countColor").value;
	var color = document.getElementById("txt_color").value;
	//alert(countRow+" "+color);
	if(color==""){
		return false;
	}
	for (var i=1;i<=parseInt(countRow);i++){
		var check = document.getElementById("selectColorName-"+i);
		if(check!=null){ //----check element exist----//
			var colorIDData = document.getElementById("selectColorName-"+i).value;
			var checkData = checkColorArray.indexOf(colorIDData);
			if(checkData==-1){
				checkColorArray.push(colorIDData);
					//alert("asas");
			}
		}//---- End Check Element ----//
	}//---- End For Loop ----//
	
	//------------Validate Color Exist-------------//
	var txt_color = document.getElementById("txt_color").value;
	document.getElementById("txt_color").value = "";
	var upper_color = txt_color.toUpperCase();
	var check = checkColorArray.indexOf(upper_color);
	
	if(check==-1){
		checkColorArray.push(upper_color);
		//alert("insert");
	}else{
		return false;
	}
	
	var cc = document.getElementById("countColor").value;
    var table = document.getElementById(tableID);
	document.getElementById(tableID).style.visibility="visible";
	document.getElementById("colorTable_thead").style.visibility="visible";
	
	var num = parseInt(cc)+1;
	
	var rowCount = table.rows.length;
    var row = table.insertRow(rowCount);
	row.setAttribute("id", "row"+num);
	row.setAttribute("name", "row"+num);
	row.setAttribute("ondblclick", "dele("+num+")");
	
	var cell1 = row.insertCell(0);
    //var cell2 = row.insertCell(1);
    var cell2 = row.insertCell(1);
	var cell3 = row.insertCell(2);
	
	cell1.innerHTML = "<label id='selectColor-"+num+"'>"+upper_color+"</label><input type='hidden' value='"+upper_color+"' name='selectColor-"+num+"' id='selectColorID-"+num+"'/> "+
						"<input type='hidden' value='"+upper_color+"' name='selectColorName-"+num+"' id='selectColorName-"+num+"'/>";
		 
    // var str_bom = document.getElementById("str_bom").value;
    // cell2.innerHTML = "<input type='text' id='str_bom-"+num+"' name='str_bom-"+num+"' class='colorCode' value='"+str_bom+"'/>";
	
	var bcode = document.getElementById("bcode").value;
    cell3.innerHTML = "<input type='text' id='buyerCode-"+num+"' name='buyerCode-"+num+"' class='colorCode' value='"+bcode+"'/>";
    cell2.innerHTML = "<input type='text' id='str_bom-"+num+"' name='str_bom-"+num+"' class='colorCode' value=''/>";
	
	document.getElementById("countColor").value = num;	
	document.getElementById("bcode").value = "";
	
	//alert(checkColorArray);
	
}
//---- Back up (Old Version) ----//
function generate2(tableID){
	//alert(tableID);
	var countRow = document.getElementById("countColor").value;
	var color = document.getElementById("selectColor").value;
	if(parseInt(color)==-1){
		return;
	}
	
	for (var i=1;i<=parseInt(countRow);i++){
	var check = document.getElementById("selectColorID-"+i);
		//alert(" selectColor "+i);
		if(check!=null){//----check element exist----//
			var colorIDData = document.getElementById("selectColorID-"+i).value;
			var checkData = checkColorArray.indexOf(colorIDData);
			if(checkData==-1){
				checkColorArray.push(colorIDData);
					//alert("asas");
			}
		}
	}//----end for loop----//
	//alert(countRow);

	//------------Validate Color Exist-------------//
	var e = document.getElementById("selectColor");
	var colorID = e.options[e.selectedIndex].value; //---colorID---//
	//alert(colorID);
	var check = checkColorArray.indexOf(colorID);
	//alert(check);
	if(check==-1){
		checkColorArray.push(colorID);
		//alert("insert");
	}else{
		return false;
	}
	
	//count the table rows
	var cc = document.getElementById("countColor").value;
	
    var table = document.getElementById(tableID);
		
	document.getElementById(tableID).style.visibility="visible";
	document.getElementById("colorTable_thead").style.visibility="visible";
	//document.getElementById("create").style.visibility="visible";
	
	var num = table.rows.length;
	num++;
	num = parseInt(cc)+1;
	//alert(num);
	
    var rowCount = table.rows.length;
    var row = table.insertRow(rowCount);
	row.setAttribute("id", "row"+num);
	row.setAttribute("name", "row"+num);
	row.setAttribute("ondblclick", "dele("+num+")");
		
    var cell1 = row.insertCell(0);
    //cell1.appendChild(element1);
		
    var cell2 = row.insertCell(1);
	//var cell3 = row.insertCell(2);
		
	//var rCount = table.rows.length;
        
	//document.getElementById("colorRow").value = parseFloat(rCount);

	for(var i=num; i<=num; i++) {
		//alert(i);
	var e = document.getElementById("selectColor");
	var strUserValue = e.options[e.selectedIndex].value; //---colorID---//
    var strUser = e.options[e.selectedIndex].text; //---colorName---//
	//alert(strUserValue);
		
	cell1.innerHTML = "<label id='selectColor-"+i+"'>"+strUser+"</label><input type='hidden' value='"+strUserValue+"' name='selectColor-"+i+"' id='selectColorID-"+i+"'/> "+
						"<input type='hidden' value='"+strUser+"' name='selectColorName-"+i+"' id='selectColorName-"+i+"'/>";
		 
    var bcode = document.getElementById("bcode").value;
    cell2.innerHTML = "<input type='text' id='buyerCode-"+ i +"' name='buyerCode-"+ i +"' class='colorCode' value='"+bcode+"'/>";
	
	}//---- End For Loop ----//

	//addition 1 for each rows value
	var ccAdd = parseInt(cc) + 1;
	document.getElementById("countColor").value = ccAdd;	
	
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

function dele(num) {
    //alert( "Hello World");
	   try {
    //alert(num);
		var check = confirm("Are you sure to delete this color?");
		
		if(check){
		
		//alert(checkColorArray);
		var colorID = document.getElementById("selectColorName-"+num).value;
		checkColorArray.remove(colorID);
		colorArray.remove(colorID);
		//alert(checkColorArray);
		
		var row = document.getElementById("row"+num);
		row.parentNode.removeChild(row);
		
		var row2 = document.getElementById("tbGen"+num);
		row2.parentNode.removeChild(row2);
		
		}
		
	}catch(e){
		//alert(e);
	}
}

//================================Generate Color Size Table=========================================//
var colorArray = new Array();
var colorNameArray = new Array();
	
function funcAddTable(){
	var checkStatus = document.getElementById("sizeStatus").value;
	//alert(checkStatus);
	if(checkStatus=="new"){
		//alert("here");
		funcAddNew(); 
		var citrus2 = getRangeSize();
		addTable(citrus2, citrus2.length);
	}else{
	
		var countRow = document.getElementById("countColor").value;
		//alert(colorArray);
		
		for(var color=1;color<=countRow;color++){
			var check = document.getElementById("selectColorID-"+color);
			//alert(check);
			if(check != null){
				var colorID = document.getElementById("selectColorID-"+color).value;
				var currentColorName = document.getElementById("selectColorName-"+color).value;
				//alert(currentColorName);
				var check = colorArray.indexOf(colorID);
				
				if(check==-1){
					colorArray.push(colorID);
					colorNameArray[colorID] = currentColorName;
					//alert(colorNameArray[colorID]);
				}
			}
		}
		//alert("asd");
		//alert(colorArray.toString());
		
		var temp = document.getElementById("tempStatus").value;
		var statusSize = document.getElementById("sizeStatus").value;

		if(statusSize == "change"){
			//alert("here S");
			document.getElementById("tempStatus").value = "change";
			//var fromSize = document.getElementById("fromSelect").value;
			//var toSize = parseInt(document.getElementById("toSelect").value) + 1;  //because the position start from 0.
			
			//var citrus = myArray.slice(fromSize, toSize); //splice can use variable php implode
			//alert("<---");
			var citrus2 = getRangeSize();
			addTable(citrus2, citrus2.length);//*/
			
		}else{
			if(temp=="change"){
				//alert("here A");
				funcGeneRefresh();
			}else{
				//alert("here B");
				funcGeneAddRow();
				
			}
		}
	}//---end if check new---//
}//----function add table----//


function getRangeSize(){
	var rangeArray = new Array();
	var select = document.getElementById("toSelectRange");
	var range = "";
	for(var i=0;i<select.length;i++){
		if(select[i].selected){
			
			rangeArray.push(select[i].text);
			/*if(range==""){
				range = select[i].text;
			}else{
				range = range+","+select[i].text;
			}//*/
		}
	}
	//alert(range);
	return rangeArray;
}

function funcTest(){
	var test = document.getElementById("tblGenerate");
	alert(test);
}
function funcGeneRefresh(){
	//document.getElementById("submit").style.visibility ="visible";
	//alert("display");
	//alert("refresh");
	var myTableDiv = document.getElementById("myDynamicTable");
    //document.getElementById("myDynamicTable").innerHTML = "";
	
	/*var table = document.createElement("TABLE");
	table.id = "tblGenerate2";
	table.className = "tblGenerate";*/
	
	var table = document.getElementById("tblGenerate"); //tbGen+i
	var tbl_rows = table.rows.length;
	//alert(tbl_rows);
	table.innerHTML = "";
	
	//alert("here");
    
    //var tableBody = document.createElement('TBODY');
   // table.appendChild(tableBody);

	var countRow = document.getElementById("countColor").value; //retrieve the value from the hidden text (colorsize.php)
	
	var countSize = document.getElementById("existcountSizeName").value;
	document.getElementById("countSizeName").value = countSize;
	
	//================================================================//
	//=============== First Row (Label Size Name) ====================//
	//================================================================//
	var tr = document.createElement("TR");
	tr.id = "default";
	table.appendChild(tr);
	
	var td = document.createElement("TH");
	td.innerHTML = "";
	td.className = "td_short";
	tr.appendChild(td);
	
	var td = document.createElement("TH");
	td.innerHTML = "Color / Size Name";
	td.className = "td_short2";
	tr.appendChild(td);
	
	//alert(""+countSize);
	
	for(var i=1;i<=parseInt(countSize);i++){
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
	}//----end for loop----//
	
	var td = document.createElement("TH");
	td.innerHTML = "Total";
	tr.appendChild(td);
	
	//===============End First Row (Label Size Name)====================//
	//==================================================================//
	document.getElementById("countGeneColor").value = countRow;
	//alert(countRow);
    for (var i=1; i<=parseInt(countRow); i++){
		var check = document.getElementById("selectColor-"+i);
		//alert("-------"+check);
		if(check!=null){//check element exist
		
		var colorID = document.getElementById("selectColorID-"+i).value;
		//var checkArray = generate.indexOf(colorID);
		//alert("-------"+colorID);
			var tr = document.createElement("TR");
			tr.id = "tbGen"+i;
			
			table.appendChild(tr);
			
			var td = document.createElement('TD');
			/*var btn = document.createElement("BUTTON");
			var value = document.createTextNode("Delete");
			btn.setAttribute("id", "btn_id");
			btn.setAttribute("class", "deletebtn_class");
			btn.setAttribute("width", "250px");
			btn.setAttribute("onclick","dele("+i+")");
			btn.appendChild(value);
			td.appendChild(btn);*/
			td.innerHTML = "<input type='button' value='Delete' onclick='dele(&#39;"+i+"&#39;)' />"+
							"<input type='hidden' value='"+colorID+"' name='selectColor-"+i+"' id='selectColorGenID-"+i+"' />";
			tr.appendChild(td);
			//alert("check here");
			var td = document.createElement('TD');
			//var strUser = s.options[s.selectedIndex].text;
			
			var s = document.getElementById("selectColor-"+i).innerHTML;
			//alert(s);
			
			//var strUser = s.options[s.selectedIndex].text;
			//alert("--"+strUser);
			td.appendChild(document.createTextNode(s));
			tr.appendChild(td);	
			
			 //----status exist----//
			
			for(var j=0;j<parseInt(countSize);j++){
				   var td = document.createElement('TD');
				   //td.width='75';
				   td.className = "td_sizeQty";
				   var text = document.createElement("input");
				   text.type = "number";
				   text.readOnly = true;
				   text.setAttribute("id", "text_id"+i+"-"+j);
				   text.setAttribute("name", "text_id"+i+"-"+j);
				   text.setAttribute("class", "txt_qty");
				   //text.value = i+""+j;
				   
				   text.setAttribute("min", "0");
				   //text.setAttribute("max", "10000");
				   text.setAttribute("onkeyup", "total("+i+","+countSize+")");
				   //text.setAttribute("onchange", "total("+i+","+countCol+")");
				   text.setAttribute("onclick", "total("+i+","+countSize+")");
				  // text.setAttribute("value", i+""+j);
				   
				   td.appendChild(text);
				   tr.appendChild(td);
			   
			}  // end for loop (td)
			
			
		
		
	   
	   var td = document.createElement('TD');
       td.width='75';
	   
       var totalText = document.createElement("label");
		   //totalText.type = "text";
		   totalText.setAttribute("id", "totalText_id"+i);
		   totalText.setAttribute("class", "totalText");   
	   td.appendChild(totalText);
	   tr.appendChild(td);
	   
	   }//--end check element exist
	   
    }// end for loop (tr)//*/
	
	
	//-----------------------------------//
	table.appendChild(tr);
	//myTableDiv.appendChild(tableBody);
	
	document.getElementById("tempStatus").value = "exist";
}

function funcGeneAddRow(){
	var checkColor = document.getElementById("colorTable").rows.length;
	//alert(checkColor);
	
	if(checkColor>1){
	
	//document.getElementById("submit").style.visibility ="visible";
	var cc = document.getElementById("countGeneColor").value;
	//alert(cc);
	for(var i=1;i<=parseInt(cc);i++){
		var validate = document.getElementById("selectColorGenID-"+i);
		//alert(validate);
		if(validate!=null){
			var currentColor = document.getElementById("selectColorGenID-"+i).value;
			//alert(currentColor);
			var check = colorArray.indexOf(currentColor);
			if(check>-1){
				colorArray.remove(currentColor);
			}
		}
	}
	//alert(colorArray.length);
	//alert("test");
	
	var table = document.getElementById("tblGenerate");
	//alert(table);
	for(var i=0;i<colorArray.length;i++){
		var ccID = parseInt(document.getElementById("countGeneColor").value) + 1;
		
		var cellPos = 0;
		//alert(colorArray[i]);
		var rowCount = table.rows.length;
		//alert("sagshag "+rowCount);
		var rowSizeQty = table.insertRow(rowCount);
		rowSizeQty.id = "tbGen"+ccID;
		
		var cell = rowSizeQty.insertCell(cellPos);
			//cell.innerHTML = "<input type='button' value='Delete' onclick='dele(&#39;"+ccID+"&#39;)' />";
			//modified by Lock (2018/05/30)
			cell.innerHTML = "<input type='button' value='Delete' onclick='dele(&#39;"+ccID+"&#39;)' /><br/><br/>"+
							"<input type='file' name='attach"+ccID+"' accept='image/gif, image/jpeg, image/png' />";
		
		cellPos++;
			var cell = rowSizeQty.insertCell(cellPos);
			cell.innerHTML = "<input type='hidden' value='"+colorArray[i]+"' name='selectColor-"+ccID+"' id='selectColorGenID-"+ccID+"' />"+
								"<input type='hidden' name='inShipment"+ccID+"' id='inShipment"+ccID+"' value='0'>"+
								"<input type='hidden' name='oldcolor"+ccID+"' id='oldcolor"+ccID+"' value='0' />"+
								"<input type='hidden' id='selectColorC-"+ccID+"' value='"+colorNameArray[colorArray[i]]+"' />"+
								colorNameArray[colorArray[i]]; //--- modify by ckwai on 2017-07-05 ---//
		
		var countSizeName = document.getElementById("countSizeName").value;
		//alert("ssasasa---"+countSizeName);
		for(var s=0;s<countSizeName;s++){
			cellPos++;
			var cell = rowSizeQty.insertCell(cellPos);
			cell.className = "td_sizeQty";
			//modified by Lock (2018/05/25)
			//apend new row
			//cell.innerHTML = "<input type='number' name='text_id"+ccID+"-"+s+"' min='0'  id='text_id"+ccID+"-"+s+"' class='txt_qty' onkeyup='total(&#39;"+ccID+"&#39; , &#39;"+countSizeName+"&#39;)' onclick='total(&#39;"+ccID+"&#39; , &#39;"+countSizeName+"&#39;)' readonly />";
			cell.innerHTML = "<div class='col-md-12'><input type='number' name='text_id"+ccID+"-"+s+"' min='0'  id='text_id"+ccID+"-"+s+"' class='txt_qty' onkeyup='total(&#39;"+ccID+"&#39; , &#39;"+countSizeName+"&#39;)' onclick='total(&#39;"+ccID+"&#39; , &#39;"+countSizeName+"&#39;)' readonly /></div> <div class='col-md-12'><input type='number' step='any' name='price_id"+ccID+"-"+s+"' id='price_id"+ccID+"-"+s+"' min='0' class='txt_price' placeholder='Price'/></div>";
			
		}//
		
		cellPos++;
		var cell = rowSizeQty.insertCell(cellPos);
		cell.innerHTML = "<label id='totalText_id"+ccID+"'></label>";
		
		document.getElementById("countGeneColor").value = ccID;
		
	}
	//alert(rowCount);
	}else{
		alert("Please select color");
	}
}

function funcAddNew(){
	var checkSize = document.getElementById("selectSize").value;
	var checkColor = document.getElementById("colorTable").rows.length;
	//alert(checkSize+" "+checkColor);
	
	if(checkSize>0 && checkColor>1){
	//var fromSize = document.getElementById("fromSelect").value;
	//var toSize = parseInt(document.getElementById("toSelect").value) + 1;  //because the position start from 0.
	
	////alert(fromSize+" "+toSize);
	////alert(myArray.length);
	//var citrus = myArray.slice(fromSize, toSize); //splice can use variable php implode
	////alert(citrus+" "+citrus.length);
	//addTable(citrus, citrus.length);
	
	}else{
		alert("Please select size name and color");
	}
}

function addTable(array, arrayLength) {
	var arr_btn = new Array();
	var arr_PO_chk = new Array();
	var table_old = document.getElementById("tblGenerate"); //tbGen+i
	
	if(table_old!=null){
		var tbl_rows = table_old.rows.length;
		for(var i=1;i<table_old.rows.length;i++){
			var ele = table_old.rows[i].cells[0];
			if(ele != null){
				var str = table_old.rows[i].cells[0].innerHTML;
				if(str.indexOf("button")==-1){
					arr_btn.push(str);
				}else{
					arr_btn.push("btn");
				}
				
				//-------- Make a record if color has been used in A/MPO --------//
				if(str.indexOf("PO")==-1){
					arr_PO_chk.push("0");
				}else{
					arr_PO_chk.push("1");
				}
			}
		}
	}
	
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
    table.border = "0";
	table.id = "tblGenerate";
	table.className = "tblGenerate";
	table.cellSpacing="0";
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
	td.className = "td_short";
	tr.appendChild(td);
	
	var td = document.createElement("TH");
	td.innerHTML = "Color / Size Name";
	td.className = "td_short2";
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
	td.innerHTML = "Total";
	tr.appendChild(td);
	
	//===============End First Row (Label Size Name)====================//
	//==================================================================//
	
	//alert(countRow);

	document.getElementById("countGeneColor").value = countRow;
	//alert("here");
	
	//new field, get selected color (8-1-2016)
	var oldcolorarr = document.getElementById("oldcolorarr").value;
	
	var arr_num = 0;
	
    for (var i=1; i<=parseInt(countRow); i++){
		arr_num = i-1;
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
			//alert(arr_btn[arr_num]);
			if(arr_btn[arr_num]=="btn" || arr_btn[arr_num]==undefined){						
				//if(oldcolorarr.indexOf(colorID+",") == -1){
				var btn = document.createElement("BUTTON");
				var value = document.createTextNode("Delete");
				btn.setAttribute("id", "btn_id");
				btn.setAttribute("class", "deletebtn_class");
				btn.setAttribute("width", "250px");
				btn.setAttribute("onclick","dele("+i+")");
				btn.appendChild(value);
				td.appendChild(btn);
			}else{
				td.innerHTML = ""+arr_btn[arr_num];
			}
			
			tr.appendChild(td);
			
			var td = document.createElement('TD');
			////var strUser = s.options[s.selectedIndex].text;
			
			var s = document.getElementById("selectColor-"+i).innerHTML;
			var color_n = document.getElementById("selectColor-"+i).innerHTML;
			s = s+"<input type='hidden' id='selectColorC-"+i+"' value='"+s+"' />"; //--- Modify by ckwai on 2017-07-05 ---//
			var buyerCode = document.getElementById("buyerCode-"+i).value;
			var colorID = document.getElementById("selectColorID-"+i).value;
			//var s = document.getElementById("color_selected"+i).innerHTML;
			if(arr_PO_chk[arr_num]=="0"){
				s = "<span id='color_selected"+i+"'><input type='text' id='selectColorC-"+i+"'  "+
													"onfocus='funcAutoComplete(this.id, &#39;color&#39;)' onkeyup='funcChangeColor(&#39;"+i+"&#39;)' "+
													"onchange='funcChangeColor(&#39;"+i+"&#39;)' value='"+color_n+"' placeholder='Color...' />"+
													"<input type='text' name='this_pcode"+i+"' id='this_pcode"+i+"' placeholder='Buyer Code...' value='"+buyerCode+"' "+
													"style='width:80px' onkeyup='funcChangePCode(&#39;"+i+"&#39;)' />"+
													"<input type='hidden' name='oldcolor"+i+"' id='oldcolor"+i+"' value='"+colorID+"' />"+
													"<input type='hidden' name='inShipment"+i+"' id='inShipment"+i+"' value='1' />"+
													"</span>";
			}
			
			//alert(s);
			
			//var strUser = s.options[s.selectedIndex].text;
			//alert("--"+strUser);
			//td.appendChild(document.createTextNode(s));
			td.innerHTML = s;
			tr.appendChild(td);	
			
			if(statusSize=="change" || statusSize=="new") {
				
				for (var j=0; j<countCol; j++){
				   var td = document.createElement('TD');
				   //td.width='75';
				   td.className = "td_sizeQty";
				   var text = document.createElement("input");
				   text.type = "number";
				   text.setAttribute("id", "text_id"+i+"-"+j);
				   text.readOnly = true;
				   text.setAttribute("name", "text_id"+i+"-"+j);
				   text.setAttribute("class", "txt_qty");
				   //text.value = i+""+j;
				   
				   /*//count each row for create class name 
				   for (var c=0; c<countRow; c++){
						text.setAttribute("class", "amountRow"+c);
				   }//*/
				   
				   text.setAttribute("min", "0");
				   //text.setAttribute("max", "10000");
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
				   //td.width='75';
				   td.className = "td_sizeQty";
				   var text = document.createElement("input");
				   text.type = "number";
				   text.readOnly = true;
				   text.setAttribute("id", "text_id"+i+"-"+j);
				   text.setAttribute("name", "text_id"+i+"-"+j);
				   //text.value = i+""+j;
				   
				   text.setAttribute("min", "0");
				   //text.setAttribute("max", "10000");
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

function funcFinalCheck(){
	var countCol = document.getElementById("countColor").value;
	var countGeneColor = document.getElementById("countGeneColor").value;
	var arrCol = new Array();
	var error = 0;
	
	for(var i=1;i<=parseInt(countCol);i++){
		var colNameEle = document.getElementById("selectColorC-"+i);
		if(colNameEle!=null){
			var colorName = document.getElementById("selectColorC-"+i).value;
			var chk = arrCol.indexOf(colorName.trim());
			//alert(colorName.trim()+"<== "+chk);
			if(parseInt(chk)==-1){
				arrCol.push(colorName.trim());
			}else{
				error++;
			}
		}
	}//---- End For Loop ----//
	
	var status = document.getElementById("sizeStatus").value;
	
	if(error>0){
		alert("Cannot contains duplicate color in same styling");
		return false;
	}
	
	if(parseInt(countGeneColor)==0){
		alert("Kindly at least add one color before save");
		return false;
	}
	
	if(status=="change"){
		//alert("change");
		var check = confirm("Since you change the size name, you are required to re-insert other garment info again. \nAre you sure to change size name?");
		
		if(check==true){
			return true;
			//return false;
		}else{
			return false;
		}
	}
	return true;
	//return false;
}

function funcChangeColor(id){
	var color = document.getElementById("selectColorC-"+id).value;
	
	document.getElementById("selectColor-"+id).innerHTML = color;
	//document.getElementById("selectColorID-"+id).value = "";
	document.getElementById("selectColorName-"+id).value = color;
}

function funcChangePCode(id){
	var pcode = document.getElementById("this_pcode"+id).value;
	document.getElementById("buyerCode-"+id).value = pcode;
}
function funcChangePCode2(id){
	var pcode = document.getElementById("buyerCode-"+id).value;
	document.getElementById("this_pcode"+id).value = pcode;
}
function funcChangeBOMStr(id){
	var str_bom = document.getElementById("this_str_bom"+id).value;
	document.getElementById("str_bom-"+id).value = str_bom;
}
function funcChangeBOMStr2(id){
	var str_bom = document.getElementById("str_bom-"+id).value;
	document.getElementById("this_str_bom"+id).value = str_bom;
}