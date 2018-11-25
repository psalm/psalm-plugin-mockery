<?php

namespace
{
    class Mockery
    {
        /**
         * Static shortcut to \Mockery\Container::mock().
         *
         * @param mixed ...$args
         *
         * @return \Mockery\Mock
         */
        public static function mock(...$args) {}

        /**
         * Static and semantic shortcut for getting a mock from the container
         * and applying the spy's expected behavior into it.
         *
         * @param mixed ...$args
         *
         * @return \Mockery\MockInterface
         */
        public static function spy(...$args) {}

        /**
         * Return instance of ANYOF matcher.
         *
         * @param mixed ...$args
         *
         * @return \Mockery\Matcher\AnyOf
         */
        public static function anyOf(...$args) {}

        /**
         * Return instance of NOTANYOF matcher.
         *
         * @param mixed ...$args
         *
         * @return \Mockery\Matcher\NotAnyOf
         */
        public static function notAnyOf(...$args) {}
    }
}

namespace Mockery
{
    use Mockery\HigherOrderMessage;
    use Mockery\MockInterface;
    use Mockery\ExpectsHigherOrderMessage;
    use Mockery\Exception\BadMethodCallException;

    class Mock implements MockInterface
    {
        /**
         * Set expected method calls
         *
         * @param mixed ...$methodNames one or many methods that are expected to be called in this mock
         *
         * @return \Mockery\ExpectationInterface&static
         */
        public function shouldReceive(...$methodNames) {}

        /**
         * @return static
         */
        public function makePartial() {}

        /**
         * @return static
         */
        public function shouldAllowMockingProtectedMethods() {}
    }

    /**
     * @psalm-override-property-visibility
     * @psalm-override-method-visibility
     */
    interface MockInterface
    {
        /**
         * Set expected method calls
         *
         * @param mixed ...$methodNames one or many methods that are expected to be called in this mock
         *
         * @return \Mockery\ExpectationInterface&static
         */
        public function shouldReceive(...$methodNames);

        /**
         * Set expected method calls
         *
         * @param mixed ...$methodNames one or many methods that are expected to be called in this mock
         *
         * @return \Mockery\ExpectationInterface&static
         */
        public function shouldNotReceive(...$methodNames);

        /**
         * @return static
         */
        public function makePartial();

        /**
         * @return static
         */
        public function shouldAllowMockingProtectedMethods();
    }

    interface ExpectationInterface
    {
        /**
         * @param mixed ...$args
         * @return static
         */
        public function andReturn(...$args);

         /**
         * Set a sequential queue of return values with an array
         *
         * @param array $values
         * @return static
         */
        public function andReturnValues(array $values);

        /**
         * Set Exception class and arguments to that class to be thrown
         *
         * @param string|\Exception $exception
         * @param string $message
         * @param int $code
         * @param \Exception $previous
         * @return static
         */
        public function andThrow($exception, $message = '', $code = 0, \Exception $previous = null);

        /**
         * Set a closure or sequence of closures with which to generate return
         * values. The arguments passed to the expected method are passed to the
         * closures as parameters.
         *
         * @param callable ...$args
         * @return static
         */
        public function andReturnUsing(...$args);

        /**
         * Indicates this expectation should occur zero or more times
         *
         * @return static
         */
        public function zeroOrMoreTimes();

        /**
         * Indicates the number of times this expectation should occur
         *
         * @param int $limit
         * @throws \InvalidArgumentException
         * @return static
         */
        public function times($limit = null);

        /**
         * Indicates that this expectation is never expected to be called
         *
         * @return static
         */
        public function never();

        /**
         * Indicates that this expectation is expected exactly once
         *
         * @return static
         */
        public function once();

        /**
         * Indicates that this expectation is expected exactly twice
         *
         * @return static
         */
        public function twice();

        /**
         * Sets next count validator to the AtLeast instance
         *
         * @return static
         */
        public function atLeast();

        /**
         * Sets next count validator to the AtMost instance
         *
         * @return static
         */
        public function atMost();

        /**
         * Expected argument setter for the expectation
         *
         * @param mixed ...$args
         * @return static
         */
        public function with(...$args);

        /**
         * Expected arguments for the expectation passed as an array or a closure that matches each passed argument on
         * each function call.
         *
         * @param array|\Closure $argsOrClosure
         * @return static
         */
        public function withArgs($argsOrClosure);

        /**
         * Set expectation that any arguments are acceptable
         *
         * @return static
         */
        public function withAnyArgs();

        /**
         * @return static
         */
        public function getMock();

        /**
         * Flag this expectation as calling the original class method with the
         * any provided arguments instead of using a return value queue.
         *
         * @return static
         */
        public function passthru();
    }

    class Expectation implements ExpectationInterface
    {
        /**
         * @param mixed ...$args
         * @return static
         */
        public function andReturn(...$args) {}

        /**
         * Expected argument setter for the expectation
         *
         * @param mixed ...$args
         * @return static
         */
        public function with(...$args)
        {
            return $this->withArgs($args);
        }

        /**
         * Expected arguments for the expectation passed as an array or a closure that matches each passed argument on
         * each function call.
         *
         * @param array|\Closure $argsOrClosure
         * @return static
         */
        public function withArgs($argsOrClosure) {}
    }
}
