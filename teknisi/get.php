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
$ID_Teknisi = $_GET['ID_Teknisi'] ?? '';


if(empty($ID_Teknisi)){
    $reply['error'] = 'ID tidak boleh kosong';
    echo json_encode($reply);
    http_response_code(400);
    exit(0);
}

try{
    $queryCheck = "SELECT * FROM teknisi where ID_Teknisi = :ID_Teknisi";
    $statement = $connection->prepare($queryCheck);
    $statement->bindValue(':ID_Teknisi', $ID_Teknisi);
    $statement->execute();
    $dataBuku = $statement->fetch(PDO::FETCH_ASSOC);

    /*
     * Ambil data kategori berdasarkan kolom kategori
     */
    if($dataBuku) {

        $dataFinal = [
            'ID_Teknisi' => $dataBuku['ID_Teknisi'],
            'Nama' => $dataBuku['Nama'],
            'Kota' => $dataBuku['Kota'],
            'TTL' => $dataBuku['TTL'],
            'No_hp' => $dataBuku['No_hp'],
            'Alamat' => $dataBuku['Alamat'],
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
    $reply['error'] = 'Data tidak ditemukan ID_Teknisi '.$ID_Teknisi;
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