<?php
include "../config/connection.php";
/**
 * @var $connection PDO
 */

/*
 * Validate http method
 */
if($_SERVER['REQUEST_METHOD'] !== 'DELETE'){
    http_response_code(400);
    $reply['error'] = 'DELETE method required';
    echo json_encode($reply);
    exit();
}

/**
 * Get input data from RAW data
 */
$data = file_get_contents('php://input');
$res = [];
parse_str($data, $res);
$ID_Pelanggan = $res['ID_Pelanggan'] ?? '';


try{
    $queryCheck = "SELECT * FROM pelanggan where ID_Pelanggan = :ID_Pelanggan";
    $statement = $connection->prepare($queryCheck);
    $statement->bindValue(':ID_Pelanggan', $ID_Pelanggan);
    $statement->execute();
    $row = $statement->rowCount();
    /**
     * Jika data tidak ditemukan
     * rowcount == 0
     */
    if($row === 0){
        $reply['error'] = 'Data tidak ditemukan ID '.$ID_Pelanggan;
        echo json_encode($reply);
        http_response_code(400);
        exit(0);
    }
}catch (Exception $exception){
    $reply['error'] = $exception->getMessage();
    echo json_encode($reply);
    http_response_code(400);
    exit(0);
}

/**
 * Hapus data
 */
try{
    $queryCheck = "DELETE FROM pelanggan where ID_Pelanggan = :ID_Pelanggan";
    $statement = $connection->prepare($queryCheck);
    $statement->bindValue(':ID_Pelanggan', $ID_Pelanggan);
    $statement->execute();
}catch (Exception $exception){
    $reply['error'] = $exception->getMessage();
    echo json_encode($reply);
    http_response_code(400);
    exit(0);
}

/*
 * Send output
 */
$reply['status'] = true;
echo json_encode($reply);