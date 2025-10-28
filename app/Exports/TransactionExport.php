<?php
namespace App\Exports;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class TransactionExport implements  WithTitle, FromView
{

    public $transaction;

    public function __construct($transaction)
    {
        $this->transaction = $transaction;
    }

    public function title(): string
    {
        return 'Transaction';
    }

    public function view(): View
    {
        return view('reportesexcel.excel_transaction', [
            'transactions' => $this->transaction
        ]);
    }

}
?>
