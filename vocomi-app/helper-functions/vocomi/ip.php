<?php

function voco_real_ip()
{
    // detect cloudflare connecting IP
    return $_SERVER['HTTP_CF_CONNECTING_IP'] ?? \Request::ip();
}
