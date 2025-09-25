# Sincronización de Dominios Faltantes de IONOS

## Comandos Disponibles

### 1. Analizar Dominios Faltantes
```bash
# Analizar qué dominios de IONOS no existen en la tabla local
php artisan ionos:analyze-missing --limit=100

# Con detalles completos de cada dominio
php artisan ionos:analyze-missing --limit=50 --show-details
```

### 2. Sincronizar Dominios Faltantes (Lote)
```bash
# Modo DRY RUN - Solo mostrar qué se crearía
php artisan ionos:sync-missing --dry-run --limit=50

# Crear dominios faltantes con cliente por defecto
php artisan ionos:sync-missing --client-id=1 --limit=50

# Crear dominios faltantes sin especificar cliente (usa ID 1)
php artisan ionos:sync-missing --limit=50
```

### 3. Sincronizar TODOS los Dominios Faltantes
```bash
# Modo DRY RUN - Analizar todos los dominios faltantes
php artisan ionos:sync-all-missing --dry-run

# Crear TODOS los dominios faltantes
php artisan ionos:sync-all-missing --client-id=1

# Con tamaño de lote personalizado
php artisan ionos:sync-all-missing --client-id=1 --batch-size=100
```

## Parámetros Disponibles

### ionos:analyze-missing
- `--limit=100`: Límite de dominios a analizar
- `--offset=0`: Offset inicial para la paginación
- `--show-details`: Mostrar detalles completos de cada dominio

### ionos:sync-missing
- `--limit=50`: Límite de dominios a procesar por página
- `--offset=0`: Offset inicial para la paginación
- `--dry-run`: Solo mostrar qué dominios se añadirían sin crear
- `--client-id=`: ID del cliente por defecto para nuevos dominios

### ionos:sync-all-missing
- `--client-id=`: ID del cliente por defecto para nuevos dominios
- `--batch-size=50`: Tamaño del lote para procesar
- `--dry-run`: Solo mostrar qué dominios se añadirían sin crear

## Flujo de Trabajo Recomendado

### 1. Análisis Inicial
```bash
# Ver cuántos dominios faltan
php artisan ionos:analyze-missing --limit=100
```

### 2. Prueba con Lote Pequeño
```bash
# Probar con un lote pequeño en modo DRY RUN
php artisan ionos:sync-missing --dry-run --limit=20

# Si todo está bien, crear el lote pequeño
php artisan ionos:sync-missing --client-id=1 --limit=20
```

### 3. Sincronización Completa
```bash
# Sincronizar todos los dominios faltantes
php artisan ionos:sync-all-missing --client-id=1
```

## Características de los Dominios Creados

Los dominios creados tendrán:

- **Dominio**: Nombre del dominio de IONOS
- **Cliente**: ID especificado o 1 por defecto
- **Fecha de inicio**: Fecha de activación calculada de IONOS
- **Fecha de fin**: Fecha de renovación de IONOS
- **Estado**: 1 (activo) por defecto
- **Comentario**: "Dominio sincronizado desde IONOS"
- **Fechas IONOS**: Fechas de activación y renovación de IONOS
- **Sincronizado IONOS**: true
- **Última sincronización IONOS**: Fecha actual

## Ejemplos de Uso

### Ejemplo 1: Análisis Completo
```bash
php artisan ionos:analyze-missing --limit=200 --show-details
```

### Ejemplo 2: Sincronización Gradual
```bash
# Primero 50 dominios
php artisan ionos:sync-missing --client-id=1 --limit=50

# Luego los siguientes 50
php artisan ionos:sync-missing --client-id=1 --limit=50 --offset=50
```

### Ejemplo 3: Sincronización Completa
```bash
php artisan ionos:sync-all-missing --client-id=1 --batch-size=100
```

## Notas Importantes

1. **Modo DRY RUN**: Siempre usa `--dry-run` primero para ver qué se crearía
2. **Cliente por defecto**: Especifica un `--client-id` válido
3. **Límites de API**: Los comandos incluyen pausas para no sobrecargar la API
4. **Logs**: Los errores se registran en los logs de Laravel
5. **Rendimiento**: Los comandos procesan en lotes para optimizar el rendimiento

## Resolución de Problemas

### Error de Cliente
```
❌ Cliente con ID X no encontrado
```
**Solución**: Verifica que el cliente existe en la base de datos

### Error de API
```
❌ Error al obtener dominios de IONOS
```
**Solución**: Verifica las credenciales de IONOS en `.env`

### Error de Memoria
```
Fatal error: Allowed memory size exhausted
```
**Solución**: Reduce el `--batch-size` o aumenta la memoria de PHP
