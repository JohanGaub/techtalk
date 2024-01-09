<?php

$containerPreloadFilePath = sprintf(
    '%s/var/cache/prod/App_KernelProdContainer.preload.php',
    dirname(__DIR__)
);

if (file_exists($containerPreloadFilePath)) {
    require_once $containerPreloadFilePath;
}
