<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->printReceipt();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $order=$request->all();
        $order_number=$order['number'];
        $newOrder=Order::firstOrCreate(
            ['number'=>$order_number],
            ['number'=>$order_number]
        );
        return response()->json(['status'=>'ok','order'=>$newOrder->id]);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $order=$request->all();
        $type=$order['type'];
        $order_number=$order['number'];
        if ($type=='ready'){
                Order::where('number',$order_number)
                        ->update(['status'=>'ready']);
        }else{
            Order::where('number',$order_number)
            ->update(['status'=>'done']);

        }
        
        
        return response()->json(['status'=>'ok']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function printReceipt()
    {
        try {
            // Подключение к принтеру (IP и порт)
            $connector = new NetworkPrintConnector("192.168.1.199", 9100);

            $printer = new Printer($connector);

            // Тестовая печать
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $shopName="My Shop";
            $printer->text(str_repeat("=",floor((42-strlen($shopName))/2)).$shopName.str_repeat("=",floor((42-strlen($shopName))/2))."\n");
            $printer->text("==============\n");

            // Список товаров
            $items = [
                ['name' => 'Product A', 'price' => 12000],
                ['name' => 'Product B', 'price' => 8000],
                ['name' => 'Product C', 'price' => 4500],
            ];
            $printer->barcode("12312124124",Printer::BARCODE_CODE39);
            foreach ($items as $item) {
                $printer->setJustification(Printer::JUSTIFY_LEFT);
                $printer->text($item['name'] . "   " . number_format($item['price']) . " UZS\n");
            }

            $printer->text("==============\n");
            $printer->setJustification(Printer::JUSTIFY_RIGHT);
            $printer->text("Total: 24 500 UZS\n");

            $printer->feed(2);
            $printer->cut();
            $printer->close();

            echo "Receipt printed!";
        } catch (\Exception $e) {
            return "Print failed: " . $e->getMessage();
        }
    }
    private function formatLine($left, $right, $width = 42,$rep=' ')
    {
        $leftLength = strlen($left);
        $rightLength = strlen($right);
        $spaces = $width - ($leftLength + $rightLength);
        return $left . str_repeat($rep, max($spaces, 0)) . $right . "\n";
    }


    
}
