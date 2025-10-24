# Sistema de Justificaciones - Implementación del Servidor Receptor

## Flujo Correcto del Sistema

```
Usuario CRM → Ingresa URL → CRM crea registro → POST a tu servidor
                                                         ↓
Tu servidor recibe URL → Procesa/genera archivos → POST de vuelta al CRM
                                                         ↓
                            CRM almacena archivos ← ZIP descargable
```

## 1. Endpoint que Debes Implementar

**URL:** `https://aiapi.hawkins.es/sgbasc`  
**Método:** `POST`  
**Content-Type:** `application/json`

### Request que Recibirás

```json
{
    "url": "https://ejemplo.com/pagina-web",
    "justificacion_id": 123,
    "user_id": 45,
    "user_name": "Juan Pérez",
    "nombre_justificacion": "Segunda Justificacion Presencia Basica",
    "tipo_justificacion": "segunda_justificacion_presencia_basica",
    "callback_url": "https://crm.hawkins.es/justificaciones/receive/123",
    "timestamp": "2025-10-24 08:45:30"
}
```

### Campos Clave

- **url**: URL que el usuario ingresó - debes procesar esta URL
- **justificacion_id**: ID único para identificar la justificación
- **callback_url**: Endpoint del CRM donde DEBES enviar los archivos generados

## 2. Lo Que Debes Hacer

### Paso 1: Recibir la petición
```python
@app.route('/sgbasc', methods=['POST'])
def recibir_justificacion():
    data = request.get_json()
    url = data['url']
    justificacion_id = data['justificacion_id']
    callback_url = data['callback_url']
    
    # Procesar de forma asíncrona (usar cola/worker)
    procesar_url_async(url, justificacion_id, callback_url)
    
    return jsonify({'success': True}), 200
```

### Paso 2: Procesar la URL y Generar Archivos

Según tu lógica de negocio, debes generar 3 archivos:
- `just` - Archivo de justificación
- `titularidad` - Archivo de titularidad  
- `publicidad` - Archivo de publicidad

```python
def procesar_url_async(url, justificacion_id, callback_url):
    # AQUÍ TU LÓGICA:
    # 1. Scraping/análisis de la URL
    # 2. Generación de documentos PDF/imágenes
    # 3. Procesamiento con IA
    # etc.
    
    archivo_just = generar_justificacion(url)
    archivo_titularidad = generar_titularidad(url)
    archivo_publicidad = generar_publicidad(url)
    
    # Enviar archivos de vuelta al CRM
    enviar_archivos_al_crm(
        callback_url,
        archivo_just,
        archivo_titularidad,
        archivo_publicidad
    )
```

### Paso 3: Enviar Archivos de Vuelta al CRM

```python
import requests

def enviar_archivos_al_crm(callback_url, archivo_just, archivo_titularidad, archivo_publicidad):
    """
    Envía los 3 archivos generados de vuelta al CRM
    """
    files = {
        'archivo_just': open(archivo_just, 'rb'),
        'archivo_titularidad': open(archivo_titularidad, 'rb'),
        'archivo_publicidad': open(archivo_publicidad, 'rb')
    }
    
    response = requests.post(callback_url, files=files)
    
    if response.status_code == 200:
        print(f"✅ Archivos enviados correctamente al CRM")
    else:
        print(f"❌ Error enviando archivos: {response.status_code}")
    
    # Cerrar archivos
    for f in files.values():
        f.close()
```

## 3. Ejemplo Completo

```python
from flask import Flask, request, jsonify
import requests
import threading

app = Flask(__name__)

@app.route('/sgbasc', methods=['POST'])
def recibir_justificacion():
    data = request.get_json()
    
    url = data['url']
    justificacion_id = data['justificacion_id']
    callback_url = data['callback_url']
    
    # Procesar en segundo plano para no bloquear la respuesta
    thread = threading.Thread(
        target=procesar_y_enviar,
        args=(url, justificacion_id, callback_url)
    )
    thread.start()
    
    return jsonify({
        'success': True,
        'message': 'Procesando URL...'
    }), 200

def procesar_y_enviar(url, justificacion_id, callback_url):
    """
    Procesa la URL y envía archivos al CRM
    """
    try:
        print(f"📝 Procesando: {url}")
        
        # AQUÍ TU LÓGICA DE PROCESAMIENTO
        # Por ejemplo:
        archivo_just = f'/tmp/just_{justificacion_id}.pdf'
        archivo_titularidad = f'/tmp/titularidad_{justificacion_id}.pdf'
        archivo_publicidad = f'/tmp/publicidad_{justificacion_id}.jpg'
        
        # Generar tus archivos...
        generar_documentos(url, archivo_just, archivo_titularidad, archivo_publicidad)
        
        # Enviar al CRM
        files = {
            'archivo_just': ('justificacion.pdf', open(archivo_just, 'rb'), 'application/pdf'),
            'archivo_titularidad': ('titularidad.pdf', open(archivo_titularidad, 'rb'), 'application/pdf'),
            'archivo_publicidad': ('publicidad.jpg', open(archivo_publicidad, 'rb'), 'image/jpeg')
        }
        
        response = requests.post(callback_url, files=files)
        
        if response.status_code == 200:
            print(f"✅ Archivos enviados para justificación {justificacion_id}")
        else:
            print(f"❌ Error: {response.status_code}")
            
        # Limpiar archivos temporales
        for name, (_, file_obj, _) in files.items():
            file_obj.close()
            
    except Exception as e:
        print(f"❌ Error procesando: {e}")

def generar_documentos(url, archivo_just, archivo_titularidad, archivo_publicidad):
    """
    IMPLEMENTAR: Tu lógica para generar los archivos
    
    Ejemplos de lo que podrías hacer:
    - Scraping de la URL
    - Captura de screenshot
    - Generación de PDF con información
    - Análisis de contenido
    - Procesamiento con IA
    """
    # Placeholder - reemplazar con tu lógica
    with open(archivo_just, 'wb') as f:
        f.write(b'PDF de justificacion')
    with open(archivo_titularidad, 'wb') as f:
        f.write(b'PDF de titularidad')
    with open(archivo_publicidad, 'wb') as f:
        f.write(b'Imagen de publicidad')

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000)
```

