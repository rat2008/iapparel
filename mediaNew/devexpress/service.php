<?php
/*
    more info about PHP datasource can view this link:
    https://github.com/DevExpress/DevExtreme-PHP-Data
*/
include("../lock.php");
//$conn = new PDO('mysql:host=localhost;dbname=apparelezi_uat2', 'apparele_superb', '31sEtIAtrOpIkA02', array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
//$conn = new PDO('mysql:host=localhost;dbname=easyui2', 'root', '', array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));

function GetParseParams($params, $assoc = false) {
    $result = NULL;
    if (is_array($params)) {
        $result = array();
        foreach ($params as $key => $value) {
            $result[$key] = json_decode($params[$key], $assoc);
            if ($result[$key] === NULL) {
                $result[$key] = $params[$key];
            }
        }
    } 
    else {
        $result = $params;
    }
    return $result;
}
function GetParamsFromInput() {
    $result = NULL;
    $content = file_get_contents("php://input");
    if ($content !== false) {
        $params = array();
        parse_str($content, $params);
        $result = GetParseParams($params, true); 
    } 
    return $result;
}


/* CRUD determine by "mode":
0: read
1: insert
2: delete
3: update
actually can do by using GET, POST, PUT, DELETE, but will facing CORS issue in chrome, so change to using GET "mode"
*/
switch($_GET["mode"]) {   
    case "1":
        $OrderNumber = $_POST["OrderNumber"];
        $OrderDate = $_POST["OrderDate"];
        $SaleAmount = $_POST["SaleAmount"];
        $TotalAmount = $_POST["TotalAmount"];
        $Employee = $_POST["Employee"];
        $CustomerStoreCity = $_POST["CustomerStoreCity"];
        $CustomerStoreState = $_POST["CustomerStoreState"];

        $query = $conn->prepare("INSERT INTO demo (OrderNumber, OrderDate, SaleAmount, TotalAmount, Employee, CustomerStoreCity, CustomerStoreState) VALUES ('$OrderNumber', '$OrderDate', '$SaleAmount', '$TotalAmount', '$Employee', '$CustomerStoreCity', '$CustomerStoreState')");
        $query->execute();
    
        $response = "ok";
    break;
    
    case "2":
        $ID = $_GET["ID"];

        $query = $conn->prepare("DELETE FROM demo WHERE ID = '$ID' LIMIT 1");
        $query->execute();
    
        $response = "ok";
    break;
    
    case "3": 
        $params = GetParamsFromInput();

        $filter = array();
        foreach($params AS $key=>$value){
            $words = "$key = $value";
            array_push($filter, $words);
        }

        $filter = implode(",", $filter);

        $ID = $_GET["ID"];

        $query = $conn->prepare("UPDATE demo SET $filter WHERE ID = '$ID' LIMIT 1");
        $query->execute();
        
        $response = "ok";
    break;
    
    default:
        $query = $conn->prepare("SELECT * FROM demo LIMIT 10000");
        $query->execute();
        $arr = array();
        while($row = $query->fetch(PDO::FETCH_ASSOC)){
            $ID = $row["ID"];
            $OrderNumber = $row["OrderNumber"];
            $OrderDate = $row["OrderDate"];
            $SaleAmount = $row["SaleAmount"];
            $Terms = $row["Terms"];
            $TotalAmount = $row["TotalAmount"];
            $CustomerStoreState = $row["CustomerStoreState"];
            $CustomerStoreCity = $row["CustomerStoreCity"];
            $Employee = $row["Employee"];
            
            /* if columns setting in index.js is removed, the key will become column header name. Word spacing can be determined by using capital letters
            eg: OrderNumber = Order Number
            */
            $mini_arr = array('ID'=>$ID, 'OrderNumber'=>"$OrderNumber", 'OrderDate'=>"$OrderDate", 'SaleAmount'=>$SaleAmount, 'Terms'=>"$Terms", 'TotalAmount'=>$TotalAmount, 'CustomerStoreState'=>"$CustomerStoreState", 'CustomerStoreCity'=>"$CustomerStoreCity", 'Employee'=>"$Employee");
            array_push($arr, $mini_arr);
        }

        $response = $arr;
    break;
}

// using JSON_NUMERIC_CHECK to avoid json passing numeric value as string
// if (isset($response) && !is_string($response)) {
//    header("Content-type: application/json");    
    echo json_encode($response, JSON_NUMERIC_CHECK);
// }
// else {
    // header("HTTP/1.1 500 Internal Server Error");
    // header("Content-Type: application/json");
    // echo json_encode(array("message" => $response, "code" => 500));
// }
