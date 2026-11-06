<?php

// include all general purpose helper functions
foreach (glob(__DIR__ . '/vocomi/*.php') as $file) {
    require_once $file;
}

// include all app specific helper functions
foreach (glob(__DIR__ . '/app-specific/*.php') as $file) {
    require_once $file;
}
