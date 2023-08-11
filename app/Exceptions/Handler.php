<?php

namespace App\Exceptions;

use App\Traits\JsonResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    use JsonResponse;
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        // $this->renderable(function (NotFoundHttpException $e, Request $request) {
        //     // dd($request->is('api/*'));
        //     if ($request->is('api/*')) {
        //         return response()->json([
        //             'message' => 'Record not found.'
        //         ], 404);
        //     }
        // });

        $this->renderable(function (NotFoundHttpException $e, $request) {
            if ($request->is('api/*')) {
                return $this->errorResponse('Url not found',404);
            }
        });

        $this->renderable(function (MethodNotAllowedHttpException $e, $request) {
            if ($request->is('api/*')) {
                return $this->errorResponse('Method Not Allowed', 405);
            }
        });

        $this->renderable(function (ValidationException $e, $request) {
            if ($request->is('api/*')) {
                return $this->errorResponse($e->getMessage(), 422, $e->validator->errors());
            }
        });

        $this->renderable(function (QueryException $e, $request) {
            if ($request->is('api/*')) {
                $error = array(
                    'exception' => get_class($e),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTrace()
                );
                return $this->errorResponse($e->getMessage(), 500, $error);
            }
        });

        $this->renderable(function (UnauthorizedHttpException $e, $request) {
            if ($request->is('api/*')) {
                return $this->errorResponse('User tidak dikenal / ' . $e->getMessage(), 401);
            }
        });

        $this->renderable(function (ConnectionException $e, $request) {
            if ($request->is('api/*')) {
                $error = array(
                    'exception' => get_class($e),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTrace()
                );
                return $this->errorResponse($e->getMessage(), 408, $error);
            }
        });
    }

    // public function render($request, Exception $exception)
    // {
    //     if ($request->is('api/*') && $request->wantsJson())
    //     {
    //         // return $this->handleApiException($request, $exception);
    //         if($exception instanceof NotFoundHttpException)
    //         {
    //             return $this->errorResponse($exception->getMessage(),404);
    //         }
    //     }

    //     return parent::render($request, $exception);;
    // }

    // private function handleApiException($request, Exception $exception)
    // {
    //     $exception = $this->prepareException($exception);
    //     return $exception;
    // }
}
