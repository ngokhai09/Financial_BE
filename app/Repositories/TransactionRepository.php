<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

//use App\Helpers\UploadHelper;
use App\Interfaces\CrudInterface;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;

class TransactionRepository implements CrudInterface
{
    /**
     * Authenticated User Instance.
     *
     * @var User
     */
    public User|null $user;

    /**
     * Constructor..
     */
    public function __construct()
    {
        // $this->user = Auth::guard()->user();
//        $this->middleware('auth:api');
    }

    /**
     * Get All Products.
     *
     * @return collections Array of wallet Collection
     */
    public function getAll()
    {
//         return $this->user->categories()
//             ->orderBy('id', 'desc')
//             ->with('user')
//             ->get();
        return DB::table("transaction")->get();
    }

    /**
     * Get Paginated wallet Data.
     *
     * @param int $pageNo
     * @return collections Array of wallet Collection
     */
    public function getPaginatedData($perPage): Paginator
    {
        $perPage = isset($perPage) ? intval($perPage) : 12;
        return Transaction::orderBy('id', 'desc')
            ->with('user')
            ->paginate($perPage);
    }

    public function findAllByCategoryId(int $id)
    {
        return Transaction::where('category_id', $id)
            ->orderBy('id', 'desc');
    }

    public function findAllByWalletId(int $id)
    {
        return DB::table('transaction')
            ->join('category', 'category_id', '=', 'category.id')
            ->join('wallet', 'wallet_id', '=', 'wallet.id')
            ->join('money_type', 'wallet.money_type_id', '=', 'money_type.id')
            ->select('transaction.*', 'category.status as categoryStatus', 'category.name as categoryName', 'category.color as categoryColor', 'money_type.name as MoneyTypeName')
            ->where('wallet_id', $id)
            ->get();
    }

    public function findAllByTimeBetween(string $start_time, string $end_time)
    {
        return Transaction::whereBetween('time', [$start_time, $end_time])
            ->orderBy('id', 'desc');
    }

    /**
     * Get Searchable wallet Data with Pagination.
     *
     * @param int $pageNo
     * @return collections Array of wallet Collection
     */
    public function searchTransaction($keyword, $perPage): Paginator
    {
        $perPage = isset($perPage) ? intval($perPage) : 10;

        return Transaction::where('name', 'like', '%' . $keyword . '%')
            ->orderBy('id', 'desc')
            ->paginate($perPage);
    }

    public function findByUserId(int $id, $perPage)
    {
        $perPage = isset($perPage) ? intval($perPage) : 10;
        return Transaction::join('category', 'category_id', '=', 'category.id')
            ->join('wallet', 'wallet_id', '=', 'wallet.id')
            ->join('users', 'wallet.user_id', '=', 'users.id')
            ->select('transaction.*', 'category.name', 'wallet.name', 'users.username')
            ->where('users.id', $id)
            ->orderBy('id', 'desc')
            ->paginate($perPage);
    }

    public function findAllByCategoryUserId($id, $status)
    {
        return Transaction::join('category', 'category_id', '=', 'category.id')
            ->join('wallet', 'wallet_id', '=', 'wallet.id')
            ->join('users', 'wallet.user_id', '=', 'users.id')
            ->select('transaction.*', 'category.name', 'wallet.name', 'users.username')
            ->where('users.id', $id)
            ->where('category.status', $status)
            ->orderBy('id', 'desc')
            ->get();
    }

    public function findAllByMonth($status, $month, $id)
    {
        return Transaction::join('category', 'category_id', 'category.id')
            ->where('category.status', $status)
            ->select('transaction.*', 'category.status as categoryStatus', 'category.name as categoryName', 'category.color as categoryColor')
            ->where('transaction.time', 'like', '%' . $month . '%')
            ->where('wallet_id', $id)
            ->get();
    }

    public function findAllTransactionFor6Month($id, $sixMonth, $presentTime,  $status)
    {
        return Transaction::join('category', 'category_id', 'category.id')
            ->whereBetween('transaction.time', [$sixMonth, $presentTime])
            ->where('wallet_id', $id)
            ->where('category.status', $status)
            ->get();
    }

    public function findByRange($id, $status, $startTime, $endTime, $from, $to)
    {

        return Transaction::join('category', 'category_id', 'category.id')
            ->join('wallet', 'wallet_id', '=', 'wallet.id')
            ->join('money_type', 'wallet.money_type_id', '=', 'money_type.id')
            ->select('transaction.*', 'category.status as categoryStatus', 'category.name as categoryName', 'category.color as categoryColor', 'money_type.name as MoneyTypeName')
            ->whereBetween('transaction.time', [$startTime, $endTime])
            ->whereBetween('total', [$from, $to])
            ->where('category.status', $status)
            ->where('wallet_id', $id)
            ->get();
    }

    public function findByStatus(int $status, $perPage)
    {
        $perPage = isset($perPage) ? intval($perPage) : 10;
        return Transaction::where('status', $status)
            ->orderBy('id', 'desc')
            ->paginate($perPage);
    }

    /**
     * Create New wallet.
     *
     * @param array $data
     * @return object wallet Object
     */
    public function create(array $data): Transaction|null
    {

        return Transaction::create($data);
    }

    /**
     * Delete wallet.
     *
     * @param int $id
     * @return boolean true if deleted otherwise false
     */
    public function delete(int $id): bool
    {
        $wallet = Transaction::find($id);
        if (empty($wallet)) {
            return false;
        }
        $wallet->delete($wallet);
        return true;
    }

    /**
     * Get wallet Detail By ID.
     *
     * @param int $id
     * @return \Illuminate\Support\Collection
     */
    public function getByID(int $id)
    {
        return DB::table('transaction')
            ->join('category', 'category_id', '=', 'category.id')
            ->join('wallet', 'wallet_id', '=', 'wallet.id')
            ->join('money_type', 'wallet.money_type_id', '=', 'money_type.id')
            ->select('transaction.*', 'category.status as categoryStatus', 'category.name as categoryName', 'category.color as categoryColor', 'money_type.name as MoneyTypeName')
            ->where('transaction.id', $id)
            ->get();
    }
    public function findById(int $id)
    {
        return Transaction::find($id);
    }

    /**
     * Update wallet By ID.
     *
     * @param int $id
     * @param array $data
     * @return object Updated wallet Object
     */
    public function update(int $id, array $data): Transaction|null
    {
        $transaction = Transaction::find($id);

        if (is_null($transaction)) {
            return null;
        }

        Transaction::where('id', $id)->update([
            'description' => $data[0]->description,
            'time' => $data[0]->time,
            'total' => $data[0]->total,
            'wallet_id' => $data[0]->wallet_id,
            'category_id' => $data[0]->category_id
        ]);

        // Finally return the updated wallet.
        return $this->findById($transaction->id);
    }
}
