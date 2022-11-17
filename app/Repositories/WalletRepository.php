<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
//use App\Helpers\UploadHelper;
use App\Interfaces\CrudInterface;
use App\Models\wallet;
use App\Models\User;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;

class WalletRepository implements CrudInterface
{
    /**
     * Authenticated User Instance.
     *
     * @var User
     */
    public User | null $user;

    /**
     * Constructor..
     */
    public function __construct()
    {
        // $this->user = Auth::guard()->user();
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
//             ->paginate(10);
        return DB::table("wallet")->paginate(1)->get();
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
        return wallet::orderBy('id', 'desc')
            ->with('user')
            ->paginate($perPage);
    }

    /**
     * Get Searchable wallet Data with Pagination.
     *
     * @param int $pageNo
     * @return collections Array of wallet Collection
     */
    public function searchWallet($keyword, $perPage): Paginator
    {
        $perPage = isset($perPage) ? intval($perPage) : 10;

        return wallet::where('name', 'like', '%' . $keyword . '%')
            ->orderBy('id', 'desc')
            ->paginate($perPage);
    }

    public function findByUserId(int $id, $perPage){
        $perPage = isset($perPage) ? intval($perPage) : 10;
        return wallet::where('user_id', $id)
            ->where('status', '!=', '0')
            ->orderBy('id', 'desc')
            ->paginate($perPage);
    }

    public function findByStatus($id){
            return wallet::where('status', '!=', '0')
                ->where('user_id', $id)
            ->orderBy('id', 'desc')
            ->get();
    }

    /**
     * Create New wallet.
     *
     * @param array $data
     * @return object wallet Object
     */
    public function create(array $data): wallet|null
    {

         return Wallet::create($data);
    }

    /**
     * Delete wallet.
     *
     * @param int $id
     * @return boolean true if deleted otherwise false
     */
    public function delete(int $id): bool
    {
         $wallet = wallet::find($id);
         if (empty($wallet)) {
             return false;
         }
         Wallet::where('id', $id)->update(['status'=>0]);
         return true;
    }

    /**
     * Get wallet Detail By ID.
     *
     * @param int $id
     * @return void
     */
    public function getByID(int $id)
    {
        return Wallet::join('money_type', 'money_type_id', 'money_type.id')
            ->select('wallet.*', 'money_type.name as moneyTypeName')
            ->where('wallet.id', $id)
            ->paginate(10);
    }

    public function findById(int $id){
        return Wallet::find($id);
    }

    /**
     * Update wallet By ID.
     *
     * @param int $id
     * @param array $data
     * @return object Updated wallet Object
     */
    public function update(int $id, array $data): wallet|null
    {
         $wallet = Wallet::find($id);

         if (is_null($wallet)) {
             return null;
         }

         // If everything is OK, then update.
         Wallet::where('id', $id)->update([
             'name' => $data[0]['name'],
             'money_type_id' => $data[0]['money_type_id'],
             'icon' => $data[0]['icon'],
             'money' => $data[0]['money'],
             'status' => $data[0]['status'],
             'user_id' => $data[0]['user_id']
         ]);


         // Finally return the updated wallet.
         return Wallet::find($id);
    }
    public function turnOffWallet($id){
        $wallet = Wallet::find($id);

        if (is_null($wallet)) {
            return null;
        }

        // If everything is OK, then update.
        Wallet::where('id', $id)->update([
            'status' => 1
        ]);
    }
}
