<?php

namespace mageekguy\atoum\tests\units;

use
	mageekguy\atoum
;

require_once __DIR__ . '/../runner.php';

class score extends atoum\test
{
	public function test__construct()
	{
		$this
			->if($score = new atoum\score())
			->then
				->integer($score->getPassNumber())->isZero()
				->array($score->getFailAssertions())->isEmpty()
				->array($score->getExceptions())->isEmpty()
				->array($score->getErrors())->isEmpty()
				->array($score->getOutputs())->isEmpty()
				->array($score->getDurations())->isEmpty()
				->array($score->getMemoryUsages())->isEmpty()
				->array($score->getUncompletedMethods())->isEmpty()
				->object($score->getCoverage())->isInstanceOf('mageekguy\atoum\score\coverage')
			->if($dependencies = new atoum\dependencies())
			->and($dependencies['coverage'] = $coverage = new \mageekguy\atoum\score\coverage())
			->and($score = new atoum\score($dependencies))
			->then
				->integer($score->getPassNumber())->isZero()
				->array($score->getFailAssertions())->isEmpty()
				->array($score->getExceptions())->isEmpty()
				->array($score->getErrors())->isEmpty()
				->array($score->getOutputs())->isEmpty()
				->array($score->getDurations())->isEmpty()
				->array($score->getMemoryUsages())->isEmpty()
				->object($score->getCoverage())->isIdenticalTo($coverage)
		;
	}

	public function testAddException()
	{
		$this
			->if($score = new atoum\score())
			->then
				->object($score->addException($file = uniqid(), $line = rand(1, PHP_INT_MAX), $class = uniqid(), $method = uniqid(), $exception = new \exception()))->isIdenticalTo($score)
				->array($score->getExceptions())->isEqualTo(array(
						array(
							'case' => null,
							'dataSetKey' => null,
							'dataSetProvider' => null,
							'class' => $class,
							'method' => $method,
							'file' => $file,
							'line' => $line,
							'value' => (string) $exception
						)
					)
				)
				->integer($score->getExceptionNumber())->isEqualTo(1)
				->object($score->addException($otherFile = uniqid(), $otherLine = rand(1, PHP_INT_MAX), $otherClass = uniqid(), $otherMethod = uniqid(), $otherException = new \exception()))->isIdenticalTo($score)
					->array($score->getExceptions())->isEqualTo(array(
						array(
							'case' => null,
							'dataSetKey' => null,
							'dataSetProvider' => null,
							'class' => $class,
							'method' => $method,
							'file' => $file,
							'line' => $line,
							'value' => (string) $exception
						),
						array(
							'case' => null,
							'dataSetKey' => null,
							'dataSetProvider' => null,
							'class' => $otherClass,
							'method' => $otherMethod,
							'file' => $otherFile,
							'line' => $otherLine,
							'value' => (string) $otherException
						)
					)
				)
		;
	}

	public function testAddPass()
	{
		$this
			->if($score = new atoum\score())
			->then
				->object($score->addPass())->isIdenticalTo($score)
				->integer($score->getPassNumber())->isEqualTo(1)
				->object($score->addPass())->isIdenticalTo($score)
				->integer($score->getPassNumber())->isEqualTo(2)
		;
	}

	public function testAddFail()
	{
		$this
			->if($score = new atoum\score())
			->then
				->integer($score->addFail($file = uniqid(), $line = rand(1, PHP_INT_MAX), $class = uniqid(), $method = uniqid(), $asserter = new atoum\asserters\integer(new atoum\asserter\generator()), $reason = uniqid()))->isGreaterThan(0)
				->array($score->getFailAssertions())->isEqualTo(array(
						array(
							'case' => null,
							'dataSetKey' => null,
							'dataSetProvider' => null,
							'class' => $class,
							'method' => $method,
							'file' => $file,
							'line' => $line,
							'asserter' => $asserter,
							'fail' => $reason
						)
					)
				)
				->integer($score->addFail($otherFile = uniqid(), $otherLine = rand(1, PHP_INT_MAX), $otherClass = uniqid(), $otherMethod = uniqid(), $otherAsserter = new atoum\asserters\integer(new atoum\asserter\generator()), $otherReason = uniqid()))->isGreaterThan(0)
				->array($score->getFailAssertions())->isEqualTo(array(
						array(
							'case' => null,
							'dataSetKey' => null,
							'dataSetProvider' => null,
							'class' => $class,
							'method' => $method,
							'file' => $file,
							'line' => $line,
							'asserter' => $asserter,
							'fail' => $reason
						),
						array(
							'case' => null,
							'dataSetKey' => null,
							'dataSetProvider' => null,
							'class' => $otherClass,
							'method' => $otherMethod,
							'file' => $otherFile,
							'line' => $otherLine,
							'asserter' => $otherAsserter,
							'fail' => $otherReason
						)
					)
				)
		;
	}

