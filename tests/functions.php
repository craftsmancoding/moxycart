<?php
/**
 * Normalize (HTML) strings for valid comparison.
 */
function normalize_string($str) {
    $str = preg_replace('/\s+/',' ',$str);
    return trim($str);
}

/**
 * Does the $haystack begin with the $needle?
 * http://stackoverflow.com/questions/834303/startswith-and-endswith-functions-in-php
 * @return boolean
 */
function startsWith($haystack, $needle) {
     $length = strlen($needle);
     return (substr($haystack, 0, $length) === $needle);
}
/*EOF*/