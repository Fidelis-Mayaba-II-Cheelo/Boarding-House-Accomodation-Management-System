<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once('head.php'); ?>
</head>

<body class="sb-nav-fixed">
    <?php include_once('topnav.php'); ?>
    <div id="layoutSidenav">
        <?php include_once('sidebar.php'); ?>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <?php echo $body??null; ?>
                </div>
            </main>
            <?php include_once('footer.php'); ?>
        </div>
    </div>
    <?php include_once('scripts.php'); ?>
</body>
</html>