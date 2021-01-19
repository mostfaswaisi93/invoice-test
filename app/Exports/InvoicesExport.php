<?php

namespace App\Exports;

use App\Models\Invoice;
use Maatwebsite\Excel\Concerns\FromCollection;

class InvoicesExport implements FromCollection
{
    public function collection()
    {
        return Invoice::all();
        //return Invoice::select('invoice_number', 'invoice_date', 'due_date','section', 'product', 'amount_collection','amount_commission', 'rate_vat', 'value_vat','total', 'status', 'payment_date','note')->get();
    }
}
