<?php
/**
 * Class MySkeetBotTest
 *
 * @created      04.11.2024
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2024 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace PHPSkeetBot\MySkeetBotTest;

use PHPSkeetBot\MySkeetBot\MySkeetBot;
use PHPSkeetBot\MySkeetBot\MySkeetBotOptions;
use PHPSkeetBot\PHPSkeetBot\SkeetBotInterface;
use PHPUnit\Framework\TestCase;

class MySkeetBotTest extends TestCase{

	public function testInstance():void{
		$this::assertInstanceOf(SkeetBotInterface::class, new MySkeetBot(new MySkeetBotOptions));
	}

}
