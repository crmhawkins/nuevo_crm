# 🚀 Cómo Iniciar el Procesamiento de IA en Segundo Plano

## ⚠️ IMPORTANTE

Para que el contexto empresarial se procese con IA, **DEBES tener el worker de cola ejecutándose**.

---

## 📋 Opción 1: Usar Cola con Base de Datos (RECOMENDADO)

### Paso 1: Ejecutar las migraciones
```bash
php artisan migrate
```

### Paso 2: Verificar configuración en `.env`
Asegúrate de tener esta línea (si no existe, agrégala):
```env
QUEUE_CONNECTION=database
```

### Paso 3: Iniciar el Worker (DEJAR EJECUTANDO)
```bash
php artisan queue:work --tries=3 --timeout=150
```

**⚠️ IMPORTANTE**: Esta terminal debe quedarse ejecutándose todo el tiempo. No la cierres.

---

## 📋 Opción 2: Procesamiento Inmediato (Para Testing)

Si solo quieres probar rápidamente sin configurar cola:

### Editar `.env`
```env
QUEUE_CONNECTION=sync
```

Con `sync`, el job se ejecutará inmediatamente (puede tardar hasta 2 minutos la primera vez).

---

## 🧪 Cómo Probar que Funciona

### 1. Crear un Cliente
1. Abre el formulario de AutoSEO
2. Rellena todos los campos
3. En "Descripción de la Empresa" escribe al menos 100 caracteres (por ejemplo):
   ```
   Empresa dedicada al desarrollo de software empresarial. Ofrecemos soluciones personalizadas de CRM, ERP y gestión comercial. Contamos con más de 10 años de experiencia en el sector tecnológico.
   ```
4. Click en "Guardar Cliente"

### 2. Verificar que se Guardó
- El modal se cierra inmediatamente ✅
- El cliente aparece en la lista ✅

### 3. Ver el Procesamiento (si usas `database` queue)

**Abrir una terminal y ver los logs en vivo:**
```bash
tail -f storage/logs/laravel.log
```

**Deberías ver algo como:**
```
📤 Despachando Job para procesar contexto del cliente ID: 35
🤖 [Job] Procesando contexto empresarial para Autoseo ID: 35
📝 [Job] Texto original (156 caracteres)
✅ [Job] Contexto procesado y actualizado (1050 caracteres)
```

### 4. Verificar el Resultado
- Espera 30-120 segundos
- Refresca la página
- Edita el cliente
- El campo "Descripción de la Empresa" ahora tiene el texto optimizado por IA ✨

---

## 🔍 Troubleshooting

### El contexto no se procesa

#### Problema 1: Worker no está ejecutándose
**Síntoma**: El cliente se crea pero el contexto nunca se actualiza.

**Solución**:
```bash
# Verificar si hay jobs pendientes
php artisan queue:monitor database

# Si hay jobs pendientes, iniciar worker
php artisan queue:work
```

#### Problema 2: IA no responde
**Síntoma**: En los logs ves errores de conexión.

**Solución**:
```bash
# Verificar que la IA está accesible
curl -X POST http://192.168.1.45:5000/chat \
  -H "X-Api-Key: OllamaAPI_2024_K8mN9pQ2rS5tU7vW3xY6zA1bC4eF8hJ0lM" \
  -H "Content-Type: application/json" \
  -d '{"modelo":"gpt-oss:120b-cloud","prompt":"Hola"}'
```

#### Problema 3: QUEUE_CONNECTION no configurado
**Síntoma**: El job no se ejecuta nunca.

**Solución**:
```bash
# Agregar a .env
echo "QUEUE_CONNECTION=database" >> .env

# Limpiar cache
php artisan config:clear
```

---

## 🖥️ Comandos Útiles

### Ver jobs en cola
```bash
php artisan queue:monitor database
```

### Procesar solo un job (testing)
```bash
php artisan queue:work --once
```

### Ver jobs fallidos
```bash
php artisan queue:failed
```

### Reintentar jobs fallidos
```bash
php artisan queue:retry all
```

### Limpiar jobs completados
```bash
php artisan queue:flush
```

### Ver logs en tiempo real
```bash
# Windows PowerShell
Get-Content storage\logs\laravel.log -Wait -Tail 50

# Linux/Mac
tail -f storage/logs/laravel.log
```

---

## ⚡ Inicio Rápido (Copy-Paste)

### En una terminal (dejar ejecutando):
```bash
php artisan migrate
php artisan queue:work --tries=3 --timeout=150
```

### En otra terminal (para ver logs):
```bash
tail -f storage/logs/laravel.log
```

### Luego:
1. Crea un cliente en AutoSEO
2. Observa los logs
3. En 30-120 segundos verás el contexto optimizado

---

## 📊 Flujo Completo

```
Usuario crea cliente
       ↓
[< 1 segundo] Cliente guardado en BD con contexto original
       ↓
[< 1 segundo] Job despachado a la cola
       ↓
[< 1 segundo] Modal se cierra ✅
       ↓
[Worker detecta job] "Hay un job pendiente"
       ↓
[30-120 segundos] IA procesa el contexto
       ↓
[< 1 segundo] BD se actualiza automáticamente
       ↓
Usuario refresca y ve el texto optimizado ✨
```

---

## 🎯 Verificación Final

**¿Funcionó correctamente?**

✅ El modal se cerró inmediatamente  
✅ El cliente aparece en la lista  
✅ En logs viste: "📤 Despachando Job"  
✅ En logs viste: "🤖 [Job] Procesando contexto"  
✅ En logs viste: "✅ [Job] Contexto procesado"  
✅ Al refrescar, el contexto está optimizado  

**Si todos los ✅ están marcados: ¡PERFECTO! 🎉**

---

## 🚨 Nota Importante

**El worker DEBE estar ejecutándose para que funcione la cola.**

Si cierras la terminal donde corre `php artisan queue:work`, los jobs no se procesarán.

Para producción, configura Supervisor o un cron job (ver `INSTRUCCIONES_COLA_TRABAJOS.md`).

