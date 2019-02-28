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
composer require in-the-beam/laravel-backup
```
Package Auto-Discovery is supported

## Setup

```
php artisan vendor:publish --provider="ITB\Backup\BackupServiceProvider" --tag=config
```

#### Database(s) backup

Package looking for database connections declared in configuration `ITB-backup.database.connections`
In `config/database.php` connections is a keys of the array `database.connections`.
That it!

## Usage

#### Backup database(s)

To make an database backup, just run
```
php artisan ITB:backup-database
```

#### Backup Storage Cleanup

To remove all backups, just run
```
php artisan ITB:backup-cleanup
```
At the same time, you will be interactively asked for confirmations where the affirmative answer is the word “I confirm”. Other response options, including blank entry, cancel store clearing.<br>
Forcing deletion by adding a key or option will not be added.

# IN DEVELOPMENT

* Local Filesystem Backup

## License MIT