	public function testAddError()
	{
		$this
			->if($score = new atoum\score())
			->then
				->array($score->getErrors())->isEmpty()
				->integer($score->getErrorNumber())->isZero()
				->object($score->addError($file = uniqid(), $line = rand(1, PHP_INT_MAX), $class = uniqid(), $method = uniqid(), $type = rand(1, PHP_INT_MAX), $message = uniqid(), $errorFile = uniqid(), $errorLine = rand(1, PHP_INT_MAX)))->isIdenticalTo($score)
				->array($score->getErrors())->isEqualTo(array(
						array(
							'case' => null,
							'dataSetKey' => null,
							'dataSetProvider' => null,
							'class' => $class,
							'method' => $method,
							'file' => $file,
							'line' => $line,
							'type' => $type,
							'message' => $message,
							'errorFile' => $errorFile,
							'errorLine' => $errorLine
						)
					)
				)
				->integer($score->getErrorNumber())->isEqualTo(1)
				->object($score->addError($otherFile = uniqid(), $otherLine= rand(1, PHP_INT_MAX), $otherClass = uniqid(), $otherMethod = uniqid(), $otherType = rand(1, PHP_INT_MAX), $otherMessage = uniqid(), $otherErrorFile = uniqid(), $otherErrorLine = rand(1, PHP_INT_MAX)))->isIdenticalTo($score)
				->array($score->getErrors())->isEqualTo(array(
						array(
							'case' => null,
							'dataSetKey' => null,
							'dataSetProvider' => null,
							'class' => $class,
							'method' => $method,
							'file' => $file,
							'line' => $line,
							'type' => $type,
							'message' => $message,
							'errorFile' => $errorFile,
							'errorLine' => $errorLine
						),
						array(
							'case' => null,
							'dataSetKey' => null,
							'dataSetProvider' => null,
							'class' => $otherClass,
							'method' => $otherMethod,
							'file' => $otherFile,
							'line' => $otherLine,
							'type' => $otherType,
							'message' => $otherMessage,
							'errorFile' => $otherErrorFile,
							'errorLine' => $otherErrorLine
						)
					)
				)
				->integer($score->getErrorNumber())->isEqualTo(2)
				->object($score->addError($file, $line, $class, $method, $type, $anAnotherMessage = uniqid(), $errorFile, $errorLine))->isIdenticalTo($score)
				->array($score->getErrors())->isEqualTo(array(
						array(
							'case' => null,
							'dataSetKey' => null,
							'dataSetProvider' => null,
							'class' => $class,
							'method' => $method,
							'file' => $file,
							'line' => $line,
							'type' => $type,
							'message' => $message,
							'errorFile' => $errorFile,
							'errorLine' => $errorLine
						),
						array(
							'case' => null,
							'dataSetKey' => null,
							'dataSetProvider' => null,
							'class' => $class,
							'method' => $method,
							'file' => $file,
							'line' => $line,
							'type' => $type,
							'message' => $anAnotherMessage,
							'errorFile' => $errorFile,
							'errorLine' => $errorLine
						),
						array(
							'case' => null,
							'dataSetKey' => null,
							'dataSetProvider' => null,
							'class' => $otherClass,
							'method' => $otherMethod,
							'file' => $otherFile,
							'line' => $otherLine,
							'type' => $otherType,
							'message' => $otherMessage,
							'errorFile' => $otherErrorFile,
							'errorLine' => $otherErrorLine
						)
					)
				)
				->integer($score->getErrorNumber())->isEqualTo(3)
		;
	}

	public function testAddOutput()
	{
		$this
			->if($score = new atoum\score())
			->then
				->array($score->getOutputs())->isEmpty()
				->integer($score->getOutputNumber())->isZero()
				->object($score->addOutput($class = uniqid(), $method = uniqid(), $output = uniqid()))->isIdenticalTo($score)
				->array($score->getOutputs())->isEqualTo(array(
						array(
							'class' => $class,
							'method' => $method,
							'value' => $output
						)
					)
				)
				->integer($score->getOutputNumber())->isEqualTo(1)
				->object($score->addOutput($otherClass = uniqid(), $otherMethod = uniqid(), $otherOutput = uniqid()))->isIdenticalTo($score)
				->array($score->getOutputs())->isEqualTo(array(
						array(
							'class' => $class,
							'method' => $method,
							'value' => $output
						),
						array(
							'class' => $otherClass,
							'method' => $otherMethod,
							'value' => $otherOutput
						)
					)
				)
				->integer($score->getOutputNumber())->isEqualTo(2)
				->object($score->addOutput($class, $method, $moreOutput = uniqid()))->isIdenticalTo($score)
				->array($score->getOutputs())->isEqualTo(array(
						array(
							'class' => $class,
							'method' => $method,
							'value' => $output
						),
						array(
							'class' => $otherClass,
							'method' => $otherMethod,
							'value' => $otherOutput
						),
						array(
							'class' => $class,
							'method' => $method,
							'value' => $moreOutput
						)
					)
				)
				->integer($score->getOutputNumber())->isEqualTo(3)
		;
	}

