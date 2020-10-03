<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
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
     * @param Throwable $exception
     * @return void
     *
     * @throws Exception
     * @throws Throwable
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     * @param Throwable $exception
     * @return Response
     *
     * @throws Throwable
     */
    public function render($request, Throwable $exception)
    {

        $handle = $this->handleException($request, $exception);
        if ($handle)
            return $handle;

        if (app()->bound('sentry') && $this->shouldReport($exception)) {
            app('sentry')->captureException($exception);
        }

        return parent::render($request, $exception);
    }

    /**
     * Check if a exception is handled and return a custom view.
     *
     * @param Request $request
     * @param Throwable $exception
     * @return Response
     */
    private function handleException($request, Throwable $exception)
    {
        switch (get_class($exception)) {
            case 'MongoDB\Driver\Exception\AuthenticationException':
                return response()->view('errors.custom', [
                    'title' => 'Base de donnée indisponible',
                    'fa' => 'database',
                    'message' => 'La base de donnée est indisponible. Nous vous invitons à réessayer dans quelques instants.',
                    'redirect' => false,
                ]);
            case 'GuzzleHttp\Exception\ClientException':
                if (!$request->get('error') === 'access_denied') break;
                return response()->view('errors.custom', [
                    'title' => 'OAuth annulé',
                    'fa' => 'sign-in-alt',
                    'message' => 'Vous avez annulé l\'authentification via votre compte Discord. Appuyez sur le bouton ci-dessous pour recommencer l\'authentification.',
                    'redirect' => true,
                ]);
            case 'Laravel\Socialite\Two\InvalidStateException':
                return response()->view('errors.custom', [
                    'title' => 'Session invalide',
                    'fa' => 'shield-alt',
                    'message' => 'Votre requête d\'authentification n\'a pas pu être validée. Merci de réessayer et de contacter un administrateur du panel en cas de besoin.',
                    'redirect' => false,
                ]);
        }
        return null;
    }
}
