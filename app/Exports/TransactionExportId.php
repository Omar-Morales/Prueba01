<?php
namespace App\Exports;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class TransactionExportId implements  WithTitle, FromView
{
 protected $transaction;

    public function __construct($transaction)
    {
        $this->transaction = $transaction;
    }

    public function view(): View
    {
        return view('reportesexcel.excel_transactionId', ['data' => $this->transaction]);
    }

    public function title(): string
    {
        return 'Detalle Transaction';
    }

}
?>
