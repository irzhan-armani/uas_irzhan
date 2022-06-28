<?php
include "../config/connection.php";
/**
 * @var $connection PDO
 */

/*
 * Validate http method
 */
if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    http_response_code(400);
    $reply['error'] = 'POST method required';
    echo json_encode($reply);
    exit();
}
/**
 * Get input data POST
 */
$ID_Teknisi = $_POST['ID_Teknisi'] ?? '';
$Nama = $_POST['Nama'] ?? '';
$TTL = $_POST['TTL'] ?? '';
$Kota = $_POST['Kota'] ?? 0;
$No_hp = $_POST['No_hp'] ?? '';
$Alamat = $_POST['Alamat'] ?? 0;




/**
 * Validation empty fields
 */
$isValidated = true;
if(empty($ID_Teknisi)){
    $reply['error'] = 'Id harus diisi';
    $isValidated = false;
}
if(empty($Nama)){
    $reply['error'] = 'Nama harus diisi';
    $isValidated = false;
}
if(empty($Alamat)) {
    $reply['error'] = 'alamat harus diisi';
    $isValidated = false;
}
if (empty($Kota)) {
    $reply['error'] = 'kota harus diisi';
    $isValidated = false;
}
if (empty($No_hp)) {
    $reply['error'] = 'No_hp harus diisi';
    $isValidated = false;
}
if (empty($TTL)) {
    $reply['error'] = 'TTL harus diisi';
    $isValidated = false;
}
/*
 * Jika filter gagal
 */
if (!$isValidated) {
    echo json_encode($reply);
    http_response_code(400);
    exit(0);
}
/**
 * Method OK
 * Validation OK
 * Prepare query
 */
try {
    $query = "INSERT INTO teknisi (ID_Teknisi, Nama, Kota, TTL, No_hp, Alamat) 
VALUES (:ID_Teknisi, :Nama, :Kota, :TTL, :No_hp, :Alamat)";
    $statement = $connection->prepare($query);
    /**
     * Bind params
     */
    $statement->bindValue(":ID_Teknisi", $ID_Teknisi);
    $statement->bindValue(":Nama", $Nama);
    $statement->bindValue(":Alamat", $Alamat);
    $statement->bindValue(":Kota", $Kota);
    $statement->bindValue(":TTL", $TTL);
    $statement->bindValue(":No_hp", $No_hp);
    /**
     * Execute query
     */
    $isOk = $statement->execute();
} catch (Exception $exception) {
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
if (!$isOk) {
    $reply['error'] = $statement->errorInfo();
    http_response_code(400);
}

/*
 * Get last data
 */
$getResult = "SELECT * FROM teknisi WHERE ID_Teknisi = :ID_Teknisi";
$stm = $connection->prepare($getResult);
$stm->bindValue(':ID_Teknisi', $ID_Teknisi);
$stm->execute();
$result = $stm->fetch(PDO::FETCH_ASSOC);

/*
 * Get kategori
 */

/*
 * Transform result
 */
$dataFinal = [
    'ID_Teknisi' => $result['ID_Teknisi'],
    'Nama' => $result['Nama'],
    'Alamat' => $result['Alamat'],
    'Kota' => $result['Kota'],
    'TTL' => $result['TTL'],
    'No_hp' => $result['No_hp'],
];

/**
 * Show output to client
 * Set status info true
 */
$reply['data'] = $dataFinal;
$reply['status'] = $isOk;
echo json_encode($reply);
