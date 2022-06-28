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
$ID_Pelanggan = $_GET['ID_Pelanggan'] ?? '';


if(empty($ID_Pelanggan)){
    $reply['error'] = 'ID tidak boleh kosong';
    echo json_encode($reply);
    http_response_code(400);
    exit(0);
}

try{
    $queryCheck = "SELECT * FROM pelanggan where ID_Pelanggan = :ID_Pelanggan";
    $statement = $connection->prepare($queryCheck);
    $statement->bindValue(':ID_Pelanggan', $ID_Pelanggan);
    $statement->execute();
    $dataBuku = $statement->fetch(PDO::FETCH_ASSOC);

    /*
     * Ambil data kategori berdasarkan kolom kategori
     */
    if($dataBuku) {

        $dataFinal = [
            'ID_Pelanggan' => $dataBuku['ID_Pelanggan'],
            'Nama' => $dataBuku['Nama'],
            'Alamat' => $dataBuku['Alamat'],
            'Kota' => $dataBuku['Kota'],
            'kode_pos' => $dataBuku['kode_pos'],
            'Tanggal' => $dataBuku['Tanggal'],
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
    $reply['error'] = 'Data tidak ditemukan ID_Pelanggan '.$ID_Pelanggan;
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