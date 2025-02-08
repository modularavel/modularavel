<?php

use Modularavel\Modularavel\Commands\InstallModularavelCommand;
use Symfony\Component\Console\Command\Command;

test('run install modularavel command with inertia stack', function () {
    $this->artisan(InstallModularavelCommand::class, [
        'stack' => 'inertia',
    ])->assertExitCode(Command::SUCCESS);
});

test('run install modularavel command with inertia stack and dark mode option enabled', function () {
    $this->artisan(InstallModularavelCommand::class, [
        'stack' => 'inertia',
        '--dark' => true,
    ])->assertExitCode(Command::SUCCESS);
});

test('run install modularavel command with inertia stack and dark mode option disabled', function () {
    $this->artisan(InstallModularavelCommand::class, [
        'stack' => 'inertia',
        '--dark' => false,
    ])->assertExitCode(Command::SUCCESS);
});

test('run install modularavel command with inertia stack and teams option enabled', function () {
    $this->artisan(InstallModularavelCommand::class, [
        'stack' => 'inertia',
        '--teams' => true,
    ])->assertExitCode(Command::SUCCESS);
});

test('run install modularavel command with inertia stack and teams option disabled', function () {
    $this->artisan(InstallModularavelCommand::class, [
        'stack' => 'inertia',
        '--teams' => false,
    ])->assertExitCode(Command::SUCCESS);
});

test('run install modularavel command with inertia stack and api option enabled', function () {
    $this->artisan(InstallModularavelCommand::class, [
        'stack' => 'inertia',
        '--api' => true,
    ])->assertExitCode(Command::SUCCESS);
});

test('run install modularavel command with inertia stack and api option disabled', function () {
    $this->artisan(InstallModularavelCommand::class, [
        'stack' => 'inertia',
        '--api' => false,
    ])->assertExitCode(Command::SUCCESS);
});

test('run install modularavel command with inertia stack and verification option enabled', function () {
    $this->artisan(InstallModularavelCommand::class, [
        'stack' => 'inertia',
        '--verification' => true,
    ])->assertExitCode(Command::SUCCESS);
});

test('run install modularavel command with inertia stack and verification option disabled', function () {
    $this->artisan(InstallModularavelCommand::class, [
        'stack' => 'inertia',
        '--verification' => false,
    ])->assertExitCode(Command::SUCCESS);
});

test('run install modularavel command with inertia stack and pest option enabled', function () {
    $this->artisan(InstallModularavelCommand::class, [
        'stack' => 'inertia',
        '--pest' => true,
    ])->assertExitCode(Command::SUCCESS);
});

test('run install modularavel command with inertia stack and pest option disabled', function () {
    $this->artisan(InstallModularavelCommand::class, [
        'stack' => 'inertia',
        '--pest' => false,
    ])->assertExitCode(Command::SUCCESS);
});

test('run install modularavel command with inertia stack and ssr option enabled', function () {
    $this->artisan(InstallModularavelCommand::class, [
        'stack' => 'inertia',
        '--ssr' => true,
    ])->assertExitCode(Command::SUCCESS);
});

test('run install modularavel command with inertia stack and ssr option disabled', function () {
    $this->artisan(InstallModularavelCommand::class, [
        'stack' => 'inertia',
        '--ssr' => false,
    ])->assertExitCode(Command::SUCCESS);
});

test('run install modularavel command with inertia stack and composer option enabled', function () {
    $this->artisan(InstallModularavelCommand::class, [
        'stack' => 'inertia',
        '--composer' => 'global',
    ])->assertExitCode(Command::SUCCESS);
});

test('run install modularavel command with inertia stack and composer option disabled', function () {
    $this->artisan(InstallModularavelCommand::class, [
        'stack' => 'inertia',
        '--composer' => null,
    ])->assertExitCode(Command::SUCCESS);
});

test('run install modularavel command with inertia stack and all options are enabled', function () {
    $this->artisan(InstallModularavelCommand::class, [
        'stack' => 'inertia',
        '--dark' => true,
        '--teams' => true,
        '--api' => true,
        '--verification' => true,
        '--pest' => true,
        '--ssr' => true,
        '--composer' => 'global',
    ])->assertExitCode(Command::SUCCESS);
});

test('run install modularavel command with inertia stack and all options are disabled', function () {
    $this->artisan(InstallModularavelCommand::class, [
        'stack' => 'inertia',
        '--dark' => false,
        '--teams' => false,
        '--api' => false,
        '--verification' => false,
        '--pest' => false,
        '--ssr' => false,
        '--composer' => 'global',
    ])->assertExitCode(Command::SUCCESS);
});
