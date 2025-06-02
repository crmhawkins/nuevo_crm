<?php

namespace App\Http\Controllers\Autoseo;

use App\Http\Controllers\Controller;
use App\Models\Autoseo\Autoseo;
use Illuminate\Http\Request;
use Storage;

class AutoseoController extends Controller
{
    public function index()
    {

        $clients = Autoseo::all();
        return view('autoseo.index', compact('clients'));
    }

    public function store(Request $request)
    {
        $client = new Autoseo();
        // Handle file uploads
        if ($request->hasFile('json_home')) {
            $jsonHomePath = $request->file('json_home')->store('autoseo/json', 'public');
            $client->json_home = $jsonHomePath;
        } else {
            dd('no hay archivo');
        }

        if ($request->hasFile('json_nosotros')) {
            $jsonNosotrosPath = $request->file('json_nosotros')->store('autoseo/json', 'public');
            $client->json_nosotros = $jsonNosotrosPath;
        }

        $client->client_name = $request->client_name;
        $client->client_email = $request->client_email;
        $client->next_seo = $request->next_seo;
        $client->json_home_update = now();
        $client->json_nosotros_update = now();
        $client->url = $request->url;

        // Save the client
        $client->save();

        return redirect()->route('autoseo.index')->with('success', 'Cliente creado correctamente');
    }

    public function update(Request $request)
    {
        $client = Autoseo::find($request->id);
        $client->client_name = $request->client_name;
        $client->client_email = $request->client_email;
        $client->url = $request->url;
        $client->next_seo = $request->next_seo;
        if ($request->hasFile('json_home')) {
            Storage::disk('public')->delete($client->json_home);
            $jsonHomePath = $request->file('json_home')->store('autoseo/json', 'public');
            $client->json_home = $jsonHomePath;
            $client->json_home_update = now();
        }
        if ($request->hasFile('json_nosotros')) {
            Storage::disk('public')->delete($client->json_nosotros);
            $jsonNosotrosPath = $request->file('json_nosotros')->store('autoseo/json', 'public');
            $client->json_nosotros = $jsonNosotrosPath;
            $client->json_nosotros_update = now();
        }
        $client->save();

        return redirect()->route('autoseo.index')->with('success', 'Cliente actualizado correctamente');
    }

    public function delete(Request $request)
    {
        $client = Autoseo::find($request->id);
        $client->delete();
        return redirect()->route('autoseo.index')->with('success', 'Cliente eliminado correctamente');
    }
}
