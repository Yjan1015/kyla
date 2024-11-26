<?php
// Include HTML Purifier library
require_once 'vendor/autoload.php'; // This will load HTML Purifier

// Configure HTML Purifier
$config = HTMLPurifier_Config::createDefault();
$purifier = new HTMLPurifier($config);
?>
