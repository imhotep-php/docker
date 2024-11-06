<?php declare(strict_types=1);

namespace Imhotep\Docker;

use Imhotep\Docker\Commands\DockerInstallCommand;
use Imhotep\Docker\Commands\DockerPublishCommand;
use Imhotep\Framework\Providers\ServiceProvider;

class DockerServiceProvider extends ServiceProvider
{
    protected array $commands = [
        'docker:install' => DockerInstallCommand::class,
        'docker:publish' => DockerPublishCommand::class
    ];

    public function boot()
    {

    }
}