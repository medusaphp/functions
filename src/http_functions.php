<?php declare(strict_types = 1);
namespace Medusa\Http;

use function preg_match;

/**
 * Get remote addr
 * @param string|null $setMagicRemoteAddr
 * @return string
 */
function getRemoteAddress(?string $setMagicRemoteAddr = null): string {

    static $magicRemoteAddr = null;

    if ($setMagicRemoteAddr !== null) {
        $magicRemoteAddr = $setMagicRemoteAddr;
    }

    $remoteAddr = null;

    if ($magicRemoteAddr !== null) {
        $remoteAddr = ipv4Convert($magicRemoteAddr);
    } elseif (!empty($_SERVER['X-Forwarded-for'])) {
        $remoteAddr = ipv4Convert($_SERVER['X-Forwarded-for']);
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $remoteAddr = ipv4Convert($_SERVER['HTTP_X_FORWARDED_FOR']);
    } elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $remoteAddr = ipv4Convert($_SERVER['HTTP_CLIENT_IP']);
    } elseif (!empty($_SERVER['HTTP_VIA'])) {
        $remoteAddr = ipv4Convert($_SERVER['HTTP_VIA']);
    } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
        $remoteAddr = ipv4Convert($_SERVER['REMOTE_ADDR']);
    }

    return $remoteAddr;
}

function ipv4Convert(string $ip): string {
    $matches = [];
    if (preg_match('/(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})/', $ip, $matches) > 0) {
        return $matches[1];
    } else {
        return $ip;
    }
}

/**
 * Get User Agent
 * @return string
 */
function getUserAgent(): string {
    return $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
}
