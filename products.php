<?php
declare(strict_types=1);
class Product{
  private $id; 
  private $name;
  private $price;
  private $stock;

  public function getID(){return $this->id;}
  public function setID($id) {$this->id = $id;}

  public function __construct(){
    $this->created_at = date('Y-m-d H:i:s');
    $this->updated_at = date('Y-m-d H:i:s');
  }
  
  public function toArray(){
    return[
      'id: ' => $this->id,
      'name: ' => $this->name,
      'price: ' => $this->price,
      'stock: ' => $this->stock
    ];
  }

}

?>
