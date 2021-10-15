<?php

return [

    /*
    |--------------------------------------------------------------------------
    | State Class Namespace
    |--------------------------------------------------------------------------
    |
    | This value sets the root namespace for Ussd State component classes in
    | your application.
    |
    */

    'state_namespace' => env('USSD_STATE_NS', 'App\\Http\\Ussd\\States'),

    /*
    |--------------------------------------------------------------------------
    | Action Class Namespace
    |--------------------------------------------------------------------------
    |
    | This value sets the root namespace for Ussd Action component classes in
    | your application.
    |
    */

    'action_namespace' => env('USSD_ACTION_NS', 'App\\Http\\Ussd\\Actions'),

     /*
    |--------------------------------------------------------------------------
    | Store
    |--------------------------------------------------------------------------
    |
    | This value sets the default store to use for the ussd record.
    | The store can be found in your cache stores config
    |
    */

    'cache_store' => env('USSD_STORE', null),


    /*
    |--------------------------------------------------------------------------
    | Time to live
    |--------------------------------------------------------------------------
    |
    | This value sets the default for how long the record values are to
    | be cached in your application when not specified.
    |
    */

    'cache_ttl' => env('USSD_TTL', null),

    /*
    |--------------------------------------------------------------------------
    | Default value
    |--------------------------------------------------------------------------
    |
    | This value return the default store value when a given cache key
    | is not found
    |
    */

    'cache_default' => env('USSD_DEFAULT_VALUE', null),
];
