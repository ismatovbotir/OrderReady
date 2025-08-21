<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Carbon\Carbon;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        dd($last_order=Order::whereDate('created_at',Carbon::today())->latest()->first());

    
        //$this->printReceipt();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $order=$request->all();
        $user=$order['user'];
        $items=$order['items'];
        $last_order=Order::whereDate('created_at',Carbon::today())->latest()->first();
        
        if($last_order==null){
            $current_order=1;
        }else{
            $current_order=$last_order->orderNumber+1;
        }

        $order_number=$order['number'];
        $newOrder=Order::Create(
            [
                'number'=>$order_number,
                'orderNumber'=>$current_order,
                'user'=>$user
                

            ]
        );
        $newOrder->statuses()->create([
            'status'=>'new'
        ]);
        $newOrder->items()->createMany(
            $items ??[]
        );
        $this->printReceipt($newOrder->id);
        return response()->json([
                                    'status'=>'ok',
                                    'message'=>[
                                        'order'=>$newOrder->id,
                                        'orderNumber'=>$newOrder->orderNumber  
                                    ]
                                ]);

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
        $msg="fail";
        if ($type=='ready'){
                $order=Order::where([
                    ['id',$id],
                    ['status','new']
                ])->first();
                if($order!=null){
                    $order->update(['status'=>'ready']);
                    $order->statuses()->create(['status'=>'ready']);
                    $msg="done";
                }
                //->update(['status'=>'ready']);
        }else{

            $order=Order::where([
                ['id',$id],
                ['status','ready']
            ])->first();
            if($order!=null){
                $order->update(['status'=>'done']);
                $order->statuses()->create(['status'=>'done']);
                $msg="done";
            }         

        }
        
        return response()->json(['status'=>'ok','message'=>$msg]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function printReceipt($id)
    {
        $order=Order::where('id',$id)->with('items')->first();
        try {
            // Подключение к принтеру (IP и порт)
            $connector = new NetworkPrintConnector("192.168.1.199", 9100);

            $printer = new Printer($connector);

            // Тестовая печать
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $shopName=" Dehqon Bakery ";
            $printer->setTextSize(2,2);
            $printer->text($shopName);
            //$printer->text(str_repeat("*",floor((42-strlen($shopName))/2)).$shopName.str_repeat("=",floor((42-strlen($shopName))/2))."\n");
            $printer->text($order->orderNumber);
            $printer->setTextSize(1,1);

            // Список товаров
         
            $printer->barcode($order->id,Printer::BARCODE_CODE39);
            foreach ($order->items() as $item) {
               // $printer->setJustification(Printer::JUSTIFY_LEFT);
                $printer->text($this->formatLine($item['item'], $item['qty'],42,'_') . "\n");
            }

            $printer->text(str_repeat('=',42)."\n");
            
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
