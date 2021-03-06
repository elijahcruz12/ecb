<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;
use Symfony\Component\Yaml\Yaml;

class Run extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = '
        run
        {{ --path= : If the path is different than the current directory. }}
        {{ --name= : The name of the ecb file }}
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
            $isJson = null;
            // Get the json error
            $error = json_last_error();
        }
        else {
            $isJson = true;
        }

        // Check if the content is yaml and if it then parse it into an array
        $yaml = Yaml::parseFile($path.'/'.$name);

        if($yaml == null) {
            $isYaml = null;
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

                // Run the command
                $command = exec($step['command'] , $output, $return);

                $this->info('Output:');
                foreach($output as $line) {
                    $this->info($line);
                }

                // If the command failed
                if($return != 0) {
                    $this->error('Step failed.');
                    $this->error('Exiting...');
                    return;
                }

                $this->info('----------------------------------------------------');
            }

        }

        if($isYaml) {
            // Check if the yaml has the correct keys
            if(!isset($yaml['name']) || !isset($yaml['type'])) {
                $this->error('ECB file is not valid.');
                $this->error('Exiting...');
                return;
            }

            // Check if the type is valid
            if(!in_array($yaml['type'], ['laravel', 'none'])) {
                $this->error('ECB file is not valid.');
                $this->error('Exiting...');
                return;
            }

            // Run the ecb runner
            $this->info('Running ECB...');
            $this->info('Path: '.$path);
            $this->info('Name: '.$yaml['name']);

            // Get the steps
            $steps = $yaml['steps'];

            // Run the steps
            foreach($steps as $step) {
                $this->info('Running step: '.$step['name']);
                $this->info('Command: '.$step['command']);

                // Run the command
                $command = exec($step['command'] , $output, $return);

                $this->info('Output:');
                foreach($output as $line) {
                    $this->info($line);
                }

                // If the command failed
                if($return != 0) {
                    $this->error('Step failed.');
                    $this->error('Exiting...');
                    return;
                }

                $this->info('----------------------------------------------------');
            }

        }


        $this->info('ECB finished.');

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
