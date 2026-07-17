<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BarangTemplateExport;
use App\Exports\CustomerTemplateExport;
use App\Exports\SupplierTemplateExport;
use App\Imports\BarangImport;
use App\Imports\CustomerImport;
use App\Imports\SupplierImport;
use Exception;

class DataImportController extends Controller
{
    public function index()
    {
        return view('import.index');
    }

    public function downloadTemplateBarang()
    {
        return Excel::download(new BarangTemplateExport, 'Template_Master_Barang.xlsx');
    }

    public function importBarang(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv|max:10240', // Max 10MB
        ]);

        try {
            Excel::import(new BarangImport, $request->file('file_excel'));
            
            return redirect()->route('import.index')->with('success', 'Data Master Barang berhasil di-import ke sistem!');
        } catch (Exception $e) {
            return redirect()->route('import.index')->with('error', 'Gagal meng-import data Barang. Pastikan format file sesuai dengan template. Error: ' . $e->getMessage());
        }
    }

    public function downloadTemplateCustomer()
    {
        return Excel::download(new CustomerTemplateExport, 'Template_Master_Customer.xlsx');
    }

    public function importCustomer(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            Excel::import(new CustomerImport, $request->file('file_excel'));
            return redirect()->route('import.index')->with('success', 'Data Master Customer berhasil di-import ke sistem!');
        } catch (Exception $e) {
            return redirect()->route('import.index')->with('error', 'Gagal meng-import data Customer. Pastikan format file sesuai. Error: ' . $e->getMessage());
        }
    }

    public function downloadTemplateSupplier()
    {
        return Excel::download(new SupplierTemplateExport, 'Template_Master_Supplier.xlsx');
    }

    public function importSupplier(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            Excel::import(new SupplierImport, $request->file('file_excel'));
            return redirect()->route('import.index')->with('success', 'Data Master Supplier berhasil di-import ke sistem!');
        } catch (Exception $e) {
            return redirect()->route('import.index')->with('error', 'Gagal meng-import data Supplier. Pastikan format file sesuai. Error: ' . $e->getMessage());
        }
    }
}
