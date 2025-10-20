<div class="bg-indigo-800 text-white w-64 min-h-screen p-4">
    <div class="mb-8">
        <h1 class="text-2xl font-bold">BookShare</h1>
        <p class="text-indigo-200 text-sm">Administration</p>
    </div>
    
    <nav>
        <ul>
            <li class="mb-2">
                <a href="{{ route('admin.dashboard') }}" class="block py-2 px-4 rounded hover:bg-indigo-700 {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-700' : '' }}">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Tableau de bord
                    </div>
                </a>
            </li>
            <li class="mb-2">
                <a href="{{ route('admin.books.index') }}" class="block py-2 px-4 rounded hover:bg-indigo-700 {{ request()->routeIs('admin.books.*') ? 'bg-indigo-700' : '' }}">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        Livres
                    </div>
                </a>
            </li>
            <li class="mb-2">
                <a href="{{ route('admin.categories.index') }}" class="block py-2 px-4 rounded hover:bg-indigo-700 {{ request()->routeIs('admin.categories.*') ? 'bg-indigo-700' : '' }}">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                        Catégories
                    </div>
                </a>
            </li>
            <li class="mb-2">
                <a href="{{ route('admin.loans.index') }}" class="block py-2 px-4 rounded hover:bg-indigo-700 {{ request()->routeIs('admin.loans.*') ? 'bg-indigo-700' : '' }}">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                        Emprunts
                    </div>
                </a>
            </li>
            <li class="mb-2">
                <a href="{{ route('admin.users.index') }}" class="block py-2 px-4 rounded hover:bg-indigo-700 {{ request()->routeIs('admin.users.*') ? 'bg-indigo-700' : '' }}">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        Utilisateurs
                    </div>
                </a>
            </li>
        </ul>
    </nav>
    
    <div class="mt-12 pt-4 border-t border-indigo-700">
        <a href="{{ route('books.index') }}" class="block py-2 px-4 rounded hover:bg-indigo-700 text-indigo-200">
            <div class="flex items-center">
                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Retour au site
            </div>
        </a>
    </div>
</div>
