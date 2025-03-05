<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class StoreProjectUserRequest extends FormRequest
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
            ],
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
