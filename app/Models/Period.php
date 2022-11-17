<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Period
 * 
 * @property int $id
 * @property string|null $name
 * 
 * @property Collection|Plan[] $plans
 *
 * @package App\Models
 */
class Period extends Model
{
	protected $table = 'period';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'id' => 'int'
	];

	protected $fillable = [
		'name'
	];

	public function plans()
	{
		return $this->hasMany(Plan::class);
	}
}
