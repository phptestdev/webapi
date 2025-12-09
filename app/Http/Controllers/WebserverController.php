<?php
/**
 * This class handles webserver actions.
 *
 * It provides methods for webserver start, stop, restart, reload actions.
 *
 * @package App\Http\Controllers
 * @author Eugene <e.a.andrushchenko@gmail.com>
 * @version 1.0.0
 * @since 2025-11-30
 */

namespace App\Http\Controllers;

use App\Services\WebserverInterface;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use \Exception;

class WebserverController extends Controller
{
    protected WebserverInterface $webserver;

    public function __construct(
        WebserverInterface $webserver
    ) {
        $this->webserver = $webserver;
    }

    /*
     * Starts webserver.
     *
     * @return JsonResponse
    */
    public function start(): JsonResponse
    {
        try {
            $this->webserver->start();

            return response()->json([
                'message' => __('app.command_successful')
            ],Response::HTTP_OK);
        } catch (Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /*
     * Stops webserver.
     *
     * @return JsonResponse
    */
    public function stop(): JsonResponse
    {
        try {
            $this->webserver->stop();

            return response()->json([
                'message' => __('app.command_successful')
            ],Response::HTTP_OK);
        } catch (Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /*
     * Restarts webserver.
     *
     * @return JsonResponse
    */
    public function restart(): JsonResponse
    {
        try {
            $this->webserver->restart();

            return response()->json([
                'message' => __('app.command_successful')
            ],Response::HTTP_OK);
        } catch (Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /*
     * Reloads webserver.
     *
     * @return JsonResponse
    */
    public function reload(): JsonResponse
    {
        try {
            $this->webserver->reload();

            return response()->json([
                'message' => __('app.command_successful')
            ],Response::HTTP_OK);
        } catch (Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
