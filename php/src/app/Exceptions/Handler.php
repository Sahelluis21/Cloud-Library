<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A lista de tipos de exceções que não devem ser reportadas.
     */
    protected $dontReport = [
        //
    ];

    /**
     * A lista de entradas que não devem ser exibidas em validações.
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Reporta ou loga uma exceção.
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Renderiza uma exceção para uma resposta HTTP.
     */
    public function render($request, Throwable $exception)
    {
        return parent::render($request, $exception);
    }
}
