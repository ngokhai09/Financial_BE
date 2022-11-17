<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Repositories\CategoryRepository;
use App\Repositories\WalletRepository;

use DebugBar\DebugBar;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests\CategoryRequest;

use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;


class CategoriesController extends Controller
{
    use ResponseTrait;

    public CategoryRepository $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
//        $this->middleware('auth:api', ['except' => ['indexAll']]);
        $this->categoryRepository = $categoryRepository;
    }

    public function index(): JsonResponse
    {
        try {
            $data = $this->categoryRepository->getAll();
            return $this->responseSuccess($data, 'Category List Fetch Successfully !');
        } catch (Exception $e) {
            return $this->responseError(null, $e->getMessage(), ResponseAlias::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function indexAll(Request $request): JsonResponse
    {
        try {
            $data = $this->categoryRepository->getPaginatedData($request->perPage);
            return $this->responseSuccess($data, 'Category List Fetched Successfully !');
        } catch (Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function search(Request $request): JsonResponse
    {
        try {
            $data = $this->categoryRepository->searchCategory($request->search, $request->perPage);
            return $this->responseSuccess($data, 'Category List Fetched Successfully !');
        } catch (Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findByUserId($id, Request $request): JsonResponse
    {
        try {
            $data = $this->categoryRepository->findByUserId($id, $request->perPage);
            return $this->responseSuccess($data, 'Category List Fetched Successfully !');
        } catch (Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);

        }
    }

    public function findByStatus($status, Request $request): JsonResponse
    {
        try {
            $data = $this->categoryRepository->findByStatus($status, $request->perPage);
            return $this->responseSuccess($data, 'Category List Fetched Successfully !');
        } catch (Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);

        }
    }

    public function store(CategoryRequest $request): JsonResponse
    {
        try {
            $product = $this->categoryRepository->create($request->all());
            return $this->responseSuccess($product, 'New Category Created Successfully !');
        } catch (Exception $exception) {
            echo "Hello";
            return $this->responseError(null, $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $data = $this->categoryRepository->getByID($id);
            if (is_null($data)) {
                return $this->responseError(null, 'Category Not Found', Response::HTTP_NOT_FOUND);
            }

            return $this->responseSuccess($data, 'Category Details Fetch Successfully !');
        } catch (Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update($id, CategoryRequest $request): JsonResponse
    {
        try {
            $data = $this->categoryRepository->update($id, $request->all());
            if (is_null($data))
                return $this->responseError(null, 'Category Not Found', Response::HTTP_NOT_FOUND);

            return $this->responseSuccess($data, 'Category Updated Successfully !');
        } catch (Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $product = $this->categoryRepository->getByID($id);
            if (empty($product)) {
                return $this->responseError(null, 'Category Not Found', Response::HTTP_NOT_FOUND);
            }

            $deleted = $this->categoryRepository->delete($id);
            if (!$deleted) {
                return $this->responseError(null, 'Failed to delete the Category.', Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            return $this->responseSuccess($product, 'Category Deleted Successfully !');
        } catch (Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
