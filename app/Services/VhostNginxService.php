<?php
/**
 * This class handles related actions to Nginx webserver virtual hosts.
 *
 * It provides methods for Nginx webserver virtual hosts get, create, delete actions.
 *
 * @package App\Services
 * @author Eugene <e.a.andrushchenko@gmail.com>
 * @version 1.0.0
 * @since 2025-11-30
 */

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use App\Services\VhostInterface;
use App\Repositories\VhostRepositoryInterface;
use App\Repositories\AvailablePortRepository;
use App\Models\Vhost;
use App\Exceptions\HostDirectoryNotCreatedException;
use App\Exceptions\HostConfigFileNotCreatedException;
use App\Exceptions\HostNotFoundException;
use \Exception;

class VhostNginxService implements VhostInterface
{
    public const WEBSITE_DIRECTORY = '/var/www/hosts';
    public const WEBSITE_FILE = 'index.html';
    public const HOST_CONFIG_AVAILABLE_DIRECTORY = '/etc/nginx-hosts/conf.d/sites-available';
    public const HOST_CONFIG_ENABLED_DIRECTORY = '/etc/nginx-hosts/conf.d/sites-enabled';

    protected Vhost $vhostEntity;
    protected VhostRepositoryInterface $vhostRepository;
    protected AvailablePortRepository $availablePortRepository;

    public function __construct(
        VhostRepositoryInterface $vhostRepository,
        AvailablePortRepository $availablePortRepository
    ) {
        $this->vhostRepository = $vhostRepository;
        $this->availablePortRepository = $availablePortRepository;
    }

    /*
     * Gets virtual host entity.
     *
     * @param array $data
     * @throw HostNotFoundException
     * @return Vhost
    */
    public function get(array $data): Vhost
    {
        try {
            $vhost = $this->vhostRepository->get($data);

            if (!$vhost) {
                throw new Exception(__('app.vhost_entity_not_found'));
            }

            return $vhost;
        } catch (Exception $exception) {
            throw new HostNotFoundException(__('app.vhost_entity_not_found'));
        }
    }

    /*
     * Creates virtual host entity.
     *
     * @param array $data
     * @throw Exception
     * @return Vhost
    */
    public function create(array $data): Vhost
    {
        try {
            $vhost = DB::transaction(function() use ($data) {
                $availablePort = $this->availablePortRepository->getEntity();

                if ($availablePort) {
                    $port = $availablePort->port;
                } else {
                    $port = $this->vhostRepository->getAvailablePort();
                }

                $data['port'] = $port;
                $vhost = $this->vhostRepository->create($data);
                $this->availablePortRepository->delete($availablePort);
                $this->setHostEntity($vhost);

                return $vhost;
            });

            if (!$vhost) {
                throw new Exception(__('app.vhost_entity_cant_create'));
            }

            return $vhost;
        } catch (Exception $exception) {
            throw new Exception(__('app.vhost_entity_cant_create') . ' ' . $exception->getMessage());
        }
    }

    /*
     * Deletes virtual host entity.
     *
     * @return bool
    */
    public function delete(): bool
    {
        try {
            return DB::transaction(function() {
                $this->availablePortRepository->create(['port' => $this->vhostEntity->port]);
                $this->vhostRepository->delete($this->vhostEntity);

                return true;
            });
        } catch (Exception $exception) {
            return false;
        }
    }

    /*
     * Set virtual host entity.
     *
     * @param Vhost $vhost
     * @return bool
    */
    public function setHostEntity(Vhost $vhost): bool
    {
        $this->vhostEntity = $vhost;
        return true;
    }

    /*
     * Gets virtual host's directory path.
     *
     * @return string
    */
    public function getHostDirectory(): string
    {
        return self::WEBSITE_DIRECTORY . DIRECTORY_SEPARATOR . $this->vhostEntity->domain;
    }

