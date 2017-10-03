<?php


namespace Digirati\ExtractTwigStrings\Utils;


class TwigUtils
{
    private function __construct() {}

    public static function undefinedSymbolHandler(string $type) {
        return function ($name) use ($type) {
            return new $type($name);
        };
    }
}