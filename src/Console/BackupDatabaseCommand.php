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
use ITB\Backup\Traits\CommandTrait;

class BackupDatabaseCommand extends Command
{
    use CommandTrait;
    /**
     * The console command name.
     * @var string
     */
    protected $name = 'ITB:backup-database';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Backup database(s)';

    protected $config;
    protected $databases;
    protected $utime;
    protected $date;
    protected $time;
    protected $timer = 0;
    protected $filename; // filename to use with full date name
    protected $dfilename; // filename to use with 'use_date_directory' option

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->config     = config( 'ITB-backup' );
        $this->databases  = config( 'database' );
        $this->utime      = time();
        $this->date       = date( 'Y-m-d', $this->utime );
        $this->time       = date( 'H-i-s', $this->utime );
        $this->filename   = $this->utime . '_' . $this->date . '_database_{CONNECTION}';
        $this->dfilename  = $this->time . '_database_{CONNECTION}';
        parent::__construct();
    }

    /**
     * Execute the console command.
     * @return void
     */
    public function handle()
    {
        if ( $this->config['database']['enabled'] != true )
        {
            $this->error( 'ITB:backup-database IS DISABLED VIA CONFIGURATION' );
            die;
        }
        $this->info( '' );
        $this->info( 'Backup Database(s)' );
        $this->registerClassAutoloadExceptions();
        $this->_makeBackupDirs();
        /*
         * EACH DECLARED DATABASE CONNECTION TO BACKUP (AT CONFIG)
         */
        foreach( $this->databases['connections'] AS $_key => $_connection )
        {
            $this->info( '' );
            $this->filename = str_replace( '{CONNECTION}', $_key, $this->filename );
            $this->dfilename = str_replace( '{CONNECTION}', $_key, $this->dfilename );
            $_driver    = $_connection['driver'];
            $_host      = $_connection['host'] . ':' .$_connection['port'];
            $_database  = $_connection['database'];
            $_schema    = $_connection['schema'];
            if ( is_array( $_schema ) )
            {
                $_schema = implode( ', ', $_schema );
            }
            $this->warn( '  Found Database Connection: '.$_key );
            $this->info( '  Driver  : ' . $_driver );
            $this->info( '  Host    : ' . $_host );
            $this->info( '  Database: ' . $_database );
            $this->info( '  Schemas : ' . $_schema );
            $this->timer = microtime(true);
            $this->_backup( $_key, $_connection );
            unset( $_filename, $_key, $_connection, $_driver, $_host, $_database, $_schema );
        }
        unset( $_filename, $_key, $_connection, $_driver, $_host, $_database, $_schema );
        $this->info( '' );
        $this->info( 'Done in   ' . number_format( microtime(true)-$this->timer, 2, ',', ' ' ) . ' seconds.' );
    }

    /**
     * Create database backup
     * @param string    $name      database connection name
     * @param array     $conn      database connection information
     */
    protected function _backup( $name, $conn )
    {
        $start = microtime(true);
        $this->info( '  Trying dump' );
        // code

        $driver    = $conn['driver'];
        $host      = $conn['host'];
        $port      = $conn['port'];
        $database  = $conn['database'];
        $schema    = $conn['schema'];
        $config    = $this->config;
        $archiver  = $config['archiver'];
        if ( !empty( $config['database']['archiver'] ) )
        {
            $archiver = $config['database']['archiver'];
        }
        if ( is_array( $schema ) )
        {
            $schema = implode( ', ', $schema );
        }
        $username = $conn['username'];
        $password = $conn['password'];
        /*
         * OPTION Date-Directories
         */
        if ( $config['use_date_directory'] )
        {
            $backupDir = $this->config['backupDir'] . $this->date;
            $filepath  = $backupDir . '/' . $this->dfilename;
            $filename  = $this->dfilename;
        }
        else
        {
            $backupDir = $this->config['backupDir'];
            $filepath  = $backupDir . $this->filename;
            $filename  = $this->filename;
        }
        /*
         * DO DUMP TO .SQL FILE
         */
        $cmd = '';
        switch ( $driver )
        {
            case 'pgsql':
                $cmd = 'PGPASSWORD="' . $password . '" pg_dump -h ' . $host . ' -U ' . $username . ' -f ' . $filepath . '.sql ' . $database;
            break;
            case 'mysql':
                $cmd = 'mysqldump - u' . $username . ' - p' . $password . ' ' . $database . ' > ' . $filepath . '.sql';
            break;
        }
        if ( !empty( $cmd ) )
        {
            $this->_runCmd( $cmd );
        }
        /*
         * DO COMPRESS
         */
        $this->info( '  Trying to compress' );
        $cmd = [];
        $cmd[] = 'cd ' . $backupDir;
        switch( $archiver )
        {
            default:
            case 'tar.gzip':
                $_extension     = '.tar.gz ';
                $cmd[]          = 'tar -czvf ' . $filename.$_extension . $filename.'.sql';
                $cmd[]          = 'rm ' . $filename.'.sql';
            break;
            case 'zip':
                $_extension     = '.zip ';
                $cmd[]          = 'zip -m -9 ' . $filename.$_extension . $filename.'.sql';
            break;
            case 'rar':
                $_extension     = '.rar ';
                $cmd[]          = 'rar m -ep1 -m5 ' . $filename.$_extension . $filename.'.sql';
            break;
        }
        $this->_runCmd( $cmd );
        // code
        $time = microtime(true)-$start;
        $this->warn( '  Backup file is ' . $filename.$_extension );
        $this->warn( '              at ' . str_replace( base_path(), null, $backupDir ) );
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
