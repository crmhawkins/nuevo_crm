# Especificación API - Sistema de Justificaciones

## Contexto del Sistema

El CRM de Hawkins ha implementado un sistema de justificaciones donde los usuarios pueden subir documentos (justificación, titularidad, publicidad) junto con una URL. Estos documentos se almacenan en el servidor del CRM y se envía una notificación a un servidor externo para su procesamiento.

## Endpoint a Implementar

### Servidor: `aiapi.hawkins.es`
### Endpoint: `POST /sgbasc`

---

## 1. Petición que Recibirás

### Headers
```
Content-Type: application/json
```

### Body (JSON)
```json
{
    "url": "https://ejemplo.com/pagina-web"
}
```

### Descripción de Campos
- `url` (string, requerido): URL proporcionada por el usuario en el campo "URL" del formulario de justificaciones

---

## 2. Flujo Completo del Sistema

```
┌─────────────────┐
│   Usuario CRM   │
│  (Dashboard)    │
└────────┬────────┘
         │
         │ 1. Selecciona tipo: "Segunda Justificacion Presencia Basica"
         │ 2. Ingresa URL
         │ 3. Sube 3 archivos (just, titularidad, publicidad)
         │ 4. Clic en "Enviar"
         │
         ▼
┌─────────────────────────────────────────────────────────┐
│  Servidor CRM (nuevo_crm)                               │
│  Route: POST /justificaciones/store                     │
│  Controller: JustificacionesController@store            │
│                                                          │
│  Acciones:                                              │
│  1. Guarda archivos en: storage/app/public/             │
│     justificaciones/{user_id}/                          │
│  2. Crea registro en BD (tabla: justificacions)         │
│  3. Envía POST a aiapi.hawkins.es/sgbasc con {url}     │
└────────┬────────────────────────────────────────────────┘
         │
         │ POST Request
         │
         ▼
┌─────────────────────────────────────────────────────────┐
│  TU SERVIDOR (aiapi.hawkins.es)                         │
│  Endpoint: POST /sgbasc                                 │
│                                                          │
│  ⚠️  IMPLEMENTAR AQUÍ ⚠️                                 │
└─────────────────────────────────────────────────────────┘
```

---

## 3. Implementación Requerida en `aiapi.hawkins.es/sgbasc`

### Opción A: Solo Notificación (Implementación Básica)

Si solo necesitas recibir la notificación de que se creó una justificación:

```python
# Ejemplo en Python/Flask
from flask import Flask, request, jsonify

app = Flask(__name__)

@app.route('/sgbasc', methods=['POST'])
def recibir_justificacion():
    data = request.get_json()
    
    # Obtener URL del request
    url = data.get('url')
    
    if not url:
        return jsonify({'error': 'URL no proporcionada'}), 400
    
    # AQUÍ: Procesar la URL como necesites
    # Ejemplos:
    # - Guardar en base de datos
    # - Enviar a cola de procesamiento
    # - Validar la URL
    # - Generar un reporte
    
    print(f"Nueva justificación recibida para URL: {url}")
    
    return jsonify({
        'success': True,
        'message': 'Justificación recibida correctamente'
    }), 200
```

### Opción B: Solicitar los Archivos al CRM (Implementación Avanzada)

Si necesitas obtener los archivos para procesarlos:

