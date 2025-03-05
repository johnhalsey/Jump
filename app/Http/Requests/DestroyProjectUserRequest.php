<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class DestroyProjectUserRequest extends FormRequest
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
            //
        ];
    }

    /**
     * Get the "after" validation callables for the request.
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                $project = $this->route('project');
                $user = $this->route('user');

                if ($project->owners->count() == 1 && $project->owners->contains($user)) {
                    // there will be no owners left
                    $validator->errors()->add(
                        'user',
                        $user->name . ' cannot be removed because they are the only owner of this project.'
                    );
                }
            }
        ];
    }


}
