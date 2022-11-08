#!/usr/bin/env bash

instal_dev=${1:-false}

if [ $instal_dev = "--dev" ]
then
    echo "Will reinstall dev build on completition"
fi

# Install dev dependencies
echo "Installing dev dependencies"
rm -rf vendor
composer config autoloader-suffix pc_stock_man_010
composer config prepend-autoloader true
composer install 

# Build all scoper patchers
echo "Building scope patchers"
php build-tools/create_patchers.php

# Run production build
echo "Building production"
composer config autoloader-suffix ""
rm -rf build/php 
rm -rf vendor
composer clear-cache
composer install --no-dev

echo "Running php-scoper"
mkdir -p build/php
php build-tools/scoper.phar add-prefix --output-dir=build/php --force --config=build-tools/scoper.inc.php

# Reset autoloader pefix & dump the autoloader to the new build path.
echo "Reset prefix for dev & rebuild autoloader in build"
composer config autoloader-suffix pc_stock_man_010

# Baesd on dev/prodction dump class map
if [ $instal_dev = "--dev" ]
then
    composer dump-autoload --working-dir build/php
    echo "Rebuilding dev dependencies"
    composer install 
    echo "Rebuilt all dev dependencies"
else
    composer dump-autoload --working-dir build/php --classmap-authoritative
fi


echo "Finished!!"


