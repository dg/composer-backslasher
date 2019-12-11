<?php
declare(strict_types=1);

/**
 * Victor The Cleaner for Composer.
 *
 * Copyright (c) 2015 David Grudl (https://davidgrudl.com)
 */

namespace DG\ComposerBackslasher;

use Composer\IO\IOInterface;
use PhpParser;


class Backslasher
{
	/** @var IOInterface */
	private $io;

	/** @var array */
	private $ignored;


	public function __construct(IOInterface $io, array $ignored = [])
	{
		$this->io = $io;
		$this->ignored = array_flip($ignored);
		$this->parser = (new PhpParser\ParserFactory)->create(
			PhpParser\ParserFactory::PREFER_PHP7,
			new PhpParser\Lexer(['usedAttributes' => ['startFilePos']])
		);
	}


	/**
	 * @return void
	 */
	public function processDir($vendorDir)
	{
		$count = 0;

		foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($vendorDir)) as $entry) {
			if (!$entry->isFile() || $entry->getExtension() !== 'php') {
				continue;
			}

			$input = file_get_contents((string) $entry);
			$output = $this->processCode($input, (string) $entry);
			if ($output !== $input) {
				file_put_contents((string) $entry, $output);
				$count += strlen($output) - strlen($input);
			}
		}

		$this->io->write("Composer Backslasher: Added $count backslashes.");
	}


	/**
	 * @return string
	 */
	public function processCode($code, $file = null)
	{
		try {
			$nodes = $this->parser->parse($code);
		} catch (PhpParser\Error $e) {
			$this->io->write("$file : {$e->getMessage()}", true, IOInterface::VERBOSE);
			return $code;
		}

		$collector = new Collector;
		$collector->ignored = $this->ignored;
		$traverser = new PhpParser\NodeTraverser;
		$traverser->addVisitor(new PhpParser\NodeVisitor\NameResolver);
		$traverser->addVisitor($collector);
		$traverser->traverse($nodes);

		foreach (array_reverse($collector->positions) as $pos) {
			$code = substr_replace($code, '\\', $pos, 0);
		}

		return $code;
	}
}
