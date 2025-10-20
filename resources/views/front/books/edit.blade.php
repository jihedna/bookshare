@extends('layouts.modern')

@section('title', 'Modifier ' . $book->getTitle())

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Modifier un livre</h1>
            <p class="text-gray-600 mt-2">Mettre à jour les informations de "{{ $book->getTitle() }}"</p>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <form action="{{ route('books.update', $book->getId()) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Titre -->
                <div class="mb-4">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Titre <span class="text-red-500">*</span></label>
                    <input type="text" name="title" id="title" value="{{ old('title', $book->getTitle()) }}" required
                        class="input w-full @error('title') border-red-500 @enderror">
                    @error('title')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Auteur -->
                <div class="mb-4">
                    <label for="author" class="block text-sm font-medium text-gray-700 mb-1">Auteur <span class="text-red-500">*</span></label>
                    <input type="text" name="author" id="author" value="{{ old('author', $book->getAuthor()) }}" required
                        class="input w-full @error('author') border-red-500 @enderror">
                    @error('author')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div class="mb-4">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" id="description" rows="5"
                        class="input w-full @error('description') border-red-500 @enderror">{{ old('description', $book->getDescription()) }}</textarea>
                    <p class="text-sm text-gray-500 mt-1">Une description détaillée aidera les autres utilisateurs à décider s'ils veulent emprunter ce livre.</p>
                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Catégories -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Catégories</label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                        @php
                            $bookCategoryIds = $book->getCategories()->map(function($category) {
                                return $category->getId();
                            })->toArray();
                        @endphp
                        
                        @foreach($categories as $category)
                            <div class="flex items-center">
                                <input type="checkbox" name="categories[]" id="category-{{ $category->getId() }}" 
                                    value="{{ $category->getId() }}" 
                                    class="rounded text-primary focus:ring-primary"
                                    {{ in_array($category->getId(), old('categories', $bookCategoryIds)) ? 'checked' : '' }}>
                                <label for="category-{{ $category->getId() }}" class="ml-2 text-sm text-gray-700">
                                    {{ $category->getName() }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                    @error('categories')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Image de couverture -->
                <div class="mb-6">
                    <label for="cover" class="block text-sm font-medium text-gray-700 mb-1">Image de couverture</label>
                    <div class="flex items-center">
                        <div class="w-24 h-32 bg-gray-100 rounded-md mr-4 overflow-hidden">
                            @if($book->getCoverPath())
                                <img src="{{ asset('storage/' . $book->getCoverPath()) }}" 
                                    alt="{{ $book->getTitle() }}" 
                                    class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-400">
                                    <i class="ri-image-line text-3xl"></i>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <input type="file" name="cover" id="cover"
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4
                                file:rounded-full file:border-0 file:text-sm file:font-semibold
                                file:bg-primary/10 file:text-primary
                                hover:file:bg-primary/20">
                            <p class="text-sm text-gray-500 mt-1">
                                @if($book->getCoverPath())
                                    Téléchargez une nouvelle image pour remplacer l'actuelle.
                                @else
                                    Format JPG, PNG ou GIF. Max 2MB.
                                @endif
                            </p>
                        </div>
                    </div>
                    @error('cover')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Boutons -->
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('books.show', $book->getId()) }}" class="btn bg-gray-100 text-gray-700 hover:bg-gray-200">
                        Annuler
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="ri-save-line mr-1"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
