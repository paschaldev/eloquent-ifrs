<?php
/**
 * Laravel IFRS Accounting
 *
 * @author Edward Mungai
 * @copyright Edward Mungai, 2020, Germany
 * @license MIT
 */
namespace Ekmungai\IFRS\Exceptions;

/**
 *
 * @author emung
 *
 */
class NegativeAmount extends IFRSException
{
    /**
     *
     * @param string $modelType
     * @param string $message
     * @param int $code
     */
    public function __construct(string $modelType, string $message = null, int $code = null)
    {
        parent::__construct($modelType._(" Amount cannot be negative ").$message, $code);
    }
}