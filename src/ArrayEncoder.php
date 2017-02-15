<?php
namespace mfmbarber\PHPHelpers;
/**
 * Based on the code chef problem Extreme Encoding (KOL16B)
 * This class encodes a set of even size arrays into a single
 * array with a hashed value, that can be decoded back into arrays
 * @author Matt Barber <mfmbarber@gmail.com>
 *
**/
class ArrayEncoder
{
    /**
     * Take in the integer x and shift the bits to the right by 16, giving us the
     * left hand value, then we need to undo the bitwise or operation using
     * the encoded value, minus $l shifted to the left 16 bits.
     *
     * @param integer   $x  The encoded value
     *
     * @return array
    **/
    private static function decodeInteger(int $x) : array {
        $l = $x >> 16;
        $r = $x - ($l << 16);
        return [$l, $r];
    }

    /**
     * Encode the left and right integer by shifting $r to the left
     * using bit wise operators - $r * 16 then using the bitwise
     * or operator to compare this with the left $l and return the result
     *
     * @param   integer     $l  The left hand integer
     * @param   integer     $r  The right hand integer
     *
     * @return int
    **/
    private static function encodeInteger(int $l, int $r) : int {
        return $r<<16 | $l;
    }

    /**
     * Merge a left and right array into an array of encoded integers
     *
     * @param   array   $left   The left array
     * @param   array   $right  The right array
     *
     * @return array
    **/
    public static function encode(array $left, array $right) : array {
        $encoded = [];
        for ($i = 0; $i < count($left); $i++) {
            $encoded[$i] = self::encodeInteger($left[$i], $right[$i]);
        }
        return $encoded;
    }

    /**
     * Decode a merged / encode array into the two original sub arrays
     *
     * @param array     $encoded    The encoded array
     *
     * @return array
    **/
    public static function decode(array $encoded) : array {
        $left = [];
        $right = [];
        foreach ($encoded as $enc_int) {
            list($left[], $right[]) = self::decodeInteger($enc_int);
        }
        return [$left, $right];
    }
}
