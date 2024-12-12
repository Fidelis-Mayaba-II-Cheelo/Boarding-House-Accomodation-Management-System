<?php
session_start();
include('db-connect.php'); 
include('menu.php'); 
include('functions.php');
include('session_handler.php');

require __DIR__ . "/../vendor/autoload.php";

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();


if ($mysqli) {
    try {
        
        $key = $_ENV["SECRET_KEY"]; 
        $iv = $_ENV["IV"];
        $sql = "SELECT * FROM `pricing`"; 
        $stmt = $mysqli->prepare($sql);
        $stmt->execute();
        $query = $stmt->get_result();

        if ($stmt->execute() && $query->num_rows > 0) {
           
            $prices = $query->fetch_all(MYSQLI_ASSOC);
        } else {
            echo "<p class='error' style='text-align:center; justify-content:center; align-items:center;'>No pricing data found.</p>";
        }
    } catch (Exception $ex) {
        $log = $ex->getMessage() . ": " . "Error coming from view_pricing.php on admin side";
        error_logger($log);
        echo "<p class='warning' style='text-align:center; justify-content:center; align-items:center;'>Error: Maya Hostels Edit pricing page currently unavailable. Please try again later.</p>";
    }
} else {
    echo "<p class='error' style='text-align:center; justify-content:center; align-items:center;'>Database connection error.</p>";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Hostel Prices</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        table,
        th,
        td {
            border: 1px solid black;
        }

        th,
        td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        h1 {
            text-align: center;
        }
    </style>
</head>

<body>
    <h1>Hostel Pricing</h1>

    <?php if (isset($prices)) { ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Hostel Type</th>
                    <th>Price Per Month</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($prices as $price) {
                    $encrypted_price = urlencode(openssl_encrypt($price['id'], 'AES-128-CTR', $key, 0, $iv));
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($price['id']); ?></td>
                        <td><?php echo htmlspecialchars($price['hostel_type']); ?></td>
                        <td><?php echo htmlspecialchars($price['price_per_bedspace']); ?></td>
                        <td>
                            <a href="manage_pricing.php?id=<?php echo htmlspecialchars($encrypted_price); ?>">Edit</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } ?>

</body>

</html>