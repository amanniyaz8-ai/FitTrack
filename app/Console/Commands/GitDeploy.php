<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GitDeploy extends Command
{
    protected $signature = 'git:deploy';
    protected $description = 'Pull latest changes from origin main';

    public function handle(): int
    {
        $path = '/var/www/vhosts/fittrack.kz/httpdocs';

        $this->info("Running git pull in {$path}...");

        $output = shell_exec("cd {$path} && git pull origin main 2>&1");

        $this->line($output);

        return Command::SUCCESS;
    }
}
