# 📘 GUÍA DE USO - PLATAFORMA MONITOREO ELEVEN LABS

## 🎯 RESUMEN RÁPIDO

Esta plataforma sincroniza conversaciones de Eleven Labs, las procesa con IA para categorizarlas automáticamente usando **categorías personalizadas por agente** y genera resúmenes en español.

---

## 🆕 SISTEMA DE CATEGORÍAS DINÁMICAS POR AGENTE

**¡NUEVO!** Cada agente puede tener sus propias categorías personalizadas generadas por IA.

### Categorías Fijas (Obligatorias):
- 😊 **Contento** - Cliente satisfecho
- 😞 **Descontento** - Cliente insatisfecho  
- 📵 **Sin Respuesta** - Sin interacción real

### Categorías Variables (3 personalizadas por agente):
Generadas automáticamente por IA según la descripción del agente.

**Ejemplo para agente de reservas:**
- 🏨 Reserva confirmada
- 📅 Consulta disponibilidad
- ❌ Cancelación

---

## ✅ CARACTERÍSTICAS

- ✅ **Modal para ver conversaciones** (sin redirecciones)
- ✅ **Caché de agentes** (solo 1 petición al inicio, ahorra API calls)
- ✅ **Filtros avanzados**:
  - Búsqueda general (ID, agente, palabras clave)
  - Por agente
  - Por categoría
  - Por satisfacción (contentos/descontentos)
  - Por si tiene transcripción o resumen
  - Por rango de fechas
- ✅ **Logs detallados** en cada paso

---

## 🚀 PROCESO COMPLETO (PASO A PASO)

### **PASO 1: Sincronizar Conversaciones de Eleven Labs**

```bash
# Sincronizar últimas conversaciones (sin procesar IA aún)
php artisan elevenlabs:sync --limit=5 --no-process
```

**¿Qué hace?**
1. 👥 Sincroniza TODOS los agentes primero (caché local en `elevenlabs_agents`)
2. 📞 Descarga conversaciones de Eleven Labs (5 páginas = ~500 conversaciones)
3. 💾 Guarda en BD con transcripciones formateadas
4. ⏳ Las marca como `pending` (pendientes de procesar con IA)

**Parámetros:**
- `--limit=N`: Máximo de páginas a sincronizar (cada página = ~100 conversaciones)
- `--no-process`: NO procesar con IA automáticamente
- `--from=YYYY-MM-DD`: Sincronizar desde una fecha específica

---

### **PASO 2: Iniciar Worker de Cola** (Terminal separada)

```bash
php artisan queue:work
```

Este comando **debe estar corriendo** para que se procesen los jobs de IA.

**Mantén esta terminal abierta** mientras procesas conversaciones.

---

### **PASO 3: Procesar con IA** (3 opciones)

#### **Opción A: Procesar UNA conversación específica** 🎯

1. Accede a: `http://tu-crm.com/elevenlabs/conversations`
2. Busca la conversación que quieres
3. Click en **"Ver"** (abre modal)
4. Click en **"Reprocesar con IA"**
5. El job se despachará y el worker lo procesará

#### **Opción B: Procesar TODAS las pendientes** 📦

```bash
# Procesar hasta 50 conversaciones pendientes
php artisan elevenlabs:process --limit=50
```

**¿Qué hace?**
- Busca conversaciones con estado `pending`
- Despacha jobs a la cola
- El worker las procesa una por una

#### **Opción C: Sincronizar Y procesar en un solo comando** 🚀

```bash
# Sincronizar 2 páginas Y procesarlas automáticamente
php artisan elevenlabs:sync --limit=2
```

(Sin `--no-process`, procesa automáticamente)

---

## 🔍 SISTEMA DE FILTROS

### **Búsqueda General:**
Busca en:
- ID de conversación
- Nombre del agente
- Transcripción completa
- Resumen en español

### **Filtros Disponibles:**

| Filtro | Opciones |
|--------|----------|
| **Agente** | Lista de todos los agentes sincronizados |
| **Categoría** | Contento, Descontento, Pregunta, Necesita Asistencia, Queja, Baja, Sin categoría |
| **Estado** | Pendiente, Procesando, Completado, Fallido |
| **Satisfacción** | Contentos, Descontentos |
| **Tiene Transcripción** | Sí / No |
| **Tiene Resumen** | Sí / No |
| **Fechas** | Desde / Hasta |

---

## 📊 CATEGORÍAS DE IA

Cuando la IA procesa una conversación, la categoriza en:

1. **😊 Contento** (Verde) - Cliente satisfecho
2. **😞 Descontento** (Rojo) - Cliente insatisfecho
3. **❓ Pregunta** (Azul) - Consulta general
4. **🤚 Necesita Asistencia** (Naranja) - Requiere escalado
5. **⚠️ Queja** (Rojo oscuro) - Queja formal
6. **👤 Baja** (Gris) - Solicita cancelación

Además genera un **resumen ejecutivo en español de España**.

---

## 🛠️ COMANDOS DISPONIBLES

### Sincronización:
```bash
# Básico (últimas conversaciones)
php artisan elevenlabs:sync --limit=5 --no-process

# Desde una fecha específica
php artisan elevenlabs:sync --from=2025-10-01 --limit=10 --no-process

# Sincronizar y procesar automáticamente
php artisan elevenlabs:sync --limit=2
```

