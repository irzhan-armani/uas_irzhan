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

$ID_Pelanggan = $formData['ID_Pelanggan'] ?? '';
$Nama = $formData['Nama'] ?? '';
$Alamat = $formData['Alamat'] ?? '';
$Kota = $formData['Kota'] ?? 0;
$kode_pos = $formData['kode_pos'] ?? '';
$Tanggal = $formData['Tanggal'] ?? 0;

/**
 * Validation empty fields
 */
$isValidated = true;
if(empty($ID_Pelanggan)){
    $reply['error'] = 'ID harus diisi';
    $isValidated = false;
}
if(empty($Nama)){
    $reply['error'] = 'Nama harus diisi';
    $isValidated = false;
}
if(empty($Alamat)){
    $reply['error'] = 'alamat harus diisi';
    $isValidated = false;
}
if(empty($Kota)){
    $reply['error'] = 'Kota harus diisi';
    $isValidated = false;
}
if(empty($kode_pos)){
    $reply['error'] = 'kode harus diisi';
    $isValidated = false;
}
if(empty($Tanggal)){
    $reply['error'] = 'Tanggal harus diisi';
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
        $reply['error'] = 'Data tidak ditemukan ID_Pelanggan '.$ID_Pelanggan;
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
    $query = "UPDATE pelanggan SET Nama = :Nama, Alamat = :Alamat, Kota = :Kota, kode_pos = :kode_pos, Tanggal = :Tanggal
WHERE ID_Pelanggan = :ID_Pelanggan";
    $statement = $connection->prepare($query);
    /**
     * Bind params
     */
    $statement->bindValue(":ID_Pelanggan", $ID_Pelanggan);
    $statement->bindValue(":Nama", $Nama);
    $statement->bindValue(":Alamat", $Alamat);
    $statement->bindValue(":Kota", $Kota);
    $statement->bindValue(":kode_pos", $kode_pos);
    $statement->bindValue(":Tanggal", $Tanggal);
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
$stmSelect = $connection->prepare("SELECT * FROM pelanggan where ID_Pelanggan = :ID_Pelanggan");
$stmSelect->bindValue(':ID_Pelanggan', $ID_Pelanggan);
$stmSelect->execute();
$dataFinal = $stmSelect->fetch(PDO::FETCH_ASSOC);

/**
 * Show output to client
 */
$reply['data'] = $dataFinal;
$reply['status'] = $isOk;
echo json_encode($reply);