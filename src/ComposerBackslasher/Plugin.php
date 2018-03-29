<?php

namespace DG\ComposerBackslasher;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Script\Event;
use Composer\Script\ScriptEvents;


class Plugin implements PluginInterface, EventSubscriberInterface
{
	public function activate(Composer $composer, IOInterface $io)
	{
	}


	public static function getSubscribedEvents()
	{
		return [
			ScriptEvents::POST_UPDATE_CMD => 'run',
			ScriptEvents::POST_INSTALL_CMD => 'run',
		];
	}


	public function run(Event $event)
	{
		$vendorDir = $event->getComposer()->getConfig()->get('vendor-dir');
		$backslasher = new Backslasher($event->getIO());
		$backslasher->processDir($vendorDir);
	}
}
