<?php

namespace App\Http\Controllers\Admin;

use App\Models\Invoice;
use App\Models\InvoiceRow;
use App\Utils\Common\History;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package App\Http\Controllers\Admin
 * @role(enabled=true)
 */
class InvoiceRowController extends BaseController
{
    /**
     * @role(super_user, stock_manager, acc_manager)
     * @rules(invoice_id="required|exists:invoices,id")
     */
    public function index(): Factory|View|Application
    {
        parent::setPageAttribute(request()->get("invoice_id"));
        $invoice = Invoice::find(request()->get('invoice_id'));
        $invoice_rows = $invoice->rows()->paginate(Invoice::getPaginationCount());
        return view('admin.pages.invoice-row.index', compact('invoice', 'invoice_rows'));
    }

    /**
     * @role(super_user, stock_manager, acc_manager)
     * @rules(invoice_id="exists:invoices,id")
     */
    public function create(): Factory|View|Application
    {
        $invoice = Invoice::find(request()->get('invoice_id'));
        return view('admin.pages.invoice-row.create', compact('invoice'));
    }

    /**
     * @role(super_user, stock_manager, acc_manager)
     * @rules(product_id="required|exists:products,id", count="required", discount_percentage="required")
     */
    public function store(Request $request): RedirectResponse|Response
    {
        $invoice_row = InvoiceRow::create($request->all());
        return
            redirect()->to(route('admin.invoice-row.index') . '?invoice_id=' . $invoice_row->id);
    }

    /**
     * @role(super_user, stock_manager, acc_manager)
     */
    public function show(InvoiceRow $invoice_row): Response|\Illuminate\Http\Response|Application|ResponseFactory
    {
        return response('invoice row show page');
    }

    /**
     * @role(super_user, stock_manager, acc_manager)
     */
    public function edit(InvoiceRow $invoice_row): Factory|View|Application
    {
        $invoice_row->load('products', 'customer');
        return view('admin.pages.invoice.edit')->with(compact("invoice_row"));
    }

    /**
     * @role(super_user, stock_manager, acc_manager)
     * @rules(product_id="required|exists:products,id", count="required", discount_percentage="required")
     */
    public function update(Request $request, InvoiceRow $invoice_row): RedirectResponse
    {
        $invoice_row->update($request->all());
        return History::redirectBack();
    }

    /**
     * @role(super_user, stock_manager, acc_manager)
     */
    public function destroy(InvoiceRow $invoice_row): RedirectResponse
    {
        $invoice_row->delete();
        return back();
    }


    public function getModel(): ?string
    {
        return Invoice::class;
    }
}
