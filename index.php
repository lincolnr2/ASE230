<?php
declare(strict_types=1);
require_once 'handlers.php';


//Headers
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Origin: *');
error_reporting(E_ALL);
ini_set('display_errors', 1);

//CORS Preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  http_response_code(200);
  exit();
}

//Find method and URI, 
$method = $_SERVER['REQUEST_METHOD'];                       //GET, POST, PUT, DELETE
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);   //  /[products, managers, sales]/id
$path = trim($path, '/');                                   //  [products, managers, sales]/id

//Find request information
$segments = explode('/', $path);                            //  Turns URI into an array, splits at /
$resource = $segments[0] ?? null;                           //First segment of URI is resource, ex:products
$id = $segments[1] ?? null;                                 //Second segment is ID
 

//If resource is empty, return possible endpoints of the API
if (empty($resource)){
echo json_encode([
  'message' => 'Inventory management api',
  'endpoints' => [
    'GET /products' => 'get all products',
    'GET /products/[id]' => 'Get product by ID',
    'POST /products' => 'create new products'

  ]
]);
exit;
}

//Establish SQL connection
$servername = "localhost";
$username = "root";
$password = "admin";
$dbname = "inventorydb";
try {
    $dsn = "mysql:host={$servername};dbname={$dbname};charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // throw exceptions
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,                  // use native prepares
    ];
    $pdo = new PDO($dsn, $username, $password, $options);

} catch (PDOException $e) {
    exit("Connection failed: " . htmlspecialchars($e->getMessage()));
}

$jsondata = json_decode(file_get_contents('php://input'), true);

//Outer switch determines which resource is being accessed, inner switch determines which method the resource is using
switch($resource){
  case 'products':
  switch($method){          
    case 'GET':                                         //GET products and GET products/id
      if($id != null) {get_product($pdo, $id);}
      else{get_all_products($pdo);}
      break;
    case 'POST':                                        //POST products
      $name = $jsondata['name'] ?? null;
      $price = $jsondata['price'] ?? null;
      $stock = $jsondata['stock'] ?? null;
      create_product($pdo, $name, $price, $stock);
      break;
    case 'PUT':                                         //PUT products (not implemented)
      if($id){update_product($pdo, $id);}
      else{
        http_response_code(400);
        echo  json_encode(["product ID required"]
        ,JSON_PRETTY_PRINT);
      }
      break;
    case 'DELETE':                                       //DELETE products
      if($id){delete_product($pdo, $id);}
      else{
        http_response_code(400);
        echo json_encode(["product ID required"]
        ,JSON_PRETTY_PRINT);
      }
      break;
    default:                                              //Default, no method selected
      http_response_code(400);
      echo json_encode(["Method not allowed"],JSON_PRETTY_PRINT);
      break;
  }
  break;
  case 'managers':
    break;
  case 'sales':
    break;
  default:
    http_response_code(404);
    echo json_encode(['error' => 'Resource not found']);
    exit();
}








?>
