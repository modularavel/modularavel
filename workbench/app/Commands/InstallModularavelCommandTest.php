<?php

namespace Workbench\App\Commands;

use Modularavel\Modularavel\Commands\InstallModularavelCommand;

test('install predis composer', function () {
    InstallModularavelCommand::installPredis();
});
