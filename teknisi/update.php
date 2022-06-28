<?php
include '../config/connection.php';
/**
 * @var $connection PDO
 */

/*
 * Validate http method
 */
if($_SERVER['REQUEST_METHOD'] !== 'PATCH'){
    header('Content-Type: application/json');
    http_response_code(400);
    $reply['error'] = 'PATCH method required';
    echo json_encode($reply);
    exit();
}
/**
 * Get input data PATCH
 */
$formData = [];
parse_str(file_get_contents('php://input'), $formData);

$ID_Teknisi = $formData['ID_Teknisi'] ?? '';
$Nama = $formData['Nama'] ?? '';
$Kota = $formData['Kota'] ?? '';
$TTL = $formData['TTL'] ?? 0;
$No_hp = $formData['No_hp'] ?? '';
$Alamat = $formData['Alamat'] ?? 0;

/**
 * Validation empty fields
 */
$isValidated = true;
if(empty($ID_Teknisi)){
    $reply['error'] = 'ID_Barang harus diisi';
    $isValidated = false;
}
if(empty($Nama)){
    $reply['error'] = 'Nama_Barang harus diisi';
    $isValidated = false;
}
if(empty($Kota)){
    $reply['error'] = 'kota harus diisi';
    $isValidated = false;
}
if(empty($TTL)){
    $reply['error'] = 'ttl harus diisi';
    $isValidated = false;
}
if(empty($No_hp)){
    $reply['error'] = 'nohp harus diisi';
    $isValidated = false;
}
if(empty($Alamat)){
    $reply['error'] = 'Alamat harus diisi';
    $isValidated = false;
}
/*
 * Jika filter gagal
 */
if(!$isValidated){
    echo json_encode($reply);
    http_response_code(400);
    exit(0);
}
/**
 * METHOD OK
 * Validation OK
 * Check if data is exist
 */
try{
    $queryCheck = "SELECT * FROM teknisi where ID_Teknisi = :ID_Teknisi";
    $statement = $connection->prepare($queryCheck);
    $statement->bindValue(':ID_Teknisi', $ID_Teknisi);
    $statement->execute();
    $row = $statement->rowCount();
    /**
     * Jika data tidak ditemukan
     * rowcount == 0
     */
    if($row === 0){
        $reply['error'] = 'Data tidak ditemukan ID_Teknisi '.$ID_Teknisi;
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
 * Prepare query
 */
try{
    $fields = [];
    $query = "UPDATE teknisi SET Nama = :Nama, Kota = :Kota, TTL = :TTL, No_hp = :No_hp, Alamat = :Alamat
WHERE ID_Teknisi = :ID_Teknisi";
    $statement = $connection->prepare($query);
    /**
     * Bind params
     */
    $statement->bindValue(":ID_Teknisi", $ID_Teknisi);
    $statement->bindValue(":Nama", $Nama);
    $statement->bindValue(":Kota", $Kota);
    $statement->bindValue(":TTL", $TTL);
    $statement->bindValue(":No_hp", $No_hp);
    $statement->bindValue(":Alamat", $Alamat);
    /**
     * Execute query
     */
    $isOk = $statement->execute();
}catch (Exception $exception){
    $reply['error'] = $exception->getMessage();
    echo json_encode($reply);
    http_response_code(400);
    exit(0);
}
/**
 * If not OK, add error info
 * HTTP Status code 400: Bad request
 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status#client_error_responses
 */
if(!$isOk){
    $reply['error'] = $statement->errorInfo();
    http_response_code(400);
}

/*
 * Get data
 */
$stmSelect = $connection->prepare("SELECT * FROM teknisi where ID_Teknisi = :ID_Teknisi");
$stmSelect->bindValue(':ID_Teknisi', $ID_Teknisi);
$stmSelect->execute();
$dataFinal = $stmSelect->fetch(PDO::FETCH_ASSOC);

/**
 * Show output to client
 */
$reply['data'] = $dataFinal;
$reply['status'] = $isOk;
echo json_encode($reply);