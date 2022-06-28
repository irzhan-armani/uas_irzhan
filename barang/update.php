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

$ID_Barang = $formData['ID_Barang'] ?? '';
$Nama_barang = $formData['Nama_barang'] ?? '';
$harga = $formData['harga'] ?? '';
$kategori = $formData['kategori'] ?? 0;
$brand = $formData['brand'] ?? '';
$tahun = $formData['tahun'] ?? 0;

/**
 * Validation empty fields
 */
$isValidated = true;
if(empty($ID_Barang)){
    $reply['error'] = 'ID_Barang harus diisi';
    $isValidated = false;
}
if(empty($Nama_barang)){
    $reply['error'] = 'Nama_Barang harus diisi';
    $isValidated = false;
}
if(empty($harga)){
    $reply['error'] = 'Harga harus diisi';
    $isValidated = false;
}
if(empty($kategori)){
    $reply['error'] = 'Kategori harus diisi';
    $isValidated = false;
}
if(empty($brand)){
    $reply['error'] = 'Brand harus diisi';
    $isValidated = false;
}
if(empty($tahun)){
    $reply['error'] = 'TAHUN harus diisi';
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
    $queryCheck = "SELECT * FROM barang where ID_Barang = :ID_Barang";
    $statement = $connection->prepare($queryCheck);
    $statement->bindValue(':ID_Barang', $ID_Barang);
    $statement->execute();
    $row = $statement->rowCount();
    /**
     * Jika data tidak ditemukan
     * rowcount == 0
     */
    if($row === 0){
        $reply['error'] = 'Data tidak ditemukan ID_Barang '.$ID_Barang;
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
    $query = "UPDATE barang SET Nama_barang = :Nama_barang, harga = :harga, kategori = :kategori, brand = :brand, tahun = :tahun
WHERE ID_Barang = :ID_Barang";
    $statement = $connection->prepare($query);
    /**
     * Bind params
     */
    $statement->bindValue(":ID_Barang", $ID_Barang);
    $statement->bindValue(":Nama_barang", $Nama_barang);
    $statement->bindValue(":harga", $harga);
    $statement->bindValue(":kategori", $kategori);
    $statement->bindValue(":brand", $brand);
    $statement->bindValue(":tahun", $tahun);
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
$stmSelect = $connection->prepare("SELECT * FROM barang where ID_Barang = :ID_Barang");
$stmSelect->bindValue(':ID_Barang', $ID_Barang);
$stmSelect->execute();
$dataFinal = $stmSelect->fetch(PDO::FETCH_ASSOC);

/**
 * Show output to client
 */
$reply['data'] = $dataFinal;
$reply['status'] = $isOk;
echo json_encode($reply);