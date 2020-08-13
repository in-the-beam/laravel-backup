<?php
/**
 * Laravel Backup
 *
 * @author    Stanislav Kabin <me@h-zone.ru>
 * @copyright 2019 Stanislav Kabin
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      https://github.com/make-it-app/laravel-backup-commands
 */

namespace MakeItApp\Backup;

use Illuminate\Support\ServiceProvider;
use MakeItApp\Backup\Console\BackupDatabaseCommand;
use MakeItApp\Backup\Console\CleanupBackupStorageCommand;
use MakeItApp\Backup\Console\BackupFilesCommand;

class BackupServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $configPath = __DIR__ . '/../config/makeitapp-backup.php';
        if ( function_exists( 'config_path' ) ) {
            $publishPath = config_path( 'makeitapp-backup.php' );
        } else {
            $publishPath = base_path( 'config/makeitapp-backup.php' );
        }
        $this->publishes([ $configPath => $publishPath ], 'config' );

        if ($this->app->runningInConsole()) {
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
        $configPath = __DIR__ . '/../config/makeitapp-backup.php';
        $this->mergeConfigFrom( $configPath, 'makeitapp-backup' );
    }
}
