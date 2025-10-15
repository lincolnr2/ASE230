<?php
declare(strict_types=1);
require_once 'handlers.php';
require_once 'auth.php';

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
    'POST /products' => 'create new products',
    'PUT /products' => 'Update a product',
    'DELETE /products' => 'Delete a product, Token req',

    'GET /managers' => 'view all managers',
    'POST /managers' => 'create new managers, Token req',
    'PUT /managers' => 'update a manager, Token req',
    'DELETE /managers' => 'delete a manager, Token req',

    'GET /sales' => 'view all sales',
    'POST /sales' => 'create new sale',
    'delete /sales' => 'delete a sale'


  ], JSON_PRETTY_PRINT
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
$token = $jsondata['token'] ?? null;
//Outer switch determines which resource is being accessed, inner switch determines which method the resource is using
switch($resource){
  case 'login':
    $uname = $jsondata['uname'] ?? null;
    $upass = $jsondata['upass'] ?? null;
    login($pdo, $uname, $upass);
    break;

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
      if($id != null){
      $name = $jsondata['name'] ?? null;
      $price = $jsondata['price'] ?? null;
      $stock = $jsondata['stock'] ?? null;
      update_product($pdo, $id, $name, $price, $stock);}
      else{
        http_response_code(400);
        echo  json_encode(["product ID required"],JSON_PRETTY_PRINT);
        exit();
      }
      break;

    case 'DELETE':                                       //DELETE products
      if($id != null && $token != null){delete_product($pdo, $id, $token);}
      else if($token != null){
        http_response_code(400);
        echo json_encode(["product ID required"],JSON_PRETTY_PRINT);
        exit();
      }
      else{
        http_response_code(401);
        echo json_encode(["No bearer token provided"],JSON_PRETTY_PRINT);
        exit();
      }
      break;

    default:                                              //Default, no method selected
      http_response_code(400);
      echo json_encode(["Method not allowed"],JSON_PRETTY_PRINT);
      break;
  }break;


  case 'managers':
    switch($method){
      case 'GET':
        if($id != null){get_manager($pdo, $id);}
        else{get_all_managers($pdo);}
        break;

      case 'POST':
        if($token != null){
          $name = $jsondata['name'] ?? null;
          $email = $jsondata['email'] ?? null;
          $permissions = $jsondata['permissions'] ?? null;
          post_manager($pdo, $token, $name, $email, $permissions);
        }
        else{
          http_response_code(401);
          echo json_encode(["No bearer token provided"],JSON_PRETTY_PRINT);
          exit();
        }
        break;

        case 'PUT':
          if($token != null){
            $name = $jsondata['name'] ?? null;
            $email = $jsondata['email'] ?? null;
            $permissions = $jsondata['permissions'] ?? null;
            put_manager($pdo, $token, $id, $name, $email, $permissions);
          }
          else{
            http_response_code(401);
            echo json_encode(["No bearer token provided"],JSON_PRETTY_PRINT);
            exit();
          }
          break;

        case 'DELETE':
          if($token != null){
            delete_manager($pdo, $token, $id);
          }
          else{
            http_response_code(401);
            echo json_encode(["No bearer token provided"],JSON_PRETTY_PRINT);
          }
          break;

        default:
        http_response_code(400);
        echo json_encode(["Method not allowed"],JSON_PRETTY_PRINT);
        exit();
    }break;


  case 'sales':
    switch($method){
      
      case 'GET':
        if($id != null){get_sale($pdo, $id);}
        else{get_all_sales($pdo);}
        break;

      case 'POST':
        $startdate = $jsondata['startdate'] ?? null;
        $enddate = $jsondata['enddate'] ?? null;
        $discount = $jsondata['discount'] ?? null;
        post_sale($pdo, $startdate, $enddate, $discount);
        break;

      case 'DELETE':
        delete_sale($pdo, $id);
        break;

        default:
          http_response_code(400);
          echo json_encode(["Method not allowed"],JSON_PRETTY_PRINT);
        exit();
    }break;

  default:
    http_response_code(404);
    echo json_encode(['error' => 'Resource not found']);
    exit();
}








?>
