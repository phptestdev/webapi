<?php
/**
 * This class handles user authentication processes.
 *
 * It provides methods for user registration, login processes.
 *
 * @package App\Http\Controllers
 * @author Eugene <e.a.andrushchenko@gmail.com>
 * @version 1.0.0
 * @since 2025-11-30
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use App\Repositories\UserRepositoryInterface;
use \Exception;

class AuthController extends Controller
{
    protected UserRepositoryInterface $userRepository;

    public function __construct(
        UserRepositoryInterface $userRepository
    ) {
        $this->userRepository = $userRepository;
    }

    /**
     * Authenticates a user.
     *
     * @param Request $request
     * @throws Exception $exception
     * @return JsonResponse
     */
    public function loginAction(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email:rfc,dns',
            'password' => [
                'required',
                'min:6'
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()->all()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $data = $validator->validated();

        if (Auth::attempt(['email' => $data['email'], 'password' => $data['password']])) {
            $user = Auth::user();
            $token = $user->createToken('API TOKEN', ['*'], now()->addWeek());

            return response()->json(['token' => $token->plainTextToken], Response::HTTP_OK);
        }

        return response()->json([
            'message' => __('app.user_not_authorized')
        ], Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Creates a new user instance.
     *
     * @param Request $request
     * @throws Exception $exception
     * @return JsonResponse
     */
    public function registerAction(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email:rfc,dns|unique:users,email',
            'name' => 'required|string|max:255',
            'password' => [
                'required',
                'min:6'
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()->all()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $data = $validator->validated();

        try {
            $this->userRepository->create($data);

            return response()->json([
                'message' => __('app.user_created')
            ],Response::HTTP_OK);
        } catch (Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
