<?php declare(strict_types=1);

namespace Imhotep\Docker\Commands;

use Imhotep\Console\Command\Command;

class DockerPublishCommand extends Command
{
    use InteractsWithDocker;

    public static string $defaultName = 'docker:publish';

    public static string $defaultDescription = 'Publish the Imhotep Docker files';

    public function handle(): void
    {

    }
}