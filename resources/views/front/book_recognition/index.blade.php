@extends('front.layout')

@section('title', 'Reconnaissance de livres par image')

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <h1 class="text-2xl font-bold mb-6">Reconnaissance de livres par image</h1>
            
            <div class="mb-6">
                <p class="text-gray-600">
                    Prenez une photo de la couverture d'un livre et notre système d'IA l'identifiera automatiquement.
                    Vous pourrez ensuite ajouter facilement ce livre à la plateforme.
                </p>
            </div>
            
            <form action="{{ route('book-recognition.identify') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                
                <div class="flex flex-col items-center justify-center w-full">
                    <label for="image-upload" class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6" id="upload-placeholder">
                            <svg class="w-10 h-10 mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Cliquez pour télécharger</span> ou glissez-déposez</p>
                            <p class="text-xs text-gray-500">PNG, JPG ou JPEG (MAX. 5MB)</p>
                        </div>
                        <div class="hidden w-full h-full" id="image-preview-container">
                            <img id="image-preview" class="w-full h-full object-contain" src="#" alt="Aperçu de l'image">
                        </div>
                        <input id="image-upload" name="image" type="file" class="hidden" accept="image/*" />
                    </label>
                </div>
                
                @error('image')
                    <div class="text-red-500 text-sm">{{ $message }}</div>
                @enderror
                
                <div class="flex justify-center">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                        Identifier le livre
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const imageUpload = document.getElementById('image-upload');
        const imagePreview = document.getElementById('image-preview');
        const imagePreviewContainer = document.getElementById('image-preview-container');
        const uploadPlaceholder = document.getElementById('upload-placeholder');
        
        imageUpload.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    imagePreviewContainer.classList.remove('hidden');
                    uploadPlaceholder.classList.add('hidden');
                }
                
                reader.readAsDataURL(this.files[0]);
            }
        });
    });
</script>
@endsection
