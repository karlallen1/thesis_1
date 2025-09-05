<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

class PrintController extends Controller
{
    public function testPrint()
    {
        try {
            // 🔴 REPLACE 'YICHIP POS58' with YOUR printer name
            $printerName = "YICHIP POS58";

            // Create connector using Windows printer name
            $connector = new WindowsPrintConnector($printerName);
            $printer = new Printer($connector);

            // Send ESC/POS commands
            $printer->initialize();
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("✅ TEST PRINT\n");
            $printer->text("Laravel + ESC/POS\n");
            $printer->feed(1);

            $printer->setEmphasis(true);
            $printer->text("Bold Text\n");
            $printer->setEmphasis(false);

            $printer->setTextSize(2, 2);
            $printer->text("Large Text\n");
            $printer->setTextSize(1, 1);

            $printer->feed(2);
            $printer->cut();

            // Close printer connection
            $printer->close();

            // Success response
            return response()->json([
                'success' => true,
                'message' => 'Test print sent to printer!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to print',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}