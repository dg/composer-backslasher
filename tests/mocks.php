<?php
declare(strict_types=1);

namespace Composer\IO {

	interface IOInterface
	{
		public const VERBOSE = 'VERBOSE';
	}
}

namespace {

	class Mock
	{
		private $log = [];


		public function __call($name, $args)
		{
			$this->log[] = [$name, $args];
		}


		public function getLog()
		{
			$res = $this->log;
			$this->log = [];
			return $res;
		}
	}

	class IOInterface extends Mock implements Composer\IO\IOInterface
	{
	}
}
