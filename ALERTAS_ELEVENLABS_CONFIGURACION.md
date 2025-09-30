# 🚨 Configuración de Alertas para ElevenLabs

## ✅ **Cambios Realizados**

### **1. Stage ID Actualizado**
- **Antes**: Usaba stage_id = 1 (Peticion Creada) y stage_id = 10 (Tesoreria Descubierta)
- **Ahora**: Usa **stage_id = 15** (Alerta Custom) para todas las alertas de ElevenLabs

### **2. Prefijo de Identificación**
- **Agregado**: `[ELEVENLABS]` en todas las descripciones de alertas
- **Propósito**: Identificar fácilmente las alertas generadas por ElevenLabs

### **3. Funciones Actualizadas**

#### **`crearAlertaPeticion()`**
```php
// ANTES
'stage_id' => 1, // ID para alertas de peticiones
'description' => 'Nueva petición de ' . $peticion->cliente->name . ': ' . substr($peticion->note, 0, 50) . '...'

// AHORA
'stage_id' => 15, // Alerta Custom - Para alertas de ElevenLabs
'description' => '[ELEVENLABS] Nueva petición de ' . $peticion->cliente->name . ': ' . substr($peticion->note, 0, 50) . '...'
```

#### **`crearAlertaCita()`**
```php
// ANTES
'stage_id' => 10, // ID para alertas de citas (nuevo stage)
'description' => 'Nueva cita agendada: ' . $cita->titulo . ' para ' . $cita->fecha_inicio->format('d/m/Y H:i')

// AHORA
'stage_id' => 15, // Alerta Custom - Para alertas de ElevenLabs
'description' => '[ELEVENLABS] Nueva cita agendada: ' . $cita->titulo . ' para ' . $cita->fecha_inicio->format('d/m/Y H:i')
```

## 📊 **Stages Disponibles en el Sistema**

| ID | Stage | Uso Recomendado |
|----|-------|-----------------|
| 1 | Peticion Creada | Peticiones internas del CRM |
| 2 | Presupuesto Pendiente Confirmar | Presupuestos |
| 3 | Presupuesto Pendiente Aceptar | Presupuestos |
| 4 | Presupuesto Pendiente Finalizar | Presupuestos |
| 5 | Presupuesto Finalizado | Presupuestos |
| 6 | Presupuesto Facturado | Presupuestos |
| 7 | Presupuesto Cancelado | Presupuestos |
| 8 | Crear Factura | Facturas |
| 9 | Factura Fuera de Plazo | Facturas |
| 10 | Tesoreria Descubierta | Tesorería |
| 11 | Tarea Nueva | Tareas |
| 12 | Aceptado terminos ver presupuesto | Presupuestos |
| 13 | Productividad | Productividad |
| 14 | Tarea Revision Antes Previsto | Tareas |
| **15** | **Alerta Custom** | **✅ ElevenLabs** |
| 16 | Peticion Vacaciones | Vacaciones |
| 17 | Vacaciones Aceptadas | Vacaciones |
| 18 | Vacaciones Denegadas | Vacaciones |
| 19 | Respuestas | Respuestas |
| 20 | Pospuesto | General |
| 21 | Prepuesto No Aceptado tras 48 horas | Presupuestos |
| 22 | Conformidad hora mes | Horarios |
| 23 | Alerta puntualidad | Puntualidad |
| 24 | Alerta 3 veces tarde | Puntualidad |
| 25 | Alerta Peticion Comercial | Comercial |
| 26 | Alerta Cobrar Comercial | Comercial |
| 27 | Alerta General | General |
| 28 | Encuesta Satisfaccion | Encuestas |
| 29 | Alerta Acta | Actas |

## 🎯 **Beneficios de la Configuración**

### **1. Identificación Clara**
- Todas las alertas de ElevenLabs tienen el prefijo `[ELEVENLABS]`
- Fácil identificación en el dashboard de alertas
- Separación clara de alertas internas vs externas

### **2. Stage Dedicado**
- **Stage 15: "Alerta Custom"** es perfecto para alertas personalizadas
- No interfiere con los flujos internos del CRM
- Permite configuración específica para alertas de ElevenLabs

### **3. Logging Mejorado**
- Logs detallados para debugging
- Información específica sobre alertas de ElevenLabs
- Fácil seguimiento de alertas generadas

## 🔍 **Verificación de Alertas**

### **Consultar Alertas de ElevenLabs**
```sql
SELECT 
    a.id,
    a.description,
    a.activation_datetime,
    s.stage,
    u.name as usuario,
    a.status_id
FROM alerts a
JOIN stages s ON a.stage_id = s.id
JOIN admin_users u ON a.admin_user_id = u.id
WHERE s.id = 15  -- Alerta Custom
AND a.description LIKE '%[ELEVENLABS]%'
ORDER BY a.activation_datetime DESC;
```

### **Alertas Activas**
```sql
SELECT 
    a.id,
    a.description,
    a.activation_datetime,
    u.name as usuario
FROM alerts a
JOIN admin_users u ON a.admin_user_id = u.id
WHERE a.stage_id = 15
AND a.status_id = 1  -- Activa
AND a.description LIKE '%[ELEVENLABS]%'
ORDER BY a.activation_datetime DESC;
```

## 📝 **Ejemplos de Alertas Generadas**

### **Alerta de Petición**
```
[ELEVENLABS] Nueva petición de Juan Pérez: Necesito ayuda con mi sitio web...
```

### **Alerta de Cita**
```
[ELEVENLABS] Nueva cita agendada: Reunión de seguimiento para 15/10/2025 10:30
```

## 🚀 **Próximos Pasos**

1. **Monitoreo**: Revisar que las alertas se generen correctamente
2. **Dashboard**: Configurar vista específica para alertas de ElevenLabs
3. **Notificaciones**: Configurar notificaciones específicas para stage 15
4. **Reportes**: Crear reportes de alertas de ElevenLabs

## ⚠️ **Consideraciones**

- **Stage 15** es compartido con otras alertas custom del sistema
- El prefijo `[ELEVENLABS]` es la clave para identificar alertas específicas
- Las alertas se crean con `status_id = 1` (Activa) por defecto
- El `reference_id` apunta al ID de la petición o cita correspondiente

## 🔧 **Configuración Técnica**

### **Archivos Modificados**
- `app/Http/Controllers/Api/ElevenLabsController.php`
  - `crearAlertaPeticion()` - Línea ~498
  - `crearAlertaCita()` - Línea ~465

### **Logs Agregados**
- Logging detallado en `crearAlertaPeticion()`
- Información específica sobre alertas de ElevenLabs
- Tracking de errores mejorado

---

**✅ Configuración completada y lista para uso en producción**
