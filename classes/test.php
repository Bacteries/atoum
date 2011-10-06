<?php

namespace mageekguy\atoum;

use
	mageekguy\atoum,
	mageekguy\atoum\mock,
	mageekguy\atoum\asserter,
	mageekguy\atoum\exceptions
;

abstract class test implements observable, adapter\aggregator, \countable
{
	const testMethodPrefix = 'test';
	const runStart = 'testRunStart';
	const beforeSetUp = 'beforeTestSetUp';
	const afterSetUp = 'afterTestSetUp';
	const beforeTestMethod = 'beforeTestMethod';
	const fail = 'testAssertionFail';
	const error = 'testError';
	const exception = 'testException';
	const success = 'testAssertionSuccess';
	const afterTestMethod = 'afterTestMethod';
	const beforeTearDown = 'beforeTestTearDown';
	const afterTearDown = 'afterTestTearDown';
	const runStop = 'testRunStop';
	const defaultTestsSubNamespace = 'tests\units';

	private $phpPath = null;
	private $path = '';
	private $class = '';
	private $adapter = null;
	private $asserterGenerator = null;
	private $score = null;
	private $observers = array();
	private $tags = null;
	private $ignore = false;
	private $testMethods = array();
	private $runTestMethods = array();
	private $currentMethod = null;
	private $testsSubNamespace = null;
	private $mockGenerator = null;
	private $child = null;
	private $testsToRun = 0;
	private $phpCode = '';
	private $children = array();
	private $maxChildrenNumber = null;
	private $codeCoverage = false;
	private $assertHasCase = false;

	public function __construct(score $score = null, locale $locale = null, adapter $adapter = null)
	{
		$this
			->setScore($score ?: new score())
			->setLocale($locale ?: new locale())
			->setAdapter($adapter ?: new adapter())
			->setSuperglobals(new atoum\superglobals())
			->enableCodeCoverage()
		;

		$class = new \reflectionClass($this);

		$this->class = $class->getName();

		$this->path = $class->getFilename();

		$testedClassName = $this->getTestedClassName();

		if ($testedClassName === null)
		{
			throw new exceptions\runtime('Test class \'' . $this->getClass() . '\' is not in a namespace which contains \'' . $this->getTestsSubNamespace() . '\'');
		}

		if ($this->adapter->class_exists($testedClassName) === false)
		{
			throw new exceptions\runtime('Tested class \'' . $testedClassName . '\' does not exist for test class \'' . $this->getClass() . '\'');
		}

		$this->getAsserterGenerator()
			->setAlias('array', 'phpArray')
			->setAlias('class', 'phpClass')
		;

		foreach (new annotations\extractor($class->getDocComment()) as $annotation => $value)
		{
			switch ($annotation)
			{
				case 'ignore':
					$this->ignore = $value == 'on';
					break;

				case 'tags':
					$this->tags = explode(' ', $value);
					break;
			}
		}

		foreach ($class->getMethods(\ReflectionMethod::IS_PUBLIC) as $publicMethod)
		{
			$methodName = $publicMethod->getName();

			if (strpos($methodName, self::testMethodPrefix) === 0)
			{
				$annotations = array();

				foreach (new annotations\extractor($publicMethod->getDocComment()) as $annotation => $value)
				{
					switch ($annotation)
					{
						case 'ignore':
							$annotations['ignore'] = $value == 'on';
							break;
					}
				}

				$this->testMethods[$methodName] = $annotations;
			}
		}

		$this->runTestMethods = $this->getTestMethods();
	}

	public function __toString()
	{
		return $this->getClass();
	}

	public function __get($property)
	{
		switch ($property)
		{
			case 'define':
				return $this->getAsserterGenerator();

			case 'assert':
				return $this->unsetCaseOnAssert()->getAsserterGenerator();

			case 'mockGenerator':
				return $this->getMockGenerator();

			default:
				throw new exceptions\logic\invalidArgument('Property \'' . $property . '\' is undefined in class \'' . get_class($this) . '\'');
		}
	}

	public function __call($method, $arguments)
	{
		switch ($method)
		{
			case 'mock':
				$this->getMockGenerator()->generate(isset($arguments[0]) === false ? null : $arguments[0], isset($arguments[1]) === false ? null : $arguments[1], isset($arguments[2]) === false ? null : $arguments[2]);
				return $this;

			case 'assert':
				$this->unsetCaseOnAssert();

				$case = isset($arguments[0]) === false ? null : $arguments[0];

				if ($case !== null)
				{
					$this->setCaseOnAssert($case);
				}

				return $this->getAsserterGenerator();

			default:
				throw new exceptions\logic\invalidArgument('Method ' . get_class($this) . '::' . $method . '() is undefined');
		}
	}

	public function codeCoverageIsEnabled()
	{
		return $this->codeCoverage;
	}

