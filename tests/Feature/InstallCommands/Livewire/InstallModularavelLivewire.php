<?php

use Modularavel\Modularavel\Commands\InstallModularavelCommand;
use Symfony\Component\Console\Command\Command;

test('run install modularavel command with livewire stack', function ()   {
    $this->artisan(InstallModularavelCommand::class, [
        'stack' => 'livewire',
    ])->assertExitCode(Command::SUCCESS);
});

test('run install modularavel command with livewire stack and dark mode option enabled', function () {
    $this->artisan(InstallModularavelCommand::class, [
        'stack' => 'livewire',
        '--dark' => true,
    ])->assertExitCode(Command::SUCCESS);
});

test('run install modularavel command with livewire stack and dark mode option disabled', function () {
    $this->artisan(InstallModularavelCommand::class, [
        'stack' => 'livewire',
        '--dark' => false,
    ])->assertExitCode(Command::SUCCESS);
});

test('run install modularavel command with livewire stack and teams option enabled', function () {
    $this->artisan(InstallModularavelCommand::class, [
        'stack' => 'livewire',
        '--teams' => true,
   ])->assertExitCode(Command::SUCCESS);
});

test('run install modularavel command with livewire stack and teams option disabled', function () {
    $this->artisan(InstallModularavelCommand::class, [
        'stack' => 'livewire',
        '--teams' => false,
    ])->assertExitCode(Command::SUCCESS);
});

test('run install modularavel command with livewire stack and api option enabled', function () {
    $this->artisan(InstallModularavelCommand::class, [
        'stack' => 'livewire',
        '--api' => true,
    ])->assertExitCode(Command::SUCCESS);
});

test('run install modularavel command with livewire stack and api option disabled', function () {
    $this->artisan(InstallModularavelCommand::class, [
        'stack' => 'livewire',
        '--api' => false,
    ])->assertExitCode(Command::SUCCESS);
});

test('run install modularavel command with livewire stack and verification option enabled', function () {
    $this->artisan(InstallModularavelCommand::class, [
        'stack' => 'livewire',
        '--verification' => true,
    ])->assertExitCode(Command::SUCCESS);
});

test('run install modularavel command with livewire stack and verification option disabled', function () {
    $this->artisan(InstallModularavelCommand::class, [
        'stack' => 'livewire',
        '--verification' => false,
    ])->assertExitCode(Command::SUCCESS);
});

test('run install modularavel command with livewire stack and pest option enabled', function () {
    $this->artisan(InstallModularavelCommand::class, [
        'stack' => 'livewire',
        '--pest' => true,
    ])->assertExitCode(Command::SUCCESS);
});

test('run install modularavel command with livewire stack and pest option disabled', function () {
    $this->artisan(InstallModularavelCommand::class, [
        'stack' => 'livewire',
        '--pest' => false,
    ])->assertExitCode(Command::SUCCESS);
});

test('run install modularavel command with livewire stack and ssr option enabled', function () {
    $this->artisan(InstallModularavelCommand::class, [
        'stack' => 'livewire',
        '--ssr' => true,
    ])->assertExitCode(Command::SUCCESS);
});

test('run install modularavel command with livewire stack and ssr option disabled', function () {
    $this->artisan(InstallModularavelCommand::class, [
        'stack' => 'livewire',
        '--ssr' => false,
    ])->assertExitCode(Command::SUCCESS);
});

test('run install modularavel command with livewire stack and composer option enabled', function () {
    $this->artisan(InstallModularavelCommand::class, [
        'stack' => 'livewire',
        '--composer' => 'global',
    ])->assertExitCode(Command::SUCCESS);
});

test('run install modularavel command with livewire stack and composer option disabled', function () {
    $this->artisan(InstallModularavelCommand::class, [
        'stack' => 'livewire',
        '--composer' => null,
    ])->assertExitCode(Command::SUCCESS);
});

test('run install modularavel command with livewire stack and all options are enabled', function () {
    $this->artisan(InstallModularavelCommand::class, [
        'stack' => 'livewire',
        '--dark' => true,
        '--teams' => true,
        '--api' => true,
        '--verification' => true,
        '--pest' => true,
        '--ssr' => true,
        '--composer' => 'global',
    ])->assertExitCode(Command::SUCCESS);
});

test('run install modularavel command with livewire stack and all options are disabled', function () {
    $this->artisan(InstallModularavelCommand::class, [
        'stack' => 'livewire',
        '--dark' => false,
        '--teams' => false,
        '--api' => false,
        '--verification' => false,
        '--pest' => false,
        '--ssr' => false,
        '--composer' => 'global',
    ])->assertExitCode(Command::SUCCESS);
});
