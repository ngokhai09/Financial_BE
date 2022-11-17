<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Repositories\UserRepository;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use App\Models\User;
use \Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use function PHPUnit\Framework\isNull;

class UserController extends Controller
{
    use ResponseTrait;

    public UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
//        $this->middleware('auth:api');
        $this->userRepository = $userRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return new Response(User::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }


    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        //
        return $this->responseSuccess($this->userRepository->getByID($id), 'User Fetch');

    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UserRequest $request, $id)
    {
        //
        try {
            $data = $this->userRepository->getByID($id);
            if (!isNull($data)) {
                return $this->responseError($data, 'User Not Found', Response::HTTP_NOT_FOUND);
            }
            $data = $this->userRepository->update($id, [$request]);
            return $this->responseSuccess($data, 'Wallet List Fetch Successfully !');
        } catch (Exception $e) {
            return $this->responseError(null, $e->getMessage(), ResponseAlias::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function createUser(UserRequest $userRequest)
    {
        $users = $this->userRepository->getAll();
        foreach ($users as $user) {
            if ($user->username == $userRequest->username) {
                return $this->responseError(null, 'User exists', Response::HTTP_CONFLICT);
            }
        }
        $user = $userRequest->all();
        $user['role_id'] = 4;
        $data = $this->userRepository->create([$user]);
        return $this->responseSuccess($data, 'Wallet List Fetch Successfully !');
    }

    public function login(UserRequest $userRequest)
    {
        try {
            $data = $this->userRepository->findByUserName($userRequest->username)[0];
            if ($data == null) {
                return $this->responseError(null, 'User not exists', Response::HTTP_CONFLICT);
            }
            if ($data->password == $userRequest['password']) {
                return $this->responseSuccess($data, 'Login Successfully !');
            }
            return $this->responseError(null, 'Unauthorized', Response::HTTP_UNAUTHORIZED);

        } catch (Exception $e) {
            return $this->responseError(null, $e->getMessage(), ResponseAlias::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
