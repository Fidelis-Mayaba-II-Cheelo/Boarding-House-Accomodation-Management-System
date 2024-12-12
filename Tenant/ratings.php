<?php
session_start();
include('student_menu.php');
include('db-connect.php');
include_once('../Admin/functions.php');
include('../Admin/session_handler.php');

date_default_timezone_set('Africa/Lusaka');


if ($mysqli) {

    try {
        // Check if the student has already submitted a rating in the last month by using the session
        if (isset($_SESSION['last_rating_submission'])) {
            $lastSubmissionDate = $_SESSION['last_rating_submission'];
            $currentDate = time();
            $monthAgo = strtotime('-1 month');

            if ($lastSubmissionDate >= $monthAgo) {
                // If the last submission was within a month, notify the student
                $timeToWait = date('F j, Y', strtotime('+1 month', $lastSubmissionDate));
                echo "<div class='warning' style='text-align:center; justify-content:center; align-items:center;'>Sorry, you can only submit one rating per month. Next submission allowed after: $timeToWait</div>";
                exit; 
            }
        }

        // Handle form submission if the student is allowed to submit
        if (isset($_POST['submit'])) {

            csrf_token_validation($_SERVER['PHP_SELF']);

            $scale = sanitize_input($_POST['scale']);
            $ratings = sanitize_input($_POST['rating']);
            $improvements = sanitize_input($_POST['improvements']);

            if ($ratings !== "" && $improvements !== "" && !empty($scale)) {
                // Insert the new rating into the database without storing the student_id (anonymous)
                $sql = "INSERT INTO `ratings` (`scale`, `ratings`, `improvements`, `date_added`) 
            VALUES (?, ?, ?, NOW())";  
                $query = $mysqli->prepare($sql);
                $query->bind_param('iss', $scale, $ratings, $improvements);

                if ($query->execute()) {
                    // Store the current time in the session to track submission time
                    $_SESSION['last_rating_submission'] = time();
                    echo "<div class='success' style='text-align:center; justify-content:center; align-items:center;'>You have successfully submitted your ratings</div>";
                    $submission_successful = true;

                    unset($_SESSION['csrf_token']);
                    unset($_SESSION['csrf_token_expires']);
                } else {
                    $log = "Ratings were not submitted. Check ratings.php on client side";
                    error_logger($log);
                    echo "<div class='error' style='text-align:center; justify-content:center; align-items:center;'>Unexpected error occurred: Ratings were not submitted</div>";
                }
            }
        }
    } catch (Exception $ex) {
        $log = $ex->getMessage() . ":" . "This is due to a database connection error from ratings.php on client side";
        error_logger($log);
        echo "<div class='error' style='text-align:center; justify-content:center; align-items:center;'>Techical errors are at play. Please contact the admin for more information</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ratings</title>
    <link rel="stylesheet" href="ratings.css" type="text/css" />
</head>

<body>
<?php
    if (isset($submission_successful) && $submission_successful): ?>
        <script>
            refreshPageAfterSuccess();
        </script>
    <?php endif; ?>
    <h1 class="headings">Ratings/Reviews</h1>
    <form class="form-container" method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>">
        <label for="scale">Rate us on a scale of 1-10</label>
        <select name="scale" required>
            <?php
            for ($i = 1; $i <= 10; $i++) {
                echo "<option value='$i'>$i</option>";
            }
            ?>
        </select>
        <br />
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
        <label for="rating">Give us your most honest thoughts on our facilities</label>
        <textarea type="text" placeholder="Rate us" name="rating" required><?php echo isset($_POST['rating'])? htmlspecialchars($ratings) : ''; ?></textarea>
        <br />
        <label for="improvements">Improvement Suggestions</label>
        <textarea type="text" placeholder="Improvement suggestions" name="improvements" required><?php echo isset($_POST['improvements'])? htmlspecialchars($improvements) : ''; ?></textarea>
        <br />
        <input class="btn" type="submit" name="submit" value="Rate Us" />
    </form>
</body>

</html>