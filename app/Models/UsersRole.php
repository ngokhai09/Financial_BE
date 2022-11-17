<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class UsersRole
 * 
 * @property int $user_id
 * @property int $roles_id
 * 
 * @property User $user
 * @property Role $role
 *
 * @package App\Models
 */
class UsersRole extends Model
{
	protected $table = 'users_roles';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'user_id' => 'int',
		'roles_id' => 'int'
	];

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function role()
	{
		return $this->belongsTo(Role::class, 'roles_id');
	}
}
