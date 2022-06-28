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
$ID_Pelanggan = $_POST['ID_Pelanggan'] ?? '';
$Nama = $_POST['Nama'] ?? '';
$Alamat = $_POST['Alamat'] ?? '';
$Kota = $_POST['Kota'] ?? 0;
$kode_pos = $_POST['kode_pos'] ?? '';
$Tanggal = $_POST['Tanggal'] ?? 0;




/**
 * Validation empty fields
 */
$isValidated = true;
if(empty($ID_Pelanggan)){
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
    if (empty($kode_pos)) {
        $reply['error'] = 'kode pos harus diisi';
        $isValidated = false;
    }
        if (empty($Tanggal)) {
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
            $query = "INSERT INTO pelanggan (ID_Pelanggan, Nama, Alamat, Kota, kode_pos, Tanggal) 
VALUES (:ID_Pelanggan, :Nama, :Alamat, :Kota, :kode_pos, :Tanggal)";
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
        $getResult = "SELECT * FROM pelanggan WHERE ID_Pelanggan = :ID_Pelanggan";
        $stm = $connection->prepare($getResult);
        $stm->bindValue(':ID_Pelanggan', $ID_Pelanggan);
        $stm->execute();
        $result = $stm->fetch(PDO::FETCH_ASSOC);

        /*
         * Get kategori
         */

        /*
         * Transform result
         */
        $dataFinal = [
            'ID_Pelanggan' => $result['ID_Pelanggan'],
            'Nama' => $result['Nama'],
            'Alamat' => $result['Alamat'],
            'Kota' => $result['Kota'],
            'kode_pos' => $result['kode_pos'],
            'Tanggal' => $result['Tanggal'],
        ];

        /**
         * Show output to client
         * Set status info true
         */
        $reply['data'] = $dataFinal;
        $reply['status'] = $isOk;
        echo json_encode($reply);
