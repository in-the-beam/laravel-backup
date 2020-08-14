<?php

return [
    /**
     * Directory for newly created backups
     */
    'backupDir' => storage_path( 'backups/' ),
    /**
     * Backup file-format [ See date() ]
     */
    'format' => 'U',
    /**
     * Save as "[YEAR]-[MONTH]-[DAY]/[UNIXTIMESTAMP]_[YEAR]-[MONTH]-[DAY]_database.tar.gz" Y-m-d/U_Y-m-d_{TYPE}.tar.gz <= as directory / filename
     * Else as "[YEAR]-[MONTH]-[DAY]_[UNIXTIMESTAMP]_[YEAR]-[MONTH]-[DAY]_database.tar.gz" Y-m-d_U_Y-m-d_{TYPE}.tar.gz <= as filename
     */
    'use_date_directory' => true,
    /**
     * Archiver
     *
     * Supports:
     *      tar.gzip    ( default;  best for multiple files; tested on tar 1.29 at linux debian 9 )
     *      zip         ( optional; poor compression;        tested on zip 3.0  at linux debian 9 )
     *      rar         ( optional; better compression;      tested on rar 5.40 at linux debian 9 )
     *
     * BENCHMARKS
     *
     * 300324 bytes _files.tar.gz           - best!
     * 302904 bytes _files.rar              - optimal
     * 345464 bytes _files.zip              - useable
     *
     * 2392 bytes _database_pgsql.rar       - best!
     * 2465 bytes _database_pgsql.tar.gz    - optimal
     * 2512 bytes _database_pgsql.zip       - useable
     *
     */
    'archiver' => 'tar.gzip',
//    'archiver' => 'zip',
//    'archiver' => 'rar',
    /**
     * DATABASE
     */
    'database'  => [
        /**
         * Enable or disable backing up the database
         */
        'enabled'    => true,
        /**
         * Database connections (see /config/database.php file) to backup
         */
        'connections' => [
            'pgsql',
        ],
        /**
         * Archiver / OVERRIDE GLOBALS
         *
         * Accepts: [ tar.gzip | zip | rar | NULL ]
         * If not null - will be used global variable
         */
        'archiver' => 'rar',
    ],
    /**
     * FILES
     */
    'files'     => [
        /**
         * Enable or disable backing up the filesystem
         */
        'enabled'       => true,
        /**
         * Exclude directories from backup
         */
        'exclude' => [
            storage_path() . '/backups',    // Archive storage, You can remove it if backups folder is outside the framework
            base_path() . '/.git/',          // git version control
            base_path() . '/.svn/',          // subversion version control
            base_path() . '/.hg/',           // mercurial version control
            'node_modules/',                 // nodejs modules
            'bower_components/',             // bower components
            'vendor/',                       // vendor directory. optional        ],
        ],
        /**
         * Archiver / OVERRIDE GLOBALS
         *
         * Accepts: [ tar.gzip | zip | rar | NULL ]
         * If not null - will be used global variable
         */
        'archiver' => 'rar',
    ],
    /**
     * CLEANUP
     */
    'cleanup' => [
        /**
         * Enable or disable backing up the filesystem
         */
        'enabled'       => true,
    ],
];
