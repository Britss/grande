<?php
declare(strict_types=1);

namespace App\Support;

use PDO;

final class Database
{
    private static ?PDO $connection = null;

    public static function connection(): PDO
    {
        if (self::$connection instanceof PDO) {
            return self::$connection;
        }

        $host = (string) Config::get('database.host');
        $port = (int) Config::get('database.port', 3306);
        $databaseName = (string) Config::get('database.name');
        $charset = (string) Config::get('database.charset', 'utf8mb4');
        $username = (string) Config::get('database.username');
        $password = (string) Config::get('database.password');

        $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s', $host, $port, $databaseName, $charset);

        self::$connection = new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        return self::$connection;
    }
}
