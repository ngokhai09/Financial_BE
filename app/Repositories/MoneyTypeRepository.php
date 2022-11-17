<?php

namespace App\Repositories;

use App\Models\MoneyType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
//use App\Helpers\UploadHelper;
use App\Interfaces\CrudInterface;
use App\Models\Category;
use App\Models\User;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;

class MoneyTypeRepository implements CrudInterface
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
     * @return collections Array of Category Collection
     */
    public function getAll()
    {
        return null;
    }

    /**
     * Create New Category.
     *
     * @param array $data
     * @return object Category Object
     */
    public function create(array $data): Category|null
    {

         return null;
    }

    /**
     * Delete Category.
     *
     * @param int $id
     * @return boolean true if deleted otherwise false
     */
    public function delete(int $id): bool
    {
         return true;
    }

    /**
     * Get Category Detail By ID.
     *
     * @param int $id
     * @return void
     */
    public function getByID(int $id): MoneyType |null
    {
        return MoneyType::find($id);
    }

    /**
     * Update Category By ID.
     *
     * @param int $id
     * @param array $data
     * @return object Updated Category Object
     */
    public function update(int $id, array $data): Category|null
    {
        return null;
    }

    public function getPaginatedData(int $perPage)
    {
        // TODO: Implement getPaginatedData() method.
    }
}
