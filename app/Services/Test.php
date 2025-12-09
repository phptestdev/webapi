<?php
// app/DockerManager.php

class Test
{
    private $composeFile;
    private $overrideFile;
    private $workDir;

    public function __construct(string $workDir = '/srv')
    {
        $this->workDir = rtrim($workDir, '/');
        $this->composeFile = $this->workDir . '/docker-compose.yml';
        $this->overrideFile = $this->workDir . '/docker-compose.override.yml';
    }

    /**
     * Выполнить docker compose с аргументами
     * @param string $args - аргументы, например: 'up -d nginx'
     * @return array [exit_code, stdout, stderr]
     */
    public function runCompose(string $args): array
    {
        // используем полный путь к docker для надёжности
        $cmd = sprintf('docker compose -f %s -f %s %s 2>&1',
            escapeshellarg($this->composeFile),
            escapeshellarg($this->overrideFile),
            $args
        );

        return $this->runCommand($cmd);
    }

    /**
     * Простейший запуск shell-команды
     */
    private function runCommand(string $cmd): array
    {
        // Рабочая папка: /srv
        $descriptorSpec = [
            1 => ["pipe", "w"], // stdout
            2 => ["pipe", "w"], // stderr
        ];

        $process = proc_open($cmd, $descriptorSpec, $pipes, $this->workDir);
        if (!is_resource($process)) {
            return [1, '', 'proc_open failed'];
        }

        $stdout = stream_get_contents($pipes[1]);
        fclose($pipes[1]);

        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[2]);

        $status = proc_close($process);
        return [$status, $stdout, $stderr];
    }

    /**
     * Проверка свободен ли порт на хосте (локально)
     */
    public function isPortFree(int $port): bool
    {
        // Попытка привязаться к порту локально (в контейнере это проверяет хостовую сеть через docker.sock? — обычно проверяет внутри контейнера)
        // Лучше: доверять API/реестру занятых портов в docker-compose.override.yml и конфликтам Docker.
        $sock = @stream_socket_server("tcp://0.0.0.0:$port", $errno, $errstr);
        if ($sock === false) {
            return false;
        }
        fclose($sock);
        return true;
    }
}
