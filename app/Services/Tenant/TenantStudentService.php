<?php

namespace App\Services\Tenant;

use App\Models\Tenant;
use App\Models\User;
use App\Traits\FileUploadTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TenantStudentService
{
    use FileUploadTrait;

    /**
     * Get paginated list of students registered under current tenant.
     */
    public function getStudents(Tenant $tenant, Request $request)
    {
        $query = User::with('grade.stage')
            ->where('tenant_id', $tenant->id)
            ->where('role', User::ROLE_STUDENT);

        if ($request->filled('grade_id')) {
            $query->where('grade_id', $request->grade_id);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN));
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('parent_phone', 'like', "%{$search}%");
            });
        }

        return $query->latest()->paginate($request->input('per_page', 15));
    }

    /**
     * Store new student under current tenant.
     */
    public function storeStudent(Tenant $tenant, array $data): User
    {
        $data['tenant_id'] = $tenant->id;
        $data['role'] = User::ROLE_STUDENT;

        if (empty($data['password'])) {
            $data['password'] = Hash::make('password123');
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        if (isset($data['image'])) {
            $data['image'] = $this->uploadFile($data['image'], 'students');
        }

        $student = User::create($data);

        return $student->load('grade.stage');
    }

    /**
     * Get single student details.
     */
    public function showStudent(Tenant $tenant, int $id): User
    {
        return User::with(['grade.stage'])
            ->where('tenant_id', $tenant->id)
            ->where('role', User::ROLE_STUDENT)
            ->findOrFail($id);
    }

    /**
     * Update student details.
     */
    public function updateStudent(User $student, array $data): User
    {
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        if (array_key_exists('image', $data)) {
            $data['image'] = $this->uploadFile($data['image'], 'students', $student->image);
        }

        $student->update($data);

        return $student->load('grade.stage');
    }

    /**
     * Delete student image file and reset image field to null.
     */
    public function deleteImage(User $student): User
    {
        if ($student->image) {
            $this->deleteFile($student->image);
            $student->update(['image' => null]);
        }

        return $student->fresh('grade.stage');
    }

    /**
     * Toggle student active status (is_active).
     */
    public function toggleStatus(User $student): User
    {
        $student->update(['is_active' => !$student->is_active]);

        return $student->fresh('grade.stage');
    }

    /**
     * Delete student.
     */
    public function deleteStudent(User $student): bool
    {
        if ($student->image) {
            $this->deleteFile($student->image);
        }

        return $student->delete();
    }
}
