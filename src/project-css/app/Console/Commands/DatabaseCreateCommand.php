<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PDO;
use PDOException;

class DatabaseCreateCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'db:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command creates a new database';

    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'db:create';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $database = env('DB_DATABASE', false);

        if (! $database) {
            $this->info('Skipping creation of database as env(DB_DATABASE) is empty');
            return;
        }

        if (\DB::statement('create database ' . $database) == true) {
            $new_connection = 'new';
            $nc = \Illuminate\Support\Facades\Config::set('database.connec‌​tions.' . $new_connect‌​ion, [
                'driver'   => 'mysql',
                'host'     => env('DB_HOST'),
                'database' => $database,
                'username' => env('DB_USERNAME'),
                'password' => env('DB_PASSWORD'),
            ]);
            Artisan::call('migrate', ['--database' => $new_connection]);
        } else {
            return 'db already exists!';
        }

        //try {
            // $pdo = $this->getPDOConnection(env('DB_HOST'), env('DB_PORT'), env('DB_USERNAME'), env('DB_PASSWORD'));

            // $response = $pdo->exec(sprintf(
            //     'CREATE DATABASE IF NOT EXISTS %s DEFAULT CHARACTER SET utf8mb4 DEFAULT COLLATE utf8mb4_unicode_ci',
            //     $database
            // ));
            //$this->info(sprintf('Successfully created %s database', $database));            
          
            // \DB::statement('create database ' .$database );

        // } catch (PDOException $exception) {
        //     $this->error(sprintf('Failed to create %s database, %s', $database, $exception->getMessage()));
        // }
    }

    /**
     * @param  string $host
     * @param  integer $port
     * @param  string $username
     * @param  string $password
     * @return PDO
     */
    private function getPDOConnection($host, $port, $username, $password)
    {
        return new PDO(sprintf('mysql:host=%s;port=%d;', $host, $port), $username, $password);
    }
}