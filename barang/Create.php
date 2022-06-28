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
$ID_Barang = $_POST['ID_Barang'] ?? '';
$Nama_Barang = $_POST['Nama_barang'] ?? '';
$harga = $_POST['harga'] ?? '';
$kategori = $_POST['kategori'] ?? 0;
$brand = $_POST['brand'] ?? '';
$tahun = $_POST['tahun'] ?? 0;


$kategoriFilter = filter_var($kategori, FILTER_VALIDATE_INT);

/**
 * Validation empty fields
 */
$isValidated = true;
if(empty($ID_Barang)){
    $reply['error'] = 'Id harus diisi';
    $isValidated = false;
}
if(empty($Nama_Barang)){
    $reply['error'] = 'Nama barang harus diisi';
    $isValidated = false;
}
if(empty($harga)) {
    $reply['error'] = 'harga harus diisi';
    $isValidated = false;
}
    if (empty($kategori)) {
        $reply['error'] = 'kategori harus diisi';
        $isValidated = false;
    }
    if (empty($brand)) {
        $reply['error'] = 'brand harus diisi';
        $isValidated = false;
    }
        if (empty($tahun)) {
            $reply['error'] = 'tahun harus diisi';
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
            $query = "INSERT INTO barang (ID_Barang, Nama_barang, harga, kategori, brand, tahun) 
VALUES (:ID_Barang, :Nama_barang, :harga, :kategori, :brand, :tahun)";
            $statement = $connection->prepare($query);
            /**
             * Bind params
             */
            $statement->bindValue(":ID_Barang", $ID_Barang);
            $statement->bindValue(":Nama_barang", $Nama_Barang);
            $statement->bindValue(":harga", $harga);
            $statement->bindValue(":kategori", $kategori);
            $statement->bindValue(":brand", $brand);
            $statement->bindValue(":tahun", $tahun);
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
        $getResult = "SELECT * FROM barang WHERE ID_Barang = :ID_Barang";
        $stm = $connection->prepare($getResult);
        $stm->bindValue(':ID_Barang', $ID_Barang);
        $stm->execute();
        $result = $stm->fetch(PDO::FETCH_ASSOC);

        /*
         * Get kategori
         */

        /*
         * Transform result
         */
        $dataFinal = [
            'ID_Barang' => $result['ID_Barang'],
            'Nama_barang' => $result['Nama_barang'],
            'harga' => $result['harga'],
            'kategori' => $result['kategori'],
            'brand' => $result['brand'],
            'tahun' => $result['tahun'],
        ];

        /**
         * Show output to client
         * Set status info true
         */
        $reply['data'] = $dataFinal;
        $reply['status'] = $isOk;
        echo json_encode($reply);
