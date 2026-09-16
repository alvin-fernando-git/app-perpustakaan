<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMemberRequest;
use Illuminate\Http\Request;

class MemberController extends Controller
{

    private array $members = [
        ['id' => 1, 'nama' => 'Ahmad Fauzi', 'nim' => '3125600001', 'email' => 'ahmad.fauzi@student.pens.ac.id', 'nomor_telepon' => '081234567890', 'alamat' => 'Jl. Raya Rungkut No. 10, Surabaya', 'status' => 'aktif'],
        ['id' => 2, 'nama' => 'Siti Nurhaliza', 'nim' => '3125600002', 'email' => 'siti.nurhaliza@student.pens.ac.id', 'nomor_telepon' => '081298765432', 'alamat' => 'Jl. Kertajaya No. 25, Surabaya', 'status' => 'aktif'],
        ['id' => 3, 'nama' => 'Budi Santoso', 'nim' => '3125600003', 'email' => 'budi.santoso@student.pens.ac.id', 'nomor_telepon' => '081345678901', 'alamat' => 'Jl. Manyar No. 7, Surabaya', 'status' => 'nonaktif'],
    ];
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $members = $this->members;

        return view('members.index', compact('members'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('members.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMembersRequest $request)
    {
        $validated = $request->validated();

        return redirect()->route('members.index')
            ->with('success', "Anggota \"{$validated['nama']}\" berhasil ditambahkan (data dummy, belum tersimpan ke database).");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return "MemberController@show, id: ($id)";
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return "MemberController@edit, id: ($id)";
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        return "MemberController@update, id: ($id)";
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return "MemberController@destroy, id: ($id)";
    }
}
