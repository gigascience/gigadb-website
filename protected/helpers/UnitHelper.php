<?php

/**
 * This is a utility component for parsing, formatting, converting and manipulating byte units in various formats.
 * @uses gabrielelana/byte-units package
 *
 * It supports both Metric (1000-byte) system and Binary (1024-byte) system.
 *
 */
class UnitHelper
{
    /**
     * Return the human readable size of files with configurable formatting
     *
     * It's extracted out of getSizeWithFormat so the functionality can be used in other contexts as well.
     *
     * @param int $bytes size in bytes to format/convert
     * @param string $unit unit to convert to kB, MB, GB, TB, B or null
     * @param int $precision number of decimals after the dot
     * @uses ByteUnits\Metric
     * @return string formatted size
     */
    public static function specifySizeUnits(int $bytes, string $unit = null, int $precision = null): string
    {
        if ($bytes<0) {
            return (string) $bytes;
        }
        if ( null == $precision ) {
            $precision = 2;
        }
        $metric = new ByteUnits\Metric($bytes);
        $formatted_size = $metric->format("$unit/$precision"," ");
        return $formatted_size ;
    }

}