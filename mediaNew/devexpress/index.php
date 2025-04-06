<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>DevExtreme Demo</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />

    
    <script type="text/javascript" src="js/jquery.min.js"></script>
 
    <!-- Reference either Knockout or AngularJS, if you do -->
    <script type="text/javascript" src="js/knockout-latest.js"></script>
    <script type="text/javascript" src="js/angular.min.js"></script>
     
    <!-- DevExtreme themes -->
    <link rel="stylesheet" href="css/dx.common.css">
    <link rel="stylesheet" href="css/dx.light.css">
    
    <!-- A CDN link -->
    <!-- or a local script -->
    <script type="text/javascript" src="js/jszip.min.js"></script>
    
    <!-- DevExtreme library -->
    <script type="text/javascript" src="js/dx.all.js"></script>
    <!-- <script type="text/javascript" src="js/dx.web.js"></script> -->
    <!-- <script type="text/javascript" src="js/dx.viz.js"></script> -->
    <!-- <script type="text/javascript" src="js/dx.viz-web.js"></script> -->
        
    
    
    <!--<script src="data.js"></script>
    <link rel="stylesheet" type="text/css" href="styles.css" />-->
    
    <!-- main script to generate table -->
    <script src="index.js"></script>
</head>
<body class="dx-viewport">
	<!-- custom hide column -->
    <select id="customColumn">
        <option value="0">Hide Term Only</option>
        <option value="1">Hide Inv Number, Order Date Only</option>
        <option value="2">Hide Employee Only</option>
    </select>

    <!-- alert column name in table -->
    <button type="button" id="buttonColumn">Alert Table Name</button>

    <div class="demo-container">
        <div id="gridContainer"></div>
        
        <!-- for trigger group toggle on bottom of page -->
        <div class="options">
            <div class="caption">Options</div>
            <div class="option">            
                <div id="autoExpand"></div>
            </div>    
        </div>
    </div>
</body>
</html>