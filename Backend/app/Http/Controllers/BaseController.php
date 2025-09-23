<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;

abstract class BaseController extends Controller
{
    /**
     * Check if user is authenticated
     */
    protected function requireAuth(): ?object
    {
        $user = Auth::user();
        if (!$user) {
            $this->unauthorizedResponse('Authentication required');
            return null;
        }
        return $user;
    }

    /**
     * Check if user has account manager role
     */
    protected function requireAccountManager(): ?object
    {
        $user = $this->requireAuth();
        if (!$user) {
            return null;
        }

        if ($user->role->name !== 'account_manager') {
            $this->forbiddenResponse('Access denied. Only account managers can perform this action.');
            return null;
        }

        return $user;
    }

    /**
     * Check if user has snack manager or operation role
     */
    protected function requireSnackManagerOrOperation(): ?object
    {
        $user = $this->requireAuth();
        if (!$user) {
            return null;
        }

        if (!in_array($user->role->name, ['snack_manager', 'operation'])) {
            $this->forbiddenResponse('Access denied. Only snack managers and operations can perform this action.');
            return null;
        }

        return $user;
    }

    /**
     * Check if user has snack manager role
     */
    protected function requireSnackManager(): ?object
    {
        $user = $this->requireAuth();
        if (!$user) {
            return null;
        }

        if ($user->role->name !== 'snack_manager') {
            $this->forbiddenResponse('Access denied. Only snack managers can perform this action.');
            return null;
        }

        return $user;
    }

    /**
     * Check if user has specific role
     */
    protected function requireRole(string|array $roles): ?object
    {
        $user = $this->requireAuth();
        if (!$user) {
            return null;
        }

        $allowedRoles = is_array($roles) ? $roles : [$roles];

        if (!in_array($user->role->name, $allowedRoles)) {
            $roleList = implode(', ', $allowedRoles);
            $this->forbiddenResponse("Access denied. Only {$roleList} can perform this action.");
            return null;
        }

        return $user;
    }

    /**
     * Standard success response
     */
    protected function successResponse(string $message = 'Success', $data = [], int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $statusCode);
    }

    /**
     * Standard error response
     */
    protected function errorResponse(string $message = 'Error occurred', $data = [], int $statusCode = 500): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => $data
        ], $statusCode);
    }

    /**
     * Unauthorized response (401)
     */
    protected function unauthorizedResponse(string $message = 'Authentication required'): JsonResponse
    {
        return $this->errorResponse($message, [], 401);
    }

    /**
     * Forbidden response (403)
     */
    protected function forbiddenResponse(string $message = 'Access denied'): JsonResponse
    {
        return $this->errorResponse($message, [], 403);
    }

    /**
     * Not found response (404)
     */
    protected function notFoundResponse(string $message = 'Resource not found'): JsonResponse
    {
        return $this->errorResponse($message, [], 404);
    }

    /**
     * Validation error response (422)
     */
    protected function validationErrorResponse(string $message = 'Validation failed', $errors = []): JsonResponse
    {
        return $this->errorResponse($message, $errors, 422);
    }

    /**
     * Handle validation exceptions consistently
     */
    protected function handleValidationException(ValidationException $e, string $defaultMessage = 'Validation failed'): JsonResponse
    {
        $errors = $e->errors();
        $message = $defaultMessage . ': ';

        if (isset($errors['money_pool_settings'])) {
            $message .= implode(', ', $errors['money_pool_settings']);
        } else {
            $message .= 'Unknown validation error';
        }

        return $this->validationErrorResponse($message, $errors);
    }

    /**
     * Handle general exceptions consistently
     */
    protected function handleException(\Exception $e, string $defaultMessage = 'An error occurred'): JsonResponse
    {
        return $this->errorResponse($defaultMessage . ': ' . $e->getMessage(), [], 500);
    }

    /**
     * Standard resource collection response
     */
    protected function resourceCollectionResponse($collection, string $message = 'Data retrieved successfully'): JsonResponse
    {
        return $this->successResponse($message, $collection);
    }

    /**
     * Standard resource response
     */
    protected function resourceResponse($resource, string $message = 'Data retrieved successfully'): JsonResponse
    {
        return $this->successResponse($message, $resource);
    }

    /**
     * Standard created response (201)
     */
    protected function createdResponse($data, string $message = 'Resource created successfully'): JsonResponse
    {
        return $this->successResponse($message, $data, 201);
    }

    /**
     * Standard updated response
     */
    protected function updatedResponse($data, string $message = 'Resource updated successfully'): JsonResponse
    {
        return $this->successResponse($message, $data);
    }

    /**
     * Standard deleted response
     */
    protected function deletedResponse(string $message = 'Resource deleted successfully'): JsonResponse
    {
        return $this->successResponse($message, []);
    }

    /**
     * No content response (204)
     */
    protected function noContentResponse()
    {
        return response()->noContent();
    }

    /**
     * Execute a closure with standard exception handling
     */
    protected function executeWithExceptionHandling(callable $callback, string $errorMessage = 'An error occurred'): JsonResponse
    {
        try {
            return $callback();
        } catch (ValidationException $e) {
            return $this->handleValidationException($e);
        } catch (\Exception $e) {
            return $this->handleException($e, $errorMessage);
        }
    }

    /**
     * Execute a closure with authorization check and exception handling
     */
    protected function executeWithAuth(callable $callback, string|array $requiredRole = null, string $errorMessage = 'An error occurred'): JsonResponse
    {
        return $this->executeWithExceptionHandling(function () use ($callback, $requiredRole) {
            if ($requiredRole) {
                $user = $this->requireRole($requiredRole);
                if (!$user) {
                    return null; // Response already sent by requireRole
                }
            } else {
                $user = $this->requireAuth();
                if (!$user) {
                    return null; // Response already sent by requireAuth
                }
            }

            return $callback($user);
        }, $errorMessage);
    }
}
