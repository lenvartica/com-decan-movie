<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Decan Movie Configuration
|--------------------------------------------------------------------------
|
| Replace the TMDB token below with your TMDB Read Access Token.
| Do NOT share this file publicly.
|
*/

const TMDB_ACCESS_TOKEN = 'SET_ON_SERVER_ONLY';

const TMDB_API_BASE = 'https://api.themoviedb.org/3';

/*
|--------------------------------------------------------------------------
| Site Settings
|--------------------------------------------------------------------------
*/

const SITE_NAME = 'Decan Movie';
const SITE_URL = 'https://decan-konnect-movie.vercel.app/';

/*
|--------------------------------------------------------------------------
| Error Reporting
|--------------------------------------------------------------------------
|
| Set to false on production if you don't want PHP errors displayed.
|
*/

const DEBUG_MODE = true;

if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}
