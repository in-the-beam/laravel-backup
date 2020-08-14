<?php
/**
 * Laravel Backup
 *
 * @author    Stanislav Kabin <me@h-zone.ru>
 * @copyright 2019 Stanislav Kabin
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      https://github.com/make-it-app/laravel-backup-commands
 */


namespace MakeItApp\Backup\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use MakeItApp\Backup\Traits\CommandTrait;

class BackupFilesCommand extends Command
{
    use CommandTrait;
    /**
     * The console command name.
     * @var string
     */
    protected $name = 'makeitapp:backup:files';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Backup Project Files';

    protected $config;
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
        $this->config     = config( 'makeitapp-backup' );
        $this->utime      = time();
        $this->date       = date( 'Y-m-d', $this->utime );
        $this->time       = date( 'H-i-s', $this->utime );
        $this->filename   = $this->utime . '_' . $this->date . '_files';
        $this->dfilename  = $this->time . '_files';
        parent::__construct();
    }

    /**
     * Execute the console command.
     * @return void
     */
    public function handle()
    {
        if ( $this->config['files']['enabled'] != true ) {
            $this->error( 'makeitapp:backup:files IS DISABLED VIA CONFIGURATION' );
            die;
        }
        $this->info( '' );
        $this->info( 'Backup Project Files' );
        $this->info( '' );
        $this->registerClassAutoloadExceptions();
        $this->_makeBackupDirs();
        $this->timer = microtime( true );
        // code
        $this->_compress();
        // code
        $this->info( '' );
        $this->info( 'Done in   ' . number_format( microtime(true)-$this->timer, 2, ',', ' ' ) . ' seconds.' );
        $this->info( '' );
    }

    /**
     * Create database backup
     */
    protected function _compress()
    {
        $start = microtime(true);
        $this->info( '  Trying to compress files' );
        // code
        $config    = $this->config;
        $exclude   = $config['files']['exclude'];
        $archiver  = $config['archiver'];
        if ( !empty( $config['files']['archiver'] ) ) {
            $archiver = $config['files']['archiver'];
        }

        /*
         * OPTION Date-Directories
         */
        if ( $config['use_date_directory'] ) {
            $backupDir = $this->config['backupDir'] . $this->date;
            $filepath  = $backupDir . '/' . $this->dfilename;
            $filename  = $this->dfilename;
        } else {
            $backupDir = $this->config['backupDir'];
            $filepath  = $backupDir . '/' .  $this->filename;
            $filename  = $this->filename;
        }

        /*
         * DO COMPRESS
         * Exclude info
         */
        
        $_excluded = '';
        if ( !empty( $exclude ) ) {
            foreach( $exclude as $e ) {
                switch( $archiver ) {
                    case 'tar.gzip':
                        $_excluded .= " --exclude='" . str_replace( base_path() . '/', null, $e ) . "'";
                    break;
                    case 'zip':
                        $_excluded .= " -x '" . str_replace( base_path() . '/', null, $e ) . "/*'";
                    break;
                    case 'rar':
                        $_excluded .= " -x" . str_replace( base_path() . '/', null, addslashes( $e ) ) . '\*';
                    break;
                }
            }
        }

        $cmd = [];
        $cmd[] = 'cd ' . base_path();
        switch( $archiver ) {
            default:
            case 'tar.gzip':
                $_extension     = '.tar.gz ';
                $cmd[]          = 'tar '. $_excluded .' -czvf ' . str_replace( base_path() . '/', null,  $filepath ) . $_extension . ' -C ' . base_path() . ' .';
            break;
            case 'zip':
                $_extension     = '.zip ';
                $cmd[]          = 'zip -r9 ' . $filepath. $_extension . ' ./ ' . $_excluded;
            break;
            case 'rar':
                $_extension     = '.rar ';
                $cmd[]          = 'rar a -r -ep1 -m5 ' . $filepath.$_extension . './' . $_excluded;
            break;
        }
        $this->_runCmd( $cmd );

        // code
        $time = microtime(true)-$start;
        $this->warn( '  Backup filename is ' . $filename.$_extension );
        $this->warn( '                  at ' . str_replace( base_path() . '/', null, $backupDir ) );
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
