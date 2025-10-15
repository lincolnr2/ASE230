<?php
declare(strict_types=1);
require_once 'products.php';
require_once 'auth.php';



//Returns all products
function get_all_products($pdo){
  try{
    $sql = "SELECT * FROM products";
    $rows = $pdo->query($sql)->fetchAll();

   if($rows && count($rows) >0){
    $data = ["Found " . count($rows) . " products", ['ID','Name', 'Price','Stock']];
    foreach($rows as $row){
      $data[] = ["{$row['id']}", "{$row['name']}", "{$row['price']}", "{$row['stock']}"];
    }
    echo json_encode($data, JSON_PRETTY_PRINT);
    http_response_code(200);
   }
   else{
    $data = ["No Products"];
    http_response_code(404);
  }
  }
  catch(PDOException $e){
  }

}

//Returns single product based on ID
function get_product($pdo, $id){
  try{
  $sql = "SELECT * from products WHERE id = :id";
  $stmt = $pdo->prepare($sql);
  $success = $stmt->execute([':id' => $id]);
  $row = $stmt->fetch();
  if($row != null){
  $data = ["Found ", ['ID','Name', 'Price','Stock']];
  $data[] = ["{$row['id']}", "{$row['name']}", "{$row['price']}", "{$row['stock']}"];
  http_response_code(200);
  }
  else{
   echo json_encode(["Unable to find product"]);
    http_response_code(404);
  }
  echo json_encode($data, JSON_PRETTY_PRINT);
}
  
  catch(PDOException $e){
  }
}

//Creates a product with specified data points, All constraints are not null and ID is PK auto increment
function create_product($pdo, $name, $price, $stock){
 $sql = "INSERT INTO products (name, price, stock) VALUES (:name, :price, :stock)";
 $stmt = $pdo->prepare($sql);

 $success = $stmt->execute([':name' => $name, ':price' => $price, ':stock' => $stock]);
 if($success){
  echo json_encode(['message' => 'Added successfully']);
  http_response_code(201);
 }
 else{
  echo json_encode(['message' => 'unable to add product']);
  http_response_code(400);
 }
}

//Changes a products info based on its ID
function update_product($pdo, $id, $name, $price, $stock){
  $sql = "SELECT * from products WHERE id = :id";
  $stmt = $pdo->prepare($sql);
  $success = $stmt->execute([':id' => $id]);
  $row = $stmt->fetch();
  if($stmt->rowCount() < 1 ){
      echo json_encode(['message' => 'Failed to find item']);
      http_response_code(400);
  }
  else{
    $name = (isset($name) && $name != '') ? $name : $row['name'];
    $price = (isset($price) && $price != '') ? $price : $row['price'];
    $stock = (isset($stock) && $stock != '') ? $stock : $row['stock'];
  
  $sql = "UPDATE products set name = :name, price = :price, stock = :stock where id = :id";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([':name' => $name, ':price' => $price, ':stock' => $stock, ':id' => $id]);
  if($stmt->rowCount() > 0){
    echo json_encode(['message' => 'Update successful']);
    http_response_code(200);
  }
  else{
    echo json_encode(['message' => 'update failed'], JSON_PRETTY_PRINT);
        http_response_code(400);
  }}}

//Deletes a product based on its ID
function delete_product($pdo, $id, $token){
  if(checkToken($pdo, $token)){
    $sql = "DELETE from products where id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
    if($stmt->rowCount() >0){
        echo json_encode(['message' => 'Deleted Item']);
        http_response_code(200);
    }
    else{
      echo json_encode(['message' => 'Failed to delete Item'], JSON_PRETTY_PRINT);
        http_response_code(400);
    }
  }
  else{
    echo json_encode(['message' => 'Token Invalid'], JSON_PRETTY_PRINT);
        http_response_code(401);
  }
}


function get_all_managers($pdo){
    $sql = "SELECT * FROM managers";
    $rows = $pdo->query($sql)->fetchAll();
   if($rows && count($rows) >0){
    $data = ["Found " . count($rows) . " managers", ['ID','Name', 'email','permissions']];
    foreach($rows as $row){
      $data[] = ["{$row['empid']}", "{$row['name']}", "{$row['email']}", "{$row['permissions']}"];
    }
    echo json_encode($data, JSON_PRETTY_PRINT);
    http_response_code(200);
   }
   else{
    echo json_encode(['message' => 'Unable to find managers']);
    http_response_code(404);
  }
}


function get_manager($pdo, $id){

  $sql = "SELECT * from managers WHERE empid = :empid";
  $stmt = $pdo->prepare($sql);
  $success = $stmt->execute([':empid' => $id]);
  $row = $stmt->fetch();
  if($row != null){
  $data = ["Found ", ['ID','Name', 'Email','Permissions']];
  $data[] = ["{$row['empid']}", "{$row['name']}", "{$row['email']}", "{$row['permissions']}"];
  echo json_encode($data, JSON_PRETTY_PRINT);
  http_response_code(200);
  }
  else{
   echo json_encode(['message' => 'Unable to find manager']);
    http_response_code(404);
  }
}

