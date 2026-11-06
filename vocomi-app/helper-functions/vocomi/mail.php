<?php

/**
 * SMTP mail sending
 *
 * @param string $to_address
 * @param \Illuminate\Mail\Mailable $handler
 */
function voco_mail_to($to_address, $handler)
{
    $status = config('vocomi.settings.use_email_queue');

    if ($status) {
        \Mail::to($to_address)->queue($handler);
    } else {
        \Mail::to($to_address)->send($handler);
    }
}
