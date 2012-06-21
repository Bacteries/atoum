<?php

namespace mageekguy\atoum\dependencies;

use
	mageekguy\atoum\exceptions\logic\invalidArgument
;

class injector implements \arrayAccess
{
	protected $closure = null;

	protected $arguments = array();

	public function __construct(\closure $closure)
	{
		$this->closure = $closure;
	}

	public function __invoke()
	{
		return call_user_func_array($this->closure, func_get_args() ?: $this->arguments);
	}

	public function __set($argument, $value)
	{
		return $this->setArgument($argument, $value);
	}

	public function __get($argument)
	{
		return $this->getArgument($argument);
	}

	public function __isset($argument)
	{
		return $this->argumentExists($argument);
	}

	public function __unset($argument)
	{
		return $this->unsetArgument($argument);
	}

	public function offsetSet($argument, $value)
	{
		return $this->setArgument($argument, $value);
	}

	public function offsetGet($argument)
	{
		return $this->getArgument($argument);
	}

	public function offsetExists($argument)
	{
		return $this->argumentExists($argument);
	}

	public function offsetUnset($argument)
	{
		return $this->unsetArgument($argument);
	}

	public function getClosure()
	{
		return $this->closure;
	}

	public function getArguments()
	{
		return $this->arguments;
	}

	public function getArgument($name)
	{
		if ($this->argumentExists($name) === false)
		{
			throw new invalidArgument('Argument \'' . $name . '\' is undefined');
		}

		return $this->arguments[$name];
	}

	public function setArgument($name, $value)
	{
		$this->arguments[$name] = $value;

		return $this;
	}

	public function argumentExists($name)
	{
		return (isset($this->arguments[$name]) === true);
	}

	public function unsetArgument($name)
	{
		if ($this->argumentExists($name) === false)
		{
			throw new invalidArgument('Argument \'' . $name . '\' is undefined');
		}

		unset($this->arguments[$name]);

		return $this;
	}
}