	public function testAddDuration()
	{
		$this
			->if($score = new atoum\score())
			->then
				->array($score->getDurations())->isEmpty()
				->integer($score->getDurationNumber())->isZero()
				->object($score->addDuration($class = uniqid(), $path = uniqid(), $method = uniqid(), $duration = rand(1, PHP_INT_MAX)))->isIdenticalTo($score)
				->array($score->getDurations())->isEqualTo(array(
						array(
							'class' => $class,
							'method' => $method,
							'value' => $duration,
							'path' => $path
						)
					)
				)
				->integer($score->getDurationNumber())->isEqualTo(1)
				->object($score->addDuration($otherClass = uniqid(), $otherPath = uniqid(), $otherMethod = uniqid(), $otherDuration = rand(1, PHP_INT_MAX)))->isIdenticalTo($score)
				->array($score->getDurations())->isEqualTo(array(
						array(
							'class' => $class,
							'method' => $method,
							'value' => $duration,
							'path' => $path
						),
						array(
							'class' => $otherClass,
							'method' => $otherMethod,
							'value' => $otherDuration,
							'path' => $otherPath
						)
					)
				)
				->integer($score->getDurationNumber())->isEqualTo(2)
				->object($score->addDuration(uniqid(), uniqid(), uniqid(), 0))->isIdenticalTo($score)
				->array($score->getDurations())->isEqualTo(array(
						array(
							'class' => $class,
							'method' => $method,
							'value' => $duration,
							'path' => $path
						),
						array(
							'class' => $otherClass,
							'method' => $otherMethod,
							'value' => $otherDuration,
							'path' => $otherPath
						)
					)
				)
				->integer($score->getDurationNumber())->isEqualTo(2)
				->object($score->addDuration(uniqid(), uniqid(), uniqid(), - rand(1, PHP_INT_MAX)))->isIdenticalTo($score)
				->array($score->getDurations())->isEqualTo(array(
						array(
							'class' => $class,
							'method' => $method,
							'value' => $duration,
							'path' => $path
						),
						array(
							'class' => $otherClass,
							'method' => $otherMethod,
							'value' => $otherDuration,
							'path' => $otherPath
						)
					)
				)
				->integer($score->getDurationNumber())->isEqualTo(2)
				->object($score->addDuration($class, $path, $method, $moreDuration = rand(1, PHP_INT_MAX)))->isIdenticalTo($score)
				->array($score->getDurations())->isEqualTo(array(
						array(
							'class' => $class,
							'method' => $method,
							'value' => $duration,
							'path' => $path
						),
						array(
							'class' => $otherClass,
							'method' => $otherMethod,
							'value' => $otherDuration,
							'path' => $otherPath
						),
						array(
							'class' => $class,
							'method' => $method,
							'value' => $moreDuration,
							'path' => $path
						)
					)
				)
				->integer($score->getDurationNumber())->isEqualTo(3)
		;
	}

