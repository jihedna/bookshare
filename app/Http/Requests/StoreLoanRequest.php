<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use LaravelDoctrine\ORM\Facades\EntityManager;
use App\Domain\Entities\Book;

class StoreLoanRequest extends FormRequest
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
            'book_id' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    $book = EntityManager::find(Book::class, $value);
                    if (!$book) {
                        $fail('Le livre sélectionné n\'existe pas.');
                        return;
                    }
                    
                    // Check if the book is available for loan
                    if (!$book->isAvailableForLoan()) {
                        $fail('Ce livre n\'est pas disponible pour l\'emprunt actuellement.');
                    }
                    
                    // Check if the user is not the owner of the book
                    if ($book->getOwner()->getId() === auth()->id()) {
                        $fail('Vous ne pouvez pas emprunter votre propre livre.');
                    }
                },
            ],
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
            'book_id.required' => 'L\'identifiant du livre est obligatoire.',
            'book_id.integer' => 'L\'identifiant du livre doit être un nombre entier.',
        ];
    }
}
