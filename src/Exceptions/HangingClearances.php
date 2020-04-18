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
class HangingClearances extends IFRSException
{
    /**
     * Hanging Clearances Exception
     *
     * @param string $message
     * @param int $code
     */
    public function __construct(string $message = null, int $code = null)
    {
        $error = _("Transaction cannot be deleted because it has been used to to Clear other Transactions ");

        parent::__construct($error.$message, $code);
    }
}