	public function testAddMemoryUsage()
	{
		$this
			->if($score = new atoum\score())
			->then
				->array($score->getMemoryUsages())->isEmpty()
				->object($score->addMemoryUsage($class = uniqid(), $method = uniqid(), $memoryUsage = rand(1, PHP_INT_MAX)))->isIdenticalTo($score)
				->array($score->getMemoryUsages())->isEqualTo(array(
						array(
							'class' => $class,
							'method' => $method,
							'value' => $memoryUsage
						)
					)
				)
				->object($score->addMemoryUsage($otherClass = uniqid(), $otherMethod = uniqid(), $otherMemoryUsage = rand(1, PHP_INT_MAX)))->isIdenticalTo($score)
				->array($score->getMemoryUsages())->isEqualTo(array(
						array(
							'class' => $class,
							'method' => $method,
							'value' => $memoryUsage
						),
						array(
							'class' => $otherClass,
							'method' => $otherMethod,
							'value' => $otherMemoryUsage
						)
					)
				)
				->object($score->addMemoryUsage(uniqid(), uniqid(), 0))->isIdenticalTo($score)
				->array($score->getMemoryUsages())->isEqualTo(array(
						array(
							'class' => $class,
							'method' => $method,
							'value' => $memoryUsage
						),
						array(
							'class' => $otherClass,
							'method' => $otherMethod,
							'value' => $otherMemoryUsage
						)
					)
				)
				->object($score->addMemoryUsage(uniqid(), uniqid(), - rand(1, PHP_INT_MAX)))->isIdenticalTo($score)
				->array($score->getMemoryUsages())->isEqualTo(array(
						array(
							'class' => $class,
							'method' => $method,
							'value' => $memoryUsage
						),
						array(
							'class' => $otherClass,
							'method' => $otherMethod,
							'value' => $otherMemoryUsage
						)
					)
				)
				->object($score->addMemoryUsage($class, $method, $moreMemoryUsage = rand(1, PHP_INT_MAX)))->isIdenticalTo($score)
				->array($score->getMemoryUsages())->isEqualTo(array(
						array(
							'class' => $class,
							'method' => $method,
							'value' => $memoryUsage
						),
						array(
							'class' => $otherClass,
							'method' => $otherMethod,
							'value' => $otherMemoryUsage
						),
						array(
							'class' => $class,
							'method' => $method,
							'value' => $moreMemoryUsage
						)
					)
				)
		;
	}

	public function testAddUncompletedTest()
	{
		$this
			->if($score = new atoum\score())
			->then
				->array($score->getUncompletedMethods())->isEmpty()
				->object($score->addUncompletedMethod($class = uniqid(), $method = uniqid(), $exitCode = rand(1, PHP_INT_MAX), $output = uniqid()))->isIdenticalTo($score)
				->array($score->getUncompletedMethods())->isEqualTo(array(
						array(
							'class' => $class,
							'method' => $method,
							'exitCode' => $exitCode,
							'output' => $output,
						)
					)
				)
				->object($score->addUncompletedMethod($otherClass = uniqid(), $otherMethod = uniqid(), $otherExitCode = rand(1, PHP_INT_MAX), $otherOutput = uniqid()))->isIdenticalTo($score)
				->array($score->getUncompletedMethods())->isEqualTo(array(
						array(
							'class' => $class,
							'method' => $method,
							'exitCode' => $exitCode,
							'output' => $output,
						),
						array(
							'class' => $otherClass,
							'method' => $otherMethod,
							'exitCode' => $otherExitCode,
							'output' => $otherOutput,
						)
					)
				)
		;
	}

	public function testAddRuntimeException()
	{
		$this
			->if($score = new atoum\score())
			->then
				->array($score->getRuntimeExceptions())->isEmpty()
				->integer($score->getRuntimeExceptionNumber())->isZero()
				->object($score->addRuntimeException($exception = new atoum\test\exceptions\runtime()))->isIdenticalTo($score)
				->array($score->getRuntimeExceptions())->isEqualTo(array(
						$exception
					)
				)
				->integer($score->getRuntimeExceptionNumber())->isEqualTo(1)
				->object($score->addRuntimeException($otherException = new atoum\test\exceptions\runtime()))->isIdenticalTo($score)
				->array($score->getRuntimeExceptions())->isEqualTo(array(
						$exception,
						$otherException
					)
				)
				->integer($score->getRuntimeExceptionNumber())->isEqualTo(2)
		;
	}

	public function testSetCoverage()
	{
		$this
			->if($score = new atoum\score())
			->then
				->object($score->setCoverage($coverage = new atoum\score\coverage()))->isIdenticalTo($score)
				->object($score->getCoverage())->isIdenticalTo($coverage)
		;
	}

	public function testGetExceptionNumber()
	{
		$this
			->if($score = new atoum\score())
			->then
				->integer($score->getExceptionNumber())->isZero()
			->if($score->addException(uniqid(), rand(1, PHP_INT_MAX), uniqid(), uniqid(), new \exception()))
			->then
				->integer($score->getExceptionNumber())->isEqualTo(1)
			->if($score->addException(uniqid(), rand(1, PHP_INT_MAX), uniqid(), uniqid(), new \exception()))
			->then
				->integer($score->getExceptionNumber())->isEqualTo(2)
		;
	}

	public function testGetFailNumber()
	{
		$this
			->if($score = new atoum\score())
			->then
				->integer($score->getFailNumber())->isZero()
			->if($score->addFail(uniqid(), rand(1, PHP_INT_MAX), uniqid(), uniqid(), new atoum\asserters\integer(new atoum\asserter\generator()), uniqid()))
			->then
				->integer($score->getFailNumber())->isEqualTo(1)
			->if($score->addFail(uniqid(), rand(1, PHP_INT_MAX), uniqid(), uniqid(), new atoum\asserters\integer(new atoum\asserter\generator()), uniqid()))
			->then
				->integer($score->getFailNumber())->isEqualTo(2)
		;
	}

