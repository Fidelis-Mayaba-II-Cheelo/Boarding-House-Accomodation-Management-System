<?php
session_start();
include('db-connect.php');
include('functions.php');
include('menu.php');
define('UPLOADPATH', '../Gallery/');

include("session_handler.php");


if ($mysqli) {
    try {

        if (isset($_POST['submit'])) {

            csrf_token_validation($_SERVER['PHP_SELF']);
            
            $hostel_type = sanitize_input($_POST['hostel_type']);
            $hostel_image = sanitize_input($_FILES['hostel_image']['name']);
            $target = null;

            if ($hostel_type === "") {
                echo "<p class='warning'>Please select the hostel for which you would like to upload an image</p>";
            } else if ($hostel_image === "") {
                echo "<p class='warning'>Please select an image to upload</p>";
            } else {

                if ($_FILES['hostel_image']['error'] === 0) {
                    $target = UPLOADPATH . basename($hostel_image);
                    $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
                    $file_type = mime_content_type($_FILES['hostel_image']['tmp_name']);
                    
                    if (!in_array($file_type, $allowed_types)) {
                        $message = "<div class='error'>Invalid file type. Only JPEG, JPG, and PNG files are allowed.</div>";
                    } else if ($_FILES['profile_picture']['size'] > 15 * 1024 * 1024) {
                        $message = "<div class='error'>File size exceeds the maximum limit of 15MB.</div>";
                    } else{

                    if (move_uploaded_file($_FILES['hostel_image']['tmp_name'], $target)) {
                        $sql = "INSERT INTO `image_gallery` (`hostel`,`hostel_image`) VALUES (?, ?)";
                        $stmt = $mysqli->prepare($sql);
                        $stmt->bind_param('ss', $hostel_type, $hostel_image);
                        $query = $stmt->execute();
                        if ($query) {
                            echo "<p class='success'>Image successfully added to $hostel_type room gallery</p>";

                            unset($_SESSION['csrf_token']);
                            unset($_SESSION['csrf_token_expires']);
                            $submission_successful = true;
                        } else {
                            echo "<p class='error'>Error adding image to $hostel_type room gallery</p>";
                        }
                    }
                }
            }
                @unlink($_FILES['hostel_image']['tmp_name']);
            }
        }
    } catch (Exception $ex) {
        $log = $ex->getMessage(). ": " . "This error is coming from add_to_gallery.php on admin side";
        error_logger($log);
    }
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="add_to_gallery.css">
</head>

<body>
    <?php 
    if(isset($submission_successful) && $submission_successful):
    ?>
    <p class="success" style="text-align:center; justify-content:center; align-items:center;">Image was added to gallery successfully</p>
    <script>
        refreshPageAfterSuccess(); 
    </script>
    <?php endif; ?>
    <h1 class="headings">Add Image to Room Gallery</h1>
    <div class="form-container">
    <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token) ?>" />
        <label id="hostel_type_validation" class="validation_style"></label><br/><br/>
        <label for="hostel_type">Hostel</label>
        <select name="hostel_type" id="hostel_type">
            <?php
            $sql = "SHOW COLUMNS FROM image_gallery LIKE 'hostel'";
            $query = $mysqli->query($sql);

            if ($query && $result = $query->fetch_assoc()) {
                // Get the 'Type' field which contains enum values
                $type = $result['Type'];

                // Extract enum values between parentheses
                preg_match("/^enum\((.*)\)$/", $type, $matches);

                // Split values and remove quotes
                $enumValues = explode(",", str_replace("'", "", $matches[1]));

                // Loop through each enum value and add it as an option
                foreach ($enumValues as $hostel_category) {
                    echo "<option value=\"$hostel_category\">$hostel_category</option>";
                }
            }
            ?>
        </select>
        <br /><br />
        <label id="hostel_image_validation" class="validation_style"></label><br/><br/>
        <label for="hostel_image">Image</label>
        <input type="file" accept=".jpeg, .jpg, .png" id="hostel_image" name="hostel_image" value="<?php echo isset($_FILES['hostel_image']) ? htmlspecialchars($hostel_image): ''; ?>"/>
        <br /><br />
        <input type="submit" value="Upload Image" name="submit" class="btn"/>
    </form>
    </div>
    <script type="text/javascript" src="add_to_gallery.js"></script>
</body>

</html>