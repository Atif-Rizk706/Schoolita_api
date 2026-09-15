<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStudentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $studentId = $this->route('student');

        return [
            'name' => 'sometimes|required|string|max:150',
            'email' => 'sometimes|required|email|max:150|unique:users,email,' . $studentId,
            'password' => 'nullable|string|min:6',
            'image' => 'nullable',
            'phone' => 'nullable|string|max:30',
            'whatsapp' => 'nullable|string|max:30',
            'parent_email' => 'nullable|email|max:150',
            'parent_phone' => 'sometimes|required|string|max:30',
            'parent_whatsapp' => 'nullable|string|max:30',
            'country' => 'nullable|string|max:10',
            'grade_id' => 'sometimes|required|exists:grades,id',
            'birth_date' => 'nullable|date',
        ];
    }

    /**
     * Localized validation messages (Arabic & English).
     */
    public function messages(): array
    {
        return [
            'name.required' => 'اسم الطالب مطلوب / Student name is required.',
            'email.required' => 'البريد الإلكتروني مطلوب / Email is required.',
            'email.email' => 'صيغة البريد الإلكتروني غير صحيحة / Invalid email format.',
            'email.unique' => 'البريد الإلكتروني مستخدم بالفعل / Email is already taken.',
            'parent_phone.required' => 'رقم هاتف ولي الأمر مطلوب / Parent phone is required.',
            'grade_id.required' => 'الصف الدراسي مطلوب / Grade is required.',
            'grade_id.exists' => 'الصف الدراسي المختار غير موجود / Selected grade does not exist.',
            'birth_date.date' => 'تاريخ الميلاد غير صحيح / Invalid birth date format.',
        ];
    }
}