	public function testGetFailAssertions()
	{
		$this
			->if($score = new atoum\score())
			->then
				->array($score->getFailAssertions())->isEmpty()
			->if($score->addPass())
			->then
				->array($score->getFailAssertions())->isEmpty()
			->if($score->addFail($file = uniqid(), $line = rand(1, PHP_INT_MAX), $class = uniqid(), $method = uniqid(), $asserter = new atoum\asserters\integer(new atoum\asserter\generator()), $reason = uniqid()))
			->then
				->array($score->getFailAssertions())->isEqualTo(array(
						array(
							'case' => null,
							'dataSetKey' => null,
							'dataSetProvider' => null,
							'class' => $class,
							'method' => $method,
							'file' => $file,
							'line' => $line,
							'asserter' => $asserter,
							'fail' => $reason
						)
					)
				)
		;
	}

	public function testGetPassAssertions()
	{
		$this
			->if($score = new atoum\score())
			->then
				->integer($score->getPassNumber())->isZero()
			->if($score->addFail(uniqid(), rand(1, PHP_INT_MAX), uniqid(), uniqid(), new atoum\asserters\integer(new atoum\asserter\generator()), uniqid()))
			->then
				->integer($score->getPassNumber())->isZero()
			->if($score->addPass())
			->then
				->integer($score->getPassNumber())->isEqualTo(1)
		;
	}

	public function testGetCoverage()
	{
		$this
			->if($score = new atoum\score())
			->then
				->object($coverage = $score->getCoverage())->isInstanceOf('mageekguy\atoum\score\coverage')
		;
	}

	public function testGetMethodsWithFail()
	{
		$this
			->if($score = new atoum\score())
			->then
				->array($score->getMethodsWithFail())->isEmpty()
			->if($asserter = new atoum\asserters\integer(new atoum\asserter\generator()))
			->and($score->addFail(uniqid(), rand(1, PHP_INT_MAX), $class = uniqid(), $classMethod = uniqid(), $asserter, uniqid()))
			->then
				->array($score->getMethodsWithFail())->isEqualTo(array($class => array($classMethod)))
			->if($score->addFail(uniqid(), rand(1, PHP_INT_MAX), $class, $classOtherMethod = uniqid(), $asserter, uniqid()))
			->then
				->array($score->getMethodsWithFail())->isEqualTo(array($class => array($classMethod, $classOtherMethod)))
			->if($score->addFail(uniqid(), rand(1, PHP_INT_MAX), $otherClass = uniqid(), $otherClassMethod = uniqid(), $asserter, uniqid()))
			->then
				->array($score->getMethodsWithFail())->isEqualTo(array(
						$class => array($classMethod, $classOtherMethod),
						$otherClass => array($otherClassMethod)
					)
				)
		;
	}

	public function testGetMethodsWithError()
	{
		$this
			->if($score = new atoum\score())
			->then
				->array($score->getMethodsWithError())->isEmpty()
			->if($score->addError(uniqid(), rand(1, PHP_INT_MAX), $class = uniqid(), $classMethod = uniqid(), rand(1, PHP_INT_MAX), uniqid(), uniqid(), rand(1, PHP_INT_MAX)))
			->then
				->array($score->getMethodsWithError())->isEqualTo(array($class => array($classMethod)))
			->if($score->addError(uniqid(), rand(1, PHP_INT_MAX), $class, $classOtherMethod = uniqid(), rand(1, PHP_INT_MAX), uniqid(), uniqid(), rand(1, PHP_INT_MAX)))
			->then
				->array($score->getMethodsWithError())->isEqualTo(array($class => array($classMethod, $classOtherMethod)))
			->if($score->addError(uniqid(), rand(1, PHP_INT_MAX), $otherClass = uniqid(), $otherClassMethod = uniqid(), rand(1, PHP_INT_MAX), uniqid(), uniqid(), rand(1, PHP_INT_MAX)))
			->then
				->array($score->getMethodsWithError())->isEqualTo(array(
						$class => array($classMethod, $classOtherMethod),
						$otherClass => array($otherClassMethod)
					)
				)
		;
	}

