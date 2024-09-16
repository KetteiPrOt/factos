@echo off
REM // Esto es un comentario.
REM // Este archivo por lotes restaurará los archivos de Laravel de la carpeta /public

REM // public/index.php
(
echo ^<?php
echo // ...
echo use Illuminate\Http\Request;
echo // ...
echo define^('LARAVEL_START', microtime^(true^)^);
echo // ...
echo // Determine if the application is in maintenance mode...
echo if ^(file_exists^($maintenance = __DIR__.'/../storage/framework/maintenance.php'^)^) {
echo     require $maintenance;
echo }
echo // ...
echo // Register the Composer autoloader...
echo require __DIR__.'/../vendor/autoload.php';
echo // ...
echo // Bootstrap Laravel and handle the request...
echo ^(require_once __DIR__.'/../bootstrap/app.php'^)
echo     ^-^>handleRequest^(Request::capture^(^)^);
) > public/index.php

REM // public/.htaccess
(
echo ^<IfModule mod_rewrite.c^>
echo     ^<IfModule mod_negotiation.c^>
echo         Options -MultiViews -Indexes
echo     ^</IfModule^>
echo     # ...
echo     RewriteEngine On
echo     # ... 
echo     # Handle Authorization Header
echo     RewriteCond %%{HTTP:Authorization} .
echo     RewriteRule .* - [E=HTTP_AUTHORIZATION:%%{HTTP:Authorization}]
echo     # ...
echo     # Redirect Trailing Slashes If Not A Folder...
echo     RewriteCond %%{REQUEST_FILENAME} !-d
echo     RewriteCond %%{REQUEST_URI} ^(.+^)/$
echo     RewriteRule ^^ %%1 [L,R=301]
echo     # ...
echo     # Send Requests To Front Controller...
echo     RewriteCond %%{REQUEST_URI} ^^/api/
echo     RewriteRule ^^ index.php [L]
echo     # ...
echo     RewriteBase /
echo 	RewriteRule ^^200\.html$ - [L]
echo 	RewriteCond %%{REQUEST_FILENAME} !-f
echo 	RewriteCond %%{REQUEST_FILENAME} !-d
echo 	RewriteRule . /200.html [L]
echo ^</IfModule^>
) > public/.htaccess

REM // public/.gitignore
(
echo *
echo !.gitignore
echo !.htaccess
echo !index.php
) > public/.gitignore

echo Laravel's files in '/public' directory restored