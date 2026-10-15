<?php

namespace wcf\system\hom;

/**
 * Reads the legacy_bits code of the official calculator: base64 characters carry 6 bits
 * each, least significant bit first, and multi-bit fields are assembled the same way.
 *
 * @author    Calin
 * @copyright 2026 Guild Wars Legacy
 * @license   GNU Lesser General Public License
 */
final class HomBitReader
{
    private const ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/';

    private string $code;

    private int $pos = 0;

    public function __construct(string $code)
    {
        $this->code = $code;
    }

    /**
     * Reads the next $bits bits as an unsigned integer. Past the end reads zeros.
     */
    public function get(int $bits): int
    {
        $value = 0;
        for ($i = 0; $i < $bits; $i++) {
            $value |= $this->bit($this->pos++) << $i;
        }

        return $value;
    }

    private function bit(int $position): int
    {
        $index = \intdiv($position, 6);
        if ($index >= \strlen($this->code)) {
            return 0;
        }
        $value = \strpos(self::ALPHABET, $this->code[$index]);
        if ($value === false) {
            return 0;
        }

        return ($value >> ($position % 6)) & 1;
    }

    /**
     * True when the code looks like a legacy_bits string.
     */
    public static function isValidCode(string $code): bool
    {
        return $code !== '' && \strlen($code) <= 255 && \preg_match('~^[A-Za-z0-9+/]+$~', $code) === 1;
    }
}
