<?php

// Set the correct path to Laravel's storage
require __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\Storage;

$filePath = __DIR__ . '/storage/app/public/test-escpos.bin';
$printerName = "POS58 Printer"; // ← Make sure this matches your printer

echo "Generating ESC/POS test ticket...\n";

$data = '';
$data .= chr(27) . chr(64); // INIT
$data .= chr(27) . chr(97) . chr(1); // CENTER
$data .= chr(27) . chr(69) . chr(1); // BOLD ON
$data .= "NORTH CALOOCAN CITY HALL\n";
$data .= "QUEUE TICKET\n";
$data .= chr(27) . chr(69) . chr(0); // BOLD OFF
$data .= chr(27) . chr(97) . chr(0); // LEFT
$data .= "TOKEN: 123456\n";
$data .= "DATE: " . date('M d, Y h:i A') . "\n";
$data .= "\n\n";
$data .= chr(29) . "V" . chr(66); // CUT

// Save the binary ESC/POS file
file_put_contents($filePath, $data);

echo "✅ ESC/POS file generated:\n";
echo "   $filePath\n";
echo "Now run this command to print it:\n";
echo "   print /D:\"\\\\localhost\\$printerName\" \"$filePath\"\n";
?>