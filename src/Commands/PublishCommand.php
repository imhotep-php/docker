<?php declare(strict_types=1);

namespace Imhotep\Docker\Commands;

use Imhotep\Console\Command\Command;
use Imhotep\Filesystem\Filesystem;

class PublishCommand extends Command
{
    use InteractsWithDocker;

    public static string $defaultName = 'docker:publish';

    public static string $defaultDescription = 'Publish the Imhotep Docker files';

    protected Filesystem $files;

    public function __construct(Filesystem $files)
    {
        $this->files = $files;
    }

    public function handle(): int
    {
        $this->copyDirectory(__DIR__.'/../../runtimes', base_path('docker'));

        $composePath = base_path('docker-compose.yml');
        
        file_put_contents($composePath,
            str_replace(
                './vendor/imhotep/docker/runtimes',
                './docker',
                file_get_contents($composePath)
            )
        );

        return 0;
    }

    protected function copyDirectory(string $from, string $to): void
    {
        $this->files->copyDirectory($from, $to);

        $from = str_replace(base_path().'/', '', realpath($from));
        $to = str_replace(base_path().'/', '', realpath($to));

        $this->components()->task(sprintf('Copying directory [%s] to [%s]', $from, $to));
    }
}