	public function testGetMethodsWithException()
	{
		$this
			->if($score = new atoum\score())
			->then
				->array($score->getMethodsWithError())->isEmpty()
			->if($score->addException(uniqid(), rand(1, PHP_INT_MAX), $class = uniqid(), $classMethod = uniqid(), new \exception()))
			->then
				->array($score->getMethodsWithException())->isEqualTo(array($class => array($classMethod)))
			->if($score->addException(uniqid(), rand(1, PHP_INT_MAX), $class, $classOtherMethod = uniqid(), new \exception()))
			->then
				->array($score->getMethodsWithException())->isEqualTo(array($class => array($classMethod, $classOtherMethod)))
			->if($score->addException(uniqid(), rand(1, PHP_INT_MAX), $otherClass = uniqid(), $otherClassMethod = uniqid(), new \exception()))
			->then
				->array($score->getMethodsWithException())->isEqualTo(array(
						$class => array($classMethod, $classOtherMethod),
						$otherClass => array($otherClassMethod)
					)
				)
		;
	}

	public function testReset()
	{
		$this
			->if($score = new atoum\score())
			->then
				->integer($score->getPassNumber())->isZero()
				->array($score->getFailAssertions())->isEmpty()
				->array($score->getExceptions())->isEmpty()
				->array($score->getRuntimeExceptions())->isEmpty()
				->array($score->getErrors())->isEmpty()
				->array($score->getOutputs())->isEmpty()
				->array($score->getDurations())->isEmpty()
				->array($score->getMemoryUsages())->isEmpty()
				->array($score->getUncompletedMethods())->isEmpty()
				->object($score->reset())->isIdenticalTo($score)
				->integer($score->getPassNumber())->isZero()
				->array($score->getFailAssertions())->isEmpty()
				->array($score->getExceptions())->isEmpty()
				->array($score->getRuntimeExceptions())->isEmpty()
				->array($score->getErrors())->isEmpty()
				->array($score->getOutputs())->isEmpty()
				->array($score->getDurations())->isEmpty()
				->array($score->getMemoryUsages())->isEmpty()
				->array($score->getUncompletedMethods())->isEmpty()
			->if($score
				->addPass()
				->addException(uniqid(), rand(1, PHP_INT_MAX), uniqid(), uniqid(), new \exception())
				->addRuntimeException(new atoum\exceptions\runtime())
				->addError(uniqid(), rand(1, PHP_INT_MAX), uniqid(), uniqid(), E_ERROR, uniqid(), uniqid(), rand(1, PHP_INT_MAX))
				->addOutput(uniqid(), uniqid(), uniqid())
				->addDuration(uniqid(), uniqid(), uniqid(), rand(1, PHP_INT_MAX))
				->addMemoryUsage(uniqid(), uniqid(), rand(1, PHP_INT_MAX))
				->addUncompletedMethod(uniqid(), uniqid(), rand(1, PHP_INT_MAX), uniqid())
			)
			->and($score->addFail(uniqid(), rand(1, PHP_INT_MAX), uniqid(), uniqid(), new atoum\asserters\integer(new atoum\asserter\generator()), uniqid()))
			->then
				->integer($score->getPassNumber())->isGreaterThan(0)
				->array($score->getFailAssertions())->isNotEmpty()
				->array($score->getExceptions())->isNotEmpty()
				->array($score->getRuntimeExceptions())->isNotEmpty()
				->array($score->getErrors())->isNotEmpty()
				->array($score->getOutputs())->isNotEmpty()
				->array($score->getDurations())->isNotEmpty()
				->array($score->getMemoryUsages())->isNotEmpty()
				->array($score->getUncompletedMethods())->isNotEmpty()
				->object($score->reset())->isIdenticalTo($score)
				->integer($score->getPassNumber())->isZero()
				->array($score->getFailAssertions())->isEmpty()
				->array($score->getExceptions())->isEmpty()
				->array($score->getRuntimeExceptions())->isEmpty()
				->array($score->getErrors())->isEmpty()
				->array($score->getOutputs())->isEmpty()
				->array($score->getDurations())->isEmpty()
				->array($score->getMemoryUsages())->isEmpty()
				->array($score->getUncompletedMethods())->isEmpty()
		;
	}

