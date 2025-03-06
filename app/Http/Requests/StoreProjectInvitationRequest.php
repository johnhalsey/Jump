<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Models\Invitation;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class StoreProjectInvitationRequest extends FormRequest
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
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('invitations', 'email')->where(function ($query) {
                    return $query->where('project_id', $this->route('project')->id);
                })
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'This user has already been invited to this project.',
        ];
    }

    public function after()
    {
        return [
            function (Validator $validator) {
                $project = $this->route('project');
                $email = $this->input('email');

                if (!$user = User::where('email', $email)->first()) {
                    // the user is not in the db at all, you're good
                    return;
                }

                if ($project->users->contains($user)) {
                    $validator->errors()->add(
                        'email',
                        'This user has already been added to this project.'
                    );

                }
            }
        ];
    }
}
