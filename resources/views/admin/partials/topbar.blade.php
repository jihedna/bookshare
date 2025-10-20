@php
use Illuminate\Support\Facades\Auth;
@endphp

<header class="bg-white shadow">
    <div class="flex justify-between items-center py-4 px-6">
        <h2 class="text-xl font-semibold text-gray-800">
            @yield('title', 'Administration')
        </h2>
        
        <div class="flex items-center">
            <span class="text-gray-800 mr-4">{{ Auth::user() ? Auth::user()->getName() : 'Admin' }}</span>
            
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-sm text-gray-600 hover:text-gray-900">
                    Déconnexion
                </button>
            </form>
        </div>
    </div>
</header>
