<?php

namespace Modularavel\Modularavel\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\Prohibitable;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Composer;
use Illuminate\Support\HigherOrderTapProxy;
use Illuminate\Support\Str;
use JsonException;
use RuntimeException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

use function Illuminate\Support\php_binary;

class InstallModularavelCommand extends Command
{
    use Prohibitable;

    public $signature = 'modularavel:run {--pest : Indicates if Pest should be installed}
                                         {--composer=global : Absolute path to the Composer binary which should be used to install packages}';

    public $description = 'Modularavel install command';

    /**
     * The Composer instance.
     */
    protected Composer $composer;

    /**
     * Update the dependencies in the "package.json" file.
     */
    protected static function updateNodePackages(callable $callback, bool $dev = true): void
    {
        if (! file_exists(base_path('package.json'))) {
            return;
        }

        $configurationKey = $dev ? 'devDependencies' : 'dependencies';

        $packages = json_decode(file_get_contents(base_path('package.json')), true);

        $packages[$configurationKey] = $callback(
            array_key_exists($configurationKey, $packages) ? $packages[$configurationKey] : [],
            $configurationKey
        );

        ksort($packages[$configurationKey]);

        file_put_contents(
            base_path('package.json'),
            json_encode($packages, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT).PHP_EOL
        );
    }

    /**
     * Update the scripts in the "package.json" file.
     */
    protected static function updateNodeScripts(callable $callback): void
    {
        if (! file_exists(base_path('package.json'))) {
            return;
        }

        $content = json_decode(file_get_contents(base_path('package.json')), true);

        $content['scripts'] = $callback(
            array_key_exists('scripts', $content) ? $content['scripts'] : []
        );

        file_put_contents(
            base_path('package.json'),
            json_encode($content, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT).PHP_EOL
        );
    }

    /**
     * Delete the "node_modules" directory and remove the associated lock files.
     */
    protected static function flushNodeModules(): void
    {
        tap(new Filesystem, function ($files) {
            $files->deleteDirectory(base_path('node_modules'));
            $files->delete(base_path('pnpm-lock.yaml'));
            $files->delete(base_path('yarn.lock'));
            $files->delete(base_path('bun.lockb'));
            $files->delete(base_path('deno.lock'));
            $files->delete(base_path('package-lock.json'));
        });
    }

    /**
     * Install the given package using Composer.
     */
    public function handle(): int
    {
        $this->initComposer();

        $this->copyStubsToBasePath();

        $this->showTerminalAppName();

        $this->askForModulesFolderName();

        $this->installModulePackages();

        // $this->removeDarkClasses();

        $this->installPredis();

        $this->installDebugBar();

        // $this->flushNodeModules();

        $this->runCommands([
            'php artisan volt:install',
            'php artisan livewire:publish --config',
            /*'php artisan vendor:publish --provider="Nwidart\\Modules\\LaravelModulesServiceProvider"',
            'php artisan vendor:publish --tag=modules-livewire-config',*/
        ]);

        $this->installRequiredNodePackages();

        $this->modifyComposerJson([
            'scripts' => [
                'post-update-cmd' => [
                    '@php artisan vendor:publish --tag=livewire:assets --ansi --force',
                ],
            ],
        ]);

        $this->info('Running artisan optimize:clear command...');

        $this->runCommands([
            $this->phpBinary().' artisan optimize:clear',
        ]);

        $this->output->writeln('<fg=bright-green>Modularavel installation complete.</>');

        return self::SUCCESS;
    }

    protected function initComposer(): void
    {
        $this->composer = new Composer(new Filesystem, base_path());
    }

