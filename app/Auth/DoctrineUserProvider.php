<?php

namespace App\Auth;

use App\Domain\Entities\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use LaravelDoctrine\ORM\Facades\EntityManager;
use Illuminate\Support\Str;

class DoctrineUserProvider implements UserProvider
{
    /**
     * Retrieve a user by their unique identifier.
     *
     * @param  mixed  $identifier
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveById($identifier)
    {
        return EntityManager::find(User::class, $identifier);
    }

    /**
     * Retrieve a user by their unique identifier and "remember me" token.
     *
     * @param  mixed  $identifier
     * @param  string  $token
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByToken($identifier, $token)
    {
        $user = EntityManager::find(User::class, $identifier);

        if ($user && $user->getRememberToken() === $token) {
            return $user;
        }

        return null;
    }

    /**
     * Update the "remember me" token for the given user in storage.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  string  $token
     * @return void
     */
    public function updateRememberToken(Authenticatable $user, $token)
    {
        $user->setRememberToken($token);
        EntityManager::persist($user);
        EntityManager::flush();
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array  $credentials
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        if (empty($credentials) || !isset($credentials['email'])) {
            return null;
        }

        $user = EntityManager::getRepository(User::class)->findOneBy(['email' => $credentials['email']]);
        
        return $user;
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  array  $credentials
     * @return bool
     */
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        return app('hash')->check($credentials['password'], $user->getPassword());
    }
    
    /**
     * Rehash the user's password if required.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  array  $credentials
     * @param  bool  $force
     * @return void
     */
    public function rehashPasswordIfRequired(Authenticatable $user, array $credentials, bool $force = false)
    {
        if ($force || app('hash')->needsRehash($user->getPassword())) {
            $user->setPassword(app('hash')->make($credentials['password']));
            EntityManager::persist($user);
            EntityManager::flush();
        }
    }
}