```python
# Ejemplo en Python/Flask
from flask import Flask, request, jsonify
import requests
import os

app = Flask(__name__)

CRM_BASE_URL = "https://crm.hawkins.es"  # URL del CRM
CRM_API_TOKEN = "tu_token_secreto_aqui"  # Token de autenticación

@app.route('/sgbasc', methods=['POST'])
def recibir_justificacion():
    data = request.get_json()
    url = data.get('url')
    justificacion_id = data.get('justificacion_id')  # Si se envía
    
    if not url:
        return jsonify({'error': 'URL no proporcionada'}), 400
    
    # Paso 1: Registrar la notificación
    print(f"Nueva justificación recibida para URL: {url}")
    
    # Paso 2: (OPCIONAL) Solicitar archivos al CRM si se proporcionó ID
    if justificacion_id:
        archivos = descargar_archivos_desde_crm(justificacion_id)
        procesar_archivos(archivos, url)
    
    return jsonify({
        'success': True,
        'message': 'Justificación procesada'
    }), 200

def descargar_archivos_desde_crm(justificacion_id):
    """
    Descarga el ZIP de archivos desde el CRM
    """
    headers = {
        'Authorization': f'Bearer {CRM_API_TOKEN}'
    }
    
    response = requests.get(
        f"{CRM_BASE_URL}/justificaciones/download/{justificacion_id}",
        headers=headers,
        stream=True
    )
    
    if response.status_code == 200:
        # Guardar ZIP temporalmente
        zip_path = f"/tmp/justificacion_{justificacion_id}.zip"
        with open(zip_path, 'wb') as f:
            for chunk in response.iter_content(chunk_size=8192):
                f.write(chunk)
        
        return zip_path
    
    return None

def procesar_archivos(zip_path, url):
    """
    Procesar los archivos descargados
    """
    import zipfile
    
    # Extraer archivos del ZIP
    extract_path = f"/tmp/justificacion_extraida/"
    os.makedirs(extract_path, exist_ok=True)
    
    with zipfile.ZipFile(zip_path, 'r') as zip_ref:
        zip_ref.extractall(extract_path)
    
    # AQUÍ: Procesar cada archivo
    # - just_*.* (archivo de justificación)
    # - titularidad_*.* (archivo de titularidad)
    # - publicidad_*.* (archivo de publicidad)
    # - info.txt (metadata con la URL)
    
    print(f"Archivos procesados para URL: {url}")
    
    # Limpiar archivos temporales
    os.remove(zip_path)
```

### Opción C: Ejemplo en Node.js/Express

```javascript
const express = require('express');
const axios = require('axios');
const fs = require('fs');
const AdmZip = require('adm-zip');

const app = express();
app.use(express.json());

const CRM_BASE_URL = 'https://crm.hawkins.es';
const CRM_API_TOKEN = 'tu_token_secreto_aqui';

app.post('/sgbasc', async (req, res) => {
    const { url, justificacion_id } = req.body;
    
    if (!url) {
        return res.status(400).json({ error: 'URL no proporcionada' });
    }
    
    console.log(`Nueva justificación recibida para URL: ${url}`);
    
    // OPCIONAL: Descargar y procesar archivos
    if (justificacion_id) {
        try {
            await descargarYProcesarArchivos(justificacion_id, url);
        } catch (error) {
            console.error('Error procesando archivos:', error);
        }
    }
    
    res.json({
        success: true,
        message: 'Justificación procesada correctamente'
    });
});

async function descargarYProcesarArchivos(justificacionId, url) {
    // Descargar ZIP desde el CRM
    const response = await axios({
        method: 'get',
        url: `${CRM_BASE_URL}/justificaciones/download/${justificacionId}`,
        headers: {
            'Authorization': `Bearer ${CRM_API_TOKEN}`
        },
        responseType: 'arraybuffer'
    });
    
    // Guardar ZIP temporalmente
    const zipPath = `/tmp/justificacion_${justificacionId}.zip`;
    fs.writeFileSync(zipPath, response.data);
    
    // Extraer archivos
    const zip = new AdmZip(zipPath);
    const zipEntries = zip.getEntries();
    
    zipEntries.forEach(entry => {
        console.log(`Archivo: ${entry.entryName}`);
        // AQUÍ: Procesar cada archivo según sea necesario
        
        if (entry.entryName.startsWith('just_')) {
            // Procesar archivo de justificación
        } else if (entry.entryName.startsWith('titularidad_')) {
            // Procesar archivo de titularidad
        } else if (entry.entryName.startsWith('publicidad_')) {
            // Procesar archivo de publicidad
        } else if (entry.entryName === 'info.txt') {
            // Leer metadata
            const content = entry.getData().toString('utf8');
            console.log('Metadata:', content);
        }
    });
    
    // Limpiar
    fs.unlinkSync(zipPath);
}

app.listen(3000, () => {
    console.log('Servidor escuchando en puerto 3000');
});
```

---

## 4. Mejora Sugerida: Enviar ID de Justificación

Para facilitar la descarga de archivos, podemos modificar el CRM para que también envíe el ID de la justificación:

