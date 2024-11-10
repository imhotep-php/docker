<?php declare(strict_types=1);

namespace Imhotep\Docker\Commands;

use Imhotep\Console\Command\Command;
use Imhotep\Console\Input\InputOption;

class AddCommand extends Command
{
    use InteractsWithDocker;

    public static string $defaultName = 'docker:add';

    public static string $defaultDescription = 'Add a service to an existing installation';

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
        $this->components()->info('Additional services installed successfully.');
    }

    public function getOptions(): array
    {
        return [
            InputOption::builder('services')->valueOptional()
                ->description('The services that should be added')
                ->build()
        ];
    }
}