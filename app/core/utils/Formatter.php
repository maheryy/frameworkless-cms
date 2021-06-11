<?php

namespace App\Core\Utils;

use DateTime;
use DateTimeZone;

class Formatter
{
    const DATE_TIME_ZONE = 'Europe/Paris';
    const DATE_TIME_FORMAT = 'Y-m-d H:i:s';
    const DATE_FORMAT = 'Y-m-d';
    const TIME_FORMAT = 'H:i:s';
    const DATE_DISPLAY_FORMAT = 'd/m/Y';

    public static function sanitizeInput(string $s)
    {
        return htmlspecialchars(trim($s));
    }

    public static function encodeUrlQuery(string $s)
    {
        return urlencode(json_encode(['uri' => $s]));
    }

    public static function decodeUrlQuery(string $s)
    {
        return json_decode($s)->uri ?? null;
    }

    public static function camelToSnakeCase(string $s)
    {
        $s = lcfirst($s);
        $length = strlen($s);
        $i = 0;
        $res = [];

        while ($i < $length) {
            if (ctype_upper($s[$i])) {
                $res[] = '_';
                $res[] = mb_strtolower($s[$i]);
            } else {
                $res[] = $s[$i];
            }

            $i++;
        }

        return implode('', $res);
    }

    public static function snakeToCamelCase(string $s)
    {
        $length = strlen($s);
        $i = 0;
        $res = [];

        while ($i < $length) {
            $res[] = $s[$i] === '_' ? mb_strtoupper($s[++$i]) : $s[$i];
            $i++;
        }

        return implode('', $res);
    }

    public static function propertyToGetter(string $s)
    {
        return 'get' . ucfirst(self::snakeToCamelCase($s));
    }

    public static function propertyToSetter(string $s)
    {
        return 'set' . ucfirst(self::snakeToCamelCase($s));
    }

    public static function getTableName(string $table)
    {
        return DB_PREFIX . '_' . $table;
    }

    public static function getDateTimeObject()
    {
        return new DateTime('now', new DateTimeZone(self::DATE_TIME_ZONE));
    }

    public static function getDateTime(string $format = self::DATE_TIME_FORMAT)
    {
        return (new DateTime('now', new DateTimeZone(self::DATE_TIME_ZONE)))->format($format);
    }

    public static function getDateTimeFromTimestamp(int $timestamp, string $format = self::DATE_TIME_FORMAT)
    {
        return (new DateTime())->setTimestamp($timestamp)->format($format);
    }

    public static function getTimestamp()
    {
        return (new DateTime('now', new DateTimeZone(self::DATE_TIME_ZONE)))->getTimestamp();
    }

    public static function getTimestampFromDateTime(string $date, string $format = self::DATE_TIME_FORMAT)
    {
        return DateTime::createFromFormat($format, $date, new DateTimeZone(self::DATE_TIME_ZONE))->getTimestamp();
    }

    public static function getModifiedDateTime(string $modifier, string $format = self::DATE_TIME_FORMAT)
    {
        return (new DateTime('now', new DateTimeZone(self::DATE_TIME_ZONE)))->modify($modifier)->format($format);
    }
}
