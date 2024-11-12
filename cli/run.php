<?php
/**
 * run.php
 *
 * @created      27.10.2024
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2024 smiley
 * @license      MIT
 */
declare(strict_types=1);

use chillerlan\DotEnv\DotEnv;
use chillerlan\Utilities\File;
use PHPSkeetBot\MySkeetBot\MySkeetBot;
use PHPSkeetBot\MySkeetBot\MySkeetBotOptions;
use Psr\Log\LogLevel;

ini_set('date.timezone', 'UTC');
mb_internal_encoding('UTF-8');

require_once __DIR__.'/../vendor/autoload.php';

// if we're running on gh-actions, we're going to fetch the variables from gh.secrets,
// otherwise we'll load them from the local .env file into the global environment
if(!isset($_SERVER['GITHUB_ACTIONS'])){
	(new DotEnv(__DIR__.'/../.config', '.env', true))->load();
}

// invoke the options instance
$options = new MySkeetBotOptions;

// HTTPOptions
$options->ca_info        = File::realpath(__DIR__.'/cacert.pem'); // https://curl.se/ca/cacert.pem
$options->user_agent     = 'phpSkeetBot/1.0 +https://github.com/php-skeetbot/php-skeetbot';
$options->timeout        = 30;
$options->retries        = 3;

// SkeetBotOptions
$options->handle         = getenv('BLUESKY_HANDLE');
$options->appPassword    = getenv('BLUESKY_APP_PW');
$options->loglevel       = LogLevel::INFO;
#$options->buildDir       = __DIR__.'/../.build';
$options->dataDir        = __DIR__.'/../data';

#var_dump($options);
// invoke the bot instance and post
(new MySkeetBot($options))
	->connect()
	->post()
;