	public function testMerge()
	{
		$this
			->if($score = new atoum\score())
			->and($otherScore = new atoum\score())
			->then
				->object($score->merge($otherScore))->isIdenticalTo($score)
				->integer($score->getPassNumber())->isZero()
				->array($score->getFailAssertions())->isEmpty()
				->array($score->getExceptions())->isEmpty()
				->array($score->getRuntimeExceptions())->isEmpty()
				->array($score->getErrors())->isEmpty()
				->array($score->getOutputs())->isEmpty()
				->array($score->getDurations())->isEmpty()
				->array($score->getMemoryUsages())->isEmpty()
				->array($score->getUncompletedMethods())->isEmpty()
			->if($score->addPass())
			->and($score->addFail(uniqid(), rand(1, PHP_INT_MAX), uniqid(), uniqid(), new atoum\asserters\integer(new atoum\asserter\generator()), uniqid()))
			->and($score->addException(uniqid(), rand(1, PHP_INT_MAX), uniqid(), uniqid(), new \exception()))
			->and($score->addRuntimeException(new atoum\exceptions\runtime()))
			->and($score->addError(uniqid(), rand(1, PHP_INT_MAX), uniqid(), uniqid(), E_ERROR, uniqid(), uniqid(), rand(1, PHP_INT_MAX)))
			->and($score->addOutput(uniqid(), uniqid(), uniqid()))
			->and($score->addDuration(uniqid(), uniqid(), uniqid(), rand(1, PHP_INT_MAX)))
			->and($score->addMemoryUsage(uniqid(), uniqid(), rand(1, PHP_INT_MAX)))
			->and($score->addUncompletedMethod(uniqid(), uniqid(), rand(1, PHP_INT_MAX), uniqid()))
			->then
				->object($score->merge($otherScore))->isIdenticalTo($score)
				->integer($score->getPassNumber())->isEqualTo(1)
				->integer($score->getFailNumber())->isEqualTo(1)
				->integer($score->getExceptionNumber())->isEqualTo(1)
				->integer($score->getErrorNumber())->isEqualTo(1)
				->integer($score->getOutputNumber())->isEqualTo(1)
				->integer($score->getDurationNumber())->isEqualTo(1)
				->integer($score->getMemoryUsageNumber())->isEqualTo(1)
				->integer($score->getUncompletedMethodNumber())->isEqualTo(1)
			->if($otherScore->addPass())
			->and($otherScore->addFail(uniqid(), rand(1, PHP_INT_MAX), uniqid(), uniqid(), new atoum\asserters\integer(new atoum\asserter\generator()), uniqid()))
			->and($otherScore->addException(uniqid(), rand(1, PHP_INT_MAX), uniqid(), uniqid(), new \exception()))
			->and($otherScore->addRuntimeException(new atoum\exceptions\runtime()))
			->and($otherScore->addError(uniqid(), rand(1, PHP_INT_MAX), uniqid(), uniqid(), E_ERROR, uniqid(), uniqid(), rand(1, PHP_INT_MAX)))
			->and($otherScore->addOutput(uniqid(), uniqid(), uniqid()))
			->and($otherScore->addDuration(uniqid(), uniqid(), uniqid(), rand(1, PHP_INT_MAX)))
			->and($otherScore->addMemoryUsage(uniqid(), uniqid(), rand(1, PHP_INT_MAX)))
			->and($otherScore->addUncompletedMethod(uniqid(), uniqid(), rand(1, PHP_INT_MAX), uniqid()))
			->then
				->object($score->merge($otherScore))->isIdenticalTo($score)
				->integer($score->getPassNumber())->isEqualTo(2)
				->integer($score->getFailNumber())->isEqualTo(2)
				->integer($score->getExceptionNumber())->isEqualTo(2)
				->integer($score->getRuntimeExceptionNumber())->isEqualTo(2)
				->integer($score->getErrorNumber())->isEqualTo(2)
				->integer($score->getOutputNumber())->isEqualTo(2)
				->integer($score->getDurationNumber())->isEqualTo(2)
				->integer($score->getMemoryUsageNumber())->isEqualTo(2)
				->integer($score->getUncompletedMethodNumber())->isEqualTo(2)
				->object($score->merge($otherScore))->isIdenticalTo($score)
				->integer($score->getPassNumber())->isEqualTo(3)
				->integer($score->getFailNumber())->isEqualTo(3)
				->integer($score->getExceptionNumber())->isEqualTo(3)
				->integer($score->getRuntimeExceptionNumber())->isEqualTo(3)
				->integer($score->getErrorNumber())->isEqualTo(3)
				->integer($score->getOutputNumber())->isEqualTo(3)
				->integer($score->getDurationNumber())->isEqualTo(3)
				->integer($score->getMemoryUsageNumber())->isEqualTo(3)
				->integer($score->getUncompletedMethodNumber())->isEqualTo(3)
		;
	}

