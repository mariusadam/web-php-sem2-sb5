<?php

namespace WebApp\Services;


/**
 * Class Utils
 *
 * @package App\Services
 *
 * @author  Marius Adam
 */
class Utils
{
    /**
     * @param string $string
     *
     * @return string
     */
    public static function snakeToPascalCase($string)
    {
        return preg_replace_callback(
            "/(?:^|_)([a-z])/",
            function ($matches) {
                return strtoupper($matches[1]);
            },
            $string
        );
    }

    /**
     * @param string $string
     *
     * @return string
     */
    public static function camelCaseToSnakeCase($string)
    {
        return strtolower(preg_replace('/(?<=\\w)(?=[A-Z])/', '_$1', $string));
    }
}
