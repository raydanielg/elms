<?php

namespace App\Http\Controllers;

use App\Models\ApiKey;
use Illuminate\Http\Request;

class ApiKeyController extends Controller
{
    public function index()
    {
        $this->authorize('adminOrAbove');
        $keys = ApiKey::where('user_id', auth()->id())->latest()->get();
        return view('api-keys.index', compact('keys'));
    }

    public function store(Request $request)
    {
        $this->authorize('adminOrAbove');
        $validated = $request->validate([
            'name' => 'required|string',
            'scopes' => 'nullable|array',
            'expires_at' => 'nullable|date|after:now',
        ]);

        $generated = ApiKey::generate();
        $key = ApiKey::create(array_merge($validated, [
            'user_id' => auth()->id(),
            'tenant_id' => auth()->user()->tenant_id,
            'key_hash' => $generated['key_hash'],
            'key_prefix' => $generated['key_prefix'],
        ]));

        return response()->json([
            'message' => 'API key created. Save it now — it won\'t be shown again.',
            'key' => $generated['key'],
            'prefix' => $generated['key_prefix'],
        ]);
    }

    public function destroy(ApiKey $apiKey)
    {
        if ($apiKey->user_id !== auth()->id()) abort(403);
        $apiKey->delete();
        return response()->json(['message' => 'API key revoked']);
    }
}
