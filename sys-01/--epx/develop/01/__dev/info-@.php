<?=__FILE__?>
<?php
// while(\ob_get_level() > \_\OB_OUT){ @\ob_end_clean(); }
// !empty($_SERVER['HTTP_HOST']) AND \header('Content-Type: application/json');
//$const = \get_defined_constants(true)['user'] ?? [];

// foreach (\get_defined_constants(true)['user'] ?? [] as $k => $v) {
//     if (is_string($v) && !mb_check_encoding($v, 'UTF-8')) {
//         echo "Invalid UTF-8 constant: $k\n";
//     }
// }

\_\prt(
    [
        'elapsed' => \number_format((((\defined('_\SIG_END') ? \_\SIG_END : \microtime(true)) - \_\MSTART)),6).'s',
        'tsp' => \explode(PATH_SEPARATOR, \get_include_path()),
        'env' => $_ENV,
        'const' => \get_defined_constants(true)['user'] ?? [],

    ],
    \JSON_PRETTY_PRINT | \JSON_UNESCAPED_SLASHES
);
