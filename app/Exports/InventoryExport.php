<?php
namespace App\Exports;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class InventoryExport implements  WithTitle, FromView
{

    public $inventories;

    public function __construct($inventories)
    {
        $this->inventories = $inventories;
    }

    public function title(): string
    {
        return 'Inventario';
    }

    public function view(): View
    {
        return view('reportesexcel.excel_inventory', [
            'inventories' => $this->inventories
        ]);
    }

}
?>