### Procesamiento:
```bash
# Procesar pendientes
php artisan elevenlabs:process --limit=50

# Worker de cola (DEBE estar corriendo)
php artisan queue:work
```

### Ver logs:
```bash
# PowerShell (tiempo real)
Get-Content storage\logs\laravel.log -Tail 50 -Wait
```

---

## 💡 FLUJO RECOMENDADO

### **Primera vez:**

1. **Terminal 1:**
   ```bash
   php artisan queue:work
   ```

2. **Terminal 2:**
   ```bash
   # Sincronizar 2 páginas
   php artisan elevenlabs:sync --limit=2 --no-process
   ```

3. **Terminal 2:**
   ```bash
   # Procesar 10 conversaciones con IA
   php artisan elevenlabs:process --limit=10
   ```

4. **Ver resultados:**
   - Abre: `http://tu-crm.com/elevenlabs/dashboard`
   - Verás las gráficas actualizadas

### **Uso diario:**

1. Mantén el worker corriendo: `php artisan queue:work`
2. Cuando quieras actualizar:
   ```bash
   php artisan elevenlabs:sync --limit=1
   ```
3. Ve al dashboard para ver los resultados

---

## 🎨 CARACTERÍSTICAS DEL DASHBOARD

### **Modal de Conversación:**
- Click en "Ver" para abrir modal sin salir de la página
- Muestra: Info, Análisis IA, Resumen, Transcripción completa
- Botón para reprocesar con IA desde el modal

### **Filtros Inteligentes:**
- Busca por cualquier cosa (agente, palabras clave, ID)
- Filtra por múltiples criterios combinados
- Mantiene filtros en la paginación

### **Exportación:**
- Click en "Exportar" para descargar CSV
- Incluye todos los filtros aplicados
- Columnas: ID, Fecha, Agente, Cliente, Duración, Categoría, Confianza, Resumen, Estado

---

## 📈 OPTIMIZACIONES IMPLEMENTADAS

✅ **Caché de Agentes:**
- Solo 1 petición a `/v1/convai/agents` al inicio
- Guarda agentes en BD local
- Reutiliza nombres sin peticiones adicionales

✅ **Sincronización Eficiente:**
- Paginación controlada (límite de páginas)
- Obtiene detalles completos solo si es necesario
- Formateo optimizado de transcripciones

✅ **Logs Detallados:**
- Cada paso del proceso registrado
- Fácil debugging
- Ver en: `storage/logs/laravel.log`

---

## 🔧 TROUBLESHOOTING

### **Las conversaciones no se procesan:**
✅ Asegúrate de tener el worker corriendo:
```bash
php artisan queue:work
```

### **Quiero procesar más rápido:**
✅ Aumenta el número de workers:
```bash
# Terminal 1
php artisan queue:work --queue=default

# Terminal 2
php artisan queue:work --queue=default

# Terminal 3
php artisan queue:work --queue=default
```

### **Ver qué está haciendo la IA:**
✅ Abre los logs en tiempo real:
```bash
Get-Content storage\logs\laravel.log -Tail 100 -Wait
```

---

## 📞 EJEMPLO COMPLETO

```bash
# === TERMINAL 1: Worker de cola ===
php artisan queue:work

# === TERMINAL 2: Comandos ===

# 1. Sincronizar últimas 2 páginas sin procesar
php artisan elevenlabs:sync --limit=2 --no-process

# 2. Procesar 10 conversaciones con IA
php artisan elevenlabs:process --limit=10

# 3. Ver cuántas hay en cada estado
# (desde el dashboard web)
```

Luego ve a: `http://tu-crm.com/elevenlabs/dashboard`

---

## ✨ RESUMEN DE MEJORAS

✅ Modales en vez de redirecciones
✅ Caché de agentes (ahorro de peticiones API)
✅ Filtros avanzados (8 tipos diferentes)
✅ Búsqueda potente (busca en todo)
✅ Logs detallados paso a paso
✅ 3 formas de procesar con IA
✅ Exportación con todos los filtros

---

## 🤖 CONFIGURAR AGENTES (NUEVO)

### Paso 1: Acceder al panel de agentes
1. Ve a: `http://tu-crm.com/elevenlabs/agents`
2. Verás todos los agentes sincronizados

### Paso 2: Configurar un agente
1. Click en **"Configurar"** en el agente que quieras
2. Escribe una descripción detallada del agente:
   ```
   Ejemplo: "Este agente atiende llamadas de reservas de apartamentos 
   turísticos. Maneja consultas sobre disponibilidad, precios, 
   check-in/check-out, y problemas durante la estancia."
   ```

### Paso 3: Generar categorías con IA
1. Click en **"Generar Categorías con IA"**
2. La IA analizará la descripción y sugerirá 3 categorías personalizadas
3. Revisa y edita las categorías sugeridas si quieres
4. Click en **"Guardar Configuración"**

### Resultado:
✅ El agente tendrá 6 categorías totales:
- 3 fijas (contento, descontento, sin_respuesta)
- 3 personalizadas generadas por IA

---

## 📊 CATEGORÍAS PERSONALIZADAS

Cuando proceses conversaciones de ese agente, la IA usará SUS categorías específicas en lugar de las genéricas.

**Beneficios:**
- ✅ Análisis más preciso por tipo de agente
- ✅ Estadísticas segmentadas por función
- ✅ Mejor comprensión del negocio

---

**¡Todo listo para usar!** 🎉