### Modificación en el CRM (JustificacionesController.php):

```php
// Línea ~75 del controlador actual
try {
    Http::post('https://aiapi.hawkins.es/sgbasc', [
        'url' => $request->input('url_campo'),
        'justificacion_id' => $justificacion->id,  // ⬅️ AÑADIR ESTO
        'user_id' => $user->id,
        'nombre' => $request->nombre_justificacion,
        'timestamp' => now()->toDateTimeString()
    ]);
} catch (\Exception $e) {
    \Log::error('Error al enviar a aiapi.hawkins.es: ' . $e->getMessage());
}
```

Entonces recibirás:

```json
{
    "url": "https://ejemplo.com/pagina",
    "justificacion_id": 123,
    "user_id": 45,
    "nombre": "Segunda Justificacion Presencia Basica",
    "timestamp": "2025-10-24 08:45:30"
}
```

---

## 5. Estructura de Archivos en el ZIP

Cuando descargues el ZIP, encontrarás:

```
justificacion_123.zip
├── just_archivo_original.pdf
├── titularidad_documento.pdf
├── publicidad_imagen.jpg
└── info.txt
```

**Contenido de info.txt:**
```
INFORMACIÓN ADICIONAL

URL: https://ejemplo.com/pagina-web
```

---

## 6. Respuestas Esperadas

### Éxito (200 OK)
```json
{
    "success": true,
    "message": "Justificación recibida correctamente"
}
```

### Error (400 Bad Request)
```json
{
    "error": "URL no proporcionada"
}
```

### Error (500 Internal Server Error)
```json
{
    "error": "Error interno del servidor",
    "details": "Descripción del error"
}
```

---

## 7. Consideraciones de Seguridad

1. **Validar la URL recibida**: Asegúrate de que sea una URL válida
2. **Autenticación**: Considera implementar un token de autenticación compartido
3. **Rate Limiting**: Implementa límites de peticiones
4. **Validación de origen**: Verifica que las peticiones vengan del servidor del CRM
5. **Logs**: Registra todas las peticiones para auditoría

### Ejemplo con Autenticación:

```python
from flask import Flask, request, jsonify

API_SECRET = "tu_clave_secreta_compartida_con_crm"

@app.route('/sgbasc', methods=['POST'])
def recibir_justificacion():
    # Verificar token de autenticación
    auth_token = request.headers.get('Authorization')
    
    if not auth_token or auth_token != f"Bearer {API_SECRET}":
        return jsonify({'error': 'No autorizado'}), 401
    
    data = request.get_json()
    url = data.get('url')
    
    # ... resto del código
```

---

## 8. Testing del Endpoint

### Usando cURL:
```bash
curl -X POST https://aiapi.hawkins.es/sgbasc \
  -H "Content-Type: application/json" \
  -d '{"url": "https://ejemplo.com/test"}'
```

### Usando Python:
```python
import requests

response = requests.post(
    'https://aiapi.hawkins.es/sgbasc',
    json={'url': 'https://ejemplo.com/test'}
)

print(response.json())
```

### Usando JavaScript (Fetch):
```javascript
fetch('https://aiapi.hawkins.es/sgbasc', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json'
    },
    body: JSON.stringify({
        url: 'https://ejemplo.com/test'
    })
})
.then(response => response.json())
.then(data => console.log(data));
```

---

## 9. Casos de Uso Comunes

### A. Solo necesitas la URL
- Guardar en base de datos
- Añadir a cola de procesamiento
- Validar disponibilidad
- Generar reporte básico

### B. Necesitas los archivos también
- Análisis de documentos
- Validación de contenido
- Almacenamiento en sistema externo
- Procesamiento con IA/OCR

---

## Resumen

**Lo mínimo que necesitas implementar:**
```python
@app.route('/sgbasc', methods=['POST'])
def recibir_justificacion():
    url = request.json.get('url')
    # Procesar URL
    return jsonify({'success': True}), 200
```

**Si necesitas los archivos:**
1. Solicita al usuario del CRM que modifique el controlador para enviar `justificacion_id`
2. Usa ese ID para descargar el ZIP con los archivos
3. Procesa los archivos según tus necesidades

