<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Permission\Models\Role as ModelsRole;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;
    use HasRoles;


    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn($word) => Str::substr($word, 0, 1))
            ->implode('');
    }



    public function moreOneRole(): bool
    {
        return $this->roles()->count() > 1;
    }

    // recibe el id de un rol por defecto y lo cambia en de
    public function setRole(?int $role): bool
    {
        // si el rol es nul entonces establecerlo unico caso
        if (is_null($role)) {
            $this->default_role = null;
            $this->save();
            return true;
        }

        // pero primero busca que el rol exista y ademas pertenezca al usuario
        if ($this->roles()->where('id', $role)->exists()) {
            $this->default_role = $role;
            $this->save();
            return true;
        }
        return false;
    }


    public function getAllPermissionsUser(): array
    {
        $permissions = [];
        if ($this->moreOneRole() && $this->default_role) {
            return ModelsRole::findById($this->default_role, $this->getDefaultGuardName())
                ->permissions()
                ->pluck('name')
                ->toArray();
        }
        $permissions = $this->getPermissionsViaRoles()
            ->pluck('name')
            ->toArray();
        return $permissions;
    }


    public function getLabelROle(): string
    {
        if ($this->default_role) {
            $role = ModelsRole::findById($this->default_role, $this->getDefaultGuardName());
            if ($role) {
                return $role->name;
            }
        }
        return 'Todos Los Roles';
    }
}
