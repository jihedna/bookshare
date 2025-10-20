<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Entities\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use LaravelDoctrine\ORM\Facades\EntityManager;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index()
    {
        $users = EntityManager::getRepository(User::class)->findAll();
        
        return view('admin.users.index', [
            'users' => $users
        ]);
    }

    /**
     * Display the specified user.
     */
    public function show(int $id)
    {
        $user = EntityManager::find(User::class, $id);
        
        if (!$user) {
            abort(404);
        }
        
        return view('admin.users.show', [
            'user' => $user
        ]);
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(int $id)
    {
        $user = EntityManager::find(User::class, $id);
        
        if (!$user) {
            abort(404);
        }
        
        return view('admin.users.edit', [
            'user' => $user
        ]);
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, int $id)
    {
        $user = EntityManager::find(User::class, $id);
        
        if (!$user) {
            abort(404);
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'is_admin' => 'boolean'
        ]);
        
        $user->setName($validated['name']);
        $user->setEmail($validated['email']);
        
        // Update roles
        $roles = isset($validated['is_admin']) && $validated['is_admin'] ? ['ROLE_ADMIN', 'ROLE_USER'] : ['ROLE_USER'];
        $user->setRoles($roles);
        
        EntityManager::flush();
        
        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur mis à jour avec succès.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(int $id)
    {
        $user = EntityManager::find(User::class, $id);
        
        if (!$user) {
            abort(404);
        }
        
        // Check if this is the last admin user
        if ($user->isAdmin()) {
            $adminCount = count(EntityManager::getRepository(User::class)->findBy(['roles' => 'ROLE_ADMIN']));
            if ($adminCount <= 1) {
                return redirect()->route('admin.users.index')
                    ->with('error', 'Impossible de supprimer le dernier administrateur.');
            }
        }
        
        // Check if the user has books or loans
        if (count($user->getBooks()) > 0) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Impossible de supprimer un utilisateur qui possède des livres.');
        }
        
        if (count($user->getLoans()) > 0) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Impossible de supprimer un utilisateur qui a des emprunts en cours.');
        }
        
        EntityManager::remove($user);
        EntityManager::flush();
        
        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur supprimé avec succès.');
    }
}
