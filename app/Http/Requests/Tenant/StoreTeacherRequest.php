<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;

class StoreTeacherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required', // string or array ['ar' => '...', 'en' => '...']
            'title' => 'nullable',
            'email' => 'nullable|email|max:150',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:255',
            'avatar' => 'nullable', // file or string
            'bio' => 'nullable',
            'features' => 'nullable|array',
            'experience_years' => 'nullable|integer',
            'whatsapp_number' => 'nullable|string|max:30',
            'color_theme' => 'nullable|string|max:30',
            'is_active' => 'nullable|boolean',
            'subject_ids' => 'nullable|array',
            'subject_ids.*' => 'exists:subjects,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'اسم المعلم مطلوب / Teacher name is required.',
            'email.email' => 'صيغة البريد الإلكتروني غير صحيحة / Invalid email format.',
            'subject_ids.*.exists' => 'المادة الدراسية المختارة غير موجودة / Selected subject does not exist.',
        ];
    }
}
