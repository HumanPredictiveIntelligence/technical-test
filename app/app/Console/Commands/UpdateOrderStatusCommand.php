<?php

namespace App\Console\Commands;

use App\Services\OrderProcessor;
use Illuminate\Console\Command;

class UpdateOrderStatusCommand extends Command
{
    protected $signature = 'order:update {order_id} {status}';

    protected $description = 'Update status of an order.';


    public function __construct(private OrderProcessor $orderProcessor)
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $orderId = $this->input->getArgument('order_id');
        $status = $this->input->getArgument('status');

        $this->orderProcessor->updateOrderStatus($orderId, $status);
    }
}
