<?php
/**
 * This class handles related actions to Nginx webserver.
 *
 * It provides methods for Nginx webserver start, stop, restart, reload actions.
 *
 * @package App\Services
 * @author Eugene <e.a.andrushchenko@gmail.com>
 * @version 1.0.0
 * @since 2025-11-30
 */

namespace App\Services;

use Illuminate\Support\Facades\Process;
use App\Services\WebserverInterface;
use \Exception;

class NginxService implements WebserverInterface
{
    public const COMMAND_START = 'docker start nginx-hosts';
    public const COMMAND_STOP = 'docker stop nginx-hosts';
    public const COMMAND_RESTART = 'docker restart nginx-hosts';
    public const COMMAND_RELOAD = 'docker exec nginx-hosts nginx -s reload';

    /*
     * Starts Nginx.
     *
     * @throw Exception
     * @return bool
    */
    public function start(): bool
    {
        return $this->executeCommand(self::COMMAND_START);
    }

    /*
     * Stops Nginx.
     *
     * @throw Exception
     * @return bool
    */
    public function stop(): bool
    {
        return $this->executeCommand(self::COMMAND_STOP);
    }

    /*
     * Restarts Nginx.
     *
     * @throw Exception
     * @return bool
    */
    public function restart(): bool
    {
        return $this->executeCommand(self::COMMAND_RESTART);
    }

    /*
     * Reloads Nginx.
     *
     * @throw Exception
     * @return bool
    */
    public function reload(): bool
    {
        return $this->executeCommand(self::COMMAND_RELOAD);
    }

    /*
     * Execute command.
     *
     * @param string $command
     * @throw Exception
     * @return bool
    */
    public function executeCommand(string $command): bool
    {
        $result = Process::run($command);

        if ($result->successful()) {
            return true;
        }

        throw new Exception(
            ($result->failed()) ? $result->errorOutput() : __('app.command_failed')
        );
    }
}
