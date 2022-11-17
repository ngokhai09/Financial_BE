<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Transaction
 * 
 * @property int $id
 * @property string|null $description
 * @property Carbon|null $time
 * @property float|null $total
 * @property int|null $wallet_id
 * @property int|null $category_id
 * 
 * @property Category|null $category
 * @property Wallet|null $wallet
 *
 * @package App\Models
 */
class Transaction extends Model
{
	protected $table = 'transaction';
	public $timestamps = false;

	protected $casts = [
		'total' => 'float',
		'wallet_id' => 'int',
		'category_id' => 'int'
	];

	protected $dates = [
		'time'
	];

	protected $fillable = [
		'description',
		'time',
		'total',
		'wallet_id',
		'category_id'
	];

	public function category()
	{
		return $this->belongsTo(Category::class);
	}

	public function wallet()
	{
		return $this->belongsTo(Wallet::class);
	}
}
