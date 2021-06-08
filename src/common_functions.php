<?php declare(strict_types = 1);
namespace Medusa;

use function hash;

/**
 * Sha256
 * @param string $data
 * @param bool   $rawOutput
 * @return string
 */
function sha256(string $data, bool $rawOutput = false): string {
    return hash('sha256', $data, $rawOutput);
}

/**
 * Clean all active output buffers
 * @return void
 */
function obCleanAll(): void {
    while (ob_get_level()) {
        ob_end_clean();
    }
}
