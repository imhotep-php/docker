<?php declare(strict_types=1);

namespace Imhotep\Docker\Commands;

use Imhotep\Console\Command\Command;
use Imhotep\Console\Input\InputArgument;
use Imhotep\Console\Input\InputOption;
use Imhotep\Console\Input\OptionBuilder;

class InstallCommand extends Command
{
    use InteractsWithDocker;

    public static string $defaultName = 'docker:install';

    public static string $defaultDescription = 'Install Docker Compose file';

    public function handle(): void
    {
        $services = $this->defaultServices;

        if ($this->input->hasOption('with')) {
            $services = explode(',', $this->input->getOption('with'));
        }

        if ($invalidServices = array_diff($services, $this->services)) {
            $this->components()->error('Invalid services ['.implode(', ', $invalidServices).']');

            return;
        }

        $this->buildComposeFile($services);
        $this->updateEnvVariables($services);
        $this->prepareInstallation($services);

        $this->output->writeln('');
        $this->components()->info('Imhotep docker installed successfully. You may run your Docker containers with command:');
        $this->output->writeln('<fg=gray>➜</> <options=bold>./vendor/bin/docker up</>');

        if ((bool)array_intersect(['mysql','mariadb','pgsql'], $this->services)) {
            $this->components()->warn('A database service was installed. Run migarate to prepare your database:');

            $this->output->writeln('<fg=gray>➜</> <options=bold>./vendor/bin/docker imhotep migrate</>');
        }

        $this->output->writeln('');
    }

    public function getOptions(): array
    {
        return [
            InputOption::builder('with')->valueOptional()
                ->description('The services that should be included in the installation')
                ->build(),
            InputOption::builder('php')->valueOptional()->default('8.3')
                ->description('The PHP version that should be used')
                ->build()
        ];
    }
}