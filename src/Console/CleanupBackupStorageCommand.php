<?php
/**
 * Laravel Backup
 *
 * @author    Stanislav Kabin <me@h-zone.ru>
 * @copyright 2019 Stanislav Kabin
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      https://github.com/in-the-beam/laravel-backup
 */


namespace ITB\Backup\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use \DirectoryIterator;

class CleanupBackupStorageCommand extends Command
{
    /**
     * The console command name.
     * @var string
     */
    protected $name = 'ITB:backup-cleanup';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Remove all stored backups';

    protected $config;
    protected $timer = 0;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->config     = config( 'ITB-backup' );
        parent::__construct();
    }

    /**
     * Execute the console command.
     * @return void
     */
    public function handle()
    {
        if ( $this->config['cleanup']['enabled'] != true )
        {
            $this->error( 'ITB:backup-cleanup IS DISABLED VIA CONFIGURATION' );
            die;
        }
        $this->info( '' );
        $this->info( 'Removing all stored backups...' );
        $this->registerClassAutoloadExceptions();

        $this->timer = microtime(true);

        if ( strtolower( $this->ask( 'DO YOU REALLY WANT TO REMOVE ALL BACKUPS? [type `confirm` to perform cleanup]' ) ) == 'confirm' )
        {
            if( is_dir( $this->config[ 'backupDir' ] ) )
            {
                $this->_rrmdir( $this->config[ 'backupDir' ] );
            }
            else
            {
                $this->error( 'Nothing to remove.' );
            }
        }
        else
        {
            $this->error( 'Cancelled.' );
        }
        $this->info( '' );
        $time = microtime(true)-$this->timer;
        $this->info( 'Done in ' . number_format( $time, 2, ',', ' ' ) . ' seconds.' );
        $this->info( '' );
    }

    /**
     * Recursively removes a folder along with all its files and directories
     * @param String $path 
     */
    protected function _rrmdir( $path )
    {
        $i = new DirectoryIterator( $path );
        foreach( $i as $f )
        {
            if( $f->isFile() )
            {
                $this->warn( 'Permanently removed file ' . str_replace( base_path(), null, $f->getRealPath() ) );
                unlink( $f->getRealPath() );
            }
            else if( !$f->isDot() && $f->isDir() )
            {
                $this->_rrmdir( $f->getRealPath() );
            }
        }
        $this->warn( 'Permanently removed directory' . str_replace( base_path(), null, $path ) );
        rmdir( $path );
    }

    /**
     * Get the console command options.
     * @return array
     */
    protected function getOptions()
    {
        return [];
    }

    /**
     * Register an autoloader the throws exceptions when a class is not found.
     */
    protected function registerClassAutoloadExceptions()
    {
        spl_autoload_register(function ($class) {
            throw new \Exception("Class '$class' not found.");
        });
    }

}
