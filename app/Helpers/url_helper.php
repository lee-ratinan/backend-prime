<?php

/**
 * *********************************************************************
 * THIS FILE IS SYSTEM HELPER, PLEASE REFRAIN FROM MAKING
 * ANY CHANGES TO THIS FILE UNLESS YOU KNOW WHAT YOU ARE DOING.
 * *********************************************************************
 */

/**
 * Generate the Caesar cipher for the input word
 * @param string $word
 * @return string
 */
function encode_caesar_cipher(string $word): string
{
    $array = [
        'a' => 'h',
        'b' => 'i',
        'c' => 'j',
        'd' => 'k',
        'e' => 'l',
        'f' => 'm',
        'g' => 'n',
        'h' => 'o',
        'i' => 'p',
        'j' => 'q',
        'k' => 'r',
        'l' => 's',
        'm' => 't',
        'n' => 'u',
        'o' => 'v',
        'p' => 'w',
        'q' => 'x',
        'r' => 'y',
        's' => 'z',
        't' => 'a',
        'u' => 'b',
        'v' => 'c',
        'w' => 'd',
        'x' => 'e',
        'y' => 'f',
        'z' => 'g',
        'A' => 'M',
        'B' => 'N',
        'C' => 'O',
        'D' => 'P',
        'E' => 'Q',
        'F' => 'R',
        'G' => 'S',
        'H' => 'T',
        'I' => 'U',
        'J' => 'V',
        'K' => 'W',
        'L' => 'X',
        'M' => 'Y',
        'N' => 'Z',
        'O' => 'A',
        'P' => 'B',
        'Q' => 'C',
        'R' => 'D',
        'S' => 'E',
        'T' => 'F',
        'U' => 'G',
        'V' => 'H',
        'W' => 'I',
        'X' => 'J',
        'Y' => 'K',
        'Z' => 'L',
        '@' => '-',
        '-' => '_',
        '.' => '~'
    ];
    $result = '';
    for ($i = 0; $i < strlen($word); $i++) {
        $result .= $array[$word[$i]] ?? $word[$i];
    }
    return $result;
}

/**
 * Decode the Caesar cipher for the input word
 * @param string $word
 * @return string
 */
function decode_caesar_cipher(string $word): string
{
    $array = [
        'h' => 'a',
        'i' => 'b',
        'j' => 'c',
        'k' => 'd',
        'l' => 'e',
        'm' => 'f',
        'n' => 'g',
        'o' => 'h',
        'p' => 'i',
        'q' => 'j',
        'r' => 'k',
        's' => 'l',
        't' => 'm',
        'u' => 'n',
        'v' => 'o',
        'w' => 'p',
        'x' => 'q',
        'y' => 'r',
        'z' => 's',
        'a' => 't',
        'b' => 'u',
        'c' => 'v',
        'd' => 'w',
        'e' => 'x',
        'f' => 'y',
        'g' => 'z',
        'M' => 'A',
        'N' => 'B',
        'O' => 'C',
        'P' => 'D',
        'Q' => 'E',
        'R' => 'F',
        'S' => 'G',
        'T' => 'H',
        'U' => 'I',
        'V' => 'J',
        'W' => 'K',
        'X' => 'L',
        'Y' => 'M',
        'Z' => 'N',
        'A' => 'O',
        'B' => 'P',
        'C' => 'Q',
        'D' => 'R',
        'E' => 'S',
        'F' => 'T',
        'G' => 'U',
        'H' => 'V',
        'I' => 'W',
        'J' => 'X',
        'K' => 'Y',
        'L' => 'Z',
        '-' => '@',
        '_' => '-',
        '~' => '.',
    ];
    $result = '';
    for ($i = 0; $i < strlen($word); $i++) {
        $result .= $array[$word[$i]] ?? $word[$i];
    }
    return $result;
}