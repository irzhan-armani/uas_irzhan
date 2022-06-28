<?php
include "../config/connection.php";
/**
 * @var $connection PDO
 */

/*
 * Validate http method
 */
if($_SERVER['REQUEST_METHOD'] !== 'GET'){
    header('Content-Type: application/json');
    http_response_code(400);
    $reply['error'] = 'DELETE method required';
    echo json_encode($reply);
    exit();
}

$dataFinal = [];
$ID_Barang = $_GET['ID_Barang'] ?? '';


if(empty($ID_Barang)){
    $reply['error'] = 'ID tidak boleh kosong';
    echo json_encode($reply);
    http_response_code(400);
    exit(0);
}

try{
    $queryCheck = "SELECT * FROM barang where ID_Barang = :ID_Barang";
    $statement = $connection->prepare($queryCheck);
    $statement->bindValue(':ID_Barang', $ID_Barang);
    $statement->execute();
    $dataBuku = $statement->fetch(PDO::FETCH_ASSOC);

    /*
     * Ambil data kategori berdasarkan kolom kategori
     */
    if($dataBuku) {

        $dataFinal = [
            'ID_Barang' => $dataBuku['ID_Barang'],
            'Nama_barang' => $dataBuku['Nama_barang'],
            'harga' => $dataBuku['harga'],
            'kategori' => $dataBuku['kategori'],
            'brand' => $dataBuku['brand'],
            'tahun' => $dataBuku['tahun'],
        ];
    }
}catch (Exception $exception){
    $reply['error'] = $exception->getMessage();
    echo json_encode($reply);
    http_response_code(400);
    exit(0);
}

/*
 * Show response
 */
if(!$dataFinal){
    $reply['error'] = 'Data tidak ditemukan ID_Barang '.$ID_Barang;
    echo json_encode($reply);
    http_response_code(400);
    exit(0);
}

/*
 * Otherwise show data
 */
$reply['status'] = true;
$reply['data'] = $dataFinal;
echo json_encode($reply);