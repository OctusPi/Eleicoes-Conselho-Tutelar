<?php
namespace App\Utils;

class Log{
    private const PATH = __DIR__.'/../../storage/logs/app.log';

    private static function write(string $message):void
    {
        file_put_contents(self::PATH, Dates::dateTimeNow().' - '.$message.PHP_EOL, FILE_APPEND);
    }

    private static function read():?string
    {
        if(file_exists(self::PATH)){
            return file_get_contents(self::PATH);
        }

        return '';
    }

    public static function info(string $message):void
    {
        self::write('INFO: '.$message);
    }

    public static function notice(string $message):void
    {
        self::write('NOTICE: '.$message);
    }

    public static function warning(string $message):void
    {
        self::write('WARNING: '.$message);
    }

    public static function critical(string $message):void
    {
        self::write('CRITICAL: '.$message);
    }

    public static function success(string $message):void
    {
        self::write('SUCCESS: '.$message);
    }
}