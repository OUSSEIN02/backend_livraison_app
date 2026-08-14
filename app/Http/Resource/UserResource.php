<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'email_verified_at' => $this->email_verified_at,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            // Transformation des rôles et permissions pour le frontend
            'roles' => $this->roles->map(function ($role) {
                return [
                    'name' => $role->name,
                    'slug' => $role->name, // Utilise le nom comme slug si vous n'avez pas de colonne 'slug' dédiée
                    'permissions' => $role->permissions->map(function ($permission) {
                        return [
                            'name' => $permission->name
                        ];
                    })->values()
                ];
            })->values(),
        ];
    }
}