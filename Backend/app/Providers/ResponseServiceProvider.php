<?php

namespace App\Providers;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;

class ResponseServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        JsonResource::withoutWrapping();

        $this->registerErrorMacro();
        $this->registerMessageMacro();
        $this->registerUnprocessableEntityMacro();
        $this->registerUnauthorizedMacro();
        $this->registerbadRequestMacro();
        $this->registerconflictMacro();
        $this->registernotFoundMacro();
        $this->registerinternalServerErrorMacro();
    }

    private function registerErrorMacro(): void
    {
        Response::macro('error', function ($errors, $statusCode, $message = null) {
            $messages = Arr::flatten($errors);

            if (! count($messages) || ! is_string($messages[0])) {
                return 'The given data was invalid.';
            }

            $message = array_shift($messages);
            $additional = count($messages);
            if ($additional) {
                $pluralized = $additional === 1 ? 'error' : 'errors';

                $message .= " (and {$additional} more {$pluralized})";
            }

            return response()->json(
                ['message' => $message, 'errors' => $errors],
                $statusCode
            );
        });
    }

    private function registerMessageMacro(): void
    {
        Response::macro('message', function ($message, $statusCode = HttpResponse::HTTP_OK) {
            return response()->json(
                ['message' => $message],
                $statusCode
            );
        });
    }

    private function registerUnprocessableEntityMacro(): void
    {
        Response::macro('unprocessableEntity', function ($errors, $message = null) {
            $statusCode = HttpResponse::HTTP_UNPROCESSABLE_ENTITY;

            return is_array($errors)
                ? response()->error($errors, $statusCode, $message)
                : response()->message(
                    is_string($errors) ? $errors : __('default.unexpected'),
                    $statusCode
                );
        });
    }

    private function registerUnauthorizedMacro(): void
    {
        Response::macro('unauthorized', function ($errors, $message = null) {
            $statusCode = HttpResponse::HTTP_UNAUTHORIZED;

            return is_array($errors)
                ? response()->error($errors, $statusCode, $message)
                : response()->message(
                    is_string($errors) ? $errors : __('default.unexpected'),
                    $statusCode
                );
        });
    }

    private function registerbadRequestMacro(): void
    {
        Response::macro('badRequest', function ($errors, $message = null) {
            $statusCode = HttpResponse::HTTP_BAD_REQUEST;

            return is_array($errors)
                ? response()->error($errors, $statusCode, $message)
                : response()->message(
                    is_string($errors) ? $errors : __('default.unexpected'),
                    $statusCode
                );
        });
    }

    private function registerconflictMacro(): void
    {
        Response::macro('conflict', function ($errors, $message = null) {
            $statusCode = HttpResponse::HTTP_CONFLICT;

            return is_array($errors)
                ? response()->error($errors, $statusCode, $message)
                : response()->message(
                    is_string($errors) ? $errors : __('default.unexpected'),
                    $statusCode
                );
        });
    }

    private function registernotFoundMacro(): void
    {
        Response::macro('notFound', function ($message = null) {
            return response()->json(
                ['message' => $message ?? __('Resource not found.')],
                HttpResponse::HTTP_NOT_FOUND
            );
        });
    }

    private function registerinternalServerErrorMacro(): void
    {
        Response::macro('internalServerError', function ($message = null) {
            return response()->json(
                ['message' => $message ?? __('default.unexpected')],
                HttpResponse::HTTP_INTERNAL_SERVER_ERROR
            );
        });
    }
}
