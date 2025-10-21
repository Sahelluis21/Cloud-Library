<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Caminhos das Views
    |--------------------------------------------------------------------------
    |
    | Aqui você especifica os diretórios onde as views Blade estão localizadas.
    | Normalmente é o diretório "resources/views".
    |
    */

    'paths' => [
        resource_path('views'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Caminho do Cache das Views Compiladas
    |--------------------------------------------------------------------------
    |
    | Este diretório armazena as versões compiladas das views Blade para
    | melhorar o desempenho da aplicação.
    |
    */

    'compiled' => env(
        'VIEW_COMPILED_PATH',
        realpath(storage_path('framework/views'))
    ),

];
