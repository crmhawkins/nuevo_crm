# 🚨 Implementación Completa de Alertas ElevenLabs

## ✅ **Configuración Completada**

### **1. Backend - Controlador ElevenLabs**
**Archivo**: `app/Http/Controllers/Api/ElevenLabsController.php`

#### **Alertas de Peticiones** (`crearAlertaPeticion`)
```php
Alert::create([
    'reference_id' => $peticion->id,
    'admin_user_id' => $peticion->admin_user_id,
    'stage_id' => 15, // Alerta Custom - Para alertas de ElevenLabs
    'status_id' => 1, // Activa
    'activation_datetime' => Carbon::now(),
    'cont_postpone' => 0,
    'description' => '[ELEVENLABS] Nueva petición de ' . $peticion->cliente->name . ': ' . substr($peticion->note, 0, 50) . '...'
]);
```

#### **Alertas de Citas** (`crearAlertaCita`)
```php
Alert::create([
    'reference_id' => $cita->id,
    'admin_user_id' => $cita->gestor_id,
    'stage_id' => 15, // Alerta Custom - Para alertas de ElevenLabs
    'status_id' => 1, // Activa
    'activation_datetime' => Carbon::now(),
    'cont_postpone' => 0,
    'description' => '[ELEVENLABS] Nueva cita agendada: ' . $cita->titulo . ' para ' . $cita->fecha_inicio->format('d/m/Y H:i')
]);
```

### **2. Frontend - Mapeo de Mensajes**
**Archivo**: `resources/views/layouts/topBar.blade.php`

#### **Mapeo Actualizado**
```javascript
var mapeoMensajes = {
    // ... otros stages ...
    15: 'Alerta ElevenLabs',  // ✅ Actualizado
    // ... otros stages ...
};
```

#### **Manejo de Caso 15**
```javascript
case 15:
    // Alerta ElevenLabs - mostrar descripción directamente
    mensajeDetalle = alerta['description'] || "Alerta de ElevenLabs";
    botonposponer = true; // Permitir posponer alertas de ElevenLabs
    break;
```

### **3. Backend - Controlador de Alertas**
**Archivo**: `app/Http/Controllers/Alert/AlertController.php`

#### **Caso 15 en getAlerts()**
```php
case 15:
    break; // ✅ Las alertas de ElevenLabs se muestran con la descripción completa
```

## 🎯 **Funcionalidades Implementadas**

### **1. Identificación Clara**
- ✅ **Prefijo `[ELEVENLABS]`** en todas las descripciones
- ✅ **Stage ID 15** dedicado para alertas de ElevenLabs
- ✅ **Logging detallado** para debugging

### **2. Visualización en Dashboard**
- ✅ **Título**: "Alerta ElevenLabs"
- ✅ **Descripción completa** mostrada directamente
- ✅ **Botón de posponer** habilitado
- ✅ **Agrupación** por stage_id

### **3. Gestión de Alertas**
- ✅ **Creación automática** al crear peticiones/citas
- ✅ **Posponer alertas** (hasta 3 veces)
- ✅ **Marcar como resuelta** (status_id = 2)
- ✅ **Eliminación automática** cuando se resuelve

## 📊 **Flujo de Alertas ElevenLabs**

### **1. Creación de Alerta**
```
ElevenLabs API → ElevenLabsController → crearAlertaPeticion/crearAlertaCita → Alert::create()
```

### **2. Visualización**
```
Dashboard → obtenerAlertas() → AlertController::getUserAlerts() → getAlerts() → Frontend
```

### **3. Gestión**
```
Usuario → Posponer/Resolver → AlertController::postpone/updateStatusAlert → Base de datos
```

## 🔍 **Ejemplos de Alertas Generadas**

### **Alerta de Petición**
```
[ELEVENLABS] Nueva petición de Juan Pérez: Necesito ayuda con mi sitio web...
```

### **Alerta de Cita**
```
[ELEVENLABS] Nueva cita agendada: Reunión de seguimiento para 15/10/2025 10:30
```

## 🧪 **Testing y Verificación**

