<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use Illuminate\Http\Request;

class PartnerController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'admin']);
    }

    /**
     * Display a listing of partners.
     */
    public function index()
    {
        $partners = Partner::paginate(15);
        return view('partners.index', compact('partners'));
    }

    /**
     * Show the form for creating a new partner.
     */
    public function create()
    {
        return view('partners.create');
    }

    /**
     * Store a newly created partner in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate(Partner::rules());
        
        Partner::create($validated);

        return redirect('/partners')->with('success', 'Partner added successfully.');
    }

    /**
     * Show the form for editing the specified partner.
     */
    public function edit(Partner $partner)
    {
        return view('partners.edit', compact('partner'));
    }

    /**
     * Update the specified partner in storage.
     */
    public function update(Request $request, Partner $partner)
    {
        $validated = $request->validate(Partner::rules($partner->id));
        
        $partner->update($validated);

        return redirect('/partners')->with('success', 'Partner updated successfully.');
    }

    /**
     * Remove the specified partner from storage.
     */
    public function destroy(Partner $partner)
    {
        $partner->delete();

        return redirect('/partners')->with('success', 'Partner deleted successfully.');
    }
}
