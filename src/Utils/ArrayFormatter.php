<?php

namespace MEC_AmicronSchnittstelle\Utils;

class ArrayFormatter
{
    /**
     * Format an array with proper indentation for better readability
     * 
     * @param array $array The array to format
     * @param int $indent Initial indentation level
     * @return string
     */
    public static function prettyPrint(array $array, int $indent = 0): string
    {
        $result = '';
        $prefix = str_repeat('    ', $indent);

        foreach ($array as $key => $value) {
            $result .= $prefix;
            $result .= "[" . $key . "] => ";

            if (is_array($value)) {
                $result .= "{\n";
                $result .= self::prettyPrint($value, $indent + 1);
                $result .= $prefix . "}\n";
            } else {
                $result .= var_export($value, true) . "\n";
            }
        }

        return $result;
    }
}