	public function enableCodeCoverage()
	{
		$this->codeCoverage = $this->adapter->extension_loaded('xdebug');

		return $this;
	}

	public function disableCodeCoverage()
	{
		$this->codeCoverage = false;

		return $this;
	}

	public function setMaxChildrenNumber($number)
	{
		if ($number < 1)
		{
			throw new exceptions\logic\invalidArgument('Maximum number of children must be greater or equal to 1');
		}

		$this->maxChildrenNumber = $number;

		return $this;
	}

	public function setSuperglobals(atoum\superglobals $superglobals)
	{
		$this->superglobals = $superglobals;

		return $this;
	}

	public function getSuperglobals()
	{
		return $this->superglobals;
	}

	public function setMockGenerator(mock\generator $generator)
	{
		$this->mockGenerator = $generator;

		return $this;
	}

	public function getMockGenerator()
	{
		return $this->mockGenerator ?: $this->setMockGenerator(new mock\generator())->mockGenerator;
	}

	public function setAsserterGenerator(asserter\generator $generator)
	{
		$this->asserterGenerator = $generator->setTest($this);

		return $this;
	}

	public function getAsserterGenerator()
	{
		atoum\test\adapter::resetCallsForAllInstances();

		return $this->asserterGenerator ?: $this->setAsserterGenerator(new asserter\generator($this, $this->locale))->asserterGenerator;
	}

	public function setTestsSubNamespace($testsSubNamespace)
	{
		$this->testsSubNamespace = trim((string) $testsSubNamespace, '\\');

		if ($this->testsSubNamespace === '')
		{
			throw new atoum\exceptions\logic\invalidArgument('Tests sub-namespace must not be empty');
		}

		return $this;
	}

	public function getTestsSubNamespace()
	{
		return ($this->testsSubNamespace === null ? self::defaultTestsSubNamespace : $this->testsSubNamespace);
	}

	public function setPhpPath($path)
	{
		$this->phpPath = (string) $path;

		return $this;
	}

	public function getPhpPath()
	{
		if ($this->phpPath === null)
		{
			if (isset($this->superglobals->_SERVER['_']) === false)
			{
				throw new exceptions\runtime('Unable to find PHP executable');
			}

			$this->setPhpPath($this->superglobals->_SERVER['_']);
		}

		return $this->phpPath;
	}

	public function getTags()
	{
		return $this->tags;
	}

	public function setAdapter(atoum\adapter $adapter)
	{
		$this->adapter = $adapter;

		return $this;
	}

	public function getAdapter()
	{
		return $this->adapter;
	}

	public function setScore(score $score)
	{
		$this->score = $score;

		return $this;
	}

	public function getScore()
	{
		return $this->score;
	}

	public function setLocale(locale $locale)
	{
		$this->locale = $locale;

		return $this;
	}

	public function getLocale()
	{
		return $this->locale;
	}

	public function getTestedClassName()
	{
		$class = null;

		$testClass = $this->getClass();
		$testsSubNamespace = $this->getTestsSubNamespace();

		$position = strpos($testClass, $testsSubNamespace);

		if ($position !== false)
		{
			$class = trim(substr($testClass, 0, $position) . substr($testClass, $position + strlen($testsSubNamespace) + 1), '\\');
		}

		return $class;
	}

	public function getClass()
	{
		return $this->class;
	}

	public function getPath()
	{
		return $this->path;
	}

	public function getTestMethods()
	{
		$testMethods = array();

		foreach ($this->testMethods as $methodName => $annotations)
		{
			if (isset($annotations['ignore']) === true ? $annotations['ignore'] === false : $this->ignore === false)
			{
				$testMethods[] = $methodName;
			}
		}

		return $testMethods;
	}

	public function getCurrentMethod()
	{
		return $this->currentMethod;
	}

	public function count()
	{
		return sizeof($this->runTestMethods);
	}

	public function addObserver(atoum\observers\test $observer)
	{
		$this->observers[] = $observer;

		return $this;
	}

	public function callObservers($method)
	{
		foreach ($this->observers as $observer)
		{
			$observer->{$method}($this);
		}

		return $this;
	}

	public function ignore($boolean)
	{
		$this->ignore = ($boolean == true);

		$this->runTestMethods = $this->getTestMethods();

		return $this;
	}

	public function isIgnored()
	{
		return ($this->ignore === true);
	}

	public function methodIsIgnored($testMethodName)
	{
		if (isset($this->testMethods[$testMethodName]) === false)
		{
			throw new exceptions\logic\invalidArgument('Test method ' . $this->class . '::' . $testMethodName . '() is unknown');
		}

		return (isset($this->testMethods[$testMethodName]['ignore']) === true ? $this->testMethods[$testMethodName]['ignore'] : $this->ignore);
	}

