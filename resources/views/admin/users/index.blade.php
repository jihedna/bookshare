@extends('admin.layout')

@section('title', 'Gestion des utilisateurs')

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">Gestion des utilisateurs</h1>
            </div>
            
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
            
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif
            
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">ID</th>
                            <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Nom</th>
                            <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Email</th>
                            <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Rôle</th>
                            <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Date d'inscription</th>
                            <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($users as $user)
                            <tr>
                                <td class="py-3 px-4 text-gray-700">{{ $user->getId() }}</td>
                                <td class="py-3 px-4 text-gray-700">{{ $user->getName() }}</td>
                                <td class="py-3 px-4 text-gray-700">{{ $user->getEmail() }}</td>
                                <td class="py-3 px-4">
                                    @if($user->isAdmin())
                                        <span class="bg-purple-100 text-purple-800 text-xs font-semibold px-2 py-1 rounded">Admin</span>
                                    @else
                                        <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2 py-1 rounded">Utilisateur</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-gray-700">{{ $user->getCreatedAt()->format('d/m/Y') }}</td>
                                <td class="py-3 px-4">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('admin.users.edit', $user->getId()) }}" class="text-indigo-600 hover:text-indigo-900">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </a>
                                        <form action="{{ route('admin.users.destroy', $user->getId()) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
