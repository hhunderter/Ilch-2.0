<?php
/**
 * @copyright Ilch 2.0
 */

namespace Ilch\Validation\Validators;

/**
 * Max validation class.
 */
class Max extends Base
{
    /**
     * Default error key for this validator.
     *
     * @var string
     */
    protected $errorKey = 'validation.errors.max.numeric';

    /**
     * Minimum parameter count needed.
     *
     * @var int
     */
    protected $minParams = 1;

    /**
     * Runs the validation.
     *
     * @return self
     */
    public function run()
    {
        $numberString = $this->getParameter(1) === 'string';

        $this->setIsValid(
            $this->getValue() === '' ||
            $this->getSize($this->getValue(), $numberString) <= (int) $this->getParameter(0)
        );
        $this->setErrorParameters([$this->getParameter(0)]);

        return $this;
    }

    /**
     * Gets the size.
     *
     * @param string|int|array $value        The value to check
     * @param bool             $numberString Is it a number string?
     *
     * @return int The size of the value
     */
    protected function getSize($value, $numberString)
    {
        if (is_numeric($value) && !$numberString) {
            return (int) $value;
        }

        if (is_array($value)) {
            $this->setErrorKey('validation.errors.max.array');
            return count($value);
        }

        $this->setErrorKey('validation.errors.max.string');
        return mb_strlen($value);
    }
}
