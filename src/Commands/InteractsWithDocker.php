<?php declare(strict_types=1);

namespace Imhotep\Docker\Commands;

use Imhotep\Process\Process;

trait InteractsWithDocker
{
    protected array $services = [
        'mysql',
        'pgsql',
        'mariadb',
        'redis',
        'memcached'
    ];

    protected array $defaultServices = ['mysql', 'redis'];

    protected function buildComposeFile(array $services): void
    {
        $composePath = base_path('docker-compose.yml');

        $compose = file_exists($composePath) ?
            yaml_parse_file($composePath) :
            yaml_parse_file(__DIR__.'../stubs/docker-compose.yml');

        foreach ($services as $service) {
            $compose[$service] = yaml_parse_file(__DIR__.'../stubs/'.$service.'.yml');
        }

        $yaml = yaml_emit($compose);

        file_put_contents(base_path('docker-compose.yml'), $yaml);
    }

    protected function prepareInstallation(array $services): void
    {
        // Ensure docker is installed...
        if ($this->runCommand('docker info > /dev/null 2>&1') !== 0) {
            return;
        }

        if (count($services) > 0) {
            $this->runCommand('./vendor/bin/sail pull '.implode(' ', $services));
        }

        $this->runCommand('./vendor/bin/sail build');
    }

    protected function runCommand(string $command): int
    {
        $process = Process::fromCommand($command);

        if ($process->isTtySupported()) {
            $process->tty(true);
        }

        return $process->run(function ($type, $data) {
            $this->output->write($data);
        });
    }
}