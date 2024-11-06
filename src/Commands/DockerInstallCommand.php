<?php declare(strict_types=1);

namespace Imhotep\Docker\Commands;

use Imhotep\Console\Command\Command;

class DockerInstallCommand extends Command
{
    use InteractsWithDocker;

    public static string $defaultName = 'docker:install';

    public static string $defaultDescription = 'Install Docker Compose file';

    public function handle(): void
    {
        $services = ['mysql','redis','memcache'];

        if ($invalidServices = array_diff($services, $this->services)) {
            $this->components()->error('Invalid services ['.implode(',', $invalidServices).'].');

            return;
        }

        $this->buildComposeFile($services);
        //$this->replaceEnvVariables($services);
        //$this->configurePhpUnit();

        //if ($this->option('devcontainer')) {
        //    $this->installDevContainer();
        //}

        $this->prepareInstallation($services);

        $this->output->writeln('');
        $this->components->info('Imhotep docker installed successfully. You may run your Docker containers using "imhodock up" command.');

        $this->output->writeln('<fg=gray>➜</> <options=bold>./vendor/bin/imhodock up</>');

        if (in_array('mysql', $services) ||
            in_array('mariadb', $services) ||
            in_array('pgsql', $services)) {
            $this->components->warn('A database service was installed. Run "imhotep migrate" to prepare your database:');

            $this->output->writeln('<fg=gray>➜</> <options=bold>./vendor/bin/imhodock imhotep migrate</>');
        }

        $this->output->writeln('');
    }
}