	public function run(array $runTestMethods = array())
	{
		if ($this->ignore === false)
		{
			$this->callObservers(self::runStart);

			if (sizeof($runTestMethods) > 0)
			{
				$unknownTestMethods = array_diff($runTestMethods, $this->getTestMethods());

				if (sizeof($unknownTestMethods) > 0)
				{
					throw new exceptions\logic\invalidArgument('Test method ' . $this->class . '::' . current($unknownTestMethods) . '() is unknown or ignored');
				}

				$this->runTestMethods = $runTestMethods;
			}

			$this->testsToRun = sizeof($this->runTestMethods);

			if ($this->testsToRun > 0)
			{
				$this->phpCode =
					'<?php ' .
					'ob_start();' .
					'define(\'' . __NAMESPACE__ . '\autorun\', false);' .
					'require \'' . $this->path . '\';' .
					'$test = new ' . $this->class . '();' .
					'$test->setLocale(new ' . get_class($this->locale) . '(' . $this->locale->get() . '));' .
					'$test->setPhpPath(\'' . $this->getPhpPath() . '\');' .
					($this->codeCoverageIsEnabled() === true ? '' : '$test->disableCodeCoverage();') .
					'$test->runTestMethod($method = \'%s\');' .
					'echo serialize($test->getScore()->addOutput(\'' . $this->class . '\', $method, ob_get_clean()));' .
					'?>'
				;

				$null = null;

				try
				{
					$this->callObservers(self::beforeSetUp);
					$this->setUp();
					$this->callObservers(self::afterSetUp);

					while (sizeof($this->runChild()->children) > 0)
					{
						$children = $this->children;
						$this->children = array();

						foreach ($children as $testMethod => $child)
						{
							$child[2] .= stream_get_contents($child[1][1]);
							$child[3] .= stream_get_contents($child[1][2]);

							$phpStatus = proc_get_status($child[0]);

							if ($phpStatus['running'] === true)
							{
								$this->children[$testMethod] = $child;
							}
							else
							{
								fclose($child[1][1]);
								fclose($child[1][2]);

								proc_close($child[0]);

								$this->currentMethod = $testMethod;
								$this->callObservers(self::afterTestMethod);
								$this->currentMethod = null;

								switch ($phpStatus['exitcode'])
								{
									case 126:
									case 127:
										throw new exceptions\runtime('Unable to execute test with \'' . $this->getPhpPath() . '\'');
								}

								$score = new score();

								if ($child[2] !== '')
								{
									$score = @unserialize($child[2]);

									if ($score instanceof score === false)
									{
										$score = new score();
									}
								}

								if ($child[3] !== '')
								{
									if (preg_match_all('/([^:]+): (.+) in (.+) on line ([0-9]+)/', trim($child[3]), $errors, PREG_SET_ORDER) === 0)
									{
										$score->addError($this->path, null, $this->class, $testMethod, 'UNKNOWN', $child[3]);
									}
									else foreach ($errors as $error)
									{
										$score->addError($this->path, null, $this->class, $testMethod, $error[1], $error[2], $error[3], $error[4]);
									}
								}

								if ($score->getFailNumber() > 0)
								{
									$this->callObservers(self::fail);
								}

								if ($score->getErrorNumber() > 0)
								{
									$this->callObservers(self::error);
								}

								if ($score->getExceptionNumber() > 0)
								{
									$this->callObservers(self::exception);
								}

								if ($score->getPassNumber() > 0)
								{
									$this->callObservers(self::success);
								}

								$this->score->merge($score);
							}
						}
					}

					$this
						->callObservers(self::beforeTearDown)
						->tearDown()
						->callObservers(self::afterTearDown)
					;
				}
				catch (\exception $exception)
				{
					$this
						->callObservers(self::beforeTearDown)
						->tearDown()
						->callObservers(self::afterTearDown)
					;

					throw $exception;
				}
			}
		}

		$this->callObservers(self::runStop);

		return $this;
	}

	public function errorHandler($errno, $errstr, $errfile, $errline, $context)
	{
		if (error_reporting() !== 0)
		{
			list($file, $line) = $this->getBacktrace();

			$this->score->addError($file, $line, $this->class, $this->currentMethod, $errno, $errstr, $errfile, $errline);
		}

		return true;
	}

	public static function getObserverEvents()
	{
		return array(
			self::runStart,
			self::beforeSetUp,
			self::afterSetUp,
			self::beforeTestMethod,
			self::fail,
			self::error,
			self::exception,
			self::success,
			self::afterTestMethod,
			self::beforeTearDown,
			self::afterTearDown,
			self::runStop
		);
	}

	protected function setUp()
	{
		return $this;
	}

	protected function startCase($case)
	{
		$this->score->setCase($case);

		return $this;
	}

