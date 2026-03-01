<?php

namespace App\Http\Controllers;

use App\Models\ContractTemplate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ContractTemplateController extends Controller
{
    public function index()
    {
        return ContractTemplate::all();
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'file' => 'required|file|max:10240',
        ]);

        $path = $request->file('file')->store('contract-templates', 'public');
        $content = file_get_contents($request->file('file')->getRealPath());

        // Extract variables like {{variable_name}}
        preg_match_all('/\{\{(\w+)\}\}/', $content, $matches);
        $variables = array_unique($matches[1] ?? []);

        ContractTemplate::create([
            'name' => $validated['name'],
            'file_path' => $path,
            'variables' => array_values($variables),
        ]);

        return back()->with('success', 'Šablona byla nahrána.');
    }

    public function destroy(ContractTemplate $contractTemplate): RedirectResponse
    {
        \Storage::disk('public')->delete($contractTemplate->file_path);
        $contractTemplate->delete();

        return back()->with('success', 'Šablona byla smazána.');
    }
}
