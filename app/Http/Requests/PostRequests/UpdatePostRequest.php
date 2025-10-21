<?php

namespace App\Http\Requests\PostRequests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true ;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['sometimes','string','max:255'],
            'content' => ['sometimes','string'],
            'image' => ['sometimes','mimes:jpg,jpeg,png,webp,gif','max:2048'],
            'status' => ['sometimes','in:draft,published'],
            'category' => ['sometimes','exists:categories,id']
        ];
    }

}
