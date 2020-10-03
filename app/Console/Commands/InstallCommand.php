<?php

namespace App\Console\Commands;

use Dotenv\Dotenv;
use Dotenv\Exception\InvalidPathException;
use Illuminate\Console\Command;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the dashboard easily';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if ($this->confirm('Do you want to configure Swan Dashboard before installing it?')) {
            $this->configureEnvironmentFile();
            $this->configureKeyApp();
            $this->configureDatabase();
        }

        $this->compileAssets();
        $this->call('config:cache');
        $this->call('route:cache');
        $this->call('optimize');
        $this->call('storage:link');

        $this->info("Configuration of Laravel completed. Optimize autoload via Composer, this may take a while ...");
        exec('composer dumpautoload -o');

        $this->info('Swan Dashboard successfully installed!');
        return 0;
    }

    protected function compileAssets()
    {
        if ($this->confirm('Would you like to compile assets with Laravel Mix via NPM?')) {
            $this->info('Installing modules... This may take a while.');
            exec('npm install'); // Installing Node modules.
            $this->info('Compiling assets... This may take a while.');
            exec('npm run production'); // Run Laravel Mix's production build command.
            $this->info('Compilation of assets completed.');
        }
    }

    protected function configureDatabase()
    {
        $config = [
            'DB_HOST' => null,
            'DB_DATABASE' => null,
            'DB_USERNAME' => null,
            'DB_PASSWORD' => null,
        ];

        $config['DB_HOST'] = $this->ask('What is the host of the MongoDB database?', '127.0.0.1');
        $config['DB_DATABASE'] = $this->ask('What is the name of the database to be used?', 'swan');
        $config['DB_USERNAME'] = $this->ask('What username should be used to log in?');
        $config['DB_PASSWORD'] = $this->secret('What password should be used to log in?');

        $this->info('Writing data to the environment file...');
        foreach ($config as $setting => $value) {
            $this->writeEnv($setting, $value);
        }

    }

    protected function configureKeyApp()
    {
        $this->info('Generating new application key ...');
        $this->call('key:generate');
    }

    protected function configureEnvironmentFile()
    {
        $this->info('Configuring environment file ...');
        $dir = app()->environmentPath();
        $file = app()->environmentFile();
        $path = "{$dir}/{$file}";

        if (file_exists($path)) {
            $this->info('Environment file already exists.');
            return;
        }

        copy("$path.example", $path);
        $this->info('Environment file created from the example file.');
    }


    /**
     * Writes to the .env file with given parameters.
     *
     * This function comes from CachetHQ/Cachet, you can
     * find the source code here : github.com/CachetHQ/Cachet.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return void
     */
    protected function writeEnv($key, $value)
    {
        $dir = app()->environmentPath();
        $file = app()->environmentFile();
        $path = "{$dir}/{$file}";

        try {
            (Dotenv::createImmutable($dir))->load();

            $envKey = strtoupper($key);
            $envValue = env($envKey) ?: 'null';

            $envFileContents = file_get_contents($path);
            $envFileContents = str_replace("{$envKey}={$envValue}", "{$envKey}={$value}", $envFileContents, $count);
            if ($count < 1 && $envValue === 'null') {
                $envFileContents = str_replace("{$envKey}=", "{$envKey}={$value}", $envFileContents);
            }
            file_put_contents($path, $envFileContents);
        } catch (InvalidPathException $e) {
            throw $e;
        }
    }
}
