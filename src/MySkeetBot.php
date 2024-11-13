<?php
/**
 * Class MySkeetBot
 *
 * @todo: update/change docblock
 *
 * @created      04.11.2024
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2024 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace PHPSkeetBot\MySkeetBot;

use chillerlan\HTTP\Utils\MessageUtil;
use chillerlan\Settings\SettingsContainerInterface;
use chillerlan\Utilities\Arr;
use PHPSkeetBot\PHPSkeetBot\SkeetBot;
use Psr\Http\Message\ResponseInterface;
use function date;
use function explode;
use function sprintf;

/**
 * My first skeet bot
 */
class MySkeetBot extends SkeetBot{

	/**
	 * MySkeetBot constructor
	 */
	public function __construct(SettingsContainerInterface|MySkeetBotOptions $options){ // phpcs:ignore
		parent::__construct($options);

		// do some constructor stuff...
	}

	/**
	 * this method is mandated by the interface and called in the runner
	 *
	 * @link https://github.com/bluesky-social/atproto/blob/main/lexicons/app/bsky/feed/post.json
	 */
	public function post():static{
		// @todo: prepare your dataset
		$text   = 'Hello World!';
		$images = [/* one or more image blobs returned by $this->uploadMedia($mediafile) */];

		// prepare the skeet JSON body
		$body = [
			'collection' => 'app.bsky.feed.post',
			'repo'       => $this->bluesky->getAccountDID(),
			'record'     => [
				// the text property *must* be present, even if empty
				'text'      => $text,
				'langs'     => ['en'],
				'createdAt' => date('c'), // ISO 8601 date
				'$type'     => 'app.bsky.feed.post',
				// you can omit the embed part if there's no media to post
				'embed'     => [
					'$type'  => 'app.bsky.embed.images',
					'images' => $images,
				],
			],
		];

		$this->submitSkeet($body);

		return $this;
	}

	/**
	 * a simple implementation of the success handler, which prints the skeet URL to the console and exits the script
	 */
	protected function submitSkeetSuccess(ResponseInterface $response):never{
		$json = MessageUtil::decodeJSON($response);

		if(!isset($json->validationStatus) || $json->validationStatus !== 'valid'){
			$this->logger->error('invalid status');

			exit(255);
		}

		// my god bsky just give me the full url to the post it's not hard
		$url = sprintf('https://bsky.app/profile/%s/post/%s', $this->bluesky->getHandle(), Arr::last(explode('/', $json->uri)));

		$this->logger->info(sprintf('posted: %s', $url));

		exit(0);
	}

	/**
	 * the failure handler prints the error message (if any) to the console and exits with a non-success status
	 */
	protected function submitSkeetFailure(ResponseInterface|null $response):never{

		if($response instanceof ResponseInterface){
			$json = MessageUtil::decodeJSON($response);

			if(isset($json->message)){
				$this->logger->error($json->message);
			}
		}

		exit(255);
	}

}
