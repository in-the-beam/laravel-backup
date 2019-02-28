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
     * Old backups behavior (Backup 51+)
     */
    'old'    => [
        'remove'   => false,
        'store_at' => storage_path( 'backups/old' ),
    ],
    /**
     * Archiver
     * Supports:
     *      tar.gzip    (default)
     *      zip         (poor compression;      tested on zip 3.0  at linux debian)
     *      rar         (better compression;    tested on rar 5.40 at linux debian)
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
         * How much backups to store
         * Zero [0] - disable backups counting
         */
        'store'         => 50,
        /**
         * Exclude directories from backup
         */
        'exclude' => [
            storage_path() . '/backups',    // Archive storage, You can remove it if backups folder is outside the framework
            base_path() . '/.git',          // git version control
            base_path() . '/.svn',          // subversion version control
            base_path() . '/.hg',           // mercurial version control
            'node_modules',                 // nodejs modules
            'bower_components',             // bower components
            'vendor',                       // vendor directory. optional        ],
        ],
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
