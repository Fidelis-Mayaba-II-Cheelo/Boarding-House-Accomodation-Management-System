<?php
session_start();
include('db-connect.php'); 
include_once('../Admin/functions.php');
include('../Admin/session_handler.php');


if ($mysqli) {
    $message = '';
    $sql = "SELECT * FROM `pricing`"; 
    $stmt = $mysqli->prepare($sql);
    $stmt->execute();
    $query = $stmt->get_result();

    if ($query && $query->num_rows > 0) {
        
        $prices = $query->fetch_all(MYSQLI_ASSOC);
    } else {
        $log = "No pricing data found on client side. Check view_pricing.php for more information";
        error_logger($log);
        $message = "<div class='warning'>Oops, prices are currently unavailable, check again later</div>";
    }
} else {
    $log = "Database connection error. on client side. Check view_pricing.php for more information";
    error_logger($log);
    $message = "<div class='error'>Prices unavailable due to a technical error, check again later</div>";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Hostel Prices</title>
    <style>
        .success {
            border: 2px solid green;
            border-radius: 5px;
            background-color: green;
            color: white;
        }

        .warning {
            border: 2px solid yellow;
            border-radius: 5px;
            background-color: yellow;
            color: white;
        }

        .error {
            border: 2px solid red;
            border-radius: 5px;
            background-color: red;
            color: white;
        }

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

        .header {
            text-align: center;
            background-color: #333;
            border-radius: 5px;
            border-bottom-left-radius: 0%;
            border-bottom-right-radius: 0%;
            margin-top: 0%;
            margin-bottom: 0%;
            color: white;
            padding: 5px;
            padding-top: 10px;
            position: sticky;
        }
    </style>
</head>

<body>
    <?php include('student_menu.php'); ?>
    <?php
    if(!empty($message)){
        echo "<div style='text-align:center; justify-content:center; align-items:center;'>$message</div>";
    }
    ?>
    <h1>Hostel Price List</h1>

    <?php if (isset($prices)) { ?>
        <table>
            <thead>
                <tr>
                    <th>Hostel Type</th>
                    <th>Price Per Month</th>
                    <th>Price Per Semester</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($prices as $price) { ?>
                    <tr>
                        <td><?php echo $price['hostel_type']; ?></td>
                        <td><?php echo $price['price_per_bedspace']; ?></td>
                        <td><?php echo $price['price_per_bedspace'] * 4; ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } ?>

</body>

</html>