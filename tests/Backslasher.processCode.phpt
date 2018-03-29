<?php

require __DIR__ . '/bootstrap.php';

use Tester\Assert;

$io = new IOInterface;
$backslasher = new DG\ComposerBackslasher\Backslasher($io);

for ($i = 1; $i <= 3; $i++) {
	Assert::matchFile(
		__DIR__ . "/fixtures/test{$i}.expected.php",
		$backslasher->processCode(file_get_contents(__DIR__ . "/fixtures/test{$i}.php"))
	);
}
