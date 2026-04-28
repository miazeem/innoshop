<?php
/**
 * Copyright (c) Since 2024 InnoShop - All Rights Reserved
 *
 * @link       https://www.innoshop.com
 * @author     InnoShop <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoShop\Front\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use InnoShop\Common\Models\Order;
use InnoShop\Common\Repositories\OrderRepo;

class PaymentController extends Controller
{
    /**
     * Payment success page
     *
     * @param  Request  $request
     * @return View
     */
    public function success(Request $request)
    {
        $order = $this->resolveOrderFromRequest($request);

        return inno_view('payment.success', ['order' => $order]);
    }

    /**
     * Payment fail page
     *
     * @param  Request  $request
     * @return View
     */
    public function fail(Request $request)
    {
        $orderNumber = $request->get('order_number');
        $order       = $orderNumber ? OrderRepo::getInstance()->builder(['number' => $orderNumber])->first() : null;

        return inno_view('payment.fail', ['order' => $order]);
    }

    /**
     * Payment cancel page
     *
     * @param  Request  $request
     * @return View
     */
    public function cancel(Request $request)
    {
        $orderNumber = $request->get('order_number');
        $order       = $orderNumber ? OrderRepo::getInstance()->builder(['number' => $orderNumber])->first() : null;

        return inno_view('payment.cancel', ['order' => $order]);
    }

    /**
     * Resolve order from request params.
     * Supports: order_number, out_trade_no (format: {id}-{timestamp})
     */
    private function resolveOrderFromRequest(Request $request): ?Order
    {
        $orderNumber = $request->get('order_number');
        if ($orderNumber) {
            return OrderRepo::getInstance()->builder(['number' => $orderNumber])->first();
        }

        $outTradeNo = $request->get('out_trade_no');
        if ($outTradeNo && str_contains($outTradeNo, '-')) {
            $orderId = (int) explode('-', $outTradeNo)[0];
            if ($orderId) {
                return OrderRepo::getInstance()->builder(['id' => $orderId])->first();
            }
        }

        return null;
    }
}
