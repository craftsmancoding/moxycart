<?php
require_once dirname(dirname(__FILE__)).'/vendor/autoload.php';

function normalize_string($str) {
    $str = preg_replace('/\s+/',' ',$str);
    return trim($str);
}
/*EOF*/