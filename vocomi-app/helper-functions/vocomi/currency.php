<?php

function voco_price_format($price = 0)
{
    if (empty($price)) {
        $price = '0.00';
    }

    return number_format((float) $price, 2, '.', '');
}
