<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceAttachment;
use App\Models\InvoiceDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use File;

class InvoicesDetailsController extends Controller
{
    public function edit($id)
    {
        $invoices = Invoice::where('id', $id)->first();
        $details  = InvoiceDetails::where('id_invoice', $id)->get();
        $attachments  = InvoiceAttachment::where('invoice_id', $id)->get();

        return view('invoices.details_invoice', compact('invoices', 'details', 'attachments'));
    }

    public function destroy(Request $request)
    {
        $invoices = InvoiceAttachment::findOrFail($request->id_file);
        $invoices->delete();
        Storage::disk('public_uploads')->delete($request->invoice_number . '/' . $request->file_name);
        session()->flash('delete', 'تم حذف المرفق بنجاح');
        return back();
    }

    public function getFile($invoice_number, $file_name)
    {
        $contents = Storage::disk('public_uploads')->getDriver()->getAdapter()->applyPathPrefix($invoice_number . '/' . $file_name);
        return response()->download($contents);
    }

    public function openFile($invoice_number, $file_name)
    {
        $files = Storage::disk('public_uploads')->getDriver()->getAdapter()->applyPathPrefix($invoice_number . '/' . $file_name);
        return response()->file($files);
    }
}
