<?php

function login($pdo, $uname, $upass){
    $sql = "SELECT id, password from users where username = :username";
    $stmt = $pdo->prepare($sql);
    $success = $stmt->execute([':username' => $uname]);
    $data = $stmt->fetch();
    if(isset($data['password']) && password_verify($upass, $data['password'])){
        createToken($pdo, $data['id']);
    }
    else{
        echo json_encode(['message' => 'Incorrect username or Password']);
        http_response_code(401);
    }
}
function checkToken($pdo, $token){
    $sql = "select token, expires from tokens where token = :token";
    $stmt = $pdo->prepare($sql);
    $success = $stmt->execute([':token' => $token]);
    $row = $stmt->fetch();
    if($row && (is_null($row['expires']) || new DateTime($row['expires']) > new DateTime())){
        return true;
    }
    else{
        return false;
    }
}

function createToken($pdo, $id){
    $sql = "select token, expires from tokens where userid = :id";
    $stmt = $pdo->prepare($sql);
    $success = $stmt->execute([':id' => $id]);
    $row = $stmt->fetch();
    $token = $row['token'];
    if($stmt->rowCount()>0 && new DateTime($row['expires']) > new DateTime()){
        echo json_encode(['token' => $token], JSON_PRETTY_PRINT);
        http_response_code(200);
    }
    else if ($stmt->rowCount() <1){
        $token = bin2hex(random_bytes(32));
        $sql = "INSERT into tokens(token, userid, expires) values (:token, :userid, :expires)";
        $stmt = $pdo->prepare($sql);
        $exp = date('Y-m-d H:i:s', time()+86000);
        $success = $stmt->execute([':token' => $token, ':userid' => $id, ':expires' => $exp]);
        echo json_encode(['token' => $token], JSON_PRETTY_PRINT);
        http_response_code(200);
    }
    else{
        $token = bin2hex(random_bytes(32));
        $sql = "UPDATE tokens SET token = :token, expires = :expires WHERE userid = :userid";
        $stmt = $pdo->prepare($sql);
        $exp = date('Y-m-d H:i:s', time()+86000);
        $success = $stmt->execute([':token' => $token, 'expires' => $exp, ':userid' => $id]);
        echo json_encode(['token' => $token], JSON_PRETTY_PRINT);
        http_response_code(200);
    }
}

function recycleTokens($pdo){
    $sql = "delete from tokens where expires is not null and expires < now()";
    $stmt = $pdo->prepare($sql);
    $success = $stmt->execute();
    }





?>