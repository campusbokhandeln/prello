<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use LaravelZero\Framework\Commands\Command;

class InstallCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'install';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Auth Trello';

    protected ?string $key;
    protected ?string $token;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->title('Enter Trello token and key');
        $this->info('Go to https://trello.com/app-key to set up api token.');
        $this->line('Good Luck!');
        $this->newLine();
        $this->key = $this->secret('Trello Key:');
        $this->token = $this->secret('Trello Token:');

        $this->task('Saving Trello Auth', [$this, 'saveTrelloAuth']);
    }

    public function saveTrelloAuth()
    {
        try {
            $authData = json_encode([
                'key' => $this->key,
                'token' => $this->token,
            ]);

            $authFileName = base_dir() . config('app.auth.filename');

            $this->line('Saving config to: ' . $authFileName);

            if (!File::isDirectory(base_dir())) {
                File::makeDirectory(base_dir());
            }

            // Auth.
            File::put($authFileName, $authData);

            // Settings
            File::put(base_dir() . "settings.json", json_encode([
                'pr' => [
                    'lastBoardId' => null,
                    'lastListId' => null,
                ],
                'changelog' => [
                    'lastBoardId' => null,
                    'lastListId' => null,
                ],
                'quick' => [
                    'lastBoardId' => null,
                    'lastListId' => null,
                ],
            ]));

            return true;
        } catch (\Throwable $e) {
            $this->error($e->getMessage());
            return false;
        }
    }
}
