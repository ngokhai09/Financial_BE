<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class CategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules() : array
    {
        return [
            'name' => 'required|max:255',
            'status' => 'required|numeric',
            'user_id' => 'required|numeric'
        ];
    }
    public function messages(): array
    {
        return [
            'name.required'  => 'Please give category name',
            'name.max'       => 'Please give category name maximum of 255 characters',
            'status.required'  => 'Please give category status',
            'status.numeric'   => 'Please give a numeric category status',
            'user_id.required'     => 'Please give category user_id',
            'user_id.numeric'       => 'Please give a numeric category user_id',
        ];
    }
    protected function failedValidation(Validator $validator)
    {

        $errors = (new ValidationException($validator))->errors();
        throw new HttpResponseException(response()->json(
            [
                'error' => $errors,
                'status_code' => 422,
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY));
    }
}
