<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class Init extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = '
        init
        {{ --type= : the type of project.  (Optional) }}
        {{ --name= : the name of the project. (Optional) }}
        {{ --force : overwrite ecb.yml if it already exists. (Optional) }}
        {{ --json : output the generated config as JSON. (Optional) }}
        ';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Creates a ecb.yml file in the current directory';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $type = $this->option('type') ?? 'laravel';
        $name = $this->option('name');
        $useJson = $this->option('json') ?? false;

        if($name == null) {
            // Get the name from the directory
            $name = basename(getcwd());
        }

        $force = $this->option('force');
        $path = getcwd();

        if($useJson) {

            if(file_exists($path.'/ecb.json') && !$force) {
                $this->error('ecb.json already exists. Use --force to overwrite.');
                return;
            }

            $this->info('Creating ecb.json...');

            $stub = file_get_contents(__DIR__.'/../Stubs/ecb.json.stub');

            $jsonStub = json_decode($stub, true);

            $jsonStub['name'] = $name;
            $jsonStub['type'] = $type;

            $json = json_encode($jsonStub, JSON_PRETTY_PRINT);

            file_put_contents($path.'/ecb.json', $json);

            $this->info('ecb.json created.');

        }
        else {
            if (file_exists($path . '/ecb.yml') && !$force) {
                $this->error('ecb.yml already exists. Use --force to overwrite.');
                return;
            }

            $this->info('Creating ecb.yml...');

            // Get the stub
            $stub = file_get_contents(__DIR__ . '/../Stubs/ecb.stub');

            // Replace the placeholders
            $stub = str_replace('{{ name }}', $name, $stub);
            $stub = str_replace('{{ type }}', $type, $stub);

            // Write the file
            file_put_contents($path . '/ecb.yml', $stub);

            $this->info('ecb.yml created.');
        }
    }

    /**
     * Define the command's schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