function post_manager($pdo, $token, $name, $email, $permissions){
  if(checkToken($pdo, $token)){
    $sql = "INSERT INTO managers (name, email, permissions) VALUES (:name, :email, :permissions)";
    $stmt = $pdo->prepare($sql);
    $success = $stmt->execute([':name' => $name, ':email' => $email, ':permissions' => $permissions]);
    if($success){
     echo json_encode(['message' => 'Added successfully']);
     http_response_code(201);
    }
    else{
     echo json_encode(['message' => 'unable to add product']);
     http_response_code(400);
   }
  }
  else{
    echo json_encode(['message' => 'Token Invalid'], JSON_PRETTY_PRINT);
        http_response_code(401);
  }
  
}

function put_manager($pdo, $token, $id, $name, $email, $permissions){
  if(checkToken($pdo, $token)){
    $sql = "SELECT * from managers WHERE empid = :id";
    $stmt = $pdo->prepare($sql);
    $success = $stmt->execute([':id' => $id]);
    $row = $stmt->fetch();
    if($stmt->rowCount() < 1 ){
        echo json_encode(['message' => 'Failed to find manager']);
        http_response_code(400);
    }
    else{
      $name = (isset($name) && $name != '') ? $name : $row['name'];
      $email = (isset($email) && $email != '') ? $email : $row['email'];
      $permissions = (isset($permissions) && $permissions != '') ? $permissions : $row['permissions'];
    
    $sql = "UPDATE managers set name = :name, email = :email, permissions = :permissions where empid = :empid";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':name' => $name, ':email' => $email, ':permissions' => $permissions, ':empid' => $id]);
    if($stmt->rowCount() > 0){
      echo json_encode(['message' => 'Update successful']);
      http_response_code(200);
    }
    else{
      echo json_encode(['message' => 'update failed'], JSON_PRETTY_PRINT);
          http_response_code(400);
    }
  }
}}

function delete_manager($pdo, $token, $id){
    if(checkToken($pdo, $token)){
      $sql = "DELETE from managers where id = :id";
      $stmt = $pdo->prepare($sql);
      $stmt->execute([':id' => $id]);
      if($stmt->rowCount() >0){
          echo json_encode(['message' => 'Deleted manager']);
          http_response_code(200);
      }
      else{
        echo json_encode(['message' => 'Failed to delete manager'], JSON_PRETTY_PRINT);
          http_response_code(400);
      }
    }
    else{
      echo json_encode(['message' => 'Token Invalid'], JSON_PRETTY_PRINT);
          http_response_code(401);
    }
}



function get_all_sales($pdo){

    $sql = "SELECT * FROM sales";
    $rows = $pdo->query($sql)->fetchAll();
   if($rows && count($rows) >0){
    $data = ["Found " . count($rows) . " sales", ['ID','Start Date', 'End Date','Discount']];
    foreach($rows as $row){
      $data[] = ["{$row['saleid']}", "{$row['startdate']}", "{$row['enddate']}", "{$row['discount']}"];
    }
    echo json_encode($data, JSON_PRETTY_PRINT);
    http_response_code(200);
   }
}

function get_sale($pdo, $id){

  $sql = "SELECT * from sales WHERE saleid = :saleid";
  $stmt = $pdo->prepare($sql);
  $success = $stmt->execute([':saleid' => $id]);
  $row = $stmt->fetch();
  if($row != null){
  $data = ["Found ", ['ID','Start date', 'End date','discout']];
  $data[] = ["{$row['saleid']}", "{$row['startdate']}", "{$row['enddate']}", "{$row['discount']}"];
  echo json_encode($data, JSON_PRETTY_PRINT);
  http_response_code(200);
  }
  else{
   echo json_encode(['message' => 'Unable to find sale']);
    http_response_code(404);
  }
}

function post_sale($pdo, $startdate, $enddate, $discount){
  $sql = "INSERT INTO sales (startdate, enddate, discount) VALUES (:startdate, :enddate, :discount)";
 $stmt = $pdo->prepare($sql);
 $success = $stmt->execute([':startdate' => date('Y-m-d H:i:s', strtotime($startdate)), ':enddate' => date('Y-m-d H:i:s', strtotime($enddate)), ':discount' => $discount]);
 if($success){
  echo json_encode(['message' => 'Added successfully']);
  http_response_code(201);
 }
 else{
  echo json_encode(['message' => 'unable to add sale']);
  http_response_code(400);
 }
}

function delete_sale($pdo){
  return;
}













?>
