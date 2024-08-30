<?php

$apiKey = '77a92e2fa2a2e479a248a108210062ec';
$costUrl = 'https://api.rajaongkir.com/starter/cost';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $originCode = isset($_POST['origin']) ? trim($_POST['origin']) : '';
    $destinationCode = isset($_POST['destination']) ? trim($_POST['destination']) : '';
    $weight = isset($_POST['weight']) ? trim($_POST['weight']) : '';

    // Validasi input
    if (empty($originCode) || empty($destinationCode) || empty($weight)) {
        die('Semua field harus diisi.');
    }


    $data = array(
        'origin' => $originCode,
        'destination' => $destinationCode,
        'weight' => $weight
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $costUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/x-www-form-urlencoded',
        'key: ' . $apiKey
    ));


    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        die('CURL Error: ' . curl_error($ch));
    }
    curl_close($ch);

    // Tampilkan respon API
    $result = json_decode($response, true);
} else {
    die('Form belum disubmit.');
}
?>
<?php

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Cek Ongkir</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 800px;
        }

        h1 {
            margin-top: 0;
            color: #4CAF50;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            color: #333;
        }

        .result-header {
            margin-bottom: 20px;
        }

        .button-container {
            text-align: center;
        }

        .print-button {
            display: inline-block;
            padding: 10px 20px;
            color: #fff;
            background-color: #4CAF50;
            text-decoration: none;
            border-radius: 4px;
            font-size: 16px;
            border: none;
            cursor: pointer;
        }

        .print-button:hover {
            background-color: #45a049;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Hasil Cek Ongkir</h1>
        <div class="result-header">
            <p><strong>Kota Asal:</strong>
                <?php echo htmlspecialchars($result['rajaongkir']['origin_details']['city_name']); ?></p>
            <p><strong>Kota Tujuan:</strong>
                <?php echo htmlspecialchars($result['rajaongkir']['destination_details']['city_name']); ?></p>
            <p><strong>Berat:</strong> <?php echo htmlspecialchars($_POST['weight']); ?> gram</p>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Kurir</th>
                    <th>Layanan</th>
                    <th>Deskripsi</th>
                    <th>Biaya (IDR)</th>
                    <th>Estimasi Pengiriman</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (isset($result['rajaongkir']['results'])) {
                    foreach ($result['rajaongkir']['results'] as $courier) {
                        foreach ($courier['costs'] as $cost) {
                            foreach ($cost['cost'] as $detail) {
                                echo '<tr>';
                                echo '<td>' . htmlspecialchars($courier['name']) . '</td>';
                                echo '<td>' . htmlspecialchars($cost['service']) . '</td>';
                                echo '<td>' . htmlspecialchars($cost['description']) . '</td>';
                                echo '<td>' . number_format($detail['value'], 0, ',', '.') . '</td>';
                                echo '<td>' . htmlspecialchars($detail['etd']) . '</td>';
                                echo '</tr>';
                            }
                        }
                    }
                }
                ?>
            </tbody>
        </table>

        <div class="button-container">
            <a href="javascript:window.print();" class="print-button">Cetak Hasil</a>
            <a href="index.php" class="print-button">Kembali</a>
        </div>
    </div>
</body>

</html>