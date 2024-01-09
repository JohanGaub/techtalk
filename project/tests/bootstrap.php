<?php

use Symfony\Component\Dotenv\Dotenv;

const GENERIC_PATH_PATTERN = '%s%s%s';

$directory = dirname(__DIR__);

require_once sprintf(GENERIC_PATH_PATTERN, $directory, DIRECTORY_SEPARATOR, 'vendor/autoload.php');

$bootstrapFile = sprintf(GENERIC_PATH_PATTERN, $directory, DIRECTORY_SEPARATOR, 'config/bootstrap.php');

if (file_exists($bootstrapFile)) {
    require_once $bootstrapFile;
} elseif (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(sprintf(GENERIC_PATH_PATTERN, $directory, DIRECTORY_SEPARATOR, '.env'));
}