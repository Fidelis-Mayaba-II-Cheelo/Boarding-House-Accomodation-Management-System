<?php
session_start();
include('db-connect.php');
include('menu.php');
include('functions.php');

require __DIR__ . "/../vendor/autoload.php";

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

include('session_handler.php');

$result = []; 

if ($mysqli) {
    try {
        
        $key = $_ENV["SECRET_KEY"];
        $iv = $_ENV["IV"];
        if (isset($_POST['submit'])) {

            csrf_token_validation($_SERVER['PHP_SELF']);

            $id = sanitize_input($_POST['id']); 
            $hostel = sanitize_input($_POST['hostel']);
            $price = sanitize_input($_POST['price']);

            if ($hostel != null && $price != null) {
                
                $sql = "UPDATE `pricing` SET `hostel_type` = ?, `price_per_bedspace` = ? WHERE `id` = ?";
                $stmt = $mysqli->prepare($sql);
                $stmt->bind_param("sii",$hostel, $price, $id );
                $query = $stmt->execute();

                if ($query) {
                    echo "<div class='success'>Pricing details updated successfully.</div>";
                    header("Location: view_pricing.php"); 
                    unset($_SESSION['csrf_token']);
                    unset($_SESSION['csrf_token_expires']);
                    exit();
                } else {
                    echo "<div class='error' style='text-align:center; justify-content:center; align-items:center;'>Error updating pricing details.</div>";
                }
            }
        } else if (isset($_GET['id'])) {
            $id = openssl_decrypt(urldecode($_GET['id']), 'AES-128-CTR', $key, 0, $iv);
            $sql = "SELECT * FROM `pricing` WHERE `id`= ?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("i",$id );
            $query = $stmt->execute();
            $query_result = $stmt->get_result();

            if ($query && $query_result->num_rows > 0) {
                $result = $query_result->fetch_assoc();
            } else {
                echo "<div class='warning' style='text-align:center; justify-content:center; align-items:center;'>No pricing found with this ID.</div>";
                exit;
            }
        }
    } catch (Exception $ex) {
        $log = $ex->getMessage() . ": " . "This is coming from manage_pricing.php on admin side";
        error_logger($log);
        echo "<div class='error' style='text-align:center; justify-content:center; align-items:center;'>Pricing data not available</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Pricing</title>
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

        .btn {
            background-color: gainsboro;
            color: white;
            justify-content: center;
            border: 2px solid white;
            border-radius: 5px;
            font-size: 18px;
            font-style: normal;
            cursor: pointer;
            width: 100%;
            padding: 10px;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #333;
            color: white;
            justify-content: center;
            border: 2px solid white;
            border-radius: 5px;
            font-size: 18px;
            font-style: normal;
            cursor: pointer;
            width: 100%;
            padding: 10px;
            transition: background-color 0.3s ease;
        }

        .form-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin: 20px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            /* Adjusted for better form layout */
            width: 100%;
        }

        input,
        select {
            width: calc(100% - 20px);
            /* Adjust width for padding */
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        p {
            align-items: center;
            justify-content: center;
            font-weight: 300;
        }
    </style>
</head>

<body>
    <h1 style="text-align: center;">Manage Pricing</h1>
    <?php if (!empty($result)): ?>
        <form class="form-container" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($result['id']); ?>">
            <select name="hostel" id="hostel" required>
                <option value="Single" <?php if ($result['hostel_type'] == 'Single') echo 'selected'; ?>>Single</option>
                <option value="Double" <?php if ($result['hostel_type'] == 'Double') echo 'selected'; ?>>Double</option>
                <option value="Triple" <?php if ($result['hostel_type'] == 'Triple') echo 'selected'; ?>>Triple</option>
                <option value="Quadruple" <?php if ($result['hostel_type'] == 'Quadruple') echo 'selected'; ?>>Quadruple</option>
            </select>
            <input type="number" name="price" placeholder="Price" value="<?php echo htmlspecialchars($result['price_per_bedspace']); ?>" required>
            <input class='btn' type="submit" name="submit" value="Update Price" />
        </form>
    <?php else: ?>
        <p>No pricing data available for this record.</p>
    <?php endif; ?>
</body>

</html>