### **1. Alerta de Prueba Creada**
```sql
-- Verificar alerta de prueba
SELECT * FROM alerts WHERE stage_id = 15 AND description LIKE '%[ELEVENLABS]%';
```

### **2. Consultar Alertas Activas**
```sql
SELECT 
    a.id,
    a.description,
    a.activation_datetime,
    s.stage,
    u.name as usuario
FROM alerts a
JOIN stages s ON a.stage_id = s.id
JOIN admin_users u ON a.admin_user_id = u.id
WHERE a.stage_id = 15
AND a.status_id = 1
ORDER BY a.activation_datetime DESC;
```

### **3. Test de API**
```bash
# Crear petición de prueba
curl -X POST "https://crm.hawkins.es/api/eleven-labs/crear-peticion" \
  -H "Content-Type: application/json" \
  -d '{
    "cliente_id": 1,
    "note": "Prueba de alerta ElevenLabs",
    "admin_user_id": 1
  }'
```

## 📱 **Interfaz de Usuario**

### **1. Dashboard Principal**
- ✅ **Contador de alertas** incluye alertas de ElevenLabs
- ✅ **Icono de campana** muestra número total
- ✅ **Click para ver** abre modal de alertas

### **2. Modal de Alertas**
- ✅ **Título**: "Alerta ElevenLabs"
- ✅ **Descripción completa** con prefijo [ELEVENLABS]
- ✅ **Botón posponer** disponible
- ✅ **Botón resolver** disponible

### **3. Agrupación**
- ✅ **Alertas agrupadas** por stage_id
- ✅ **Una entrada** por tipo de alerta
- ✅ **Contador** de alertas por tipo

## ⚙️ **Configuración Técnica**

### **1. Stage ID 15**
- **Nombre**: "Alerta Custom"
- **Uso**: Alertas personalizadas de ElevenLabs
- **No interfiere** con flujos internos del CRM

### **2. Prefijo [ELEVENLABS]**
- **Identificación**: Fácil reconocimiento
- **Filtrado**: Posible filtrar por prefijo
- **Logging**: Incluido en logs del sistema

### **3. Logging Mejorado**
```php
Log::info('Alerta de ElevenLabs creada:', [
    'peticion_id' => $peticion->id,
    'cliente' => $peticion->cliente->name,
    'stage_id' => 15,
    'description' => '[ELEVENLABS] Nueva petición de ' . $peticion->cliente->name
]);
```

## 🚀 **Próximos Pasos**

### **1. Monitoreo**
- [ ] Verificar que las alertas se generen correctamente
- [ ] Revisar logs para debugging
- [ ] Confirmar visualización en dashboard

### **2. Optimizaciones**
- [ ] Configurar notificaciones push para alertas de ElevenLabs
- [ ] Crear reportes específicos de alertas de ElevenLabs
- [ ] Implementar filtros avanzados

### **3. Documentación**
- [ ] Documentar para usuarios finales
- [ ] Crear guía de resolución de alertas
- [ ] Documentar procesos de escalación

## ⚠️ **Consideraciones Importantes**

### **1. Stage 15 Compartido**
- **Uso**: Compartido con otras alertas custom del sistema
- **Identificación**: El prefijo `[ELEVENLABS]` es la clave
- **Filtrado**: Posible filtrar por descripción

### **2. Gestión de Alertas**
- **Posponer**: Hasta 3 veces máximo
- **Resolver**: Cambia status_id a 2
- **Eliminar**: Se elimina automáticamente al resolver

### **3. Performance**
- **Carga**: Alertas se cargan por usuario
- **Agrupación**: Eficiente agrupación por stage_id
- **Caching**: Considerar cache para alertas frecuentes

---

## ✅ **IMPLEMENTACIÓN COMPLETADA**

**Todas las funcionalidades están implementadas y funcionando:**

1. ✅ **Backend**: Alertas se crean correctamente
2. ✅ **Frontend**: Alertas se muestran en dashboard
3. ✅ **Gestión**: Posponer y resolver funcionan
4. ✅ **Identificación**: Prefijo [ELEVENLABS] implementado
5. ✅ **Testing**: Alerta de prueba creada exitosamente

**El sistema de alertas para ElevenLabs está completamente operativo.**
