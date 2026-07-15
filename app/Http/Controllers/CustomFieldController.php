<?php

namespace App\Http\Controllers;

use App\Models\CustomField;
use Illuminate\Http\Request;

class CustomFieldController extends Controller
{
    public function index()
    {
        $this->authorize('adminOrAbove');
        $fields = CustomField::where('tenant_id', auth()->user()->tenant_id)
            ->orWhereNull('tenant_id')
            ->orderBy('sort_order')->get();
        return view('custom-fields.index', compact('fields'));
    }

    public function store(Request $request)
    {
        $this->authorize('adminOrAbove');
        $validated = $request->validate([
            'form_type' => 'required|string',
            'field_name' => 'required|string',
            'field_label' => 'required|string',
            'field_type' => 'required|in:text,number,dropdown,checkbox,date,file',
            'is_required' => 'boolean',
            'options' => 'nullable|array',
            'sort_order' => 'integer|min:0',
        ]);
        $validated['tenant_id'] = auth()->user()->tenant_id;
        CustomField::create($validated);
        return response()->json(['message' => 'Custom field created']);
    }

    public function update(Request $request, CustomField $field)
    {
        $this->authorize('adminOrAbove');
        $field->update($request->only(['field_label', 'field_type', 'is_required', 'options', 'sort_order', 'is_active']));
        return response()->json(['message' => 'Custom field updated']);
    }

    public function destroy(CustomField $field)
    {
        $this->authorize('adminOrAbove');
        $field->delete();
        return response()->json(['message' => 'Custom field deleted']);
    }
}
