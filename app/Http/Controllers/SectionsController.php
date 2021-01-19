<?php

namespace App\Http\Controllers;

use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SectionsController extends Controller
{
    public function index()
    {
        $sections = Section::all();
        return view('sections.sections', compact('sections'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'section_name' => 'required|unique:sections|max:255',
        ], [
            'section_name.required' => 'يرجي إدخال اسم القسم',
            'section_name.unique' => 'اسم القسم مسجل مسبقًا',
        ]);

        Section::create([
            'section_name' => $request->section_name,
            'description' => $request->description,
            'created_by' => (Auth::user()->name),
        ]);

        session()->flash('Add', 'تم إضافة القسم بنجاح ');
        return redirect('/sections');
    }

    public function update(Request $request)
    {
        $id = $request->id;

        $this->validate($request, [
            'section_name' => 'required|max:255|unique:sections,section_name,' . $id,
            'description' => 'required',
        ], [
            'section_name.required' => 'يرجي إدخال اسم القسم',
            'section_name.unique' => 'اسم القسم مسجل مسبقًا',
            'description.required' => 'يرجي إدخال البيانات',
        ]);

        $sections = Section::find($id);
        $sections->update([
            'section_name' => $request->section_name,
            'description' => $request->description,
        ]);

        session()->flash('edit', 'تم تعديل القسم بنجاج');
        return redirect('/sections');
    }

    public function destroy(Request $request)
    {
        $id = $request->id;
        Section::find($id)->delete();
        session()->flash('delete', 'تم حذف القسم بنجاح');
        return redirect('/sections');
    }
}
