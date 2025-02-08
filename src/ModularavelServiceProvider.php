<?php

namespace Modularavel\Starter;

use Modularavel\Starter\Commands\InstallModularavelCommand;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ModularavelServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('modularavel')
            ->hasConfigFile()
            ->hasViews()
            // ->hasViewComponent('spatie', Alert::class)
            // ->hasViewComposer('*', MyViewComposer::class)
            ->sharesDataWithAllViews('downloads', 3)
            ->hasTranslations()
            ->hasAssets()
            // ->hasRoute('web')
            // ->hasMigration('create_package_tables')
            ->hasCommand(InstallModularavelCommand::class)
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->startWith(function (InstallCommand $command) {
                        $command->info('Hello, and welcome to my great new package!');
                    })
                    ->publishConfigFile()
                    ->publishAssets()
                    ->publishMigrations()
                    ->askToRunMigrations()
                    ->copyAndRegisterServiceProviderInApp()
                    ->askToStarRepoOnGitHub('modularavel/modularavel')
                    ->endWith(function (InstallCommand $command) {
                        $command->info('Have a great day!');
                        $command->call('modularavel:run');
                    });
            });
    }
}
