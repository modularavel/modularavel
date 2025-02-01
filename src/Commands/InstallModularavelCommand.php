<?php

namespace Modularavel\Modularavel\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\Prohibitable;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Composer;
use Illuminate\Support\Str;
use JsonException;
use RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

use function Illuminate\Support\php_binary;
use function Laravel\Prompts\confirm;
use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\select;

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

        $this->showTerminalAppName();

        $this->modifyTailwindConfig();

        $this->choiceStarterKit();

        // $this->installModulePackages();

        // $this->installLaravelDebugBar();

        $this->info('Modularavel installation complete!');

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
     * Modifies the tailwind.config.js file to include the Modules folder.
     */
    protected function modifyTailwindConfig(): int
    {
        $file = $this->getTailwindConfig();

        $extensions = '{blade.php,js,ts,cjs,mjs,jsx,tsx,vue}';

        $this->replaceInFile("'./resources/views/**/*.blade.php',", "'./resources/**/*.$extensions',\n        './Modules/*/resources/**/*.$extensions',", $file);

        $this->info('Tailwind config file modified successfully.');

        return self::SUCCESS;
    }

    /**
     * Returns the path to the tailwind.config.js file.
     */
    private function getTailwindConfig(): string
    {
        $extensions = ['js', 'ts', 'cjs', 'mjs'];

        $file = null;

        foreach ($extensions as $extension) {
            if (file_exists($tw = base_path('tailwind.config.'.$extension))) {
                $file = $tw;
                break;
            }
        }

        return $file;
    }

    /**
     * Replace a given string within a given file.
     */
    protected function replaceInFile(string $search, string $replace, string $path): void
    {
        file_put_contents($path, str_replace($search, $replace, file_get_contents($path)));
    }

    /**
     *  Choose the starter kit to install
     */
    protected function choiceStarterKit(): void
    {
        $choice = $this->choice('Select a starter kit', [
            'Unpoly',
            'Livewire',
        ], 1);

        match ($choice) {
            'Unpoly' => $this->installUnpolyStack(),
            'Livewire' => $this->installBreezeStack(),
        };
    }

    private function installUnpolyStack() {}

    protected function installBreezeStack(): void
    {
        $this->choice('Select a livewire option:', [
            'Livewire volt',
            'Livewire component class',
        ], 0);

        $selectedOptionCSSFramework = $this->selectedOptionCSSFramework();

        match ($selectedOptionCSSFramework) {
            'TailwindCSS' => $this->installTailwindCSS(),
            'Bootstrap5' => $this->installBootstrap5(),
            'Quasar Framework' => $this->installQuasarFramework(),
        };

        $this->askForModulesFolderName();

        $this->newLine();

        $this->info('Installing Breeze Stack... '.$selectedOptionCSSFramework);
    }

    protected function selectedOptionCSSFramework(): array|string
    {
        return $this->choice('Select your preferred CSS framework', [
            'TailwindCSS',
            'Bootstrap5',
            'Quasar Framework',
        ], 0);
    }

    private function installTailwindCSS() {}

    private function installBootstrap5()
    {
        $this->choice('Select a Bootstrap option:', [
            'Bootstrap 5 with TailwindCSS',
            'Bootstrap 5 with Bulma',
        ], 0);
    }

    private function installQuasarFramework() {}

    protected function askForModulesFolderName(): string
    {
        // Ask for the Modules folder name and create it if it doesn't exist
        $answer = $this->ask('Modules folder:', 'Modules');

        // Check if the Modules folder name is valid
        if (! preg_match('/^[A-Za-z]+$/', $answer)) {
            $this->error('Invalid folder name. Only letters are allowed. Please try again!');

            return $this->askForModulesFolderName();
        }

        $answer = Str::ucfirst($answer);

        // Check if another package already uses the Modules folder name
        (new Filesystem)->ensureDirectoryExists(base_path($answer), 0755, true);

        // Add the Modules folder to the composer.json file
        $this->modifyComposerJson([
            'extra' => [
                'merge-plugin' => [
                    'include' => [
                        "$answer/*/composer.json",
                    ],
                ],
            ],
        ]);

        return $answer;
    }

    /**
     * Modify the "composer.json" file.
     *
     * @throws JsonException
     */
    protected function modifyComposerJson(array $value): void
    {
        $this->composer->modify(function (array $content) use ($value) {
            return array_merge($content, $value);
        });

        $this->info('composer.json file modified successfully.');

        $this->line();

        $this->warn('Running composer dump-autoload...');

        $this->composer->dumpAutoloads();
    }

    /**
     * Install the given middleware names into the application.
     */
    protected function installMiddleware(array|string $names, string $group = 'web', string $modifier = 'append'): void
    {
        $bootstrapApp = file_get_contents(base_path('bootstrap/app.php'));

        $names = collect(Arr::wrap($names))
            ->filter(fn ($name) => ! Str::contains($bootstrapApp, $name))
            ->whenNotEmpty(function ($names) use ($bootstrapApp, $group, $modifier) {
                $names = $names->map(fn ($name) => "$name")
                    ->implode(','.PHP_EOL.'            ');

                $bootstrapApp = str_replace(
                    '->withMiddleware(function (Middleware $middleware) {',
                    '->withMiddleware(function (Middleware $middleware) {'
                    .PHP_EOL."        \$middleware->$group($modifier: ["
                    .PHP_EOL."            $names,"
                    .PHP_EOL.'        ]);'
                    .PHP_EOL,
                    $bootstrapApp,
                );

                file_put_contents(base_path('bootstrap/app.php'), $bootstrapApp);
            });
    }

    /**
     * Install the given middleware aliases into the application.
     */
    protected function installMiddlewareAliases(array $aliases): void
    {
        $bootstrapApp = file_get_contents(base_path('bootstrap/app.php'));

        $aliases = collect($aliases)
            ->filter(fn ($alias) => ! Str::contains($bootstrapApp, $alias))
            ->whenNotEmpty(function ($aliases) use ($bootstrapApp) {
                $aliases = $aliases->map(fn ($name, $alias) => "'$alias' => $name")
                    ->implode(','.PHP_EOL.'            ');

                $bootstrapApp = str_replace(
                    '->withMiddleware(function (Middleware $middleware) {',
                    '->withMiddleware(function (Middleware $middleware) {'
                    .PHP_EOL.'        $middleware->alias(['
                    .PHP_EOL."            $aliases,"
                    .PHP_EOL.'        ]);'
                    .PHP_EOL,
                    $bootstrapApp,
                );

                file_put_contents(base_path('bootstrap/app.php'), $bootstrapApp);
            });
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
        foreach ($finder as $file) {
            file_put_contents($file->getPathname(), preg_replace('/\sdark:[^\s"\']+/', '', $file->getContents()));
        }
    }

    /**
     * Prompt for missing input arguments using the returned questions.
     *
     * @return array<string, callable>
     */
    protected function promptForMissingArgumentsUsing(): array
    {
        return [
            'stack' => fn () => select(
                label: 'Which Breeze stack would you like to install?',
                options: [
                    'blade' => 'Blade with Alpine',
                    'livewire' => 'Livewire (Volt Class API) with Alpine',
                    'livewire-functional' => 'Livewire (Volt Functional API) with Alpine',
                    'react' => 'React with Inertia',
                    'vue' => 'Vue with Inertia',
                    'api' => 'API only',
                ],
                scroll: 6,
            ),
        ];
    }

    /**
     * Interact further with the user if they were prompted for missing
     * arguments.
     */
    protected function afterPromptingForMissingArguments(InputInterface $input, OutputInterface $output): void
    {
        $stack = $input->getArgument('stack');

        if (in_array($stack, ['react', 'vue'])) {
            collect(multiselect(
                label: 'Would you like any optional features?',
                options: [
                    'dark' => 'Dark mode',
                    'ssr' => 'Inertia SSR',
                    'typescript' => 'TypeScript',
                    'eslint' => 'ESLint with Prettier',
                ],
                hint: 'Use the space bar to select options.'
            ))->each(fn ($option) => $input->setOption($option, true));
        } elseif (in_array($stack, [
            'blade',
            'livewire',
            'livewire-functional',
        ])) {
            $input->setOption('dark', confirm(
                label: 'Would you like dark mode support?',
                default: false
            ));
        }

        $input->setOption('pest', select(
            label: 'Which testing framework do you prefer?',
            options: ['Pest', 'PHPUnit'],
            default: 'Pest',
        ) === 'Pest');
    }

    /**
     * Determine if the user is using Pest.
     */
    protected function isUsingPest(): bool
    {
        return class_exists(\Pest\TestSuite::class);
    }

    /**
     * Install the required packages for the Modules feature.
     */
    protected function installModulePackages(): void
    {
        $this->requireComposerPackages([
            'nwidart/laravel-modules',
            'joshbrw/laravel-module-installer',
            'mhmiton/laravel-modules-livewire',
        ]);

        $this->runCommands([
            'php artisan vendor:publish --provider="Nwidart\\Modules\\LaravelModulesServiceProvider" --force',
            'php artisan vendor:publish --tag=modules-livewire-config --force',
        ]);

        $file = base_path('tailwind.config.js');

        $this->replaceInFile("'./resources/views/**/*.blade.php'", '
        `./resources/**/*.${blade.php,vue,js,jsx,ts,tsx,mjs,cjs}`,
        ./Modules/*/resources/**/*.${blade.php,vue,js,jsx,ts,tsx,mjs,cjs}`', $file);
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

    /**
     * Install the Laravel Debug Bar package.
     */
    protected function installLaravelDebugBar(): void
    {
        $this->requireComposerPackages([
            'barryvdh/laravel-debugbar',
        ], true);
    }
}
