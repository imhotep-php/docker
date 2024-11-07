<?php declare(strict_types=1);

namespace Imhotep\Docker;

use Imhotep\Docker\Commands\AddCommand;
use Imhotep\Docker\Commands\InstallCommand;
use Imhotep\Docker\Commands\PublishCommand;
use Imhotep\Framework\Providers\ServiceProvider;

class DockerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->commands([
            'docker:install' => InstallCommand::class,
            'docker:publish' => PublishCommand::class,
            'docker:add' => AddCommand::class,
        ]);
    }
}