	protected function beforeTestMethod($testMethod)
	{
		return $this;
	}

	public function runTestMethod($testMethod)
	{
		if ($this->methodIsIgnored($testMethod) === false)
		{
			set_error_handler(array($this, 'errorHandler'));

			ini_set('display_errors', 'stderr');
			ini_set('log_errors', 'Off');
			ini_set('log_errors_max_len', '0');

			$this->currentMethod = $testMethod;

			try
			{
				try
				{
					$this->beforeTestMethod($testMethod);

					ob_start();

					if ($this->codeCoverageIsEnabled() === true)
					{
						xdebug_start_code_coverage(XDEBUG_CC_UNUSED | XDEBUG_CC_DEAD_CODE);
					}

					$time = microtime(true);
					$memory = memory_get_usage(true);

					$this->{$testMethod}();

					$memoryUsage = memory_get_usage(true) - $memory;
					$duration = microtime(true) - $time;

					if ($this->codeCoverageIsEnabled() === true)
					{
						$this->score->getCoverage()->addXdebugDataForTest($this, xdebug_get_code_coverage());
						xdebug_stop_code_coverage();
					}

					$this->score
						->addMemoryUsage($this->class, $this->currentMethod, $memoryUsage)
						->addDuration($this->class, $this->currentMethod, $duration)
						->addOutput($this->class, $this->currentMethod, ob_get_clean())
					;

					$this->afterTestMethod($testMethod);
				}
				catch (\exception $exception)
				{
					$this->score->addOutput($this->class, $this->currentMethod, ob_get_clean());
					$this->score->addDuration($this->class, $this->currentMethod, microtime(true) - $time);

					throw $exception;
				}
			}
			catch (asserter\exception $exception)
			{
				if ($this->score->failExists($exception) === false)
				{
					$this->addExceptionToScore($exception);
				}
			}
			catch (\exception $exception)
			{
				$this->addExceptionToScore($exception);
			}

			$this->currentMethod = null;

			restore_error_handler();

			ini_restore('display_errors');
			ini_restore('log_errors');
			ini_restore('log_errors_max_len');
		}

		return $this;
	}

	protected function afterTestMethod($testMethod)
	{
		return $this;
	}

	protected function tearDown()
	{
		return $this;
	}

	protected function addExceptionToScore(\exception $exception)
	{
		list($file, $line) = $this->getBacktrace($exception->getTrace());

		$this->score->addException($file, $line, $this->class, $this->currentMethod, $exception);

		return $this;
	}

	protected function getBacktrace(array $trace = null)
	{
		$debugBacktrace = $trace === null ? debug_backtrace(false) : $trace;

		foreach ($debugBacktrace as $key => $value)
		{
			if (isset($value['class']) === true && isset($value['function']) === true && $value['class'] === $this->class && $value['function'] === $this->currentMethod)
			{
				if (isset($debugBacktrace[$key - 1]) === true)
				{
					$key -= 1;
				}

				return array(
					$debugBacktrace[$key]['file'],
					$debugBacktrace[$key]['line']
				);
			}
		}

		return null;
	}

	protected static function getAnnotations($comments)
	{
		$annotations = array();

		$comments = explode("\n", trim(trim($comments, '/*')));

		foreach ($comments as $comment)
		{
			$comment = preg_split("/\s+/", trim($comment));

			if (sizeof($comment) == 2)
			{
				if (substr($comment[0], 0, 1) == '@')
				{
					$annotations[$comment[0]] = $comment[1];
				}
			}
		}

		return $annotations;
	}

	private function runChild()
	{
		if ($this->canRunChild() === true)
		{
			$php = @proc_open(
				$this->getPhpPath(),
				array(
					0 => array('pipe', 'r'),
					1 => array('pipe', 'w'),
					2 => array('pipe', 'w')
				),
				$pipes
			);

			$currentMethod = array_shift($this->runTestMethods);

			$this->callObservers(self::beforeTestMethod);

			fwrite($pipes[0], sprintf($this->phpCode, $currentMethod));
			fclose($pipes[0]);
			unset($pipes[0]);

			stream_set_blocking($pipes[1], 0);
			stream_set_blocking($pipes[2], 0);

			$this->children[$currentMethod] = array(
				$php,
				$pipes,
				'',
				''
			);

			$this->testsToRun--;
		}

		return $this;
	}

	private function canRunChild()
	{
		return ($this->testsToRun > 0 && ($this->maxChildrenNumber === null || sizeof($this->children) < $this->maxChildrenNumber));
	}

	private function setCaseOnAssert($case)
	{
		$this->startCase($case)->assertHasCase = true;

		return $this;
	}

	private function unsetCaseOnAssert()
	{
		if ($this->assertHasCase === true)
		{
			$this->score->unsetCase();
		}

		return $this;
	}
}

?>
