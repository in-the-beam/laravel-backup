# Laravel Backup

Tested on Laravel 5.8

## Requirements

* Archiver
  * tar (with gzip support) - Laravel backup uses command `tar cfvz`
  * zip (optional) - Laravel backup uses command `zip`
  * rar (optional) - Laravel backup uses command `rar`
* Database
  * MySQL 5.5+
  * PostgreSQL 9+

## Installation

```
composer require in-the-beam/laravel-backup-commands
```
Package Auto-Discovery is supported

## Setup

```
php artisan vendor:publish --provider="ITB\Backup\BackupServiceProvider" --tag=config
```

#### Database(s)

Package is looking for database connections declared in configuration `ITB-backup.database.connections`
In `config/database.php` connections is a keys of the array `database.connections`.
That it!

## Usage

#### Database(s)

To make an database backup, just run
```
php artisan ITB:backup-database
```

#### Local project files

To make an files backup, just run
```
php artisan ITB:backup-files
```
Note: see config for exclude files and/or directories

#### Backup Storage Cleanup

To remove all backups, just run
```
php artisan ITB:backup-cleanup
```
At the same time, you will be interactively asked for confirmations where the affirmative answer is the word “I confirm”. Other response options, including blank entry, cancel store clearing.<br>
Forcing deletion by adding a key or option will not be added.

#### Notes for Archiver

If You see, config have global archiver value and separatelly for each action.<br>
If You need, You can disable action archiver by setting `'archiver' => null,`.<br>
But i recommend to benchmark Your system by compressing database dump and project files by each archiver, then select better option.

## License MIT
