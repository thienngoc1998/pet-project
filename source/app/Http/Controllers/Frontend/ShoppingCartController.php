<?php

namespace App\Http\Controllers\Frontend;

use App\enums\ToastrEnum;
use App\Exceptions\OrderException;
use App\Http\Controllers\Controller;
use App\Http\Requests\OrderRequest;
use App\Jobs\OrderProductJob;
use App\Services\CartService;
use App\Services\OrderService;
use App\Services\ToastrBuilder\ToastrBuilder;
use App\Services\ToastrService;
use Exception;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\Product;
use Carbon\Carbon;
use App\Models\Transaction;
use App\Models\Order;
use App\Mail\TransactionSuccess;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class ShoppingCartController extends Controller
{
    private OrderService $orderService;
    private CartService $cartService;
    private ToastrBuilder $toastrBuilder;
    private ToastrService $toastrService;

    public function __construct(
        OrderService $orderService,
        CartService $cartService,
        ToastrBuilder $toastrBuilder,
        ToastrService $toastrService
    )
    {
        $this->orderService = $orderService;
        $this->cartService = $cartService;
        $this->toastrBuilder = $toastrBuilder;
        $this->toastrService = $toastrService;
    }

    public function index()
    {
        $shopping = \Cart::content();
        $viewData = [
            'title_page' => 'Danh sách giỏ hàng',
            'shopping'   => $shopping
        ];
        return view('frontend.pages.shopping.index', $viewData);
    }

    /**
     * Thêm giỏ hàng
     * */
    public function add(Request $request, $id)
    {
        $product = Product::find($id);

        //1. Kiểm tra tồn tại sản phẩm
        if (!$product) return redirect()->to('/');

        // 2. Kiểm tra số lượng sản phẩm
        if ($product->pro_number < 1) {
            //4. Thông báo
            \Session::flash('toastr', [
                'type'    => 'error',
                'message' => 'Số lượng sản phẩm không đủ'
            ]);

            return redirect()->back();
        }

        // 3. Thêm sản phẩm vào giỏ hàng
        \Cart::add([
            'id'      => $product->id,
            'name'    => $product->pro_name,
            'qty'     => 1,
            'price'   => number_price($product->pro_price, $product->pro_sale),
            'weight'  => '1',
            'options' => [
                'sale'      => $product->pro_sale,
                'price_old' => $product->pro_price,
                'image'     => $product->pro_avatar,
                'size'      => $request->size,
                'color'      => $request->color,
                'gender'      => $request->gender,
            ]
        ]);

        //4. Thông báo
        \Session::flash('toastr', [
            'type'    => 'success',
            'message' => 'Thêm giỏ hàng thành công'
        ]);

        return response([
            'size' => $request->size
        ]);
    }

    public function postPay(OrderRequest $request): RedirectResponse
    {
        try {
            $validatedRequest = $request->validated();
            $validatedRequest['tst_user_id'] = auth()->id();
            $validatedRequest['tst_total_money'] = str_replace(',', '', Cart::subtotal(0));

            $transaction = Transaction::create($validatedRequest);
            dispatch(new OrderProductJob($transaction))->onQueue('payments');

//            $this->orderService->orderProducts($validatedRequest);
//
//            $toastr = $this->toastrBuilder
//                ->setType(ToastrEnum::SUCCESS)
//                ->setMessage('Order products successfully')
//                ->build();
//
//
//            $this->cartService->destroyCart();
        } catch (OrderException $exception) {
            report($exception);

            $toastr = $this->toastrBuilder
                ->setType(ToastrEnum::ERROR)
                ->setMessage($exception->getMessage())
                ->build();
        }  finally {
            //$this->toastrService->show($toastr);

            return redirect()->back();
        }
    }

    public function update(Request $request, $id)
    {
        if ($request->ajax()) {

            //1.Lấy tham số
            $qty       = $request->qty ?? 1;
            $idProduct = $request->idProduct;
            $product   = Product::find($idProduct);

            //2. Kiểm tra tồn tại sản phẩm
            if (!$product) return response(['messages' => 'Không tồn tại sản sản phẩm cần update']);

            //3. Kiểm tra số lượng sản phẩm còn ko
            if ($product->pro_number < $qty) {
                return response([
                    'messages' => 'Số lượng cập nhật không đủ',
                    'error'    => true
                ]);
            }

            //4. Update
            \Cart::update($id, $qty);

            return response([
                'messages'   => 'Cập nhật thành công',
                'totalMoney' => \Cart::subtotal(0),
                'totalItem'  => number_format(number_price($product->pro_price, $product->pro_sale) * $qty, 0, ',', '.')
            ]);
        }
    }

    /**
     *  Xoá sản phẩm đơn hang
     * */
    public function delete(Request $request, $rowId)
    {
        if ($request->ajax())
        {
            \Cart::remove($rowId);
            return response([
                'totalMoney' => \Cart::subtotal(0),
                'type'       => 'success',
                'message'    => 'Xoá sản phẩm khỏi đơn hàng thành công'
            ]);
        }
    }
}
