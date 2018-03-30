<?php

/**
 * Victor The Cleaner for Composer.
 *
 * Copyright (c) 2015 David Grudl (https://davidgrudl.com)
 */

namespace DG\ComposerBackslasher;

use Composer\IO\IOInterface;


class Backslasher
{
	/** @var IOInterface */
	private $io;

	/** @var int */
	private $count = 0;


	public function __construct(IOInterface $io)
	{
		$this->io = $io;
	}


	/**
	 * @return void
	 */
	public function processDir($vendorDir)
	{
		$this->count = 0;

		foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($vendorDir)) as $entry) {
			if (!$entry->isFile() || $entry->getExtension() !== 'php') {
				continue;
			}

			$input = file_get_contents((string) $entry);
			$output = $this->processCode($input);
			if ($output !== $input) {
				file_put_contents((string) $entry, $output);
			}
		}

		$this->io->write("Composer Backslasher: Added $this->count backslashes.");
	}


	/**
	 * @return string
	 */
	public function processCode($code)
	{
		$tokens = token_get_all($code);
		$res = $prev1 = $prev2 = $prev3 = $pos = $enabled = null;

		foreach ($tokens as $token) {
			if ($token[0] === T_WHITESPACE) {

			} elseif ($token[0] === T_NAMESPACE) {
				$enabled = true;

			} elseif ($enabled // constant
				&& $token !== '='
				&& $prev1[0] === T_STRING
				&& defined($prev1[1])
				&& !preg_match('~true|false|null~i', $prev1[1])
				&& $prev2[0] !== T_DOUBLE_COLON
				&& $prev2[0] !== T_OBJECT_OPERATOR
				&& $prev2[0] !== T_NS_SEPARATOR
			) {
				$res = substr_replace($res, '\\', $pos, 0);
				$this->count++;

			} elseif ($enabled // function
				&& $token === '('
				&& $prev1[0] === T_STRING
				&& function_exists($prev1[1])
				&& $prev2[0] !== T_DOUBLE_COLON
				&& $prev2[0] !== T_OBJECT_OPERATOR
				&& $prev2[0] !== T_FUNCTION
				&& $prev3[0] !== T_FUNCTION
				&& $prev2[0] !== T_NEW
				&& $prev2[0] !== T_NS_SEPARATOR
			) {
				$res = substr_replace($res, '\\', $pos, 0);
				$this->count++;
			}

			if ($token[0] !== T_WHITESPACE) {
				list($prev3, $prev2, $prev1, $pos) = [$prev2, $prev1, $token, strlen($res)];
			}

			$res .= is_array($token) ? $token[1] : $token;
		}
		return $res;
	}
}
