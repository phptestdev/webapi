<?php
/**
 * This class handles related actions to virtual host.
 *
 * It provides methods for virtual host create, delete actions.
 *
 * @package App\Http\Controllers
 * @author Eugene <e.a.andrushchenko@gmail.com>
 * @version 1.0.0
 * @since 2025-11-30
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Services\VhostInterface;
use App\Services\WebserverInterface;
use App\Repositories\VhostRepositoryInterface;
use App\Http\Resources\VhostResource;
use App\Http\Resources\VhostCollection;
use App\Exceptions\HostDirectoryNotCreatedException;
use App\Exceptions\HostConfigFileNotCreatedException;
use App\Exceptions\HostNotFoundException;
use \Exception;

class VhostController extends Controller
{
    protected VhostInterface $vhost;
    protected WebserverInterface $webserver;
    protected VhostRepositoryInterface $vhostRepository;

    public function __construct(
        VhostInterface $vhost,
        WebserverInterface $webserver,
        VhostRepositoryInterface $vhostRepository
    ) {
        $this->vhost = $vhost;
        $this->webserver = $webserver;
        $this->vhostRepository = $vhostRepository;
    }

    /*
     * Get virtual host list.
     *
     * @param Request $request
     * @return JsonResponse
    */
    public function index(): JsonResponse
    {
        $paginator = $this->vhostRepository->getList(Auth::id());
        $pager = [
            'total' => $paginator->total(),
            'lastPage' => $paginator->lastPage(),
            'perPage' => $paginator->perPage(),
            'currentPage' => $paginator->currentPage(),
        ];
        $response = [];
        $response['hosts'] = new VhostCollection($paginator);
        $response['meta'] = [
            'pager' => $pager
        ];

        return response()->json($response, 200);
    }

    /*
     * Gets virtual host data.
     *
     * @param int $id
     * @return JsonResponse
    */
    public function get(int $id): JsonResponse
    {
        try {
            $vhost = $this->vhost->get(['id' => $id, 'user_id' => Auth::id()]);

            return response()->json([
                'host' => new VhostResource($vhost)
            ], Response::HTTP_OK);
        } catch (Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /*
     * Creates virtual host.
     *
     * @param Request $request
     * @return JsonResponse
    */
    public function create(Request $request): JsonResponse
    {
        $request->merge([
            'domain' => strtolower($request->input('name')),
            'user_id' => Auth::id()
        ]);

        $validator = Validator::make($request->all(), [
            'domain' => [
                'required',
                'unique:vhosts,domain',
                'max:255',
                'string',
                'regex:/^([a-zA-Z0-9-]+\.)+[a-zA-Z]{2,}$/'
            ],
            'user_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()->all()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $data = $validator->validated();

        try {
            $vhost = $this->vhost->create($data);
            $this->vhost->createHostDirectory();
            $this->vhost->createHostConfigFile();
            $this->webserver->reload();

            return response()->json([
                'message' => __('app.vhost_created'),
                'host' => new VhostResource($vhost)
            ], Response::HTTP_OK);
        } catch (HostDirectoryNotCreatedException $exception) {
            $this->vhost->deleteHostDirectory();
            $this->vhost->delete();

            return response()->json([
                'message' => $exception->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (HostConfigFileNotCreatedException $exception) {
            $this->vhost->deleteHostConfigFile();
            $this->vhost->deleteHostDirectory();
            $this->vhost->delete();

            return response()->json([
                'message' => $exception->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /*
     * Deletes virtual host.
     *
     * @param Request $request
     * @return JsonResponse
    */
    public function delete(Request $request): JsonResponse
    {
        $request->merge([
            'user_id' => Auth::id()
        ]);

        $validator = Validator::make($request->all(), [
            'id' => 'required|int|min:1',
            'user_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()->all()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $data = $validator->validated();

        try {
            $vhost = $this->vhost->get($data);
            $this->vhost->setHostEntity($vhost);
            $this->vhost->deleteHostConfigFile();
            $this->vhost->deleteHostDirectory();
            $this->vhost->delete();
            $this->webserver->reload();

            return response()->json([
                'message' => __('app.vhost_deleted')
            ],Response::HTTP_OK);
        } catch (HostNotFoundException $exception) {
            return response()->json([
                'message' => $exception->getMessage()
            ], Response::HTTP_NOT_FOUND);
        } catch (Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
