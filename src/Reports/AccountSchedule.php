<?php
/**
 * Laravel IFRS Accounting
 *
 * @author Edward Mungai
 * @copyright Edward Mungai, 2020, Germany
 * @license MIT
 */
namespace Ekmungai\IFRS\Reports;

use Carbon\Carbon;

use Ekmungai\IFRS\Models\Balance;
use Ekmungai\IFRS\Models\Transaction;
use Ekmungai\IFRS\Models\Currency;
use Ekmungai\IFRS\Models\ReportingPeriod;
use Ekmungai\IFRS\Models\Account;

use Ekmungai\IFRS\Exceptions\MissingAccount;

class AccountSchedule extends AccountStatement
{

    /**
     * Account Schedule balances.
     *
     * @var array
     */
    public $balances = [
        "originalAmount" => 0,
        "clearedAmount" => 0,
        "unclearedAmount" => 0,
    ];

    /**
     * Get Transaction amounts.
     *
     * @param Transaction|Balance $transaction
     * @param string $transactionType
     *
     */
    private function getAmounts($transaction, $transactionType) : void
    {
        $clearedAmount = $originalAmount = 0;

        $originalAmount = $transaction->amount/$transaction->exchangeRate->rate;
        $clearedAmount = $transaction->clearedAmount();
        $unclearedAmount = $originalAmount - $clearedAmount;

        if ($unclearedAmount > 0) {
            $outstanding = new \stdClass();

            $outstanding->id = $transaction->id;
            $outstanding->transactionType = $transactionType;

            $this->balances["originalAmount"] += $originalAmount;
            $outstanding->originalAmount = $originalAmount;

            $this->balances['clearedAmount'] += $clearedAmount;
            $outstanding->clearedAmount = $clearedAmount;

            $this->balances['unclearedAmount'] += $unclearedAmount;
            $outstanding->unclearedAmount = $unclearedAmount;

            array_push($this->transactions, $outstanding);
        }
    }

    /**
     * Account Schedule for the account for the period.
     *
     * @param Account $account
     * @param Currency $currency
     * @param Carbon $endDate
     */
    public function __construct(Account $account = null, Currency $currency = null, Carbon $endDate = null)
    {
        if (is_null($account)) {
            throw new MissingAccount("Account Schedule");
        }
        parent::__construct($account, $currency, null, $endDate);
    }

    /**
     * Get Account Schedule Transactions.
     */
    public function getTransactions() : void
    {
        // Opening Balances
        foreach ($this->account->balances->where("year", ReportingPeriod::year($this->period['endDate'])) as $balance) {
            $this->getAmounts($balance, _("Opening Balance"));
        }

        // Clearable Transactions
        $transactions = $this->buildQuery()->whereIn('transaction_type', [
            Transaction::IN,
            Transaction::BL,
            Transaction::JN
        ])->select('transactions.id');

        foreach ($transactions->get() as $transaction) {
            $transaction = Transaction::find($transaction->id);

            if ($transaction->transaction_type == Transaction::JN
                and (($this->account->account_type == Account::RECEIVABLE and $transaction->credited)
                    or ($this->account->account_type == Account::PAYABLE and !$transaction->credited)
                    )
                ) {
                continue;
            }
            $this->getAmounts($transaction, config('ifrs')['transactions'][$transaction->transaction_type]);
        }
    }
}