	public function testErrorExists()
	{
		$this
			->if($score = new atoum\score())
			->then
				->variable($score->errorExists(uniqid()))->isNull()
				->variable($score->errorExists(uniqid(), rand(1, PHP_INT_MAX)))->isNull()
				->variable($score->errorExists(uniqid(), rand(1, PHP_INT_MAX), rand(1, PHP_INT_MAX)))->isNull()
			->if($score->addError(uniqid(), rand(1, PHP_INT_MAX), uniqid(), uniqid(), $type = rand(1, PHP_INT_MAX - 1), $message = uniqid(), uniqid(), rand(1, PHP_INT_MAX)))
			->then
				->variable($score->errorExists(uniqid()))->isNull()
				->variable($score->errorExists(uniqid(), rand(1, PHP_INT_MAX)))->isNull()
				->integer($score->errorExists($message))->isEqualTo(0)
				->integer($score->errorExists(null, $type))->isEqualTo(0)
				->integer($score->errorExists($message, $type))->isEqualTo(0)
			->if($score->addError(uniqid(), rand(1, PHP_INT_MAX), uniqid(), uniqid(), $type, $otherMessage = uniqid(), uniqid(), rand(1, PHP_INT_MAX)))
			->then
				->variable($score->errorExists(uniqid()))->isNull()
				->variable($score->errorExists(uniqid(), rand(1, PHP_INT_MAX)))->isNull()
				->integer($score->errorExists($message))->isEqualTo(0)
				->integer($score->errorExists(null, $type))->isEqualTo(0)
				->integer($score->errorExists($message, $type))->isEqualTo(0)
				->integer($score->errorExists($otherMessage, $type))->isEqualTo(1)
			->if($score->addError(uniqid(), rand(1, PHP_INT_MAX), uniqid(), uniqid(), $otherType = $type + 1, $message, uniqid(), rand(1, PHP_INT_MAX)))
			->then
				->variable($score->errorExists(uniqid()))->isNull()
				->variable($score->errorExists(uniqid(), rand(1, PHP_INT_MAX)))->isNull()
				->integer($score->errorExists($message))->isEqualTo(0)
				->integer($score->errorExists(null, $type))->isEqualTo(0)
				->integer($score->errorExists($message, $type))->isEqualTo(0)
				->integer($score->errorExists($otherMessage, $type))->isEqualTo(1)
				->variable($score->errorExists($otherMessage, $otherType))->isNull()
				->integer($score->errorExists($message, $otherType))->isEqualTo(2)
				->integer($score->errorExists(null, $otherType))->isEqualTo(2)
			->if($score->addError(uniqid(), rand(1, PHP_INT_MAX), uniqid(), uniqid(), $otherType, $otherMessage, uniqid(), rand(1, PHP_INT_MAX)))
			->then
				->variable($score->errorExists(uniqid()))->isNull()
				->variable($score->errorExists(uniqid(), rand(1, PHP_INT_MAX)))->isNull()
				->integer($score->errorExists($message))->isEqualTo(0)
				->integer($score->errorExists(null, $type))->isEqualTo(0)
				->integer($score->errorExists($message, $type))->isEqualTo(0)
				->integer($score->errorExists($otherMessage, $type))->isEqualTo(1)
				->integer($score->errorExists($otherMessage, $otherType))->isEqualTo(3)
				->integer($score->errorExists($message, $otherType))->isEqualTo(2)
				->integer($score->errorExists(null, $otherType))->isEqualTo(2)
			->if($score->addError(uniqid(), rand(1, PHP_INT_MAX), uniqid(), uniqid(), $otherType, $pattern = uniqid() . 'FOO' . uniqid(), uniqid(), rand(1, PHP_INT_MAX)))
			->then
				->variable($score->errorExists(uniqid()))->isNull()
				->variable($score->errorExists(uniqid(), rand(1, PHP_INT_MAX)))->isNull()
				->integer($score->errorExists($message))->isEqualTo(0)
				->integer($score->errorExists(null, $type))->isEqualTo(0)
				->integer($score->errorExists($message, $type))->isEqualTo(0)
				->integer($score->errorExists($otherMessage, $type))->isEqualTo(1)
				->integer($score->errorExists($otherMessage, $otherType))->isEqualTo(3)
				->integer($score->errorExists($message, $otherType))->isEqualTo(2)
				->integer($score->errorExists(null, $otherType))->isEqualTo(2)
				->integer($score->errorExists($pattern, $otherType))->isEqualTo(4)
				->variable($score->errorExists('/FOO/', $otherType))->isNull()
				->integer($score->errorExists('/FOO/', $otherType, true))->isEqualTo(4)
		;
	}

	public function testDeleteError()
	{
		$this
			->if($score = new atoum\score())
			->exception(function() use ($score, & $key) { $score->deleteError($key = rand(- PHP_INT_MAX, PHP_INT_MAX)); })
				->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
				->hasMessage('Error key \'' . $key . '\' does not exist')
			->if($score->addError(uniqid(), rand(1, PHP_INT_MAX), uniqid(), uniqid(), $type = rand(1, PHP_INT_MAX), $message = uniqid(), uniqid(), rand(1, PHP_INT_MAX)))
			->then
				->integer($score->errorExists($message, $type))->isEqualTo(0)
				->object($score->deleteError(0))->isIdenticalTo($score)
				->variable($score->errorExists($message, $type))->isNull()
		;
	}
}
