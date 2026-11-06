<?php

function percentage_amount($amount, $percent, $decimals = 2)
{
    return round($amount * $percent / 100, $decimals);
}

function percentage_increase($amount, $percent, $decimals = 2)
{
    return round($amount + percentage_amount($amount, $percent), $decimals);
}
