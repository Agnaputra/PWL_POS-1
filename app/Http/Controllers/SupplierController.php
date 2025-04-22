<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SupplierModel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class SupplierController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Daftar Supplier',
            'list' => ['Home', 'Supplier']
        ];

        $page = (object) [
            'title' => 'Daftar Supplier'
        ];

        $activeMenu = 'supplier';
        $suppliers = SupplierModel::all();

        return view('supplier.index', compact('breadcrumb', 'page', 'suppliers', 'activeMenu'));
    }

    public function create()
    {
        $breadcrumb = (object) [
            'title' => 'Tambah Supplier',
            'list' => ['Home', 'Supplier', 'Tambah']
        ];

        $page = (object) [
            'title' => 'Tambah Supplier Baru'
        ];

        $activeMenu = 'supplier';

        return view('supplier.create', compact('breadcrumb', 'page', 'activeMenu'));
    }

    public function show(string $id)
    {
        $supplier = SupplierModel::find($id);

        $breadcrumb = (object) [
            'title' => 'Detail Supplier',
            'list' => ['Home', 'Supplier', 'Detail']
        ];

        $page = (object) [
            'title' => 'Detail Supplier'
        ];

        $activeMenu = 'supplier';

        return view('supplier.show_ajax', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'supplier' => $supplier,
            'activeMenu' => $activeMenu
        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'nama_supplier' => 'required|string|max:100',
            'email' => 'required|email|unique:m_supplier,email',
            'telepon' => 'required|string|max:20|unique:m_supplier,telepon',
            'alamat' => 'required|string|max:255'
        ]);

        SupplierModel::create($request->all());

        return redirect('/supplier')->with('success', 'Supplier berhasil ditambahkan!');
    }

    public function edit(string $id)
    {
        $supplier = SupplierModel::find($id);
        if (!$supplier) {
            return redirect('/supplier')->with('error', 'Supplier tidak ditemukan.');
        }

        $breadcrumb = (object) [
            'title' => 'Edit Supplier',
            'list' => ['Home', 'Supplier', 'Edit']
        ];

        $page = (object) [
            'title' => 'Edit Supplier'
        ];

        $activeMenu = 'supplier';

        return view('supplier.edit', compact('breadcrumb', 'page', 'supplier', 'activeMenu'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'nama_supplier' => 'required|string|max:100',
            'email' => 'required|email|unique:m_supplier,email,' . $id . ',supplier_id',
            'telepon' => 'required|string|max:20|unique:m_supplier,telepon,' . $id . ',supplier_id',
            'alamat' => 'required|string|max:255'
        ]);

        $supplier = SupplierModel::find($id);
        if (!$supplier) {
            return redirect('/supplier')->with('error', 'Supplier tidak ditemukan.');
        }

        $supplier->update($request->all());

        return redirect('/supplier')->with('success', 'Supplier berhasil diperbarui!');
    }

    public function destroy(string $id)
    {
        $supplier = SupplierModel::find($id);
        if (!$supplier) {
            return redirect('/supplier')->with('error', 'Supplier tidak ditemukan.');
        }

        try {
            $supplier->delete();
            return redirect('/supplier')->with('success', 'Supplier berhasil dihapus!');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect('/supplier')->with('error', 'Supplier gagal dihapus karena masih terkait dengan data lain.');
        }
    }

    public function list(Request $request)
    {
        $suppliers = SupplierModel::select('supplier_id', 'nama_supplier', 'email', 'telepon', 'alamat',);

        return DataTables::of($suppliers)
            ->addIndexColumn()
            ->addColumn('action', function ($supplier) {
                $btn = '<button onclick="modalAction(\'' . url('/supplier/' . $supplier->supplier_id) . '\')" class="btn btn-info btn-sm">Detail</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/supplier/' . $supplier->supplier_id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/supplier/' . $supplier->supplier_id . '/delete_ajax') . '\')" class="btn btn-danger btn-sm">Delete</button> ';
                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function create_ajax()
    {
        return view('supplier.create_ajax');
    }

    public function store_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'nama_supplier' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:m_supplier,email',
                'telepon' => 'required|string|max:15',
                'alamat' => 'required|string|max:255'
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors()
                ]);
            }

            SupplierModel::create($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Data supplier berhasil disimpan'
            ]);
        }
        return redirect('/');
    }

    public function edit_ajax(string $id)
    {
        $supplier = SupplierModel::find($id);
        return view('supplier.edit_ajax', compact('supplier'));
    }

    public function update_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'nama_supplier' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:m_supplier,email,' . $id . ',supplier_id',
                'telepon' => 'required|string|max:15',
                'alamat' => 'required|string|max:255'
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors()
                ]);
            }

            $supplier = SupplierModel::find($id);
            if ($supplier) {
                $supplier->update($request->all());
                return response()->json([
                    'status' => true,
                    'message' => 'Data supplier berhasil diperbarui'
                ]);
            }
            return response()->json([
                'status' => false,
                'message' => 'Data tidak ditemukan'
            ]);
        }
        return redirect('/');
    }

    public function confirm_ajax(string $id)
    {
        $supplier = SupplierModel::find($id);
        return view('supplier.confirm_ajax', ['supplier' => $supplier]);
    }

    public function delete_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $supplier = SupplierModel::find($id);
            if ($supplier) {
                $supplier->delete();
                return response()->json([
                    'status' => true,
                    'message' => 'Data supplier berhasil dihapus'
                ]);
            }
            return response()->json([
                'status' => false,
                'message' => 'Data tidak ditemukan'
            ]);
        }
        return redirect('/');
    }
}
