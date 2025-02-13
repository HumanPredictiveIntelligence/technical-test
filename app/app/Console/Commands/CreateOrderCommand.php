<?php

namespace App\Console\Commands;

use App\Services\OrderProcessor;
use Illuminate\Console\Command;

class CreateOrderCommand extends Command
{
    protected $signature = 'order:create {email} {products}';

    protected $description = 'Create a new order. Provide an email for the customer and a list of products formated as follow: sku:quantity separated by ";"';


    public function __construct(private OrderProcessor $orderProcessor)
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $email = $this->input->getArgument('email');
        $products = explode(',', $this->input->getArgument('products'));

        $items = [];
        foreach ($products as $product) {
            $item = explode(':', $product);

            $items[] = [
                'sku' => $item[0],
                'quantity' => $item[1],
            ];
        }

        $this->orderProcessor->processOrder([
            'customer_email' => $email
        ], $items);
    }
}
