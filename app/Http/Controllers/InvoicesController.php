<?php

namespace App\Http\Controllers;

use App\Events\MyEventClass;
use App\Exports\InvoicesExport;
use App\Models\Invoice;
use App\Models\InvoiceAttachment;
use App\Models\InvoiceDetails;
use App\Models\Section;
use App\Models\User;
use App\Notifications\AddNewInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Excel;

class InvoicesController extends Controller
{
    public function index()
    {
        $invoices = Invoice::all();
        return view('invoices.invoices', compact('invoices'));
    }

    public function create()
    {
        $sections = Section::all();
        return view('invoices.add_invoice', compact('sections'));
    }

    public function store(Request $request)
    {
        Invoice::create([
            'invoice_number' => $request->invoice_number,
            'invoice_date' => $request->invoice_date,
            'due_date' => $request->due_date,
            'product' => $request->product,
            'section_id' => $request->Section,
            'amount_collection' => $request->amount_collection,
            'amount_commission' => $request->amount_commission,
            'discount' => $request->discount,
            'value_vat' => $request->value_vat,
            'rate_vat' => $request->rate_vat,
            'total' => $request->total,
            'status' => 'غير مدفوعة',
            'value_status' => 2,
            'note' => $request->note,
        ]);

        $invoice_id = Invoice::latest()->first()->id;
        InvoiceDetails::create([
            'id_invoice' => $invoice_id,
            'invoice_number' => $request->invoice_number,
            'product' => $request->product,
            'section' => $request->section,
            'status' => 'غير مدفوعة',
            'value_status' => 2,
            'note' => $request->note,
            'user' => (Auth::user()->name),
        ]);

        if ($request->hasFile('pic')) {
            $invoice_id = Invoice::latest()->first()->id;
            $image = $request->file('pic');
            $file_name = $image->getClientOriginalName();
            $invoice_number = $request->invoice_number;

            $attachments = new InvoiceAttachment();
            $attachments->file_name = $file_name;
            $attachments->invoice_number = $invoice_number;
            $attachments->created_by = Auth::user()->name;
            $attachments->invoice_id = $invoice_id;
            $attachments->save();

            // move pic
            $imageName = $request->pic->getClientOriginalName();
            $request->pic->move(public_path('attachments/' . $invoice_number), $imageName);
        }

        // $user = User::first();
        // Notification::send($user, new AddInvoice($invoice_id));

        $user = User::get();
        $invoices = Invoice::latest()->first();
        Notification::send($user, new AddNewInvoice($invoices));

        event(new MyEventClass('hello world'));

        session()->flash('Add', 'تم إضافة الفاتورة بنجاح');
        return back();
    }

    public function show($id)
    {
        $invoices = Invoice::where('id', $id)->first();
        return view('invoices.status_update', compact('invoices'));
    }

    public function edit($id)
    {
        $invoices = Invoice::where('id', $id)->first();
        $sections = Section::all();
        return view('invoices.edit_invoice', compact('sections', 'invoices'));
    }

    public function update(Request $request)
    {
        $invoices = Invoice::findOrFail($request->invoice_id);
        $invoices->update([
            'invoice_number' => $request->invoice_number,
            'invoice_date' => $request->invoice_date,
            'due_date' => $request->due_date,
            'product' => $request->product,
            'section_id' => $request->Section,
            'amount_collection' => $request->amount_collection,
            'amount_commission' => $request->amount_commission,
            'discount' => $request->discount,
            'value_vat' => $request->value_vat,
            'rate_vat' => $request->rate_vat,
            'total' => $request->total,
            'note' => $request->note,
        ]);

        session()->flash('edit', 'تم تعديل الفاتورة بنجاح');
        return back();
    }

    public function destroy(Request $request)
    {
        $id = $request->invoice_id;
        $invoices = Invoice::where('id', $id)->first();
        $details = InvoiceAttachment::where('invoice_id', $id)->first();

        $id_page = $request->id_page;

        if (!$id_page == 2) {
            if (!empty($details->invoice_number)) {
                Storage::disk('public_uploads')->deleteDirectory($details->invoice_number);
            }
            $invoices->forceDelete();
            session()->flash('delete_invoice');
            return redirect('/invoices');
        } else {
            $invoices->delete();
            session()->flash('archive_invoice');
            return redirect('/archive');
        }
    }

    public function getProducts($id)
    {
        $products = DB::table("products")->where("section_id", $id)->pluck("product_name", "id");
        return json_encode($products);
    }

    public function statusUpdate($id, Request $request)
    {
        $invoices = Invoice::findOrFail($id);

        if ($request->status === 'مدفوعة') {

            $invoices->update([
                'value_status' => 1,
                'status' => $request->status,
                'payment_date' => $request->payment_date,
            ]);

            InvoiceDetails::create([
                'id_invoice' => $request->invoice_id,
                'invoice_number' => $request->invoice_number,
                'product' => $request->product,
                'section' => $request->section,
                'status' => $request->status,
                'value_status' => 1,
                'note' => $request->note,
                'payment_date' => $request->payment_date,
                'user' => (Auth::user()->name),
            ]);
        } else {
            $invoices->update([
                'value_status' => 3,
                'status' => $request->status,
                'payment_date' => $request->payment_date,
            ]);
            InvoiceDetails::create([
                'id_invoice' => $request->invoice_id,
                'invoice_number' => $request->invoice_number,
                'product' => $request->product,
                'section' => $request->section,
                'status' => $request->status,
                'value_status' => 3,
                'note' => $request->note,
                'payment_date' => $request->payment_date,
                'user' => (Auth::user()->name),
            ]);
        }
        session()->flash('status_update');
        return redirect('/invoices');
    }

    public function invoicePaid()
    {
        $invoices = Invoice::where('value_status', 1)->get();
        return view('invoices.invoices_paid', compact('invoices'));
    }

    public function invoiceUnpaid()
    {
        $invoices = Invoice::where('value_status', 2)->get();
        return view('invoices.invoices_unpaid', compact('invoices'));
    }

    public function invoicePartial()
    {
        $invoices = Invoice::where('value_status', 3)->get();
        return view('invoices.invoices_partial', compact('invoices'));
    }

    public function printInvoice($id)
    {
        $invoices = Invoice::where('id', $id)->first();
        return view('invoices.print_invoice', compact('invoices'));
    }

    public function export()
    {
        return Excel::download(new InvoicesExport, 'invoices.xlsx');
    }

    public function markAsReadAll()
    {
        $userUnreadNotification = auth()->user()->unreadNotifications;
        if ($userUnreadNotification) {
            $userUnreadNotification->markAsRead();
            return back();
        }
    }

    public function unreadNotifications_count()
    {
        return auth()->user()->unreadNotifications->count();
    }

    public function unreadNotifications()
    {
        foreach (auth()->user()->unreadNotifications as $notification) {
            return $notification->data['title'];
        }
    }
}
