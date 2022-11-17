<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

/**
 * Class User
 *
 * @property int $id
 * @property string|null $avatar
 * @property string $email
 * @property string|null $name
 * @property string $password
 * @property string|null $phone
 * @property int|null $status
 * @property string $username
 *
 * @property Collection|Category[] $categories
 * @property Collection|Plan[] $plans
 * @property Collection|Role[] $roles
 *
 * @package App\Models
 */
class User extends Model implements JWTSubject
{
    protected $table = 'users';
    public $timestamps = false;

    protected $casts = [
        'status' => 'int',
        'age' => 'int',
        'sex' => 'int'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $fillable = [
        'avatar',
        'email',
        'name',
        'password',
        'phone',
        'status',
        'username',
        'address',
        'age',
        'sex'
    ];

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function plans()
    {
        return $this->hasMany(Plan::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'users_roles', 'user_id', 'roles_id');
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
