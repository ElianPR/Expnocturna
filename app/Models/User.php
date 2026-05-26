<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable // implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'can_create_users',
        'can_manage_events',
        'can_access_trash',
        'can_manage_animations',
        'parent_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
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
            'can_create_users' => 'boolean',
            'can_manage_events' => 'boolean',
            'can_access_trash' => 'boolean',
            'can_manage_animations' => 'boolean',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->map(fn (string $name) => Str::of($name)->substr(0, 1))
            ->implode('');
    }

    /**
     * Get the parent user that created this user.
     */
    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    /**
     * Get the children users created by this user.
     */
    public function children()
    {
        return $this->hasMany(User::class, 'parent_id');
    }

    /**
     * Recursively get all descendant user IDs.
     */
    public function getDescendantIds(): array
    {
        $ids = [];
        $children = $this->children()->pluck('id')->toArray();
        
        foreach ($children as $childId) {
            $ids[] = $childId;
            $child = User::find($childId);
            if ($child) {
                $ids = array_merge($ids, $child->getDescendantIds());
            }
        }
        
        return $ids;
    }

    /**
     * Determine if this user can manage the given target user.
     */
    public function canManageUser(User $targetUser): bool
    {
        if ($this->id === $targetUser->id) {
            return true;
        }

        $descendantIds = $this->getDescendantIds();
        return in_array($targetUser->id, $descendantIds);
    }
}
