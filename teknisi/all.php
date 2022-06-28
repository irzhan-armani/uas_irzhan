<?php
include "../config/connection.php";
/**
 * @var $connection PDO
 */
try{
    /**
     * Prepare query buku limit 50 rows
     */
    $statement = $connection->prepare("select * from teknisi ");
    $isOk = $statement->execute();
    $resultsteknisi = $statement->fetchAll(PDO::FETCH_ASSOC);
    $finalResults = [];
    foreach ($resultsteknisi as $teknisi) {

        $finalResults[] = [
            'ID_Teknisi' => $teknisi ['ID_Teknisi'],
            'Nama' => $teknisi['Nama'],
            'Kota' => $teknisi['Kota'],
            'TTL' => $teknisi['TTL'],
            'No_hp' => $teknisi['No_hp'],
            'Alamat' => $teknisi['Alamat']
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
