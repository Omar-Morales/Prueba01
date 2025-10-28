<?php
namespace App\Exports;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class InventoryExportId implements  WithTitle, FromView
{
 protected $inventories;

    public function __construct($inventories)
    {
        $this->inventories = $inventories;
    }

    public function view(): View
    {
        return view('reportesexcel.excel_inventoryId', ['data' => $this->inventories]);
    }

    public function title(): string
    {
        return 'Detalle Inventario';
    }

}
?>
