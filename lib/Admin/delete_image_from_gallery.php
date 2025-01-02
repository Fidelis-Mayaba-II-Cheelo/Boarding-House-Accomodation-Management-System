<?php
session_start();
include('db-connect.php');
include('functions.php');
include('menu.php');
define('UPLOADPATH', '../Gallery/');
include('session_handler.php');

if ($mysqli) {
    try {
        if (isset($_POST['submit'])) {
            $id = $_POST['id'];

            $delete_hostel_image_sql = "SELECT `hostel`, `hostel_image` FROM `image_gallery` WHERE `id`='$id'";
            $delete_hostel_image_query = $mysqli->query($delete_hostel_image_sql);
            if ($delete_hostel_image_query->num_rows > 0) {
                $result = $delete_hostel_image_query->fetch_assoc();
                $hostel_name = $result['hostel'];
                $deleted_hostel_image = $result['hostel_image'];

                
                if (file_exists($deleted_hostel_image)) {
                    unlink($deleted_hostel_image);
                }
            }
            
            
            $sql = "DELETE FROM `image_gallery` WHERE `id` = '$id'";
            $query = $mysqli->query($sql);
            if ($query) {
                echo "<div class='success' style='text-align:center; justify-content:center; align-items:center;'>Image was successfully deleted</div>";
            } else {
                echo "<div class='error' style='text-align:center; justify-content:center; align-items:center;'>Error deleting image</div>";
            }
        }
    } catch (Exception $ex) {
        $log = $ex->getMessage() . ": " . "This error is coming from delete_image_from_gallery.php on admin side";
        error_logger($log);
    }
}



$imagesPerPage = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $imagesPerPage;

$sql = "SELECT COUNT(*) as count FROM `image_gallery`";
$result = $mysqli->query($sql);
$totalImages = $result->fetch_assoc()['count'];
$totalPages = ceil($totalImages / $imagesPerPage);

$sql = "SELECT * FROM `image_gallery` LIMIT $offset, $imagesPerPage";
$query = $mysqli->query($sql);
$gallery = [];
if ($query) {
    while ($row = $query->fetch_assoc()) {
        $gallery[] = [
            'image' => $row['hostel_image'],
            'id' => $row['id'],
            'hostel' => $row['hostel'] 
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery Management</title>
    <link rel="stylesheet" href="delete_image_from_gallery.css">
</head>

<body>
    <h1 class="headings">Manage Gallery</h1>
    <div class="form-container">
        <?php foreach ($gallery as $item): ?>
            <form method="post" class="image-form">
                <input type="hidden" name="id" value="<?= htmlspecialchars($item['id']) ?>" />
                <?php $item_url = '/Gallery/' . htmlspecialchars($item['image']); ?>
                <div class="image-container">
                    <img src="<?= $item_url ?>" alt="Gallery Image" class="gallery-image" />
                    <p><strong>Hostel:</strong> <?= htmlspecialchars($item['hostel']) ?></p> 
                </div>
                <input type="submit" name="submit" value="Delete Image" class="btn" />
            </form>
        <?php endforeach; 
        
        if(empty($gallery)){
            echo "No records found";
        }
        ?>
    </div>

    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?page=<?= $page - 1 ?>" class="page-link">Previous</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?= $i ?>" class="page-link <?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
            <a href="?page=<?= $page + 1 ?>" class="page-link">Next</a>
        <?php endif; ?>
    </div>
</body>

</html>

