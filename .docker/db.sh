#!/bin/bash
cd /app
composer install
php spark migrate
apache2-foreground