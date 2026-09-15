<?php

namespace App\Http\Controllers\Api\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContactMessageResource;
use App\Models\ContactMessage;
use App\Services\TenantManager;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TenantContactController extends Controller
{
    use ApiResponseTrait;

    public function __construct(protected TenantManager $tenantManager)
    {
    }

    /**
     * Display listing of contact messages.
     */
    public function index(Request $request): JsonResponse
    {
        $tenant = $this->tenantManager->getTenant() ?? $request->user()->tenant;

        $messages = ContactMessage::where('tenant_id', $tenant->id)
            ->latest()
            ->paginate($request->input('per_page', 15));

        return $this->successResponse(
            ContactMessageResource::collection($messages),
            'تم جلب الرسائل بنجاح / Contact messages retrieved successfully'
        );
    }

    /**
     * Display single contact message.
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $tenant = $this->tenantManager->getTenant() ?? $request->user()->tenant;
        $message = ContactMessage::where('tenant_id', $tenant->id)->findOrFail($id);

        return $this->successResponse(new ContactMessageResource($message));
    }

    /**
     * Delete contact message.
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $tenant = $this->tenantManager->getTenant() ?? $request->user()->tenant;
        $message = ContactMessage::where('tenant_id', $tenant->id)->findOrFail($id);
        $message->delete();

        return $this->successResponse(null, 'تم حذف الرسالة بنجاح / Contact message deleted successfully');
    }
}
