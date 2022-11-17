<?php

namespace App\Http\Controllers;

use App\Http\Requests\WalletRequest;
use App\Models\Wallet;
use App\Repositories\MoneyTypeRepository;
use App\Repositories\TransactionRepository;
use App\Repositories\WalletRepository;

use DebugBar\DebugBar;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests\CategoryRequest;

use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;


class WalletController extends Controller
{
    use ResponseTrait;

    public WalletRepository $walletRepository;
    public TransactionRepository $transactionRepository;
    public MoneyTypeRepository $moneyTypeRepository;

    public function __construct(WalletRepository $walletRepository, TransactionRepository $transactionRepository, MoneyTypeRepository $moneyTypeRepository)
    {
//        $this->middleware('auth:api', ['except' => ['indexAll']]);
        $this->walletRepository = $walletRepository;
        $this->transactionRepository = $transactionRepository;
        $this->moneyTypeRepository = $moneyTypeRepository;
    }

    public function index(): JsonResponse
    {
        try {
            $data = $this->walletRepository->getAll();
            return $this->responseSuccess($data, 'Wallet List Fetch Successfully !');
        } catch (Exception $e) {
            return $this->responseError(null, $e->getMessage(), ResponseAlias::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function indexAll(Request $request): JsonResponse
    {
        try {
            $data = $this->walletRepository->getPaginatedData($request->perPage);
            return $this->responseSuccess($data, 'Wallet List Fetched Successfully !');
        } catch (Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function search(Request $request): JsonResponse
    {
        try {
            $data = $this->walletRepository->searchWallet($request->search, $request->perPage);
            return $this->responseSuccess($data, 'Wallet List Fetched Successfully !');
        } catch (Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findByUserId($id, Request $request): JsonResponse
    {
        try {
            $data = $this->walletRepository->findByUserId($id, $request->perPage);
            return $this->responseSuccess($data, 'Wallet List Fetched Successfully !');
        } catch (Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);

        }
    }

    public function findByStatus($id): JsonResponse
    {
        try {
            $data = $this->walletRepository->findByStatus($id);
            return $this->responseSuccess($data, 'Wallet List Fetched Successfully !');
        } catch (Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);

        }
    }

    public function store(WalletRequest $request): JsonResponse
    {
        try {
            $product = $request->all();

            $product = $this->walletRepository->create($product);
            return $this->responseSuccess($product, 'New Wallet Created Successfully !');
        } catch (Exception $exception) {
            echo "Hello";
            return $this->responseError(null, $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $data = $this->walletRepository->getByID($id);
            if (is_null($data)) {
                return $this->responseError(null, 'Wallet Not Found', Response::HTTP_NOT_FOUND);
            }

            return $this->responseSuccess($data, 'Wallet Details Fetch Successfully !');
        } catch (Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function updateMoneyType($id, WalletRequest $newWallet): JsonResponse
    {
        $wallet = $this->walletRepository->findById($id);
        if ($wallet == null) {
            return $this->responseError($wallet, 'Wallet Not Found', Response::HTTP_NOT_FOUND);
        }
        if ($wallet['money_type_id'] != $newWallet['money_type_id']) {
            $newWallet->id = $id;
            $transactions = $this->transactionRepository->findAllByWalletId($wallet->id);
            $ammount = $this->moneyTypeRepository->getByID($wallet['money_type_id'])->rate / $this->moneyTypeRepository->getByID($newWallet['money_type_id'])->rate;
            $newWallet->money = ceil($wallet->money * $ammount * 100) / 100;
            foreach ($transactions as $item) {
                $item->total = ceil($item->total * $ammount * 100) / 100;
                $this->transactionRepository->update($item->id, $item);
            }
        }
        $wallet = $this->walletRepository->update($id, [$newWallet]);
        return $this->responseSuccess($wallet, 'Wallet Success');
    }

    public function updateStatus($id): JsonResponse
    {
        $wallet = $this->walletRepository->findById($id);
        if ($wallet == null) {
            return $this->responseError(null, 'Wallet Not Found', Response::HTTP_NOT_FOUND);
        }
        if ($wallet->status == 1) {
            $wallet->status = 2;
        } else if ($wallet->status == 2) {
            $wallet->status = 1;
        }
        $this->walletRepository->update($id, [$wallet]);
        return $this->responseSuccess($wallet, 'Wallet');

    }

    public function update($id, WalletRequest $request): JsonResponse
    {
        try {
            $data = $this->walletRepository->update((int)$request->id, [$request->all()]);
            $this->walletRepository->turnOffWallet($id);
            if (is_null($data))
                return $this->responseError(null, 'Wallet Not Found', Response::HTTP_NOT_FOUND);

            return $this->responseSuccess($data, 'Wallet Updated Successfully !');
        } catch (Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $wallet = $this->walletRepository->findById($id);
            if (empty($wallet)) {
                return $this->responseError(null, 'Wallet Not Found', Response::HTTP_NOT_FOUND);
            }
            return $this->responseSuccess($this->walletRepository->delete($id), 'Wallet Deleted Successfully !');
        } catch (Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
