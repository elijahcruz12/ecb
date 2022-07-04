<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class Run extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = '
        run
        {{ --path : If the path is different than the current directory. }}
        {{ --name: The name of the ecb file }}
    ';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Runs the ECB runner.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $path = $this->option('path') ?? getcwd();
        $name = $this->option('name') ?? 'ecb.yml';

        // Check if path is a directory
        if(!is_dir($path)) {
            $this->error('Path is not a directory.');
            return;
        }

        // Check if name exists
        if(!file_exists($path.'/'.$name)) {
            $this->error('ECB file not found.');
            $this->error('Exiting...');
            return;
        }

        // Get the content of the file
        $content = file_get_contents($path.'/'.$name);

        // Check if the content is json
        $json = json_decode($content, true);

        if($json == null) {
            $isJson == null;
            // Get the json error
            $error = json_last_error();
        }
        else {
            $isJson = true;
        }

        // Check if the content is yaml
        $yaml = yaml_parse($content);

        if($yaml == null) {
            $isYaml == null;
        }
        else {
            $isYaml = true;
        }

        if($isJson == null && $isYaml == null) {
            $this->error('ECB file is not valid.');
            $this->error('Exiting...');
            return;
        }

        if($isJson) {
            // Check if the json has the correct keys
            if(!isset($json['name']) || !isset($json['type'])) {
                $this->error('ECB file is not valid.');
                $this->error('Exiting...');
                return;
            }

            // Check if the type is valid
            if(!in_array($json['type'], ['laravel', 'none'])) {
                $this->error('ECB file is not valid.');
                $this->error('Exiting...');
                return;
            }

            // Run the ecb runner
            $this->info('Running ECB...');
            $this->info('Path: '.$path);
            $this->info('Name: '.$json['name']);

            // Get the steps
            $steps = $json['steps'];

            // Run the steps
            foreach($steps as $step) {
                $this->info('Running step: '.$step['name']);
                $this->info('Command: '.$step['command']);
                $this->info('Output: '.$step['output']);
                $this->info('Exit code: '.$step['exit_code']);
                $this->info('----------------------------------------------------');
            }

        }

        if($isYaml) {

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
