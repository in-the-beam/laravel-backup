<?php
/**
 * Laravel Backup
 *
 * @author    Stanislav Kabin <me@h-zone.ru>
 * @copyright 2019 Stanislav Kabin
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      https://github.com/in-the-beam/laravel-backup
 */

namespace ITB\Backup;

use Illuminate\Support\ServiceProvider;
use ITB\Backup\Console\BackupDatabaseCommand;
use ITB\Backup\Console\CleanupBackupStorageCommand;
use ITB\Backup\Console\BackupFilesCommand;


class BackupServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $configPath = __DIR__ . '/../config/ITB-backup.php';
        if ( function_exists( 'config_path' ) )
        {
            $publishPath = config_path( 'ITB-backup.php' );
        }
        else
        {
            $publishPath = base_path( 'config/ITB-backup.php' );
        }
        $this->publishes([ $configPath => $publishPath ], 'config' );

        if ($this->app->runningInConsole())
        {
            $this->commands([
                BackupDatabaseCommand::class,
                BackupFilesCommand::class,
                CleanupBackupStorageCommand::class,
            ]);
        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $configPath = __DIR__ . '/../config/ITB-backup.php';
        $this->mergeConfigFrom( $configPath, 'ITB-backup' );
    }
}
