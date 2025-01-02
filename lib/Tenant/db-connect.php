<?php

require __DIR__ . "/../vendor/autoload.php";

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();


try {
    $mysqli = new mysqli (
        $_ENV["DATABASE_HOSTNAME"],
        $_ENV["DATABASE_USERNAME"],
        $_ENV["DATABASE_PASSWORD"],
        $_ENV["DATABASE_NAME"],
        $_ENV["DATABASE_PORT"]
    );
} catch(Exception $e) {
    echo 'Error connecting to database';
} catch (Error $e) {
    echo 'Error connecting to database';
}
?>