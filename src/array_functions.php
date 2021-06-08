<?php declare(strict_types = 1);
namespace Medusa;

use function is_array;
use function is_string;
use function strlen;
use function substr;

/**
 * Array extract
 * @param array  $array
 * @param string $prefix
 * @param bool   $removePrefix
 * @return array
 */
function arrayExtract(array $array, string $prefix, bool $removePrefix = false): array {

    $result = [];
    $prefixLength = strlen($prefix);

    foreach ($array as $key => $value) {
        if (substr($key, 0, $prefixLength) === $prefix) {
            if ($removePrefix) {
                $key = substr($key, $prefixLength);
            }

            $result[$key] = $value;
        }
    }

    return $result;
}

/**
 * @param array  $array
 * @param string $prefix
 * @return array
 */
function arrayPrefix(array $array, string $prefix): array {
    $result = [];
    foreach ($array as $key => $value) {
        if (is_string($key)) {
            $key = $prefix . $key;
        }
        $result[$key] = $value;
    }
    return $result;
}

/**
 * @param array  $array
 * @param string $prefix
 * @return array
 */
function arrayPrefixRecursive(array $array, string $prefix): array {

    $result = [];
    foreach ($array as $key => $value) {

        if (is_array($value)) {
            $value = arrayPrefixRecursive($value, $prefix);
        }

        if (is_string($key)) {
            $key = $prefix . $key;
        }

        $result[$key] = $value;
    }

    return $result;
}
