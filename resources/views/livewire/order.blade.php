<div wire:poll.5s="updateBoard" class="grid grid-cols-1 md:grid-cols-2 gap-4 p-6 h-full">

    <!-- Колонка "Готовятся" -->
    <div class="bg-white rounded-lg shadow-lg p-6 flex flex-col overflow-hidden">
        <div class="text-center text-orange-600 mb-6">
            <h2 class="text-4xl font-extrabold inline-flex items-center gap-3">
                <svg class="w-8 h-8 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6v6l4 2" />
                </svg>
                В процессе приготовления
            </h2>
        </div>

        <div class="grid grid-cols-2 gap-4 overflow-y-auto">
            @foreach($newOrders as $newOrder)
            <div class="bg-yellow-100 text-yellow-800 text-center py-4 rounded text-xl font-semibold">
                Заказ #{{$newOrder->orderNumber}}
            </div>
            @endforeach


        </div>
    </div>

    <!-- Колонка "Готовы" -->
    <div class="bg-white rounded-lg shadow-lg p-6 flex flex-col overflow-hidden">
        <div class="text-center text-green-600 mb-6">
            <h2 class="text-4xl font-extrabold inline-flex items-center gap-3">
                <svg class="w-8 h-8 text-green-600 animate-bounce" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M5 13l4 4L19 7" />
                </svg>
                Готово к выдаче
            </h2>
        </div>

        <div class="grid grid-cols-2 gap-4 overflow-y-auto">
            @foreach($readyOrders as $readyOrder)
            <div class="bg-green-100 text-green-800 text-center py-4 rounded text-xl font-semibold animate-pulse">
                Заказ #{{$readyOrder->orderNumber}}
            </div>

            @endforeach
        </div>
    </div>

</div>