    protected function showTerminalAppName(): void
    {
        $this->comment(PHP_EOL."  <fg=red>
  __  __           _       _                              _
 |  \/  | ___   __| |_   _| |    __ _ _ __ __ ___   _____| |
 | |\/| |/ _ \ / _` | | | | |   / _` | '__/ _` \ \ / / _ \ |
 | |  | | (_) | (_| | |_| | |__| (_| | | | (_| |\ V /  __/ |
 |_|  |_|\___/ \__,_|\__,_|_____\__,_|_|  \__,_| \_/ \___|_|
        </>".PHP_EOL.PHP_EOL);
    }

    /**
     * Replace a given string within a given file.
     */
    protected function replaceInFile(array|string $search, array|string $replace, string $path): void
    {
        file_put_contents($path, str_replace($search, $replace, file_get_contents($path)));
    }

    protected function askForModulesFolderName(): string
    {
        $folder = 'Modules';

        // Ask for the Modules folder name and create it if it doesn't exist
        // $answer = $this->ask('Modules folder:', 'Modules');
        $this->warn("Creating: $folder folder on project root...");

        // Check if the Modules folder name is valid
        /*  if (! preg_match('/^[A-Za-z]+$/', $folder)) {
              $this->error('Invalid folder name. Only letters are allowed. Please try again!');

              return $this->askForModulesFolderName();
          }*/

        // $folder = Str::ucfirst($folder);

        // Check if another package already uses the Modules folder name
        (new Filesystem)->ensureDirectoryExists(base_path($folder), 0755, true);

        // Add the Modules folder to the composer.json file
        $this->modifyComposerJson([
            'config' => [
                'allow-plugins' => [
                    'nwidart/laravel-modules' => true,
                    'joshbrw/laravel-module-installer' => true,
                    'wikimedia/composer-merge-plugin' => true,
                ],
            ],
            'extra' => [
                'merge-plugin' => [
                    'include' => [
                        "$folder/*/composer.json",
                    ],
                ],
            ],
        ]);

        return $folder;
    }

    /**
     * Modify the "composer.json" file.
     *
     * @throws JsonException
     */
    protected function modifyComposerJson(array $value): void
    {
        $this->composer->modify(function (array $content) use ($value) {
            return array_replace_recursive($content, $value);
        });

        $this->info('composer.json file modified successfully.');
    }

    /**
     * Determine if the given Composer package is installed.
     */
    protected function hasComposerPackage(string $package): bool
    {
        $packages = json_decode(file_get_contents(base_path('composer.json')), true);

        return array_key_exists($package, $packages['require'] ?? [])
            || array_key_exists($package, $packages['require-dev'] ?? []);
    }

    /**
     * Removes the given Composer Packages from the application.
     */
    protected function removeComposerPackages(array $packages, bool $asDev = false): bool
    {
        $composer = $this->option('composer');

        if ($composer !== 'global') {
            $command = ['php', $composer, 'remove'];
        }

        $command = array_merge(
            $command ?? ['composer', 'remove'],
            $packages,
            $asDev ? ['--dev'] : [],
        );

        return (new Process($command, base_path(), ['COMPOSER_MEMORY_LIMIT' => '-1']))
            ->setTimeout(null)
            ->run(function ($type, $output) {
                $this->output->write($output);
            }) === 0;
    }

    /**
     * Get the composer command for the environment.
     */
    protected function findComposer(): string
    {
        return implode(' ', $this->composer->findComposer());
    }

    /**
     * Get the path to the appropriate PHP binary.
     */
    protected function phpBinary(): string
    {
        if (function_exists('Illuminate\Support\php_binary')) {
            return php_binary();
        }

        return (new PhpExecutableFinder)->find(false) ?: 'php';
    }

    /**
     * Remove Tailwind dark classes from the given files.
     */
    protected function removeDarkClasses(Finder $finder): void
    {
        $darkModeConfirm = $this->confirm('Remove Tailwind dark mode?');

        if (! $darkModeConfirm) {
            return; // Do nothing
        }

        foreach ($finder as $file) {
            file_put_contents($file->getPathname(), preg_replace('/\sdark:[^\s"\']+/', '', $file->getContents()));
        }
    }

    /**
     * Install the required packages for the Modules feature.
     */
    protected function installModulePackages(): void
    {
        $packages = [
            'livewire/livewire',
            'livewire/volt',
            'livewire/flux',
            'nwidart/laravel-modules',
            'mhmiton/laravel-modules-livewire',
        ];

        foreach ($packages as $package) {
            if (! $this->hasComposerPackage($package)) {
                $this->info("Installing package: $package ...");

                $this->requireComposerPackages([$package], false);
            }
        }
    }

    public function installPredis()
    {
        $choice = $this->confirm('Do you want to install predis/predis to use as Redis client?', true);

        if (! $choice) {
            return; // Do nothing
        }

        if ($this->hasComposerPackage('predis/predis')) {
            $this->output->writeln('<fg=green>Predis already installed! Skipping...</>');

            return;
        }

        $this->info('Installing predis/predis dependency...');

        $success = $this->requireComposerPackages([
            'predis/predis',
        ], false);

        if ($success) {

            $this->info('Predis installed successfully!');

            // Replace a cache driver with an array driver
            $this->replaceInFile(
                [
                    'REDIS_CLIENT=phpredis',
                    'CACHE_STORE=database',
                    'CACHE_PREFIX=',
                ],
                [
                    'REDIS_CLIENT=predis',
                    'CACHE_STORE=redis',
                    'CACHE_PREFIX="${APP_NAME}_cache"',
                ],
                base_path('.env')
            );
        } else {
            $this->error('Predis installation failed :(');
        }
    }

    /**
     * Installs the given Composer Packages into the application.
     */
    protected function requireComposerPackages(array $packages, bool $asDev = false): bool
    {
        $composer = $this->option('composer');

        if ($composer !== 'global') {
            $command = ['php', $composer, 'require'];
        }

        $command = array_merge(
            $command ?? ['composer', 'require'],
            $packages,
            $asDev ? ['--dev'] : [],
        );

        return (new Process($command, base_path(), ['COMPOSER_MEMORY_LIMIT' => '-1']))
            ->setTimeout(null)
            ->run(function ($type, $output) {
                $this->output->write($output);
            }) === 0;
    }

    /**
     * Run the given commands.
     */
    protected function runCommands(array $commands): void
    {
        $process = Process::fromShellCommandline(implode(' && ', $commands), null, null, null, null);

        if ('\\' !== DIRECTORY_SEPARATOR && file_exists('/dev/tty') && is_readable('/dev/tty')) {
            try {
                $process->setTty(true);
            } catch (RuntimeException $e) {
                $this->output->writeln('  <bg=yellow;fg=black> WARN </> '.$e->getMessage().PHP_EOL);
            }
        }

        $process->run(function ($type, $line) {
            $this->output->write('    '.$line);
        });
    }

    private function installDebugBar(): int
    {
        $confirmInstallDebugBar = $this->confirm('Do you want to install Debugbar?', true);

        if (! $confirmInstallDebugBar) {
            return 0;
        }

        if ($this->hasComposerPackage('barryvdh/laravel-debugbar')) {
            $this->output->writeln('<fg=green>Debugbar already installed.</>');

            return 0;
        }

        $this->info('Installing barryvdh/laravel-debugbar as a development dependency...');

        if (! $this->requireComposerPackages(['barryvdh/laravel-debugbar:^3.6'], true)) {
            return 1;
        }

        $this->info('Publishing Debugbar configuration...');

        $this->runCommands([
            'php artisan vendor:publish --provider="Barryvdh\\Debugbar\\ServiceProvider"',
        ]);

        $this->info('Debugbar installed successfully!');

        return 0;
    }

    protected function installRequiredNodePackages(): void
    {
        $choice = $this->choice('Which package manager do you want to use?', ['npm', 'yarn', 'pnpm'], 0);

        /*$installPackages = [
            'postcss' => '^8.4.31',
            'tailwindcss' => '^3.2.1',
            'bulma' => '^1.0.3',
        ];

        $this->updateNodePackages(function ($packages) use ($installPackages) {
            return $installPackages + $packages;
        });*/

        if ($choice === 'npm') {
            $this->installNodePackages();
        } elseif ($choice === 'yarn') {
            $this->installYarnPackages();
        } elseif ($choice === 'pnpm') {
            $this->installPnpmPackages();
        } else {
            $this->error('Please run "npm run dev" or "yarn run dev" to compile your assets...');

            $this->installRequiredNodePackages();
        }
    }

    protected function installYarnPackages(): void
    {
        $this->info('Installing node dependencies with yarn...');

        $this->runCommands([
            'yarn',
            'yarn run build',
        ]);
    }

    protected function installPnpmPackages(): void
    {
        $this->info('Installing node dependencies with pnpm...');

        $this->runCommands([
            'pnpm install',
            'pnpm run build',
        ]);
    }

    protected function installNodePackages(): void
    {
        $this->info('Installing node dependencies with npm...');

        $this->runCommands([
            'npm install --legacy-peer-deps',
            'npm run build',
        ]);
    }

    protected function copyStubsToBasePath(): void
    {
        tap(new Filesystem, function (Filesystem|HigherOrderTapProxy $files) {
            $files->delete([
                base_path('vite.config.js'),
                base_path('vite.config.ts'),
                base_path('tailwind.config.js'),
                base_path('tailwind.config.cjs'),
                base_path('tailwind.config.mjs'),
                base_path('tailwind.config.ts'),
                base_path('postcss.config.js'),
                base_path('postcss.config.cjs'),
                base_path('postcss.config.mjs'),
                base_path('postcss.config.ts'),
                base_path('vite-module-loader.js'),
                base_path('vite-module-loader.cjs'),
                base_path('vite-module-loader.mjs'),
                base_path('vite-module-loader.ts'),
            ]);

            $files->deleteDirectory(resource_path('css'));
            $files->deleteDirectory(resource_path('js'));

            $files->ensureDirectoryExists(base_path('stubs'), 0755, true);

            $files->copyDirectory(__DIR__.'/../../stubs', base_path());

            // $files->deleteDirectory(resource_path('views'));
            // $files->copyDirectory(base_path('stubs/app-views'), base_path());
            // $files->copyDirectory(base_path('stubs/root'), base_path());
        });
    }
}
