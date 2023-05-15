<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for all database work. Of course
    | you may use many connections at once using the Database library.
    |
    */

    'default' => env('DB_CONNECTION', 'mysql'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the database connections setup for your application.
    | Of course, examples of configuring each database platform that is
    | supported by Laravel is shown below to make development simple.
    |
    |
    | All database work in Laravel is done through the PHP PDO facilities
    | so make sure you have the driver for your particular database of
    | choice installed on your machine before you begin development.
    |
    */

    'connections' => [

        'sqlite' => [
            'driver' => 'sqlite',
            'url' => env('DATABASE_URL'),
            'database' => env('DB_DATABASE', database_path('database.sqlite')),
            'prefix' => '',
            'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
        ],

        'mysql' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'pgsql' => [
            'driver' => 'pgsql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => 'public',
            'sslmode' => 'prefer',
        ],

        'sqlsrv' => [
            'driver' => 'sqlsrv',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', 'localhost'),
            'port' => env('DB_PORT', '1433'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            // 'encrypt' => env('DB_ENCRYPT', 'yes'),
            'trust_server_certificate' => env('DB_TRUST_SERVER_CERTIFICATE', 'false'),
        ],

        'mysql2' => [
            'driver' => 'mysql',
            'host' => '10.109.52.2',
            'port' => '3306',
            'database' => 'kitto',
            'username' => 'ympimis',
            'password' => 'ympimis',
            'unix_socket' => '',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],
        
        'kitto' => [
            'driver' => 'mysql',
            'host' => '10.109.52.2',
            'port' => '3306',
            'database' => 'kitto',
            'username' => 'ympimis',
            'password' => 'ympimis',
            'unix_socket' => '',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],

         'winds' => [
            'driver' => 'mysql',
            'host' => '10.109.52.2',
            'port' => '3306',
            'database' => 'winds',
            'username' => 'ympimis',
            'password' => 'ympimis',
            'unix_socket' => '',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],

        'mysql3' => [
            'driver' => 'mysql',
            'host' => '10.109.52.2',
            'port' => '3306',
            'database' => 'ftm',
            'username' => 'ympimis',
            'password' => 'ympimis',
            'unix_socket' => '',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],

        'ympimis_2' => [
            'driver' => 'mysql',
            'host' => '10.109.52.5',
            'port' => '3306',
            'database' => 'ympimis_2',
            'username' => 'ympimis',
            'password' => 'ympimis',
            'unix_socket' => '',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],

        'ymes' => [
            'driver' => 'pgsql',
            'host' => '10.109.52.13',
            'port' => '5432',
            'database' => 'ymesympi',
            'username' => 'iot',
            'password' => 'yamahaiot',
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'schema' => 'telas'
        ],

        'omron1' => [
            'driver' => 'mysql',
            'host' => '172.17.128.68',
            'port' => '3306',
            'database' => 'd6t',
            'username' => 'ympimis',
            'password' => 'ympimis',
            'unix_socket' => '',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],

        'omron2' => [
            'driver' => 'mysql',
            'host' => '172.17.128.152',
            'port' => '3306',
            'database' => 'd6t',
            'username' => 'ympimis',
            'password' => 'ympimis',
            'unix_socket' => '',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],

        'omron3' => [
            'driver' => 'mysql',
            'host' => '172.17.128.153',
            'port' => '3306',
            'database' => 'd6t',
            'username' => 'ympimis',
            'password' => 'ympimis',
            'unix_socket' => '',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],

        'mobile' => [
            'driver' => 'mysql',
            'host' => '10.109.52.2',
            'port' => '3306',
            'database' => 'miraimobile',
            'username' => 'ympimis',
            'password' => 'ympimis',
            'unix_socket' => '',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],

        'ympimis_online' => [
            'driver' => 'mysql',
            'host' => '10.109.52.2',
            'port' => '3306',
            'database' => 'ympimis_online',
            'username' => 'ympimis',
            'password' => 'ympimis',
            'unix_socket' => '',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
            'modes'  => [
                // 'ONLY_FULL_GROUP_BY',
                'STRICT_TRANS_TABLES',
                'NO_ZERO_IN_DATE',
                'NO_ZERO_DATE',
                'ERROR_FOR_DIVISION_BY_ZERO',
                'NO_ENGINE_SUBSTITUTION',
            ],
        ],

        'digital_kanban' => [ 
            'driver' => 'mysql',
            'host' => '10.109.52.2',
            'port' => '3306',
            'database' => 'db_ympi_rack',
            'username' => 'adminRack',
            'password' => 'rack2019',
            'unix_socket' => '',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],

        'clinic' => [ 
            'driver' => 'mysql',
            'host' => '10.109.52.2',
            'port' => '3306',
            'database' => 'ympi_klinik',
            'username' => 'adminYMPI',
            'password' => 'techsupport',
            'unix_socket' => '',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],

        'rfid' => [ 
            'driver' => 'mysql',
            'host' => '10.109.52.2',
            'port' => '3306',
            'database' => 'ympirfid',
            'username' => 'ympimis',
            'password' => 'ympimis',
            'unix_socket' => '',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],

        'pantry' => [ 
            'driver' => 'mysql',
            'host' => '10.109.52.2',
            'port' => '3306',
            'database' => 'ympipantry',
            'username' => 'ympimis',
            'password' => 'ympimis',
            'unix_socket' => '',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],

        'sunfish' => [ 
            'driver' => 'sqlsrv',
            'host' => '10.109.52.9',
            'port' => '1438',
            'database' => 'dbSF_YAMAHAHRIS',
            'username' => 'ympiview',
            'password' => '?ympisf2020!',
            'charset' => 'utf8',
            'prefix' => '',
            'trust_server_certificate' => true,
        ],

        'welding' => [
            'driver' => 'mysql',
            'host' => '10.109.52.2',
            'port' => '3306',
            'database' => 'soldering_db',
            'username' => 'ympimis',
            'password' => 'ympimis',
            'unix_socket' => '',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],

        'welding_controller' => [
            'driver' => 'mysql',
            'host' => '10.109.96.11',
            'port' => '3308',
            'database' => 'soldering_db',
            'username' => 'ympimis',
            'password' => 'ympimis',
            'unix_socket' => '',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],


        'mirai_mobile' => [
            'driver' => 'mysqli',
            'host' => 'nymeria.id.rapidplex.com',
            'port' => '3306',
            'database' => 'ympicoid_miraimobile',
            'username' => 'ympicoid_miraimobile',
            'password' => 'miraimobile',
            'unix_socket' => '',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],

        'ftm' => [
            'driver' => 'mysql',
            'host' => '10.109.33.80',
            'port' => '2525',
            'database' => 'ftm',
            'username' => 'root',
            'password' => '',
            'unix_socket' => '',
            'charset' => 'latin1',  
            'collation' => 'latin1_swedish_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],

        'ympimis_sync' => [
            'driver' => 'mysql',
            'host' => 'localhost',
            'port' => '3306',
            'database' => 'ympimis_sync',
            'username' => 'root',
            'password' => '',
            'unix_socket' => '',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],


         'tpro' => [
            'driver' => 'mysql',
            'host' => '10.109.130.1',
            'port' => '3307',
            'database' => 'tpro_db',
            'username' => 'root',
            'password' => 'admin',
            'unix_socket' => '',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run in the database.
    |
    */

    'migrations' => 'migrations',

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis is an open source, fast, and advanced key-value store that also
    | provides a richer body of commands than a typical key-value system
    | such as APC or Memcached. Laravel makes it easy to dig right in.
    |
    */

    'redis' => [

        'client' => env('REDIS_CLIENT', 'phpredis'),

        'options' => [
            'cluster' => env('REDIS_CLUSTER', 'redis'),
            'prefix' => env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_database_'),
        ],

        'default' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'username' => env('REDIS_USERNAME'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_DB', '0'),
        ],

        'cache' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'username' => env('REDIS_USERNAME'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_CACHE_DB', '1'),
        ],

    ],

];
