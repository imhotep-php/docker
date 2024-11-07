<?php declare(strict_types=1);

namespace Imhotep\Docker\Commands;

use Imhotep\Process\Process;
use Symfony\Component\Yaml\Yaml;

trait InteractsWithDocker
{
    protected array $services = ['mysql', 'pgsql', 'mariadb', 'redis', 'memcached'];

    protected array $defaultServices = ['mariadb', 'redis'];

    protected string $environment = '';

    protected function buildComposeFile(array $services): void
    {
        $composePath = base_path('docker-compose.yml');

        $compose = file_exists($composePath) ?
            Yaml::parseFile($composePath) :
            Yaml::parseFile(__DIR__.'/../../stubs/docker-compose.stub');

        foreach ($services as $service) {
            $compose['services'][$service] = Yaml::parseFile(__DIR__.'/../../stubs/'.$service.'.stub')[$service];

            if (in_array($service, ['mysql', 'pgsql', 'mariadb', 'redis'])
                    && isset($compose['services'][$service]['volumes'])) {
                $compose['volumes']["imhotep-$service"] = ['driver' => 'local'];
            }
        }

        if (empty($compose['volumes'])) {
            unset($compose['volumes']);
        }

        $yaml = Yaml::dump($compose, Yaml::DUMP_OBJECT_AS_MAP);

        $phpVersion = $this->input->hasOption('php') ? $this->input->getOption('php') : '8.3';
        $yaml = str_replace('{{PHP_VERSION}}', $phpVersion, $yaml);

        file_put_contents(base_path('docker-compose.yml'), $yaml);
    }
    
    protected function updateEnvVariables(array $services): void
    {
        $this->environment = file_get_contents(base_path('.env'));
        
        if ((bool)array_intersect($this->services, $services)) {
            $this->updateEnvValue('DB_PORT', '3306');
            $this->updateEnvValue('DB_DATABASE', 'imhotep');
            $this->updateEnvValue('DB_USERNAME', 'docker');
            $this->updateEnvValue('DB_PASSWORD', 'password');
        }

        if (in_array('mysql', $services)) {
            $this->updateEnvValue('DB_CONNECTION', 'mysql');
            $this->updateEnvValue('DB_HOST', 'mysql');
        }
        elseif (in_array('pgsql', $services)) {
            $this->updateEnvValue('DB_CONNECTION', 'pgsql');
            $this->updateEnvValue('DB_HOST', 'pgsql');
            $this->updateEnvValue('DB_PORT', '5432');
        }
        elseif (in_array('mariadb', $services)) {
            $connection = config('database.connections.mariadb') ? 'mariadb' : 'mysql';
            $this->updateEnvValue('DB_CONNECTION', $connection);
            $this->updateEnvValue('DB_HOST', 'mariadb');
        }

        if (in_array('redis', $services)) {
            $this->updateEnvValue('REDIS_HOST', 'redis');
        }

        file_put_contents(base_path('.env'), $this->environment);
    }

    protected function updateEnvValue($variable, $value): void
    {
        $this->environment = preg_replace("/(#)?( )?$variable=.*/", "$variable=$value", $this->environment);
    }

    protected function prepareInstallation(array $services): void
    {
        // Ensure docker is installed...
        if ($this->runCommand('docker info > /dev/null 2>&1') !== 0) {
            return;
        }

        if (count($services) > 0) {
            $this->runCommand('./vendor/bin/docker pull '.implode(' ', $services));
        }

        $this->runCommand('./vendor/bin/docker build');
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