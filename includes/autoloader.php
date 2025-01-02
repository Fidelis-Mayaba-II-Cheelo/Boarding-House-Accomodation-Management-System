<?php
spl_autoload_register('autoloader');

function autoloader($classname) {
    // Use absolute base directory
    $baseDir = "../lib/classes/";

    $filename = $classname . '.class.php';

    // Search recursively in subdirectories
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($baseDir));
    foreach ($iterator as $file) {
        if ($file->isFile() && basename($file) === $filename) {
            include_once $file->getPathname();
            return;
        }
    }

    error_log("Autoloader could not find class: $classname");
}

spl_autoload_register('autoloader');
