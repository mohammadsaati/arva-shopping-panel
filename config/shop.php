<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Unpaid order expiry
    |--------------------------------------------------------------------------
    |
    | Orders that stay unpaid longer than this are cancelled and inventory is
    | restored. Processed by ExpireUnpaidOrdersJob via Horizon workers.
    |
    */

    'unpaid_order_expire_hours' => (int) env('SHOP_UNPAID_ORDER_EXPIRE_HOURS', 48),

];
