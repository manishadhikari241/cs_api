<?php

namespace App\Exceptions;

use App\Constants\ErrorCodes;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof APIValidationException) {
            $message = array_values($exception->errors())[0][0];
            return respondError(ErrorCodes::VALIDATION_FAILED, Response::HTTP_UNPROCESSABLE_ENTITY, $message);
        } else if ($exception instanceof ModelNotFoundException) {
            return respondError(ErrorCodes::NOT_FOUND, Response::HTTP_NOT_FOUND, 'Object not found');
        } else if ($exception instanceof QueryException) {
            return respondError(ErrorCodes::NOT_FOUND, Response::HTTP_NOT_FOUND, $exception->getMessage());
        }

        return parent::render($request, $exception);
    }
}
