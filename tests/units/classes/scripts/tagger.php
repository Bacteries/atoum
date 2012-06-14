<?php

namespace mageekguy\atoum\tests\units\scripts;

use
	mageekguy\atoum,
	mageekguy\atoum\scripts
;

require_once __DIR__ . '/../../runner.php';

class tagger extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->isSubclassOf('mageekguy\atoum\script');
	}

	public function test__construct()
	{
		$this
			->if($tagger = new scripts\tagger(uniqid()))
			->then
				->object($dependencies = $tagger->getDepedencies())->isInstanceOf('mageekguy\atoum\dependencies')
				->boolean(isset($dependencies['locale']))->isTrue()
				->boolean(isset($dependencies['adapter']))->isTrue()
				->boolean(isset($dependencies['arguments\parser']))->isTrue()
				->boolean(isset($dependencies['writers\output']))->isTrue()
				->boolean(isset($dependencies['writers\error']))->isTrue()
				->boolean(isset($dependencies['engine']))->isTrue()
				->object($tagger->getEngine())->isInstanceOf('mageekguy\atoum\scripts\tagger\engine')
			->if($tagger = new scripts\tagger(uniqid(), $dependencies = new atoum\dependencies()))
			->then
				->object($taggerDepedencies = $tagger->getDepedencies())->isIdenticalTo($dependencies['mageekguy\atoum\scripts\tagger'])
				->boolean(isset($taggerDepedencies['locale']))->isTrue()
				->boolean(isset($taggerDepedencies['adapter']))->isTrue()
				->boolean(isset($taggerDepedencies['arguments\parser']))->isTrue()
				->boolean(isset($taggerDepedencies['writers\output']))->isTrue()
				->boolean(isset($taggerDepedencies['writers\error']))->isTrue()
				->boolean(isset($taggerDepedencies['engine']))->isTrue()
				->object($tagger->getEngine())->isInstanceOf('mageekguy\atoum\scripts\tagger\engine')
			->if($dependencies = new atoum\dependencies())
			->and($dependencies['mageekguy\atoum\scripts\tagger']['locale'] = $localeInjector = function() use (& $locale) { return $locale = new atoum\locale(); })
			->and($dependencies['mageekguy\atoum\scripts\tagger']['adapter'] = $adapterInjector = function() use (& $adapter) { return $adapter = new atoum\adapter(); })
			->and($dependencies['mageekguy\atoum\scripts\tagger']['arguments\parser'] = $argumentsParserInjector = function() use (& $argumentsParser) { return $argumentsParser = new atoum\script\arguments\parser(); })
			->and($dependencies['mageekguy\atoum\scripts\tagger']['writers\output'] = $outputWriterInjector = function() use (& $outputWriter) { return $outputWriter = new atoum\writers\std\out(); })
			->and($dependencies['mageekguy\atoum\scripts\tagger']['writers\error'] = $errorWriterInjector = function() use (& $errorWriter) { return $errorWriter = new atoum\writers\std\err(); })
			->and($dependencies['mageekguy\atoum\scripts\tagger']['engine'] = $engineInjector = function() use (& $engine) { return $engine = new atoum\scripts\tagger\engine(); })
			->if($tagger = new scripts\tagger(uniqid(), $dependencies))
			->then
				->object($taggerDepedencies = $tagger->getDepedencies())->isIdenticalTo($dependencies['mageekguy\atoum\scripts\tagger'])
				->object($taggerDepedencies['locale'])->isIdenticalTo($localeInjector)
				->object($tagger->getLocale())->isIdenticalTo($locale)
				->object($taggerDepedencies['adapter'])->isIdenticalTo($adapterInjector)
				->object($tagger->getAdapter())->isIdenticalTo($adapter)
				->object($taggerDepedencies['arguments\parser'])->isIdenticalTo($argumentsParserInjector)
				->object($tagger->getArgumentsParser())->isIdenticalTo($argumentsParser)
				->object($taggerDepedencies['writers\output'])->isIdenticalTo($outputWriterInjector)
				->object($tagger->getOutputWriter())->isIdenticalTo($outputWriter)
				->object($taggerDepedencies['writers\error'])->isIdenticalTo($errorWriterInjector)
				->object($tagger->getErrorWriter())->isIdenticalTo($errorWriter)
		;
	}

	public function testSetDepedencies()
	{
		$this
			->if($tagger = new scripts\tagger(uniqid()))
			->then
				->object($tagger->setDepedencies($dependencies = new atoum\dependencies()))->isIdenticalTo($tagger)
				->object($taggerDepedencies = $tagger->getDepedencies())->isIdenticalTo($dependencies['mageekguy\atoum\scripts\tagger'])
				->boolean(isset($taggerDepedencies['locale']))->isTrue()
				->boolean(isset($taggerDepedencies['adapter']))->isTrue()
				->boolean(isset($taggerDepedencies['arguments\parser']))->isTrue()
				->boolean(isset($taggerDepedencies['writers\output']))->isTrue()
				->boolean(isset($taggerDepedencies['writers\error']))->isTrue()
				->boolean(isset($taggerDepedencies['engine']))->isTrue()
			->if($dependencies = new atoum\dependencies())
			->and($dependencies['mageekguy\atoum\scripts\tagger']['locale'] = $localeInjector = function() {})
			->and($dependencies['mageekguy\atoum\scripts\tagger']['adapter'] = $adapterInjector = function() {})
			->and($dependencies['mageekguy\atoum\scripts\tagger']['arguments\parser'] = $argumentsParserInjector = function() {})
			->and($dependencies['mageekguy\atoum\scripts\tagger']['writers\output'] = $outputWriterInjector = function() {})
			->and($dependencies['mageekguy\atoum\scripts\tagger']['writers\error'] = $errorWriterInjector = function() {})
			->and($dependencies['mageekguy\atoum\scripts\tagger']['engine'] = $engineInjector = function() {})
			->then
				->object($tagger->setDepedencies($dependencies))->isIdenticalTo($tagger)
				->object($taggerDepedencies = $tagger->getDepedencies())->isIdenticalTo($dependencies['mageekguy\atoum\scripts\tagger'])
				->object($taggerDepedencies['locale'])->isIdenticalTo($localeInjector)
				->object($taggerDepedencies['adapter'])->isIdenticalTo($adapterInjector)
				->object($taggerDepedencies['arguments\parser'])->isIdenticalTo($argumentsParserInjector)
				->object($taggerDepedencies['writers\output'])->isIdenticalTo($outputWriterInjector)
				->object($taggerDepedencies['writers\error'])->isIdenticalTo($errorWriterInjector)
				->object($taggerDepedencies['engine'])->isIdenticalTo($engineInjector)
		;
	}

	public function testSetEngine()
	{
		$this
			->if($tagger = new scripts\tagger(uniqid()))
			->then
				->object($tagger->setEngine($engine = new scripts\tagger\engine()))->isIdenticalTo($tagger)
				->object($tagger->getEngine())->isIdenticalTo($engine)
		;
	}

	public function testRun()
	{
		$this
			->if($tagger = new \mock\mageekguy\atoum\scripts\tagger(uniqid()))
			->and($tagger
				->setEngine($engine = new \mock\mageekguy\atoum\scripts\tagger\engine())
				->getMockController()->writeMessage = $tagger
			)
			->and($engine->getMockController()->tagVersion = function() {})
			->then
				->object($tagger->run())->isIdenticalTo($tagger)
				->mock($engine)
					->call('tagVersion')->once()
			->if($engine->getMockController()->resetCalls())
			->then
				->object($tagger->run(array('-h')))->isIdenticalTo($tagger)
				->mock($tagger)
					->call('help')->atLeastOnce()
				->mock($engine)
					->call('tagVersion')->never()
		;
	}
}

?>
