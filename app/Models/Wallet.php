<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Wallet
 *
 * @property int $id
 * @property string|null $icon
 * @property float|null $money
 * @property string|null $name
 * @property int|null $status
 *
 * @property Collection|MoneyType[] $money_types
 * @property Collection|Transaction[] $transactions
 *
 * @package App\Models
 */
class Wallet extends Model
{
	protected $table = 'wallet';
	public $timestamps = false;

	protected $casts = [
		'money' => 'float',
		'status' => 'int'
	];

	protected $fillable = [
		'icon',
		'money',
		'name',
		'status',
        'user_id',
        'money_type_id'
	];

	public function money_types()
	{
		return $this->hasMany(MoneyType::class);
	}

	public function transactions()
	{
		return $this->hasMany(Transaction::class);
	}
}
