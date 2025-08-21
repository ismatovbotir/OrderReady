<?php

namespace App\Livewire;

use Carbon\Carbon;
use Livewire\Component;
use App\Models\Order as OrderModel;

class Order extends Component
{
    public $newOrders=[];
    public $readyOrders=[];

    public function mount(){
        $this->updateBoard();

    }

    public function updateBoard(){
        
        $this->newOrders=OrderModel::whereDate('created_at',Carbon::today())->where('status','new')->orderBy('id','desc')->get();
        $this->readyOrders=OrderModel::whereDate('created_at',Carbon::today())->where('status','ready')->orderBy('updated_at','desc')->get();

    }
    public function render()
    {

        return view('livewire.order');
    }
}
