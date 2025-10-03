<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\VisitaComercial;
use Illuminate\Support\Str;

class VisitaAudioController extends Controller
{
    /**
     * Subir archivo de audio para una visita
     */
    public function uploadAudio(Request $request)
    {
        try {
            $request->validate([
                'visita_id' => 'required|exists:visita_comercials,id',
                'audio' => 'required|file|mimes:mp3,wav,ogg,m4a,webm|max:102400', // 100MB máximo
                'duration' => 'nullable|integer|min:1'
            ]);

            $visita = VisitaComercial::findOrFail($request->visita_id);

            // Verificar que el usuario tenga permisos para esta visita
            if (auth()->id() !== $visita->comercial_id && auth()->user()->access_level_id != 3) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permisos para subir audio a esta visita'
                ], 403);
            }

            $audioFile = $request->file('audio');
            $duration = $request->duration ?? 0;

            // Generar nombre único para el archivo
            $extension = $audioFile->getClientOriginalExtension();
            $filename = 'visita_' . $visita->id . '_' . time() . '_' . Str::random(8) . '.' . $extension;
            
            // Guardar en storage/app/public/visitas_audio/
            $path = $audioFile->storeAs('visitas_audio', $filename, 'public');

            // Actualizar la visita con la información del audio
            $visita->update([
                'audio_file' => $path,
                'audio_duration' => $duration,
                'audio_recorded_at' => now()
            ]);

            Log::info('Audio subido para visita:', [
                'visita_id' => $visita->id,
                'comercial_id' => $visita->comercial_id,
                'audio_file' => $path,
                'duration' => $duration
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Audio subido exitosamente',
                'audio' => [
                    'file' => $path,
                    'duration' => $duration,
                    'recorded_at' => $visita->audio_recorded_at->format('Y-m-d H:i:s'),
                    'url' => Storage::url($path)
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error subiendo audio:', [
                'error' => $e->getMessage(),
                'visita_id' => $request->visita_id ?? null
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al subir el audio: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener URL del audio de una visita
     */
    public function getAudioUrl($visitaId)
    {
        try {
            $visita = VisitaComercial::findOrFail($visitaId);

            // Verificar permisos
            if (auth()->id() !== $visita->comercial_id && auth()->user()->access_level_id != 3) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permisos para acceder a este audio'
                ], 403);
            }

            if (!$visita->audio_file) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay audio disponible para esta visita'
                ], 404);
            }

            // Verificar que el archivo existe
            if (!Storage::disk('public')->exists($visita->audio_file)) {
                return response()->json([
                    'success' => false,
                    'message' => 'El archivo de audio no existe'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'audio' => [
                    'url' => Storage::url($visita->audio_file),
                    'duration' => $visita->audio_duration,
                    'recorded_at' => $visita->audio_recorded_at?->format('Y-m-d H:i:s'),
                    'file_size' => Storage::disk('public')->size($visita->audio_file)
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error obteniendo audio:', [
                'error' => $e->getMessage(),
                'visita_id' => $visitaId
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el audio: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar audio de una visita
     */
    public function deleteAudio($visitaId)
    {
        try {
            $visita = VisitaComercial::findOrFail($visitaId);

            // Verificar permisos
            if (auth()->id() !== $visita->comercial_id && auth()->user()->access_level_id != 3) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permisos para eliminar este audio'
                ], 403);
            }

            if (!$visita->audio_file) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay audio para eliminar'
                ], 404);
            }

            // Eliminar archivo físico
            if (Storage::disk('public')->exists($visita->audio_file)) {
                Storage::disk('public')->delete($visita->audio_file);
            }

            // Limpiar campos en la base de datos
            $visita->update([
                'audio_file' => null,
                'audio_duration' => null,
                'audio_recorded_at' => null
            ]);

            Log::info('Audio eliminado para visita:', [
                'visita_id' => $visita->id,
                'comercial_id' => $visita->comercial_id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Audio eliminado exitosamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error eliminando audio:', [
                'error' => $e->getMessage(),
                'visita_id' => $visitaId
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el audio: ' . $e->getMessage()
            ], 500);
        }
    }
}