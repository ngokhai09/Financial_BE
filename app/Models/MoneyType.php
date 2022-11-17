<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class MoneyType
 * 
 * @property int $id
 * @property string|null $name
 * @property int|null $wallet_id
 * 
 * @property Wallet|null $wallet
 *
 * @package App\Models
 */
class MoneyType extends Model
{
	protected $table = 'money_type';
	public $timestamps = false;

	protected $casts = [
		'wallet_id' => 'int'
	];

	protected $fillable = [
		'name',
		'wallet_id'
	];

	public function wallet()
	{
		return $this->belongsTo(Wallet::class);
	}
}
