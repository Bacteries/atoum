<?php

namespace mageekguy\atoum\tests\units\scripts;

use
	mageekguy\atoum,
	mageekguy\atoum\mock,
	mageekguy\atoum\scripts,
	mageekguy\atoum\scripts\builder\vcs
;

require_once __DIR__ . '/../../runner.php';

class runner extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->isSubclassOf('mageekguy\atoum\script');
	}

	public function testClassConstants()
	{
		$this->string(scripts\runner::defaultConfigFile)->isEqualTo('.atoum.php');
	}

	public function test__construct()
	{
		$this
			->if($scriptRunner = new scripts\runner($name = uniqid()))
			->then
				->string($scriptRunner->getName())->isEqualTo($name)
				->object($scriptRunner->getLocale())->isInstanceOf('mageekguy\atoum\locale')
				->object($scriptRunner->getAdapter())->isInstanceOf('mageekguy\atoum\adapter')
				->object($scriptRunner->getIncluder())->isInstanceOf('mageekguy\atoum\includer')
				->object($scriptRunner->getRunner())->isInstanceOf('mageekguy\atoum\runner')
				->object($scriptRunner->getRunner()->getLocale())->isIdenticalTo($scriptRunner->getLocale())
				->object($scriptRunner->getRunner()->getAdapter())->isIdenticalTo($scriptRunner->getAdapter())
				->object($scriptRunner->getRunner()->getIncluder())->isIdenticalTo($scriptRunner->getIncluder())
				->variable($scriptRunner->getScoreFile())->isNull()
				->array($scriptRunner->getArguments())->isEmpty()
				->array($scriptRunner->getHelp())->isEqualTo(array(
						array(
							array('-h', '--help'),
							null,
							'Display this help'
						),
						array(
							array('-v', '--version'),
							null,
							'Display version'
						),
						array(
							array('-p', '--php'),
							'<path/to/php/binary>',
							'Path to PHP binary which must be used to run tests'
						),
						array(
							array('-drt', '--default-report-title'),
							'<string>',
							'Define default report title with <string>'
						),
						array(
							array('-c', '--configuration-files'),
							'<file>...',
							'Use all configuration files <file>'
						),
						array(
							array('-sf', '--score-file'),
							'<file>',
							'Save score in file <file>'
						),
						array(
							array('-mcn', '--max-children-number'),
							'<integer>',
							'Maximum number of sub-processus which will be run simultaneously'
						),
						array(
							array('-ncc', '--no-code-coverage'),
							null,
							'Disable code coverage'
						),
						array(
							array('-nccid', '--no-code-coverage-in-directories'),
							'<directory>...',
							'Disable code coverage in directories <directory>'
						),
						array(
							array('-nccfns', '--no-code-coverage-for-namespaces'),
							'<namespace>...',
							'Disable code coverage for namespaces <namespace>'
						),
						array(
							array('-nccfc', '--no-code-coverage-for-classes'),
							'<class>...',
							'Disable code coverage for classes <class>'
						),
						array(
							array('-f', '--test-files'),
							'<file>...',
							'Execute all unit test files <file>'
						),
						array(
							array('-d', '--directories'),
							'<directory>...',
							'Execute unit test files in all <directory>'
						),
						array(
							array('-tfe', '--test-file-extensions'),
							'<extension>...',
							'Execute unit test files with one of extensions <extension>'
						),
						array(
							array('-g', '--glob'),
							'<pattern>...',
							'Execute unit test files which match <pattern>'
						),
						array(
							array('-t', '--tags'),
							'<tag>...',
							'Execute only unit test with tags <tag>'
						),
						array(
							array('-m', '--methods'),
							'<class::method>...',
							'Execute all <class::method>, * may be used as wildcard for class name or method name'
						),
						array(
							array('-ns', '--namespaces'),
							'<namespace>...',
							'Execute all classes in all namespaces <namespace>'
						),
						array(
							array('-l', '--loop'),
							null,
							'Execute tests in an infinite loop'
						),
						array(
							array('--test-it'),
							null,
							'Execute atoum unit tests'
						),
						array(
							array('-ft', '--force-terminal'),
							null,
							'Force output as in terminal'
						),
						array(
							array('-bf', '--bootstrap-file'),
							'<file>',
							'Include <file> before executing each test method'
						),
						array(
							array('-ulr', '--use-light-report'),
							null,
							'Use "light" CLI report'
						)
					)
				)
			->if($dependencies = new atoum\dependencies())
			->and($dependencies['mageekguy\atoum\scripts\runner']['locale'] = $locale = new atoum\locale())
			->and($dependencies['mageekguy\atoum\scripts\runner']['adapter'] = $adapter = new atoum\adapter())
			->and($dependencies['mageekguy\atoum\scripts\runner']['runner'] = $runner = new atoum\runner())
			->and($dependencies['mageekguy\atoum\scripts\runner']['includer'] = $includer = new atoum\includer())
			->and($scriptRunner = new scripts\runner($name = uniqid(), $dependencies))
			->then
				->string($scriptRunner->getName())->isEqualTo($name)
				->object($scriptRunner->getAdapter())->isIdenticalTo($adapter)
				->object($scriptRunner->getLocale())->isIdenticalTo($locale)
				->object($scriptRunner->getRunner())->isIdenticalTo($runner)
				->object($scriptRunner->getIncluder())->isIdenticalTo($includer)
				->variable($scriptRunner->getScoreFile())->isNull()
				->array($scriptRunner->getArguments())->isEmpty()
				->array($scriptRunner->getHelp())->isEqualTo(array(
						array(
							array('-h', '--help'),
							null,
							'Display this help'
						),
						array(
							array('-v', '--version'),
							null,
							'Display version'
						),
						array(
							array('-p', '--php'),
							'<path/to/php/binary>',
							'Path to PHP binary which must be used to run tests'
						),
						array(
							array('-drt', '--default-report-title'),
							'<string>',
							'Define default report title with <string>'
						),
						array(
							array('-c', '--configuration-files'),
							'<file>...',
							'Use all configuration files <file>'
						),
						array(
							array('-sf', '--score-file'),
							'<file>',
							'Save score in file <file>'
						),
						array(
							array('-mcn', '--max-children-number'),
							'<integer>',
							'Maximum number of sub-processus which will be run simultaneously'
						),
						array(
							array('-ncc', '--no-code-coverage'),
							null,
							'Disable code coverage'
						),
						array(
							array('-nccid', '--no-code-coverage-in-directories'),
							'<directory>...',
							'Disable code coverage in directories <directory>'
						),
						array(
							array('-nccfns', '--no-code-coverage-for-namespaces'),
							'<namespace>...',
							'Disable code coverage for namespaces <namespace>'
						),
						array(
							array('-nccfc', '--no-code-coverage-for-classes'),
							'<class>...',
							'Disable code coverage for classes <class>'
						),
						array(
							array('-f', '--test-files'),
							'<file>...',
							'Execute all unit test files <file>'
						),
						array(
							array('-d', '--directories'),
							'<directory>...',
							'Execute unit test files in all <directory>'
						),
						array(
							array('-tfe', '--test-file-extensions'),
							'<extension>...',
							'Execute unit test files with one of extensions <extension>'
						),
						array(
							array('-g', '--glob'),
							'<pattern>...',
							'Execute unit test files which match <pattern>'
						),
						array(
							array('-t', '--tags'),
							'<tag>...',
							'Execute only unit test with tags <tag>'
						),
						array(
							array('-m', '--methods'),
							'<class::method>...',
							'Execute all <class::method>, * may be used as wildcard for class name or method name'
						),
						array(
							array('-ns', '--namespaces'),
							'<namespace>...',
							'Execute all classes in all namespaces <namespace>'
						),
						array(
							array('-l', '--loop'),
							null,
							'Execute tests in an infinite loop'
						),
						array(
							array('--test-it'),
							null,
							'Execute atoum unit tests'
						),
						array(
							array('-ft', '--force-terminal'),
							null,
							'Force output as in terminal'
						),
						array(
							array('-bf', '--bootstrap-file'),
							'<file>',
							'Include <file> before executing each test method'
						),
						array(
							array('-ulr', '--use-light-report'),
							null,
							'Use "light" CLI report'
						)
					)
				)
		;
	}

	public function testSetDepedencies()
	{
		$this
			->if($scriptRunner = new scripts\runner($name = uniqid()))
			->then
				->object($scriptRunner->setDepedencies($dependencies = new atoum\dependencies()))->isIdenticalTo($scriptRunner)
				->boolean(isset($dependencies['mageekguy\atoum\scripts\runner']['cli']))->isTrue()
				->boolean(isset($dependencies['mageekguy\atoum\scripts\runner']['locale']))->isTrue()
				->boolean(isset($dependencies['mageekguy\atoum\scripts\runner']['adapter']))->isTrue()
				->boolean(isset($dependencies['mageekguy\atoum\scripts\runner']['includer']))->isTrue()
				->boolean(isset($dependencies['mageekguy\atoum\scripts\runner']['runner']))->isTrue()
				->boolean(isset($dependencies['mageekguy\atoum\scripts\runner']['reports\default']))->isTrue()
				->boolean(isset($dependencies['mageekguy\atoum\scripts\runner']['reports\light']))->isTrue()
				->boolean(isset($dependencies['mageekguy\atoum\scripts\runner']['configurator']))->isTrue()
				->boolean(isset($dependencies['mageekguy\atoum\scripts\runner']['reflection\class']))->isTrue()
				->boolean(isset($dependencies['mageekguy\atoum\scripts\runner']['mageekguy\atoum\runner']['locale']))->isTrue()
				->boolean(isset($dependencies['mageekguy\atoum\scripts\runner']['mageekguy\atoum\runner']['adapter']))->isTrue()
				->boolean(isset($dependencies['mageekguy\atoum\scripts\runner']['mageekguy\atoum\runner']['includer']))->isTrue()
			->if($dependencies = new atoum\dependencies())
			->and($dependencies['mageekguy\atoum\scripts\runner']['cli'] = $cliInjector = function() {})
			->and($dependencies['mageekguy\atoum\scripts\runner']['locale'] = $localeInjector = function() {})
			->and($dependencies['mageekguy\atoum\scripts\runner']['adapter'] = $adapterInjector = function() {})
			->and($dependencies['mageekguy\atoum\scripts\runner']['includer'] = $includerInjector = function() {})
			->and($dependencies['mageekguy\atoum\scripts\runner']['runner'] = $runnerInjector = function() {})
			->and($dependencies['mageekguy\atoum\scripts\runner']['reports\default'] = $defaultReportInjector = function() {})
			->and($dependencies['mageekguy\atoum\scripts\runner']['reports\light'] = $lightReportInjector = function() {})
			->and($dependencies['mageekguy\atoum\scripts\runner']['configurator'] = $configuratorInjector = function() {})
			->and($dependencies['mageekguy\atoum\scripts\runner']['reflection\class'] = $reflectionClassInjector = function() {})
			->and($dependencies['mageekguy\atoum\scripts\runner']['mageekguy\atoum\runner']['locale'] = $localeRunnerInjector = function() {})
			->and($dependencies['mageekguy\atoum\scripts\runner']['mageekguy\atoum\runner']['adapter'] = $adapterRunnerInjector = function() {})
			->and($dependencies['mageekguy\atoum\scripts\runner']['mageekguy\atoum\runner']['includer'] = $includerRunnerInjector = function() {})
			->then
				->object($scriptRunner->setDepedencies($dependencies))->isIdenticalTo($scriptRunner)
				->object($dependencies['mageekguy\atoum\scripts\runner']['cli'])->isIdenticalTo($cliInjector)
				->object($dependencies['mageekguy\atoum\scripts\runner']['locale'])->isIdenticalTo($localeInjector)
				->object($dependencies['mageekguy\atoum\scripts\runner']['adapter'])->isIdenticalTo($adapterInjector)
				->object($dependencies['mageekguy\atoum\scripts\runner']['includer'])->isIdenticalTo($includerInjector)
				->object($dependencies['mageekguy\atoum\scripts\runner']['runner'])->isIdenticalTo($runnerInjector)
				->object($dependencies['mageekguy\atoum\scripts\runner']['reports\default'])->isIdenticalTo($defaultReportInjector)
				->object($dependencies['mageekguy\atoum\scripts\runner']['reports\light'])->isIdenticalTo($lightReportInjector)
				->object($dependencies['mageekguy\atoum\scripts\runner']['configurator'])->isIdenticalTo($configuratorInjector)
				->object($dependencies['mageekguy\atoum\scripts\runner']['reflection\class'])->isIdenticalTo($reflectionClassInjector)
				->object($dependencies['mageekguy\atoum\scripts\runner']['mageekguy\atoum\runner']['locale'])->isIdenticalTo($localeRunnerInjector)
				->object($dependencies['mageekguy\atoum\scripts\runner']['mageekguy\atoum\runner']['adapter'])->isIdenticalTo($adapterRunnerInjector)
				->object($dependencies['mageekguy\atoum\scripts\runner']['mageekguy\atoum\runner']['includer'])->isIdenticalTo($includerRunnerInjector)
		;
	}

	public function testSetArguments()
	{
		$this
			->if($runner = new scripts\runner($name = uniqid()))
			->then
				->object($runner->setArguments(array()))->isIdenticalTo($runner)
				->array($runner->getArguments())->isEmpty()
				->object($runner->setArguments($arguments = array(uniqid(), uniqid(), uniqid())))->isIdenticalTo($runner)
				->array($runner->getArguments())->isEqualTo($arguments)
		;
	}

	public function testUseConfigFile()
	{
		$this
			->if($dependencies = new atoum\dependencies())
			->and($dependencies['mageekguy\atoum\scripts\runner']['locale'] = $locale = new \mock\mageekguy\atoum\locale())
			->and($runner = new scripts\runner($name = uniqid(), $dependencies))
			->then
				->exception(function() use ($runner, & $file) {
						$runner->useConfigFile($file = uniqid());
					}
				)
					->isInstanceOf('mageekguy\atoum\includer\exception')
					->hasMessage('Unable to find configuration file \'' . $file . '\'')
				->mock($locale)->call('_')->withArguments('Unable to find configuration file \'%s\'')->once()
			->if($streamController = atoum\mock\stream::get('includeWithoutOutput'))
			->and($streamController->file_get_contents = '<?php $runner->disableCodeCoverage(); ?>')
			->then
				->boolean($runner->getRunner()->codeCoverageIsEnabled())->isTrue()
				->object($runner->useConfigFile('atoum://includeWithoutOutput'))->isIdenticalTo($runner)
				->boolean($runner->getRunner()->codeCoverageIsEnabled())->isFalse()
		;
	}

	public function testUseDefaultConfigFiles()
	{
		$this
			->if($runner = new \mock\mageekguy\atoum\scripts\runner($name = uniqid()))
			->and($runner->getMockController()->useConfigFile = function() {})
			->then
				->object($runner->useDefaultConfigFiles())->isIdenticalTo($runner)
				->mock($runner)
					->foreach(scripts\runner::getSubDirectoryPath(atoum\directory), function($mock, $path) {
							$mock->call('useConfigFile')->withArguments($path . scripts\runner::defaultConfigFile)->once();
						}
					)
		;
	}

	public function testGetSubDirectoryPath()
	{
		$this
			->array(scripts\runner::getSubDirectoryPath(''))->isEmpty()
			->array(scripts\runner::getSubDirectoryPath('', '/'))->isEmpty()
			->array(scripts\runner::getSubDirectoryPath('', '\\'))->isEmpty()
			->array(scripts\runner::getSubDirectoryPath('/', '/'))->isEqualTo(array('/'))
			->array(scripts\runner::getSubDirectoryPath('/toto', '/'))->isEqualTo(array('/', '/toto/'))
			->array(scripts\runner::getSubDirectoryPath('/toto/', '/'))->isEqualTo(array('/', '/toto/'))
			->array(scripts\runner::getSubDirectoryPath('/toto/tutu', '/'))->isEqualTo(array('/', '/toto/', '/toto/tutu/'))
			->array(scripts\runner::getSubDirectoryPath('/toto/tutu/', '/'))->isEqualTo(array('/', '/toto/', '/toto/tutu/'))
			->array(scripts\runner::getSubDirectoryPath('c:\\', '\\'))->isEqualTo(array('c:\\'))
			->array(scripts\runner::getSubDirectoryPath('c:\toto', '\\'))->isEqualTo(array('c:\\', 'c:\toto\\'))
			->array(scripts\runner::getSubDirectoryPath('c:\toto\\', '\\'))->isEqualTo(array('c:\\', 'c:\toto\\'))
			->array(scripts\runner::getSubDirectoryPath('c:\toto\tutu', '\\'))->isEqualTo(array('c:\\', 'c:\toto\\', 'c:\toto\tutu\\'))
			->array(scripts\runner::getSubDirectoryPath('c:\toto\tutu\\', '\\'))->isEqualTo(array('c:\\', 'c:\toto\\', 'c:\toto\tutu\\'))
		;
	}
}

?>
