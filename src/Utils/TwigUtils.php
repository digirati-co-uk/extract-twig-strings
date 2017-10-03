<?php


namespace Digirati\ExtractTwigStrings\Utils;


/**
 * Static utilities related to the parsing of Twig files.
 */
class TwigUtils
{
    private function __construct() {}

    /**
     * Return a factory to shim any symbols that can't be resolved in the Twig template being parsed.
     *
     * @param string $type
     * @return \Closure
     */
    public static function undefinedSymbolHandler(string $type) {
        return function ($name) use ($type) {
            return new $type($name);
        };
    }
}