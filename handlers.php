<?php
declare(strict_types=1);
require_once 'products.php';



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
  }
  else{
    $data = ["Unable to find product"];
  }
  echo json_encode($data, JSON_PRETTY_PRINT);
  http_response_code(200);
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
  http_response_code(200);
 }
 else{
  echo json_encode(['message' => 'unable to add product']);
 }
}

//Changes a products info based on its ID
function update_product($pdo, $id){
  return;
}

//Deletes a product based on its ID
function delete_product($pdo, $id){
  return;
}















?>
