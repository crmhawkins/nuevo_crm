# Integración con IONOS API

## Configuración

### 1. Variables de Entorno

Agrega las siguientes variables a tu archivo `.env`:

```env
# Configuración de IONOS API
IONOS_API_KEY=bdfc990dd8824274afceeca5a71f142e.hNwfLBjHk9PAHpJpRMw3r2X9276upDOgWak0iwB2kZwU-yMglD4_zcTYaVq3wcTZUTnfAje3lI-GAuGbOxUDFQ
IONOS_TENANT_ID=ivandominios
IONOS_BASE_URL=https://api.ionos.com
```

### 2. Estructura de la API Key

La API key de IONOS tiene el formato: `{PUBLIC_PREFIX}.{SECRET_API_KEY}`

- **PUBLIC_PREFIX**: `bdfc990dd8824274afceeca5a71f142e`
- **SECRET_API_KEY**: `hNwfLBjHk9PAHpJpRMw3r2X9276upDOgWak0iwB2kZwU-yMglD4_zcTYaVq3wcTZUTnfAje3lI-GAuGbOxUDFQ`
- **API_KEY completa**: `bdfc990dd8824274afceeca5a71f142e.hNwfLBjHk9PAHpJpRMw3r2X9276upDOgWak0iwB2kZwU-yMglD4_zcTYaVq3wcTZUTnfAje3lI-GAuGbOxUDFQ`

## Funcionalidades Implementadas

### 1. Nuevas Columnas en la Base de Datos

- `fecha_activacion_ionos`: Fecha de activación del dominio en IONOS
- `fecha_renovacion_ionos`: Fecha de renovación del dominio en IONOS
- `sincronizado_ionos`: Estado de sincronización con IONOS
- `ultima_sincronizacion_ionos`: Última vez que se sincronizó con IONOS

### 2. Servicios Implementados

#### IonosApiService

- `getDomainInfo($domainName)`: Obtener información de un dominio específico
- `getAllDomains()`: Obtener todos los dominios de la cuenta
- `testConnection()`: Probar la conexión con la API

### 3. Rutas Disponibles

- `POST /dominios/sincronizar-ionos/{id}`: Sincronizar un dominio con IONOS
- `GET /dominios/info-ionos/{id}`: Obtener información de IONOS para un dominio
- `GET /dominios/probar-ionos`: Probar la conexión con IONOS

### 4. Comandos Artisan

```bash
# Probar conexión con IONOS
php artisan test:ionos-connection
```

## Uso en la Interfaz

### Vista de Dominio (show.blade.php)

1. **Sección de Información de IONOS**: Muestra las fechas de activación y renovación
2. **Botón "Sincronizar con IONOS"**: Sincroniza el dominio con la API
3. **Botón "Probar Conexión"**: Verifica la conectividad con IONOS
4. **Botón "Ver Info IONOS"**: Muestra información detallada del dominio

### Funcionalidades JavaScript

- `sincronizarIonos(dominioId)`: Sincroniza un dominio con IONOS
- `obtenerInfoIonos(dominioId)`: Obtiene información detallada de IONOS
- `probarConexionIonos()`: Prueba la conexión con la API

## Endpoints de la API de IONOS

### Base URL
```
https://api.ionos.com
```

### Autenticación
```
Authorization: Bearer {API_KEY}
Content-Type: application/json
```

### Endpoints Utilizados

1. **Obtener dominios**:
   ```
   GET /domains
   ```

2. **Obtener dominio específico**:
   ```
   GET /domains?name={domain_name}
   ```

## Respuesta de la API

### Estructura de Respuesta Exitosa

```json
{
  "success": true,
  "domain_name": "ejemplo.com",
  "status": "active",
  "fecha_activacion_ionos": "2023-01-15 10:30:00",
  "fecha_renovacion_ionos": "2024-01-15 10:30:00",
  "registrar": "IONOS",
  "auto_renew": true
}
```

### Estructura de Respuesta de Error

```json
{
  "success": false,
  "message": "Descripción del error"
}
```

## Códigos de Estado HTTP

- **200**: Sitio web funcionando correctamente
- **301/302**: Redirección permanente/temporal
- **404**: Página no encontrada
- **500**: Error interno del servidor
- **ERROR**: No se pudo conectar al dominio

## Manejo de Errores

El sistema maneja automáticamente:

1. **Errores de conexión**: Timeout, DNS, etc.
2. **Errores de autenticación**: API key inválida
3. **Errores de dominio**: Dominio no encontrado en IONOS
4. **Errores de formato**: Fechas inválidas

## Logs

Los errores se registran en:
- `storage/logs/laravel.log`
- Categoría: `IonosApiService`

## Comandos Disponibles

### Comandos Artisan

```bash
# Probar conexión con IONOS
php artisan test:ionos-connection

# Sincronizar todos los dominios (límite de 50 por defecto)
php artisan ionos:sync-all

# Sincronizar con límite personalizado
php artisan ionos:sync-all --limit=100 --offset=0
```

### Endpoints Web

- `POST /dominios/sincronizar-ionos/{id}`: Sincronizar un dominio específico
- `GET /dominios/info-ionos/{id}`: Obtener información de IONOS para un dominio
- `GET /dominios/probar-ionos`: Probar la conexión con IONOS

## Próximos Pasos

1. **Configurar las variables de entorno** en el archivo `.env`
2. **Probar la conexión** con `php artisan test:ionos-connection`
3. **Sincronizar dominios** desde la interfaz web o con comandos
4. **Verificar las fechas** en la vista de dominio

## Notas Importantes

- La API de IONOS puede tener limitaciones de rate limiting
- Las fechas se almacenan en formato `Y-m-d H:i:s`
- La sincronización es manual por dominio
- Se recomienda sincronizar periódicamente para mantener datos actualizados
