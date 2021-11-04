<?php

declare(strict_types=1);


namespace App\Console;

use App\Exceptions\InvalidAuthException;
use App\Exceptions\InvalidSettingsException;
use App\Exceptions\MissingCommandsException;
use App\Services\Settings\Settings;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use LaravelZero\Framework\Kernel as BaseKernel;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class Kernel extends BaseKernel
{
    protected $input = null;

    /**
     * {@inheritdoc}
     */
    public function handle($input, $output = null)
    {
        $this->input = $input;

        return parent::handle($input, $output);
    }


    /**
     * {@inheritdoc}
     */
    public function bootstrap(): void
    {
        parent::bootstrap();

        $this->checkRequirements();
        $this->loadSettings();
    }

    protected function loadSettings()
    {
        if (
            collect(['install', 'app:build'])->contains($this->input->getFirstArgument())
            || is_null($this->input->getFirstArgument())
        ) {
            return;
        }

        try {
            $auth = json_decode(File::get(base_dir() . config('app.auth.filename')), true);
            Config::set('prello.auth', $auth);
        } catch (\Throwable $e) {
            throw InvalidAuthException::create();
        }

        $settings = app(Settings::class);
        $settings->load();
    }

    public function checkRequirements()
    {
        if (
            collect(['install', 'app:build'])->contains($this->input->getFirstArgument())
            || is_null($this->input->getFirstArgument())
        ) {
            return;
        }

        try {
            File::get(base_dir() . config('app.auth.filename'));
        } catch (\Throwable $e) {
            throw InvalidAuthException::create();
        }

        try {
            File::get(base_dir() . 'settings.json');
        } catch (\Exception $e) {
            throw InvalidSettingsException::create();
        }

        try {
            $hasGit = tap(new Process(['which', 'git']), fn($p) => $p->run());

            if (!$hasGit->isSuccessful()) {
                throw new ProcessFailedException($hasGit);
            }

            $hasGh = tap(new Process(['which', 'gh']), fn($p) => $p->run());

            if (!$hasGh->isSuccessful()) {
                throw new ProcessFailedException($hasGh);
            }

        } catch (\Throwable $e) {
            throw MissingCommandsException::create();
        }
    }
}
