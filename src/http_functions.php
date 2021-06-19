<?php declare(strict_types = 1);
namespace Medusa\Http;

use Medusa\Http\Simple\MessageInterface;
use function array_map;
use function base64_decode;
use function base64_encode;
use function is_array;
use function preg_match;
use function rtrim;
use function str_pad;
use function strlen;
use const STR_PAD_RIGHT;

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

function flushMessage(MessageInterface $message): void {
    array_map('header', $message->getHeaders(true));
    echo $message->getBody();
}

/**
 * @param string $input
 * @return string
 */
function base64UrlEncode(string $input): string {
    return rtrim(strtr(base64_encode($input), '+/', '-_'), '=');
}

/**
 * @param string $input
 * @return string
 */
function base64UrlDecode(string $input): string {
    return base64_decode(str_pad(strtr($input, '-_', '+/'), strlen($input) % 4, '=', STR_PAD_RIGHT));
}

/**
 * Check if current or given request is SSL / HTTPS
 * @param array|MessageInterface|null $message
 * @return bool
 */
function isSsl(array|MessageInterface|null $message = null): bool {
    if ($message === null) {
        $haystack = $_SERVER;
    } elseif (is_array($message)) {
        $haystack = $message;
    } else {
        $headers = $message->getHeaders();
        $haystack = [
            'HTTP_SSL'     => $headers['Ssl'][0] ?? null,
            'HTTP_X_HTTPS' => $headers['X-Https'][0] ?? null,
            'HTTP_HTTPS'   => $headers['Https'][0] ?? null,

        ];
    }
    return
        (!empty($haystack['HTTP_SSL']) && $haystack['HTTP_SSL'] === 'true') ||
        (!empty($haystack['HTTP_X_HTTPS']) && $haystack['HTTP_X_HTTPS'] === 'on') ||
        (!empty($haystack['HTTP_HTTPS']) && $haystack['HTTP_HTTPS'] === 'on');
}
