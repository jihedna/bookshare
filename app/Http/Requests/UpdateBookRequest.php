<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use LaravelDoctrine\ORM\Facades\EntityManager;
use App\Domain\Entities\Category;

class UpdateBookRequest extends FormRequest
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
            'title' => 'sometimes|required|string|max:255',
            'author' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string|min:20',
            'categories' => 'sometimes|array',
            'categories.*' => [
                'integer',
                function ($attribute, $value, $fail) {
                    $category = EntityManager::find(Category::class, $value);
                    if (!$category) {
                        $fail('La catégorie sélectionnée n\'existe pas.');
                    }
                },
            ],
            'cover' => 'nullable|image|max:2048',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Le titre est obligatoire.',
            'title.max' => 'Le titre ne peut pas dépasser 255 caractères.',
            'author.required' => 'L\'auteur est obligatoire.',
            'author.max' => 'Le nom de l\'auteur ne peut pas dépasser 255 caractères.',
            'description.min' => 'La description doit contenir au moins 20 caractères.',
            'categories.array' => 'Les catégories doivent être une liste.',
            'categories.*.integer' => 'L\'identifiant de catégorie doit être un nombre entier.',
            'cover.image' => 'Le fichier doit être une image.',
            'cover.max' => 'La taille de l\'image ne peut pas dépasser 2Mo.',
        ];
    }
}
