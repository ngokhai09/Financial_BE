<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Plan
 * 
 * @property int $id
 * @property string|null $name
 * @property int|null $period_id
 * @property int|null $user_id
 * 
 * @property Period|null $period
 * @property User|null $user
 * @property Collection|Category[] $categories
 *
 * @package App\Models
 */
class Plan extends Model
{
	protected $table = 'plan';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'id' => 'int',
		'period_id' => 'int',
		'user_id' => 'int'
	];

	protected $fillable = [
		'name',
		'period_id',
		'user_id'
	];

	public function period()
	{
		return $this->belongsTo(Period::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function categories()
	{
		return $this->belongsToMany(Category::class, 'plan_category')
					->withPivot('id', 'ammount');
	}
}
