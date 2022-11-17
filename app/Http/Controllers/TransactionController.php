<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionRequest;
use App\Models\Transaction;
use App\Repositories\CategoryRepository;
use App\Repositories\TransactionRepository;

use App\Repositories\WalletRepository;
use Carbon\Carbon;
use DebugBar\DebugBar;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests\CategoryRequest;

use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;


class TransactionController extends Controller
{
    use ResponseTrait;

    public TransactionRepository $transactionRepository;
    public WalletRepository $walletRepository;
    public CategoryRepository $categoryRepository;

    public function __construct(TransactionRepository $transactionRepository, WalletRepository $walletRepository, CategoryRepository $categoryRepository)
    {
//        $this->middleware('auth:api', ['except' => ['indexAll']]);
        $this->transactionRepository = $transactionRepository;
        $this->walletRepository = $walletRepository;
        $this->categoryRepository = $categoryRepository;
    }

    public function index(): JsonResponse
    {
        try {
            $data = $this->transactionRepository->getAll();
            return $this->responseSuccess($data, 'Transaction List Fetch Successfully !');
        } catch (Exception $e) {
            return $this->responseError(null, $e->getMessage(), ResponseAlias::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function indexAll(Request $request): JsonResponse
    {
        try {
            $data = $this->transactionRepository->getPaginatedData($request->perPage);
            return $this->responseSuccess($data, 'Transaction List Fetched Successfully !');
        } catch (Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function search(Request $request): JsonResponse
    {
        try {
            $data = $this->transactionRepository->searchTransaction($request->search, $request->perPage);
            return $this->responseSuccess($data, 'Transaction List Fetched Successfully !');
        } catch (Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findByWallet($id): JsonResponse
    {
        try {
            $data = $this->transactionRepository->findAllByWalletId($id);
            return $this->responseSuccess($data, 'Transaction List Fetched Successfully !');
        } catch (Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findByCategory($id): JsonResponse
    {
        try {
            $data = $this->transactionRepository->findAllByCategoryId($id);
            return $this->responseSuccess($data, 'Transaction List Fetched Successfully !');
        } catch (Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function store(TransactionRequest $request): JsonResponse
    {
        try {
            $wallet = $this->walletRepository->findById((int)$request->wallet_id);
            $category = $this->categoryRepository->getByID((int)$request->category_id);
            $transaction = $this->transactionRepository->create((array)$request->all());
            if ($category->status == 1) {
                $wallet->money += $request->total;
            } else {
                $wallet->money -= $request->total;
            }
            $this->walletRepository->update($wallet->id, [$wallet]);
            return $this->responseSuccess($transaction, 'New Transaction Created Successfully !');

        } catch (Exception $exception) {
            return $this->responseError($this->categoryRepository->getByID((int)$request->category_id), $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $data = $this->transactionRepository->getByID($id);
            if (is_null($data)) {
                return $this->responseError(null, 'Transaction Not Found', Response::HTTP_NOT_FOUND);
            }

            return $this->responseSuccess($data, 'Transaction Details Fetch Successfully !');
        } catch (Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update($id, TransactionRequest $request): JsonResponse
    {
        try {
            $transaction = $this->transactionRepository->findById($id);
            $wallet = $this->walletRepository->findById($transaction->wallet_id);
            $oldCategory = $this->categoryRepository->getByID($transaction->category_id);
            $newCategory = $this->categoryRepository->getByID($request->category_id);
            if ($oldCategory->status == 1) {
                $wallet->money -= $transaction->total;
            } else {
                $wallet->money += $transaction->total;
            }
            if ($newCategory->status == 1) {
                $wallet->money += $request->total;
            } else {
                $wallet->money -= $request->total;
            }
            $this->walletRepository->update($wallet->id, [$wallet]);
            $this->transactionRepository->update($id, [$request]);
            return $this->responseSuccess($newCategory, 'New Transaction Created Successfully !');
        } catch (Exception $exception) {
            return $this->responseError(null, $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findAllByMonth($id, $status): JsonResponse
    {
        $currentTime = Carbon::now();
        $month = $currentTime->toArray()['month'];

        return $this->responseSuccess($this->transactionRepository->findAllByMonth($status, $month, $id), 'New Transaction Created Successfully !');

    }

    public function findAllTransactionFor6Month($id, $status): JsonResponse
    {
        $days = [0,31,28,31,30,31,30,31,31,30,31,30,31];
        $transaction = array();
        $currTime = Carbon::now();
        $currMonth = date('Y-m', strtotime($currTime->toDateString()));
        $startOfMonth = $currMonth.'-01';
        $transaction[$currMonth] = $this->transactionRepository->findAllTransactionFor6Month($id, $currMonth.'-01', $currTime->toDateString(), $status);
        $startMonth = $currTime->startOfMonth();
        $day = '';
        for ($i = 1; $i < 7; $i++) {
            $monthAgo = date('Y-m', strtotime($startMonth->toDateString() . ' -' . $i . ' month'));
            $time = preg_split('/\-/',$monthAgo);
            $year = (int)$time[0];
            if((int)$time[1]){
                if (($year % 4 == 0 && $year % 100 != 0) || $year % 400 == 0) {
                    $day = $days[2] + 1;
                }
            }
            $day = $days[(int)$time[1]];
            $transaction[$monthAgo] = $this->transactionRepository->findAllTransactionFor6Month($id, $monthAgo.'-01', $monthAgo.'-'.$day, $status);
        }
        return $this->responseSuccess((object)$transaction, 'Successfully !');

    }

    public function findAllByRange(Request $request, $id): JsonResponse
    {
        $startTime = date('Y-m-d', strtotime($request->filled('startTime') ? $request->startTime : '1990-01-01'));
        $endTime = date('Y-m-d', strtotime($request->filled('endTime') ? $request->endTime : '3000-01-01'));

        return $this->responseSuccess($this->transactionRepository->findByRange($id, $request->status, $startTime, $endTime, $request->from, $request->to), 'Successfully !');

    }

    public function destroy($id): JsonResponse
    {
        try {
            $transaction = $this->transactionRepository->findById($id);
            $wallet = $this->walletRepository->findById($transaction->wallet_id);
            $category = $this->categoryRepository->getByID($transaction->category_id);
            if ($category->status == 1) {
                $wallet->money -= $transaction->total;
            } else {
                $wallet->money += $transaction->total;
            }
            $this->walletRepository->update($wallet->id,[$wallet]);
            $this->transactionRepository->delete($id);
            return $this->responseSuccess($transaction, 'New Transaction Created Successfully !');
        } catch (Exception $exception) {
            return $this->responseError(null, $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