    /*
     * Creates virtual host's directory with website file.
     *
     * @throw HostDirectoryNotCreatedException
     * @return bool
    */
    public function createHostDirectory(): bool
    {
        $websitePath = $this->getHostDirectory();
        $websiteFile = $websitePath . DIRECTORY_SEPARATOR . self::WEBSITE_FILE;

        try {
            if (!is_writable(self::WEBSITE_DIRECTORY)) {
                throw new Exception(__('app.directory_not_writable', ['dir' => self::WEBSITE_DIRECTORY]));
            }

            if (is_dir($websitePath)) {
                throw new Exception(__('app.directory_exists', ['dir' => $websitePath]));
            }

            mkdir($websitePath, 0775, true);

            $websiteContent = View::make('vhosts.website', [
                'domain' => $this->vhostEntity->domain,
            ])->render();

            file_put_contents($websiteFile, $websiteContent);

            return true;
        } catch (Exception $exception) {
            throw new HostDirectoryNotCreatedException($exception->getMessage());
        }
    }

    /*
     * Deletes virtual host's directory with website file.
     *
     * @param string $path
     * @return bool
    */
    public function deleteHostDirectory(): bool
    {
        try {
            $websitePath = $this->getHostDirectory();
            $websiteFile = $websitePath . DIRECTORY_SEPARATOR . self::WEBSITE_FILE;

            if (is_dir($websitePath) && is_writable($websitePath)) {
                if (file_exists($websiteFile) && is_writable($websiteFile)) {
                    unlink($websiteFile);
                }

                rmdir($websitePath);

                return true;
            }

            return false;
        } catch (Exception $exception) {
            return false;
        }
    }

    /*
     * Gets virtual host's available configuration file.
     *
     * @return string
    */
    public function getHostConfigAvailableFile(): string
    {
        return self::HOST_CONFIG_AVAILABLE_DIRECTORY . DIRECTORY_SEPARATOR . $this->vhostEntity->domain . '.conf';
    }

    /*
     * Gets virtual host's enabled configuration file.
     *
     * @return string
    */
    public function getHostConfigEnabledFile(): string
    {
        return self::HOST_CONFIG_ENABLED_DIRECTORY . DIRECTORY_SEPARATOR . $this->vhostEntity->domain . '.conf';
    }

    /*
     * Creates virtual host's configuration file.
     *
     * @throw HostConfigFileNotCreatedException
     * @return bool
    */
    public function createHostConfigFile(): bool
    {
        $vhostConfigAvailable = $this->getHostConfigAvailableFile();
        $vhostConfigEnabled = $this->getHostConfigEnabledFile();

        try {
            if (file_exists($vhostConfigAvailable)) {
                throw new Exception(__('app.config_file_exists', ['dir' => $vhostConfigAvailable]));
            }

            $vhostConfig = View::make('vhosts.nginx', [
                'domain' => $this->vhostEntity->domain,
                'port' => $this->vhostEntity->port,
                'root' => self::WEBSITE_DIRECTORY . DIRECTORY_SEPARATOR . $this->vhostEntity->domain,
            ])->render();

            file_put_contents($vhostConfigAvailable, $vhostConfig);
            file_put_contents($vhostConfigEnabled, $vhostConfig);

            return true;
        } catch (Exception $exception) {
            throw new HostConfigFileNotCreatedException($exception->getMessage());
        }
    }

    /*
     * Deletes virtual host's configuration file.
     *
     * @throw Exception
     * @return bool
    */
    public function deleteHostConfigFile(): bool
    {
        $vhostConfigAvailable = $this->getHostConfigAvailableFile();
        $vhostConfigEnabled = $this->getHostConfigEnabledFile();

        try {
            if (file_exists($vhostConfigEnabled) && is_writable($vhostConfigEnabled)
                && file_exists($vhostConfigAvailable) && is_writable($vhostConfigAvailable)
            ) {
                if (unlink($vhostConfigEnabled) && unlink($vhostConfigAvailable)) {
                    return true;
                }
            }

            return false;
        } catch (Exception $exception) {
            return false;
        }
    }
}
