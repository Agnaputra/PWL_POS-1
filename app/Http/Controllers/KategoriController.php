<?php

namespace App\Http\Controllers;

use App\Models\KategoriModel;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class KategoriController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Daftar Kategori',
            'list' => ['Home', 'Kategori']
        ];

        $page = (object) [
            'title' => 'Daftar Kategori'
        ];

        $activeMenu = 'kategori';
        $kategoris = KategoriModel::all();

        return view('kategori.index', compact('breadcrumb', 'page', 'kategoris', 'activeMenu'));
    }

    public function create()
    {
        $kategori = KategoriModel::all(); // Ambil semua kategori dari database
        $activeMenu = 'kategori'; // Tambahkan ini

        $breadcrumb = (object) [
            'title' => 'Tambah Kategori',
            'list' => ['Home', 'Kategori', 'Tambah']
        ]; // Tambahkan ini

        return view('kategori.create', compact('kategori', 'activeMenu', 'breadcrumb'));
    }

    public function show(string $id)
    {
        $kategori = KategoriModel::find($id);

        $breadcrumb = (object) [
            'title' => 'Detail Kategori',
            'list' => ['Home', 'Kategori', 'Detail']
        ];

        $page = (object) [
            'title' => 'Detail Kategori'
        ];

        $activeMenu = 'kategori';

        return view('kategori.show_ajax', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'kategori' => $kategori,
            'activeMenu' => $activeMenu
        ]);
    }

    public function list(Request $request)
    {
        $kategoris = KategoriModel::select('kategori_id', 'kategori_kode', 'kategori_nama');

        return DataTables::of($kategoris)
            ->addIndexColumn()
            ->addColumn('action', function ($kategori) {
                $btn = '<button onclick="modalAction(\'' . url('/kategori/' . $kategori->kategori_id) . '\')" class="btn btn-info btn-sm">Detail</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/kategori/' . $kategori->kategori_id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/kategori/' . $kategori->kategori_id . '/delete_ajax') . '\')" class="btn btn-danger btn-sm">Delete</button> ';
                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }


    public function store(Request $request)
    {
        $request->validate([
            'kategori_kode' => 'required|string|max:10|unique:m_kategori,kategori_kode',
            'kategori_nama' => 'required|string|max:100'
        ]);

        KategoriModel::create($request->all());

        return redirect('/kategori')->with('success', 'Kategori berhasil ditambahkan!');
    }

    public function edit(string $id)
    {
        $kategori = KategoriModel::find($id);
        if (!$kategori) {
            return redirect('/kategori')->with('error', 'Kategori tidak ditemukan.');
        }

        $breadcrumb = (object) [
            'title' => 'Edit Kategori',
            'list' => ['Home', 'Kategori', 'Edit']
        ];

        $page = (object) [
            'title' => 'Edit Kategori'
        ];

        $activeMenu = 'kategori';

        // Mengambil daftar kategori yang bisa dipilih
        $kategoriOptions = KategoriModel::select('kategori_nama')->distinct()->get();

        return view('kategori.edit', compact('breadcrumb', 'page', 'kategori', 'kategoriOptions', 'activeMenu'));
    }


    public function update(Request $request, string $id)
    {
        $request->validate([
            'kategori_kode' => 'required|string|max:10|unique:m_kategori,kategori_kode,' . $id . ',kategori_id',
            'kategori_nama' => 'required|string|max:100'
        ]);

        $kategori = KategoriModel::find($id);
        if (!$kategori) {
            return redirect('/kategori')->with('error', 'Kategori tidak ditemukan.');
        }

        $kategori->update($request->all());

        return redirect('/kategori')->with('success', 'Kategori berhasil diperbarui!');
    }

    public function destroy(string $id)
    {
        $kategori = KategoriModel::find($id);
        if (!$kategori) {
            return redirect('/kategori')->with('error', 'Kategori tidak ditemukan.');
        }

        try {
            $kategori->delete();
            return redirect('/kategori')->with('success', 'Kategori berhasil dihapus!');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect('/kategori')->with('error', 'Kategori gagal dihapus karena masih terkait dengan data lain.');
        }
    }

    public function create_ajax()
    {
        $kategoris = KategoriModel::all();
        return view('kategori.create_ajax', compact('kategoris'));
    }

    public function store_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'kategori_kode' => 'required|string|max:10|unique:kategoris,kategori_kode',
                'kategori_nama' => 'required|string|max:50'
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation Failed',
                    'msgField' => $validator->errors()
                ]);
            }

            KategoriModel::create($request->all());

            return response()->json([
                'status' => true,
                'message' => 'Kategori successfully saved!'
            ]);
        }

        return redirect('/');
    }

    public function edit_ajax(string $id)
    {
        $kategori = KategoriModel::find($id);
        $kategoriList = KategoriModel::all();

        // Check if the category exists
        if (!$kategori) {
            return response()->json([
                'status' => false,
                'message' => 'Data not found'
            ], 404);
        }

        return view('kategori.edit_ajax', compact('kategori', 'kategoriList'));
    }

    public function update_ajax(Request $request, $id)
    {
        // Log the incoming request data for debugging
        Log::info("Received update request: ", $request->all());

        // Validate the request data
        $validator = Validator::make($request->all(), [
            'kategori_kode' => 'required|string', // Ensure level_kategori is provided
            'kategori_nama' => 'required|string',  // Ensure nama_kategori is provided and not null
        ]);

        // If validation fails, return a JSON response with errors
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422); // 422 is the HTTP status code for validation errors
        }

        // Find the category by ID
        $kategori = KategoriModel::find($id);
        if (!$kategori) {
            return response()->json([
                'status' => false,
                'message' => 'Category not found'
            ], 404); // 404 is the HTTP status code for "Not Found"
        }

        // Update the category
        try {
            $kategori->update([
                'kategori_kode' => $request->input('kategori_kode'),
                'kategori_nama' => $request->input('kategori_nama'),
            ]);

            // Return the updated category data
            return response()->json([
                'status' => true,
                'message' => 'Category updated successfully',
                'data' => $kategori // Include the updated category data in the response
            ], 200); // 200 is the HTTP status code for "OK"
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error("Error updating category: " . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Failed to update category',
                'error' => $e->getMessage() // Include the error message in the response (optional)
            ], 500); // 500 is the HTTP status code for server errors
        }
    }

    public function confirm_ajax(string $id)
    {
        $kategori = KategoriModel::find($id);
        return view('kategori.confirm_ajax', ['kategori' => $kategori]);
    }

    public function delete_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $kategori = KategoriModel::find($id);
            if ($kategori) {
                $kategori->delete();
                return response()->json([
                    'status' => true,
                    'message' => 'Kategori successfully deleted!'
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Kategori not found'
                ]);
            }
        }
        return redirect('/');
    }
}