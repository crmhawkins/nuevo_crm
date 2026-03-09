# 🔍 Diagnóstico de Cron Jobs - Laravel

## ⚠️ Problemas Detectados

### 1. **CRÍTICO: Falta el comando base del cron**
Laravel necesita que se ejecute `php artisan schedule:run` cada minuto en el crontab del servidor. Sin esto, **NINGÚN cron job funcionará**.

### 2. **Configuración de Queue**
La configuración actual usa `QUEUE_CONNECTION=sync` por defecto, lo que puede causar problemas con jobs largos.

### 3. **Comando queue:work en el schedule**
Hay un comando `queue:work` programado dentro del schedule (línea 300-302), pero esto no es la forma recomendada. El queue worker debe ejecutarse como un proceso separado.

## ✅ Soluciones

### **PASO 1: Verificar/Configurar el Crontab del Servidor**

Conéctate al servidor por SSH y ejecuta:

```bash
crontab -e
```

Debe contener esta línea (ajusta la ruta según tu instalación):

```bash
* * * * * cd /ruta/a/tu/proyecto && php artisan schedule:run >> /dev/null 2>&1
```

**Ejemplo para Plesk:**
```bash
* * * * * cd /var/www/vhosts/hawkins.es/crm.hawkins.es && php artisan schedule:run >> /dev/null 2>&1
```

**Para verificar que está configurado:**
```bash
crontab -l
```

### **PASO 2: Verificar que el Schedule funciona**

Ejecuta manualmente para probar:

```bash
cd /ruta/a/tu/proyecto
php artisan schedule:run
```

Si ves errores, corrígelos antes de continuar.

### **PASO 3: Verificar logs de ejecución**

Laravel guarda logs de las ejecuciones del schedule. Revisa:

```bash
tail -f storage/logs/laravel.log
```

O si tienes logs específicos del schedule:

```bash
ls -la storage/logs/
```

### **PASO 4: Verificar permisos de archivos**

El schedule usa archivos de bloqueo para `withoutOverlapping()`. Verifica permisos:

```bash
chmod -R 775 storage/
chown -R www-data:www-data storage/  # Ajusta según tu usuario
```

### **PASO 5: Verificar configuración de Queue**

Si usas jobs en cola, verifica tu `.env`:

```env
QUEUE_CONNECTION=database  # o 'redis' si tienes Redis configurado
```

**IMPORTANTE:** Si cambias a `database`, necesitas tener la tabla `jobs` creada:

```bash
php artisan queue:table
php artisan migrate
```

### **PASO 6: Ejecutar Queue Worker (si usas colas)**

El comando `queue:work` en el schedule (línea 300-302) no es recomendado. En su lugar, ejecuta el worker como un proceso separado:

**Opción A: Supervisor (Recomendado)**
```bash
# Instalar supervisor
sudo apt-get install supervisor

# Crear configuración en /etc/supervisor/conf.d/laravel-worker.conf
```

**Opción B: Systemd (Linux moderno)**
Crear un servicio systemd para el queue worker.

**Opción C: Proceso en background (temporal)**
```bash
nohup php artisan queue:work --daemon > /dev/null 2>&1 &
```

### **PASO 7: Verificar comandos específicos**

Algunos comandos programados pueden tener problemas. Prueba ejecutarlos manualmente:

```bash
# Ejemplos de comandos que están programados
php artisan correos:get
php artisan correos:getFacturas
php artisan Tesoreria:ProcesarExcel
php artisan Whatsapp:GenerarMensajeCampania
php artisan Whatsapp:Enviar
```

Si alguno falla, revisa los logs y corrige el error.

## 🔧 Comandos Útiles para Diagnóstico

### Ver todos los comandos programados:
```bash
php artisan schedule:list
```

### Ejecutar un comando específico:
```bash
php artisan nombre:comando
```

### Ver logs en tiempo real:
```bash
tail -f storage/logs/laravel.log
```

### Verificar si el cron está ejecutándose:
```bash
# Agregar esto temporalmente en Kernel.php para debug:
$schedule->call(function () {
    \Log::info('Cron ejecutado: ' . now());
})->everyMinute();
```

Luego revisa los logs para ver si aparece el mensaje cada minuto.

### Ver procesos de queue:
```bash
ps aux | grep "queue:work"
```

## 📋 Checklist de Verificación

- [ ] Crontab configurado con `php artisan schedule:run`
- [ ] Permisos correctos en `storage/`
- [ ] `php artisan schedule:run` ejecuta sin errores
- [ ] Logs muestran ejecuciones del schedule
- [ ] Comandos individuales funcionan manualmente
- [ ] Queue worker ejecutándose (si usas colas)
- [ ] Tabla `jobs` existe (si usas database queue)

## 🚨 Problemas Comunes

### "No scheduled commands are ready to run"
- Verifica que el crontab esté configurado
- Verifica que `php artisan schedule:run` se ejecute cada minuto
- Revisa la hora del servidor: `date`

### "Command is overlapping"
- Los archivos de bloqueo pueden estar corruptos
- Elimina archivos en `storage/framework/schedule-*`
- Verifica permisos de escritura

### "Queue jobs no se procesan"
- Verifica `QUEUE_CONNECTION` en `.env`
- Ejecuta `php artisan queue:work` manualmente
- Revisa la tabla `failed_jobs` para ver errores

### "Permiso denegado"
- Verifica permisos: `chmod -R 775 storage/`
- Verifica propietario: `chown -R www-data:www-data storage/`
- Verifica que el usuario del cron tenga permisos

## 📝 Notas Importantes

1. **El comando `php artisan schedule:run` DEBE ejecutarse cada minuto** - Sin esto, nada funcionará.

2. **El schedule de Laravel NO es un cron tradicional** - No pongas comandos directamente en crontab, usa el schedule de Laravel.

3. **Los comandos con `withoutOverlapping()`** crean archivos de bloqueo. Si un comando se cuelga, puede bloquear futuras ejecuciones.

4. **El queue worker** debe ejecutarse como proceso separado, no dentro del schedule.

5. **Revisa los logs regularmente** para detectar problemas temprano.
