<?php

namespace App\Services;

class PasswordGeneratorService
{
    /**
     * Parte fija de la contraseña (clave maestra)
     * Si alguien descubre esto, podría generar tus claves, ¡cuídala!
     */
    private const PARTE_FIJA = "H11+&401m$Kva";

    /**
     * Genera una contraseña determinista basada en un dominio
     *
     * @param string $dominio El dominio para el cual generar la contraseña
     * @return array ['dominio_limpio' => string, 'password' => string]
     */
    public function generarPasswordDinamica(string $dominio): array
    {
        // 1. Limpieza del dominio (Estandarización)
        // Convertimos a minúsculas y quitamos basura para evitar errores tontos
        $dominioLimpio = strtolower(trim($dominio));

        // Quitamos protocolos y www (en orden para evitar problemas)
        $dominioLimpio = str_replace('https://', '', $dominioLimpio);
        $dominioLimpio = str_replace('http://', '', $dominioLimpio);
        $dominioLimpio = str_replace('www.', '', $dominioLimpio);

        // Si el dominio tiene barra al final (maruja.com/), la quitamos
        if (substr($dominioLimpio, -1) === '/') {
            $dominioLimpio = substr($dominioLimpio, 0, -1);
        }

        // 2. Magia Matemática (HMAC-SHA256)
        // Usamos la PARTE_FIJA como "llave" para firmar el dominio
        $mensaje = $dominioLimpio;
        $llave = self::PARTE_FIJA;

        $hash = hash_hmac('sha256', $mensaje, $llave, true);

        // 3. Convertir a caracteres legibles (Base64)
        // Esto nos da una mezcla de letras y números
        $passBase64 = base64_encode($hash);

        // 4. Personalización del resultado
        // Base64 usa '+' y '/' que a veces dan problemas en URLs o scripts.
        // Los cambiamos por símbolos más amigables como '*' y '!' o lo que quieras.
        $passSegura = str_replace(['+', '/'], ['*', '!'], $passBase64);

        // 5. CONSTRUCCIÓN FINAL
        // Tomamos los primeros 10 caracteres del hash generado
        $sufijoDinamico = substr($passSegura, 0, 10);

        // Opcional: Forzar un símbolo especial extra al final para asegurar requisitos paranoicos
        $resultado = self::PARTE_FIJA . $sufijoDinamico;

        return [
            'dominio_limpio' => $dominioLimpio,
            'password' => $resultado
        ];
    }
}
