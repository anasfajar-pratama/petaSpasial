<?php

return [
    'path' => env('GDAL_PATH', ''),
    'enabled' => env('GDAL_PATH') && file_exists(env('GDAL_PATH') . '/ogr2ogr' . (PHP_OS_FAMILY === 'Windows' ? '.exe' : '')),
    'bin' => env('GDAL_PATH') ? env('GDAL_PATH') . '/ogr2ogr' : 'ogr2ogr',
];
