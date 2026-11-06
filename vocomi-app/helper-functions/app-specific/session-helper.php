<?php

function session_outlet()
{
    return auth()->user()->currentSessionOutlet();
}

function session_outlet_id()
{
    return isset(auth()->user()->currentSessionOutlet()->outlet_id) ? auth()->user()->currentSessionOutlet()->outlet_id : 1;
}
