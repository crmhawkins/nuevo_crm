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

        $client->client_name = $request->client_name;
        $client->client_email = $request->client_email;
        $client->next_seo = $request->next_seo;
        $client->url = $request->url;
        $client->username = $request->username;
        $client->password = $request->password;
        $client->user_app = $request->user_app;
        $client->password_app = $request->password_app;
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
        $client->username = $request->username;
        $client->password = $request->password;
        $client->user_app = $request->user_app;
        $client->password_app = $request->password_app;
        
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
