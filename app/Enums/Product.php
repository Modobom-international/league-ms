<?php

namespace App\Enums;

final class Product
{
    const STATUS = [
        'new', 'used',
    ];

    const CONDITION = [
        'new', 'used',
    ];

    const STATUS_POST_PENDING = "pending";
    const STATUS_POST_ACCEPT = "accepted";
    const STATUS_POST_HIDDEN = "hidden";
    const STATUS_POST_ACTIVE = "active";
    const STATUS_POST_REJECT = "reject";
    const STATUS_POST_CONFIRMED = "confirmed";
    const PRODUCT_SOLD = "sold";
}
