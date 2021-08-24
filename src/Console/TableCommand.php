<?php

namespace Asd\ActionLog\Console;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Composer;
use Illuminate\Filesystem\Filesystem;

class TableCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'action-log:tables';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a migration for the action logs database tables';

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * @var \Illuminate\Support\Composer
     */
    protected $composer;

    /**
     * Create a new queue job table command instance.
     *
     * @param \Illuminate\Filesystem\Filesystem $files
     * @param \Illuminate\Support\Composer $composer
     * @return void
     */
    public function __construct(Filesystem $files, Composer $composer)
    {
        parent::__construct();

        $this->files = $files;
        $this->composer = $composer;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->files->put(
            $this->createBaseMigration(),
            $this->files->get(__DIR__ . '/stubs/tables.stub')
        );

        $this->info('Migration created successfully!');

        $this->composer->dumpAutoloads();
    }

    /**
     * Create a base migration file for the tables.
     *
     * @return string
     */
    protected function createBaseMigration()
    {
        return $this->laravel['migration.creator']->create(
            'create_action_log_tables', $this->laravel->databasePath() . '/migrations'
        );
    }
}
