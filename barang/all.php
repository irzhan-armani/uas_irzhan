<?php
include "../config/connection.php";
/**
 * @var $connection PDO
 */
try{
    /**
     * Prepare query buku limit 50 rows
     */
    $statement = $connection->prepare("select * from barang ");
    $isOk = $statement->execute();
    $resultsbarang = $statement->fetchAll(PDO::FETCH_ASSOC);
    $finalResults = [];
    foreach ($resultsbarang as $barang) {

        $finalResults[] = [
            'ID_Barang' => $barang ['ID_Barang'],
            'Nama_barang' => $barang['Nama_barang'],
            'harga' => $barang['harga'],
            'kategori' => $barang['kategori'],
            'brand' => $barang['brand'],
            'tahun' => $barang['tahun']
        ];
    }

    $reply['data'] = $finalResults;

}catch (Exception $exception){
    $reply['error'] = $exception->getMessage();
    echo json_encode($reply);
    http_response_code(400);
    exit(0);
}

if(!$isOk){
    $reply['error'] = $statement->errorInfo();
    http_response_code(400);
}

$reply['status'] = true;
header('Content-type:application/json');
echo json_encode($reply);
