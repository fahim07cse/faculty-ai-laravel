<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('faculty-ai:about', function () {
    $this->info('Faculty AI Explorer is running.');
})->purpose('Display Faculty AI application status');