## 4. Endpoints del CRM

### Recibir archivos (callback)
```
POST https://crm.hawkins.es/justificaciones/receive/{id}
Content-Type: multipart/form-data

Campos:
- archivo_just: File
- archivo_titularidad: File
- archivo_publicidad: File
```

### Respuesta esperada:
```json
{
    "success": true,
    "message": "Archivos recibidos correctamente"
}
```

## 5. Testing

### Test del endpoint de recepción:
```bash
curl -X POST https://aiapi.hawkins.es/sgbasc \
  -H "Content-Type: application/json" \
  -d '{
    "url": "https://ejemplo.com",
    "justificacion_id": 1,
    "callback_url": "https://crm.hawkins.es/justificaciones/receive/1"
  }'
```

### Test de envío de archivos al CRM:
```bash
curl -X POST https://crm.hawkins.es/justificaciones/receive/123 \
  -F "archivo_just=@justificacion.pdf" \
  -F "archivo_titularidad=@titularidad.pdf" \
  -F "archivo_publicidad=@publicidad.jpg"
```

## 6. Diagrama de Flujo Completo

```
┌─────────────┐
│ Usuario CRM │
└──────┬──────┘
       │ 1. Ingresa URL y clic en "Enviar"
       ▼
┌───────────────────────────────────┐
│ CRM                               │
│ - Crea registro en BD             │
│ - Estado: "pendiente"             │
└──────┬────────────────────────────┘
       │ 2. POST /sgbasc
       │    {url, justificacion_id, callback_url}
       ▼
┌────────────────────────────────────┐
│ TU SERVIDOR (aiapi.hawkins.es)    │
│ - Recibe URL                       │
│ - Procesa en background            │
│ - Genera 3 archivos                │
└──────┬─────────────────────────────┘
       │ 3. POST {callback_url}
       │    archivos: just, titularidad, publicidad
       ▼
┌────────────────────────────────────┐
│ CRM                                │
│ - Recibe archivos                  │
│ - Almacena en storage              │
│ - Estado: "completado"             │
│ - Usuario puede descargar ZIP      │
└────────────────────────────────────┘
```

## 7. Estados de la Justificación

En el CRM, cada justificación tiene un estado:

- **pendiente**: Creada, esperando procesamiento
- **procesando**: Tu servidor está trabajando
- **completado**: Archivos recibidos y almacenados
- **error**: Algo falló

## 8. Consideraciones

### ⚠️ Importante
- Procesar de forma **asíncrona** (no bloquear la respuesta HTTP)
- Usar **colas** (Celery, RabbitMQ, Redis) para procesamiento largo
- **Reintentar** si falla el envío al callback_url
- **Validar** que la URL es accesible antes de procesarla
- **Timeout**: No dejar procesando indefinidamente

### 🔒 Seguridad
- Validar que las peticiones vienen del CRM
- Implementar token de autenticación
- Rate limiting
- Validar URLs (no permitir localhost, IPs privadas, etc.)

### 📊 Logging
```python
import logging

logging.info(f"Nueva justificación {justificacion_id} para URL: {url}")
logging.info(f"Archivos generados correctamente")
logging.info(f"Enviando archivos a: {callback_url}")
logging.error(f"Error en justificación {justificacion_id}: {error}")
```

## Resumen

1. **Recibes**: POST con URL + callback_url
2. **Procesas**: Generas 3 archivos según la URL
3. **Envías**: POST multipart al callback_url con los archivos
4. **Listo**: El usuario puede descargar ZIP desde el CRM
