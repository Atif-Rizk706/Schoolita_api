<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponseTrait
{
    /**
     * Return a success JSON response.
     */
    protected function successResponse($data = null, ?string $message = null, int $code = 200): JsonResponse
    {
        $payload = [
            'success' => true,
            'message' => $message,
        ];

        if ($data instanceof \Illuminate\Http\Resources\Json\AnonymousResourceCollection && $data->resource instanceof \Illuminate\Contracts\Pagination\Paginator) {
            $responseData = $data->response()->getData(true);
            $payload['data'] = $responseData['data'] ?? [];
            $payload['links'] = $responseData['links'] ?? null;
            $payload['meta'] = $responseData['meta'] ?? null;
        } else {
            $payload['data'] = $data;
        }

        return response()->json($payload, $code);
    }

    /**
     * Return a created (201) JSON response.
     */
    protected function createdResponse($data = null, ?string $message = 'تم الإنشاء بنجاح / Created successfully'): JsonResponse
    {
        return $this->successResponse($data, $message, 201);
    }

    /**
     * Return an error JSON response.
     */
    protected function errorResponse(string $message, int $code = 400, $errors = null): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }

    /**
     * Return a 404 Not Found response.
     */
    protected function notFoundResponse(string $message = 'المورد غير موجود / Resource not found'): JsonResponse
    {
        return $this->errorResponse($message, 404);
    }

    /**
     * Return a 401 Unauthorized response.
     */
    protected function unauthorizedResponse(string $message = 'غير مصرح بالدخول / Unauthorized'): JsonResponse
    {
        return $this->errorResponse($message, 401);
    }

    /**
     * Return a 403 Forbidden response.
     */
    protected function forbiddenResponse(string $message = 'غير مصرح لك بإجراء هذه العملية / Forbidden'): JsonResponse
    {
        return $this->errorResponse($message, 403);
    }

    /**
     * Return a 422 Validation Error response.
     */
    protected function validationErrorResponse($errors, string $message = 'خطأ في التحقق من البيانات / Validation Error'): JsonResponse
    {
        return $this->errorResponse($message, 422, $errors);
    }
}
