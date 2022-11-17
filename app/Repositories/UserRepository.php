<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
//use App\Helpers\UploadHelper;
use App\Interfaces\CrudInterface;
use App\Models\Category;
use App\Models\User;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;

class UserRepository implements CrudInterface
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
     * @return \Illuminate\Database\Eloquent\Collection Array of Category Collection
     */
    public function getAll()
    {
//         return $this->user->categories()
//             ->orderBy('id', 'desc')
//             ->with('user')
//             ->paginate(10);
        return User::All();
    }

    /**
     * Get Paginated Category Data.
     *
     * @param int $pageNo
     * @return collections Array of Category Collection
     */
    public function getPaginatedData($perPage): Paginator
    {
        $perPage = isset($perPage) ? intval($perPage) : 12;
        return User::orderBy('id', 'desc')
            ->with('user')
            ->paginate($perPage);
    }

    /**
     * Get Searchable Category Data with Pagination.
     *
     * @param int $pageNo
     * @return collections Array of Category Collection
     */
    public function searchCategory($keyword, $perPage): Paginator
    {
        $perPage = isset($perPage) ? intval($perPage) : 10;

        return User::where('name', 'like', '%' . $keyword . '%')
            ->orderBy('id', 'desc')
            ->paginate($perPage);
    }

    /**
     * Create New Category.
     *
     * @param array $data
     * @return object Category Object
     */
    public function create(array $data): bool|null
    {

         return User::insert([
             'username' => $data[0]['username'],
             'password' => $data[0]['password'],
             'role_id' => $data[0]['role_id']
         ]);
    }
    public function findByUserName($username){
        return User::where('username', $username)
            ->join('roles','role_id','=','roles.id')
            ->select('users.*', 'roles.name as roleName')
            ->get();
    }

    /**
     * Delete Category.
     *
     * @param int $id
     * @return boolean true if deleted otherwise false
     */
    public function delete(int $id): bool
    {
         $category = User::find($id);
         if (empty($category)) {
             return false;
         }
         $category->delete($category);
         return true;
    }

    /**
     * Get Category Detail By ID.
     *
     * @param int $id
     * @return void
     */
    public function getByID(int $id): User|null
    {
        return User::find($id);
    }

    /**
     * Update Category By ID.
     *
     * @param int $id
     * @param array $data
     * @return object Updated Category Object
     */
    public function update(int $id, array $data): User|null
    {
         $category = User::find($id);

         if (is_null($category)) {
             return null;
         }

         // If everything is OK, then update.
        User::where('id', $id)->update([
            'email' => $data[0]->email,
            'username' => $data[0]->username,
            'address' => $data[0]->address,
            'age' => $data[0]->age,
            'sex' => $data[0]->sex,
            'avatar' => $data[0]->avatar
        ]);

         // Finally return the updated Category.
         return $this->getByID($category->id);
    }
}
