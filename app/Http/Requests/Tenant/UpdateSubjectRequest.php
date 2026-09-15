<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSubjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'grade_id'           => 'sometimes|required|exists:grades,id',
            'name'               => 'sometimes|required',
            'slug'               => 'nullable|string|max:150',
            'description'        => 'nullable',
            'bio'                => 'nullable',
            'icon'               => 'nullable|string|max:50',
            'color_theme'        => 'nullable|string|max:30',
            'lessons_count'      => 'nullable|integer',
            'subscription_price' => 'nullable|numeric|min:0',
            'is_active'          => 'nullable|boolean',
            'teacher_ids'        => 'nullable|array',
            'teacher_ids.*'      => 'exists:teachers,id',

            // Learning Outcomes - replaces existing (مخرجات التعلم)
            'outcomes'           => 'nullable|array',
            'outcomes.*.title'   => 'required',
            'outcomes.*.order'   => 'nullable|integer|min:0',

            // Subject Features - replaces existing (المميزات)
            'features'           => 'nullable|array',
            'features.*.title'   => 'required',
            'features.*.order'   => 'nullable|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'grade_id.required'          => 'الصف الدراسي مطلوب / Grade is required.',
            'grade_id.exists'            => 'الصف الدراسي غير موجود / Selected grade does not exist.',
            'name.required'              => 'اسم المادة مطلوب / Subject name is required.',
            'subscription_price.numeric' => 'سعر الاشتراك يجب أن يكون رقماً / Subscription price must be a number.',
            'outcomes.*.title.required'  => 'عنوان مخرج التعلم مطلوب / Outcome title is required.',
            'features.*.title.required'  => 'عنوان الميزة مطلوب / Feature title is required.',
        ];
    }
}
