<?php

namespace mageekguy\atoum\test\engines;

use
	mageekguy\atoum,
	mageekguy\atoum\test
;

class inline extends test\engine
{
	protected $score = null;

	public function __construct(atoum\dependencies $dependencies = null)
	{
		parent::__construct($dependencies);

		$this->score = $this->dependencies['score']($this->dependencies);
	}

	public function isAsynchronous()
	{
		return false;
	}

	public function run(atoum\test $test)
	{
		$currentTestMethod = $test->getCurrentMethod();

		if ($currentTestMethod !== null)
		{
			$testScore = $test->getScore();

			$test
				->setScore($this->score->reset())
				->runTestMethod($test->getCurrentMethod())
				->setScore($testScore)
			;
		}

		return $this;
	}

	public function getScore()
	{
		return $this->score;
	}
}

?>
