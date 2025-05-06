<?php
spl_autoload_register(function ($class) {
    // Convert namespace to full file path
    $path = str_replace('\\', '/', $class);
    $file = __DIR__ . '/' . $path . '.php';

    // Check if the file exists
    if (file_exists($file)) {
        require_once $file;
        return true;
    }
    return false;
}); 