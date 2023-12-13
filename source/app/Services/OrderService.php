<?php
declare(strict_types=1);

namespace App\Services;

use App\Exceptions\OrderException;
use App\Models\Order;
use App\Models\Transaction;
use Carbon\Carbon;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\DB;
use League\Flysystem\Exception;
use Throwable;

class OrderService
{
    private CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }
    /**
     * @throws OrderException
     * @throws Throwable
     */
    public function orderProducts(array $requests)
    {
        $carts = $this->cartService->getContents();
        if ($carts->isEmpty()) {
            throw new OrderException('Please order at least one product');
        }

        DB::beginTransaction();
        try {
            $idLoggedIn = auth()->id();
            $requests['tst_user_id'] = $idLoggedIn;
            $requests['tst_total_money'] = str_replace(',', '', Cart::subtotal(0));
            $requests['created_at'] = Carbon::now();

            $transactionID = Transaction::insertGetId($requests);
            $carts = $this->cartService->getContents();
            $this->saveOrder($carts, $transactionID);

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();

            throw new OrderException('Unable to order products. Reason: ' . $e->getMessage());
        }
    }

    private function saveOrder($carts, int $transactionId): void
    {

        foreach ($carts as $item) {
            // Lưu chi tiết đơn hàng
            $order = $this->buildOrder($item, $transactionId);
            Order::create($order);

            //Tăng pay ( số lượt mua của sản phẩm dó)
            DB::table('products')
                ->where('id', $item->id)
                ->increment("pro_pay");
        }
    }

    private function buildOrder($order, int $transactionId): array
    {
        return [
            'od_transaction_id' => $transactionId,
            'od_product_id'     => $order->id,
            'od_sale'           => $order->options->sale,
            'od_qty'            => $order->qty,
            'od_price'          => $order->price,
        ];
    }

    private function buildTransaction(array $request): array
    {

    }
}
