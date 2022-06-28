<?php
include "../config/connection.php";
/**
 * @var $connection PDO
 */
try{

    $statement = $connection->prepare("select * from pelanggan ");
    $isOk = $statement->execute();
    $resultspelanggan = $statement->fetchAll(PDO::FETCH_ASSOC);
    $finalResults = [];
    foreach ($resultspelanggan as $pelanggan) {

        $finalResults[] = [
            'ID_Pelanggan' => $pelanggan ['ID_Pelanggan'],
            'Nama' => $pelanggan['Nama'],
            'Alamat' => $pelanggan['Alamat'],
            'Kota' => $pelanggan['Kota'],
            'kode_pos' => $pelanggan['kode_pos'],
            'Tanggal' => $pelanggan['Tanggal']
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
