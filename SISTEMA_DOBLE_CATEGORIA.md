# 🎯 SISTEMA DE DOBLE CATEGORIZACIÓN

## 🆕 NUEVA ARQUITECTURA

Cada conversación ahora tiene **2 categorías independientes**:

### 1️⃣ **Categoría de Sentimiento** (Fija - 3 opciones)
- 😊 **Contento** - Cliente satisfecho, acepta, agradece
- 😞 **Descontento** - Cliente rechaza, no interesado, tono negativo
- 📵 **Sin Respuesta** - Cliente NO responde, solo "..."

### 2️⃣ **Categoría Específica** (Dinámica - 4 por agente)
Generadas por IA según la descripción del agente.

**Ejemplos para agente de RESERVAS:**
- 🏨 Solicitud de Reserva
- 📅 Consulta de Disponibilidad  
- ✏️ Modificación de Reserva
- ❌ Cancelación

---

## 💡 ¿POR QUÉ 2 CATEGORÍAS?

### **Antes (1 categoría):**
❌ Difícil elegir entre sentimiento y acción
❌ "Contento" mezclado con "Solicitud de Reserva"
❌ Estadísticas confusas

### **Ahora (2 categorías):**
✅ **Sentimiento:** ¿Está contento o descontento?
✅ **Acción:** ¿Qué está haciendo? (reservar, consultar, quejar, etc.)
✅ Análisis más preciso y útil
✅ Estadísticas separadas

**Ejemplo real:**
- Cliente llama para hacer una reserva → está **contento** + hace **solicitud_reserva**
- Cliente llama para quejarse → está **descontento** + tiene **incidencia_mantenimiento**

---

## 🚀 CONFIGURACIÓN DE AGENTES

### PASO 1: Accede al panel de agentes
```
http://localhost/elevenlabs/agents
```

### PASO 2: Configura cada agente

1. Click en **"Configurar"**
2. Escribe descripción detallada (mínimo 10 caracteres):
   ```
   Ejemplo: "Este agente atiende llamadas de reservas de apartamentos 
   turísticos. Gestiona consultas sobre disponibilidad, precios, 
   modificaciones y cancelaciones de reservas."
   ```
3. Click en **"Generar Categorías con IA"**
4. La IA sugerirá **4 categorías específicas**
5. **EDITA TODO** lo que quieras:
   - ✅ Clave (sin espacios, sin acentos)
   - ✅ Nombre visible
   - ✅ Descripción detallada
   - ✅ Color (selector con 8 opciones)
   - ✅ Vista previa en tiempo real
6. Click en **"Guardar Configuración"**

### RESULTADO:
El agente tendrá **7 categorías totales:**
- 3 fijas de sentimiento (contento, descontento, sin_respuesta)
- 4 personalizadas generadas por IA

---

## 🤖 PROCESAMIENTO DE IA

Cuando procesas una conversación, la IA hace **3 pasadas**:

### **Pasada 1: Sentimiento**
```
Analiza: ¿Cliente contento, descontento o sin respuesta?
Guarda en: sentiment_category
```

### **Pasada 2: Categoría Específica**
```
Analiza: ¿Qué está haciendo? (reservar, consultar, problema, etc.)
Usa: Solo las 4 categorías personalizadas del agente
Guarda en: specific_category + confidence_score
```

### **Pasada 3: Resumen**
```
Genera: Resumen en español de España (máximo 3 párrafos)
Guarda en: summary_es
```

---

## 📊 VISUALIZACIÓN

### **En las Tablas:**
Verás **2 badges** por conversación:
- Badge verde/rojo/gris → Sentimiento
- Badge azul/naranja/morado → Categoría específica

### **En los Modales:**
```
Sentimiento: [😊 Contento]
Categoría Específica: [🏨 Solicitud de Reserva]
Confianza: 96%
```

---

## 🧪 CÓMO PROBAR

### 1. Sincronizar conversaciones
```bash
php artisan elevenlabs:sync --from="2025-10-01" --limit=5 --no-process
```

### 2. Configurar agentes
```
→ /elevenlabs/agents
→ Configurar "Maria Apartamentos"
→ Generar 4 categorías con IA
→ Editar nombres, colores, descripciones
→ Guardar
```

### 3. Procesar conversaciones
```bash
# Terminal 1: Worker
php artisan queue:work

# Terminal 2: Procesar
php artisan elevenlabs:process --limit=10
```

### 4. Ver resultados
```
→ /elevenlabs/dashboard
→ Deberías ver 2 categorías por conversación
```

---

## 🔧 VALIDACIÓN INTELIGENTE

La IA **NO puede usar categorías que no existen**:

- ✅ Si devuelve categoría inválida → intenta mapear automáticamente
- ✅ Si no puede mapear → falla y loguea error
- ✅ Logs super detallados para debugging

**Ejemplo de logs:**
```
📋 Categorías permitidas: ["solicitud_reserva", "consulta_disponibilidad", ...]
🔍 IA devolvió: "solicitud_reserva"
✅ Validando: es_valida=SÍ
✅ Categoría válida, se usará tal cual
📤 Retornando: category="solicitud_reserva", confidence=0.96
```

---

## 📈 ESTADÍSTICAS MEJORADAS

Ahora puedes analizar:
- % de contentos vs descontentos (sentimiento)
- Tipos de llamadas más frecuentes (específicas)
- Correlaciones: ej. "solicitud_reserva" mayormente con "contento"

---

## ⚙️ CAMBIOS TÉCNICOS

### **Base de Datos:**
- `sentiment_category` → VARCHAR(100) - contento/descontento/sin_respuesta
- `specific_category` → VARCHAR(100) - Categoría del agente
- `confidence_score` → Aplica a la categoría específica

### **Modelo:**
- Accessors: `sentiment_label`, `sentiment_color`
- Accessors: `specific_label`, `specific_color`
- Compatibilidad: `category_label`, `category_color` (usan specific)

### **Procesamiento:**
- 3 peticiones a IA por conversación
- Total ~10-15 segundos por conversación
- Validación automática de categorías

---

**¡Sistema completamente escalable y preciso!** 🚀

