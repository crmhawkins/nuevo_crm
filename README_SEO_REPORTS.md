# Sistema de Generación de Informes SEO

Este sistema convierte el script Python original de generación de informes SEO a un controlador Laravel que genera informes HTML usando Blade.

## Características

-   ✅ Descarga automática de datos JSON desde el servidor
-   ✅ Procesamiento de keywords (Short Tail, Long Tail, Detalles)
-   ✅ Análisis de People Also Ask (PAA)
-   ✅ Datos de Search Console mensuales
-   ✅ Generación de gráficos con Chart.js
-   ✅ Tablas comparativas
-   ✅ Envío automático al servidor
-   ✅ Interfaz web para generación
-   ✅ Comando Artisan para línea de comandos
-   ✅ Jobs para procesamiento en segundo plano

## Estructura de Archivos

```
app/
├── Http/Controllers/Autoseo/
│   └── AutoseoReportsGen.php          # Controlador principal
├── Console/Commands/
│   └── GenerateSeoReport.php          # Comando Artisan
├── Jobs/
│   └── GenerateSeoReportJob.php       # Job para procesamiento en segundo plano
└── resources/views/autoseo/
    ├── report-template.blade.php      # Template del informe HTML
    └── generate-report.blade.php      # Vista de generación
```

## Uso

### 1. Interfaz Web

Accede a la interfaz web para generar informes:

```
GET/POST /autoseo/generate-report/{id?}
```

-   **GET**: Muestra el formulario de generación
-   **POST**: Procesa la generación del informe

### 2. Comando Artisan

Genera informes desde la línea de comandos:

```bash
# Generar informe con ID por defecto (15)
php artisan seo:generate-report

# Generar informe con ID específico
php artisan seo:generate-report 25

# Ver ayuda del comando
php artisan seo:generate-report --help
```

### 3. Job en Segundo Plano

Para procesar informes grandes sin bloquear la aplicación:

```php
use App\Jobs\GenerateSeoReportJob;

// Despachar job
GenerateSeoReportJob::dispatch($id, $email);

// O con delay
GenerateSeoReportJob::dispatch($id, $email)->delay(now()->addMinutes(5));
```

## Funcionalidades del Controlador

### AutoseoReportsGen

#### Métodos Principales:

-   `generateReport(Request $request, $id = 15)`: Método principal que maneja GET/POST
-   `downloadAndExtractZip($id)`: Descarga y extrae el ZIP con datos JSON
-   `getAllKeywords($jsonDataList)`: Obtiene todas las keywords únicas
-   `buildChartjsDatasetsFromKeywords($keywords, $jsonDataList)`: Genera datasets para Chart.js
-   `processPaaData($jsonDataList)`: Procesa datos de People Also Ask
-   `processSearchConsoleData($jsonDataList)`: Procesa datos de Search Console
-   `uploadReport($filename, $id)`: Sube el informe al servidor

#### URLs Configuradas:

```php
private $zipUrl = "https://crm.hawkins.es/api/autoseo/json/storage";
private $uploadUrl = "https://crm.hawkins.es/api/autoseo/reports/upload";
```

## Proceso de Generación

1. **Descarga de Datos**: Descarga ZIP con archivos JSON desde el servidor
2. **Extracción**: Extrae y procesa los archivos JSON
3. **Procesamiento de Keywords**:
    - Short Tail keywords
    - Long Tail keywords
    - Detalles de keywords
    - People Also Ask
4. **Datos de Search Console**: Procesa métricas mensuales
5. **Generación de Gráficos**: Crea datasets para Chart.js
6. **Renderizado HTML**: Genera informe usando Blade
7. **Almacenamiento**: Guarda archivo HTML en storage
8. **Envío**: Sube informe al servidor

## Estructura de Datos JSON

El sistema espera archivos JSON con esta estructura:

```json
{
    "dominio": "ejemplo.com",
    "uploaded_at": "2024-01-01",
    "short_tail": ["keyword1", "keyword2"],
    "long_tail": ["long keyword 1", "long keyword 2"],
    "detalles_keywords": [
        {
            "keyword": "keyword1",
            "total_results": 1000000
        }
    ],
    "people_also_ask": [
        {
            "question": "¿Pregunta ejemplo?",
            "total_results": 500000
        }
    ],
    "monthly_performance": {
        "2024-01": {
            "clicks": 1000,
            "impressions": 50000,
            "avg_ctr": 2.0,
            "avg_position": 15.5
        }
    }
}
```

## Configuración

### Storage

Asegúrate de que el directorio de storage esté configurado:

```bash
php artisan storage:link
```

### Colas (Opcional)

Para usar Jobs en segundo plano, configura las colas:

```bash
# Configurar cola de base de datos
php artisan queue:table
php artisan migrate

# Ejecutar worker de colas
php artisan queue:work
```

## Logs

El sistema registra logs detallados:

```php
Log::info("Informe SEO generado correctamente", [
    'id' => $id,
    'filename' => $filename
]);
```

## Errores Comunes

### Error de Descarga

-   Verificar conectividad con el servidor
-   Comprobar que el ID existe
-   Revisar logs de Laravel

### Error de Procesamiento

-   Verificar estructura de JSON
-   Comprobar permisos de storage
-   Revisar memoria disponible

### Error de Envío

-   Verificar conectividad con servidor de destino
-   Comprobar que el archivo se generó correctamente

## Mejoras Futuras

-   [ ] Notificaciones por email
-   [ ] Programación de informes recurrentes
-   [ ] Cache de datos JSON
-   [ ] Compresión de archivos
-   [ ] Múltiples formatos de salida (PDF, Excel)
-   [ ] API REST para integración externa

## Comparación con Script Python Original

| Característica         | Python Script | Laravel Controller |
| ---------------------- | ------------- | ------------------ |
| Descarga ZIP           | ✅            | ✅                 |
| Extracción JSON        | ✅            | ✅                 |
| Procesamiento Keywords | ✅            | ✅                 |
| Gráficos Chart.js      | ✅            | ✅                 |
| Tablas comparativas    | ✅            | ✅                 |
| Search Console         | ✅            | ✅                 |
| Envío al servidor      | ✅            | ✅                 |
| Interfaz web           | ❌            | ✅                 |
| Comando CLI            | ❌            | ✅                 |
| Jobs en segundo plano  | ❌            | ✅                 |
| Logs estructurados     | ❌            | ✅                 |
| Manejo de errores      | Básico        | Avanzado           |

## Contribución

1. Fork el proyecto
2. Crea una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

## Licencia

Este proyecto está bajo la misma licencia que el proyecto principal.
