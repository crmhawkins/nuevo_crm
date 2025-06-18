<?php

namespace App\Http\Controllers\Plataforma;

use App\Http\Controllers\Controller;
use App\Models\Plataforma\WhatsappContacts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;

class ExcelUploadController extends Controller
{
    public function showForm()
    {
        return view('plataforma.upload_excel');
    }

    public function upload(Request $request)
{
    if (!Auth::check()) {
        return redirect('/login');
    }

    $request->validate([
        'excel_file' => 'required|file|mimes:xlsx,xls',
    ]);

    try {
        $data = Excel::toArray([], $request->file('excel_file'));
        $rows = $data[0] ?? [];

        if (empty($rows) || !is_array($rows)) {
            return back()->with('error', 'El archivo está vacío o no se pudo leer.');
        }

        $startRow = (int) 969;
        $headers = array_map('strtolower', $rows[0]);
        $nombreIndex = array_search('nombre', $headers);
        $telefonoIndex = array_search('telefono', $headers);

        if ($nombreIndex === false || $telefonoIndex === false) {
            return back()->with('error', 'El archivo debe contener las columnas "nombre" y "telefono".');
        }

        $insertados = 0;

        foreach (array_slice($rows, $startRow - 1) as $row) {
            $name = $row[$nombreIndex] ?? null;
            $phone = $row[$telefonoIndex] ?? null;

            if ($name && $phone) {
                $contact = WhatsappContacts::create([
                    'name' => $name,
                    'phone' => $phone,
                ]);

                $contact->wid = 'W' . $contact->id;
                $contact->save();

                $insertados++;
            }
        }


        return back()->with('success', "Se han importado $insertados contactos desde la fila $startRow.");
    } catch (\Exception $e) {
        return back()->with('error', 'Error al procesar el archivo: ' . $e->getMessage());
    }
}
}
