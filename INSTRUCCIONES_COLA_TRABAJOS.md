# Instrucciones para Configurar la Cola de Trabajos (Queue)

## ✅ Implementación Completada

Se ha implementado exitosamente el procesamiento **asíncrono** del contexto empresarial usando Laravel Jobs.

---

## 📋 Configuración Necesaria

### Opción 1: Usar `database` como driver de cola (RECOMENDADO para desarrollo)

1. **Crear la tabla de jobs en la base de datos:**
   ```bash
   php artisan queue:table
   php artisan migrate
   ```

2. **Configurar el driver en `.env`:**
   ```env
   QUEUE_CONNECTION=database
   ```

3. **Iniciar el worker de cola:**
   ```bash
   php artisan queue:work
   ```
   
   **IMPORTANTE**: Este comando debe estar ejecutándose siempre en una terminal separada para procesar los jobs.

---

### Opción 2: Usar `sync` (Para testing - procesa inmediatamente)

Si solo quieres probar sin configurar la cola:

1. **Configurar en `.env`:**
   ```env
   QUEUE_CONNECTION=sync
   ```

**Nota**: Con `sync` el job se ejecutará inmediatamente (no en segundo plano), pero funciona sin worker.

---

## 🎯 Cómo Funciona Ahora

### Flujo Anterior (Síncrono) ❌
```
Usuario crea cliente
       ↓
Espera 30s mientras IA procesa
       ↓
Se guarda en BD
       ↓
Modal se cierra
```
**Problema**: El usuario espera 30 segundos

### Flujo Nuevo (Asíncrono) ✅
```
Usuario crea cliente
       ↓
Se guarda con contexto original (instantáneo)
       ↓
Modal se cierra inmediatamente
       ↓
Job se despacha a la cola
       ↓
[En segundo plano] IA procesa el contexto
       ↓
Se actualiza el registro automáticamente
```
**Ventaja**: El usuario no espera nada, respuesta inmediata

---

## 🔄 Proceso Detallado

### 1. **Crear Cliente**
- Se guarda inmediatamente con el contexto original (texto del usuario)
- Se despacha un Job a la cola
- El modal se cierra
- El usuario ve "Cliente creado correctamente"

### 2. **Job en Cola** (en segundo plano)
- El Job `ProcessCompanyContextJob` se ejecuta
- Llama a la IA local (192.168.1.45:5000)
- Timeout de 45 segundos
- 3 intentos automáticos si falla

### 3. **Actualización Automática**
- Si la IA responde correctamente → Se actualiza el `company_context` en BD
- Si la IA falla → Se mantiene el contexto original
- Todo sucede sin afectar al usuario

---

## 📊 Logs del Sistema

El sistema genera logs detallados:

```
📤 Despachando Job para procesar contexto del cliente ID: 123
🤖 [Job] Procesando contexto empresarial para Autoseo ID: 123
📝 [Job] Texto original (1500 caracteres)
✅ [Job] Contexto procesado y actualizado (1020 caracteres)
```

Ver logs en: `storage/logs/laravel.log`

---

## 🛠️ Comandos Útiles

### Ver jobs en cola:
```bash
php artisan queue:monitor database
```

### Procesar un solo job (para testing):
```bash
php artisan queue:work --once
```

### Ver jobs fallidos:
```bash
php artisan queue:failed
```

### Reintentar jobs fallidos:
```bash
php artisan queue:retry all
```

### Limpiar jobs fallidos:
```bash
php artisan queue:flush
```

---

## 🚀 Puesta en Producción

### Opción 1: Supervisor (Linux)

Crear archivo `/etc/supervisor/conf.d/laravel-worker.conf`:

```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /ruta/a/tu/proyecto/artisan queue:work database --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/ruta/a/tu/proyecto/storage/logs/worker.log
```

Luego:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start laravel-worker:*
```

### Opción 2: Cron (fallback)

Agregar a crontab:
```bash
* * * * * cd /ruta/proyecto && php artisan schedule:run >> /dev/null 2>&1
```

Y en `app/Console/Kernel.php`:
```php
$schedule->command('queue:work --stop-when-empty')->everyMinute();
```

---

## ⚙️ Configuración del Job

El Job `ProcessCompanyContextJob` tiene:

- **Timeout**: 60 segundos
- **Intentos**: 3
- **IA Timeout**: 45 segundos
- **Endpoint**: http://192.168.1.45:5000/chat
- **Modelo**: gpt-oss

---

## 🧪 Testing

### 1. Crear un cliente con contexto largo (>1500 chars)

### 2. Verificar que:
- ✅ El modal se cierra inmediatamente
- ✅ El cliente aparece en la lista con el contexto original
- ✅ En 30-60 segundos, el contexto se actualiza automáticamente (refresh la página)

### 3. Ver los logs:
```bash
tail -f storage/logs/laravel.log
```

---

## 🔍 Troubleshooting

### El contexto no se procesa:
1. Verificar que el worker está corriendo: `ps aux | grep "queue:work"`
2. Ver logs: `tail -f storage/logs/laravel.log`
3. Verificar conexión con IA: `curl http://192.168.1.45:5000/chat`

### Jobs no se ejecutan:
1. Verificar `QUEUE_CONNECTION` en `.env`
2. Verificar que existe la tabla `jobs`: `php artisan migrate`
3. Iniciar worker: `php artisan queue:work`

### IA no responde:
- El sistema mantiene el contexto original automáticamente
- Revisar logs para ver el error específico
- El Job se reintentará 3 veces

---

## 📝 Resumen

**Antes**: Usuario espera 30s → ❌  
**Ahora**: Usuario espera 0s → ✅  

El procesamiento con IA sucede transparentemente en segundo plano sin afectar la experiencia del usuario.

