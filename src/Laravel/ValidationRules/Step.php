<?php

declare(strict_types=1);

namespace Upmind\ProvisionBase\Laravel\ValidationRules;

use Illuminate\Validation\Validator;
use RuntimeException;

/**
 * Attribute must be numeric and a multiple of the given step.
 */
class Step
{
    /**
     * @param string $attribute Attribute name
     * @param integer|float|string|null $value Attribute value
     * @param string[] $parameters Array of validation rule parameters
     * @param Validator $validator
     *
     * @return bool
     */
    public function validateStep($attribute, $value, $parameters, $validator)
    {
        $step = $this->getStepParameter($parameters);

        if (!is_numeric($value)) {
            //numeric
            $validator->addFailure($attribute, 'numeric');

            return true; //return true since we manually added a message to the error bag
        }

        if (static::isDivisible($value, $step)) {
            // Value is within the given step
            return true;
        }

        if ($step == 1) {
            //integer
            $validator->addFailure($attribute, 'integer');

            return true; //return true since we manually added a message to the error bag
        }

        return false; //default validation error message
    }

    /**
     * Replace the :step placeholder in validation messages.
     *
     * @param  string  $message
     * @param  string  $attribute
     * @param  string  $rule
     * @param  array   $parameters
     * @param  \Illuminate\Validation\Validator  $validator
     * @return string|null
     */
    public function replaceStep($message, $attribute, $rule, $parameters, $validator): ?string
    {
        $message = $this->getMessage($message);

        return str_replace(
            [
                ':attribute',
                ':step',
            ],
            [
                $attribute,
                $this->getStepParameter($parameters),
            ],
            $message
        );
    }

    /**
     * Get the step parameter/argument from the step rule. E.g., step:0.1 returns 0.1
     *
     * @param iterable $parameters
     *
     * @return int|float
     */
    public function getStepParameter($parameters)
    {
        if (!is_iterable($parameters) || count($parameters) !== 1) {
            throw new RuntimeException('Step rule requires a single argument');
        }

        $step = array_shift($parameters);

        if (!is_numeric($step)) {
            throw new RuntimeException('Step rule argument must be numeric');
        }

        if (static::isZero($step)) {
            throw new RuntimeException('Step rule argument cannot be zero');
        }

        return $step;
    }

    public function getMessage(string $message): string
    {
        return $message === 'validation.step'
            ? 'The :attribute must be a multiple of :step.'
            : $message;
    }

    /**
     * Determine whether $x is evenly divisible (no remainder) by $y.
     *
     * @param int|float $x
     * @param int|float $y
     * @param int|float $precision
     *
     * @link https://stackoverflow.com/a/21915228/4741456
     */
    public static function isDivisible($x, $y, $precision = 0.0000000001): bool
    {
        return abs(($x / $y) - round($x / $y, 0)) < $precision;
    }

    /**
     * Determine if the given number is zero.
     *
     * @param int|float $number
     * @param int|float $precision
     */
    public static function isZero($number, $precision = 0.0000000001): bool
    {
        $precision = abs($precision);
        return -$precision < (float)$number && (float)$number < $precision;
    }
}
