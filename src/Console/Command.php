<?php

namespace LunoxHoshizaki\Console;

abstract class Command
{
    /**
     * The name and signature of the console command.
     */
    public string $signature = 'command:name';

    /**
     * The console command description.
     */
    public string $description = 'Command description';

    /**
     * Execute the console command.
     */
    abstract public function handle(array $args): void;

    /**
     * Write info output.
     */
    protected function info(string $message): void
    {
        echo "✅ " . $message . "\n";
    }

    /**
     * Write error output.
     */
    protected function error(string $message): void
    {
        echo "❌ " . $message . "\n";
    }

    /**
     * Write generic line output.
     */
    protected function line(string $message): void
    {
        echo $message . "\n";
    }
}
