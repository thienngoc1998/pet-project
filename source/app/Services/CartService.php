<?php
declare(strict_types=1);

namespace App\Services;

use Gloudemans\Shoppingcart\Facades\Cart;

class CartService
{
    /**
     * @return mixed
     */
    public function getContents()
    {
        return Cart::content();
    }

    public function destroyCart(): void
    {
        Cart::destroy();
    }
}
