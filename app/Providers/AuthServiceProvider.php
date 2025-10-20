<?php

namespace App\Providers;

use App\Domain\Entities\Book;
use App\Domain\Entities\Loan;
use App\Policies\BookPolicy;
use App\Policies\LoanPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Book::class => BookPolicy::class,
        Loan::class => LoanPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
        
        // Define admin gate
        Gate::define('admin', function ($user) {
            return $user->isAdmin();
        });
    }
}
