<?php
/**
 * Laravel Backup
 *
 * @author    Stanislav Kabin <me@h-zone.ru>
 * @copyright 2019 Stanislav Kabin
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      https://github.com/in-the-beam/laravel-backup
 */

namespace ITB\Backup\Traits;

trait CommandTrait
{
    /**
     * Execute predefined non-PHP console command
     * @param string|array $cmd
     */
    protected function _runCmd( $cmd )
    {
        if ( !empty( $cmd ) )
        {
            if ( is_array( $cmd ) )
            {
                $cmd = implode( ' && ', $cmd );
            }
            if ( $this->_isWindows() )
            {
                if ( function_exists( 'popen' ) && function_exists( 'pclose' ) )
                {
                    pclose( popen( 'start / B ' . $cmd, 'r' ) );
                }
                else
                {
                    if ( $this->messaging === true )
                    {
                        $this->error( 'Windows php.exe should support \'popen\'|\'pclose\' commands to proper functionality' );
                    }
                }
            }
            else
            {
                if ( function_exists( 'exec' ) )
                {
                    exec( $cmd );
                }
                else
                {
                    if ( $this->messaging === true )
                    {
                        $this->error( 'linux php-cli should support \'exec\' command to proper functionality' );
                    }
                }
            }
        }
        else
        {
            $this->error( 'Command is empty. Check Requirements first.' );
        }
    }

    /**
     * Check for Windows Operating System
     * @return boolean - true if windows detected
     */
    protected function _isWindows()
    {
        if ( strtoupper( substr( PHP_OS, 0, 3 ) ) === 'WIN' )
        {
            return true;
        }
        return false;
    }

    /**
     * make backup directory structure
     */
    protected function _makeBackupDirs()
    {
        $this->_makeDirectory( $this->config[ 'backupDir' ] );
        /*
         * OPTION Date-Directories
         */
        if ( $this->config['use_date_directory'] )
        {
            $this->_makeDirectory( $this->config['backupDir'] . $this->date );
        }
    }

    /**
     * make directory
     * @param string $directory
     */
    protected function _makeDirectory( $directory )
    {
        try
        {
            if ( !is_dir( $directory ) && !is_file( $directory ) && !is_link( $directory ) )
            {
                mkdir( $directory, 0755, true );
            }
        }
        catch ( \Exception $e )
        {
            $this->error( $e->getMessage() );
            die;
        }
    }

}
