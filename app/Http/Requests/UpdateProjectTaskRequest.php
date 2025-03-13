<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectTaskRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'assignee_id' => [
                Rule::exists('project_user', 'user_id')->where(function ($query) {
                    $query->where('project_id', $this->route('project')->id);
                })
            ],
            'status_id'   => [
                Rule::exists('project_statuses', 'id')->where(function ($query) {
                    $query->where('project_id', $this->route('project')->id);
                })
            ],
            'title'       => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ];
    }
}
