<?php

/**
 * Apply a rupee discount on one service or product.
 * GST stays on the original price, so the refund equals the discount.
 *
 * Preview:
 *   php scripts/apply_service_discount.php BSA853 --service="BOTOX FULL HAIR" --amount=2000
 *
 * Apply:
 *   php scripts/apply_service_discount.php BSA853 --service="BOTOX FULL HAIR" --amount=2000 --execute
 *
 * A product line uses --item or --item-id instead of --service.
 * A paid invoice with several payments needs --payment-id for the refund row.
 */

$root = dirname(__DIR__);
$artisan = $root . DIRECTORY_SEPARATOR . 'artisan';
$args = array_slice($argv, 1);

$command = array_merge(
    [PHP_BINARY, $artisan, 'booking:apply-service-discount'],
    $args
);

$process = proc_open($command, [0 => STDIN, 1 => STDOUT, 2 => STDERR], $pipes, $root);

if (! is_resource($process)) {
    fwrite(STDERR, "Could not start the discount command.\n");
    exit(1);
}

exit(proc_close($process));
