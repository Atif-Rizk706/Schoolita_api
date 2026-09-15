<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubscriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'subject_teacher_id' => 'required|exists:subject_teacher,id',
            'price' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:10',
            'payment_method' => 'nullable|string|max:50',
            'status' => 'nullable|string|in:active,pending,expired,cancelled',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date',
            'notes' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'الطالب مطلوب / Student is required.',
            'user_id.exists' => 'الطالب المختار غير موجود / Selected student does not exist.',
            'subject_teacher_id.required' => 'المادة والمعلم مطلوبان / Subject teacher is required.',
            'subject_teacher_id.exists' => 'المادة والمعلم غير موجودان / Selected subject teacher does not exist.',
            'price.numeric' => 'مبلغ الاشتراك يجب أن يكون رقماً / Subscription price must be a number.',
        ];
    }
}
