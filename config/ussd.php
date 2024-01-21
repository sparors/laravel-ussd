<?php

return [

    /*
    |--------------------------------------------------------------------------
    | USSD Namespace
    |--------------------------------------------------------------------------
    |
    | This sets the root namespace for USSD component classes.
    |
    */

    'namespace' => env('USSD_NAMESPACE', 'App\Ussd'),


     /*
    |--------------------------------------------------------------------------
    | Record Store
    |--------------------------------------------------------------------------
    |
    | This sets the cache store to be used by USSD Record.
    |
    */

    'record_store' => env('USSD_STORE'),

];
