<?php

namespace BotMan\Tinker\Commands;

use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Cache\ArrayCache;
use BotMan\Tinker\Drivers\ConsoleDriver;
use Clue\React\Stdio\Stdio;
use Illuminate\Console\Command;
use React\EventLoop\Loop;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

class Tinker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'botman:tinker';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tinker around with BotMan.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->line('<fg=gray>Botman Tinker For Laravel 9+ by Prevail Ejimadu.</>');
        /** @var \Illuminate\Foundation\Application $app */
        $app = app('app');
        $loop = Loop::get();

        // Register BotMan as a singleton
        $app->singleton('botman', function () use ($loop) {
            $config = config('services.botman', []);
            $botman = BotManFactory::create($config, new ArrayCache());

            // Set up Stdio for console interaction
            $stdio = new Stdio($loop);
            $stdio->setPrompt('You: ');

            $botman->setDriver(new ConsoleDriver($config, $stdio));

            $stdio->on('data', function ($line) use ($botman) {
                // Listen for the bot's response
                $botman->listen();
            });

            return $botman;
            $this->line('\n');
        });

        if (file_exists('routes/botman.php')) {
            require base_path('routes/botman.php');
        }
        $loop->run();
    }
}
