<?php namespace Mrabbani\ModuleManager\Console\Generators;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Database\Migrations\MigrationCreator;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Composer;

class MakeMigration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:make:migration 
        {alias : The alias of module}
        {name : The name of the migration.}
        {--create : The table to be created.}
        {--table= : The table to migrate.}';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create new module based migration file';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected string $type = 'Make migration';

    /**
     * The migration creator instance.
     *
     * @var MigrationCreator|null
     */
    protected ?MigrationCreator $creator;

    /**
     * The Composer instance.
     *
     * @var Composer|null
     */
    protected ?Composer $composer;

    /**
     * Create a new migration install command instance.
     *
     * @param Composer $composer
     *
     * @return void
     */
    public function __construct(Composer $composer)
    {
        parent::__construct();

        $this->composer = $composer;
        $this->composer->setWorkingPath(base_path());
    }

    /**
     * @throws Exception
     */
    public function handle(): void
    {
        // It's possible for the developer to specify the tables to modify in this
        // schema operation. The developer may also specify if this table needs
        // to be freshly created, so we can create the appropriate migrations.
        $name = trim($this->argument('name'));

        $table = $this->option('table');

        $create = $this->option('create') ?: false;

        if (!$table && is_string($create)) {
            $table = $create;

            $create = true;
        }

        // Now we are ready to write the migration out to disk. Once we've written
        // the migration out, we will dump-autoload for the entire framework to
        // make sure that the migrations are registered by the class loaders.
        $this->writeMigration($name, $table, $create);
    }

    /**
     * Write the migration file to disk.
     *
     * @param             $name
     * @param string|null $table
     * @param bool        $create
     *
     * @throws Exception
     */
    protected function writeMigration($name, ?string $table = null, bool $create = false): void
    {
        $path = $this->getMigrationPath();

        $this->creator = new MigrationCreator(
            new Filesystem(),
            ''
        );

        $file = pathinfo($this->creator->create($name, $path, $table, $create), PATHINFO_FILENAME);

        $this->line("<info>Created Migration:</info> $file");
    }

    /**
     * Get the path to the migration directory.
     *
     * @return string
     */
    protected function getMigrationPath(): string
    {
        $module  = get_module_information($this->argument('alias'));
        $baseDir = get_base_folder(array_get($module, 'file'));

        return $baseDir . 'database/migrations';
    }
}
