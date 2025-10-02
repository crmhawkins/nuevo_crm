# 📋 **ENDPOINTS DE PRESUPUESTOS PARA ELEVENLABS**

## 🎯 **Descripción General**

Nuevos endpoints API para que el agente de ElevenLabs pueda crear presupuestos y enviarlos por email en formato PDF.

---

## 🔗 **ENDPOINTS DISPONIBLES**

### **1. Crear Presupuesto**
**POST** `/api/eleven-labs/crear-presupuesto`

#### **Parámetros de entrada:**
```json
{
  "client_id": 123,
  "project_id": 456,
  "admin_user_id": 789,
  "concept": "Desarrollo Web Corporativo",
  "description": "Desarrollo completo de página web corporativa",
  "note": "Presupuesto válido por 30 días",
  "commercial_id": 101,
  "payment_method_id": 1,
  "conceptos": [
    {
      "title": "Diseño Web",
      "concept": "Diseño responsive y moderno para la página web",
      "units": 1,
      "sale_price": 800.00,
      "concept_type_id": 2,
      "service_id": 15,
      "services_category_id": 3
    },
    {
      "title": "Desarrollo Frontend",
      "concept": "Desarrollo del frontend con HTML, CSS y JavaScript",
      "units": 1,
      "sale_price": 1200.00,
      "concept_type_id": 2
    }
  ],
  "iva_percentage": 21,
  "discount": 0,
  "expiration_date": "2025-02-15"
}
```

#### **Campos obligatorios:**
- `client_id` - ID del cliente (debe existir en la tabla clients)
- `project_id` - ID del proyecto/campaña (debe existir en la tabla projects)
- `admin_user_id` - ID del gestor (debe existir en la tabla admin_user)
- `concept` - Concepto general del presupuesto (máx. 200 caracteres)
- `conceptos` - Array de conceptos del presupuesto (mínimo 1)
  - `title` - Título del concepto
  - `concept` - Descripción del concepto
  - `units` - Cantidad de unidades
  - `sale_price` - Precio de venta por unidad

#### **Campos opcionales:**
- `description` - Descripción adicional del presupuesto
- `iva_percentage` - Porcentaje de IVA (por defecto 21%)
- `discount` - Descuento aplicado
- `expiration_date` - Fecha de vencimiento del presupuesto
- `note` - Notas adicionales
- `concept_type_id` - Tipo de concepto (por defecto 2 = Propio)

#### **Respuesta exitosa:**
```json
{
  "success": true,
  "message": "Presupuesto creado exitosamente",
  "data": {
    "budget_id": 789,
    "reference": "P2025-0001",
    "total": 2420.00,
    "client_name": "Empresa Cliente S.L."
  }
}
```

---

### **2. Enviar Presupuesto por Email**
**POST** `/api/eleven-labs/enviar-presupuesto-pdf`

#### **Parámetros de entrada:**
```json
{
  "budget_id": 789,
  "email": "cliente@empresa.com",
  "cc": "gerente@empresa.com",
  "cc2": "comercial@hawkins.es",
  "message": "Adjunto encontrará nuestro presupuesto personalizado"
}
```

#### **Campos obligatorios:**
- `budget_id` - ID del presupuesto a enviar
- `email` - Email del destinatario principal

#### **Campos opcionales:**
- `cc` - Email en copia
- `cc2` - Segundo email en copia
- `message` - Mensaje personalizado (opcional)

#### **Respuesta exitosa:**
```json
{
  "success": true,
  "message": "Presupuesto enviado exitosamente por email",
  "data": {
    "budget_id": 789,
    "reference": "P2025-0001",
    "email": "cliente@empresa.com",
    "pdf_url": "https://crm.hawkins.es/budget/cliente/encrypted_filename.pdf"
  }
}
```

---

### **3. Obtener Proyectos/Campañas**
**GET** `/api/eleven-labs/proyectos`

#### **Respuesta exitosa:**
```json
{
  "success": true,
  "message": "Proyectos obtenidos exitosamente",
  "data": [
    {
      "id": 456,
      "name": "Campaña Web 2025",
      "description": "Desarrollo de páginas web para el año 2025",
      "client_id": 123,
      "cliente": {
        "id": 123,
        "name": "Empresa Cliente S.L.",
        "company": "Empresa Cliente S.L."
      }
    }
  ]
}
```

---

## 🔧 **Funcionalidades Implementadas**

### **Creación de Presupuestos:**
- ✅ **Validación completa** - Todos los campos son validados
- ✅ **Referencia automática** - Se genera automáticamente (P2025-0001, P2025-0002, etc.)
- ✅ **Cálculos automáticos** - Subtotal, IVA, descuentos y total
- ✅ **Conceptos múltiples** - Soporte para múltiples líneas de concepto
- ✅ **Transacciones** - Operación atómica con rollback en caso de error
- ✅ **Alertas** - Se crea una alerta ElevenLabs automáticamente
- ✅ **Logs detallados** - Registro completo de la operación

### **Envío por Email:**
- ✅ **Generación PDF** - PDF profesional con diseño corporativo
- ✅ **Encriptación** - Nombre del archivo encriptado por seguridad
- ✅ **Email profesional** - Template corporativo con datos del gestor
- ✅ **Múltiples destinatarios** - Soporte para CC y BCC
- ✅ **Seguimiento automático** - Alerta de seguimiento a los 2 días
- ✅ **Log de emails** - Registro del envío para auditoría
- ✅ **URL pública** - Link directo al PDF para el cliente

---

## 📊 **Ejemplos de Uso**

### **Ejemplo 1: Presupuesto Simple**
```bash
curl -X POST https://crm.hawkins.es/api/eleven-labs/crear-presupuesto \
  -H "Content-Type: application/json" \
  -d '{
    "client_id": 123,
    "admin_user_id": 456,
    "concept": "Página Web Básica",
    "conceptos": [
      {
        "title": "Desarrollo Web",
        "concept": "Página web responsive con 5 secciones",
        "units": 1,
        "sale_price": 1500.00
      }
    ]
  }'
```

### **Ejemplo 2: Envío de Presupuesto**
```bash
curl -X POST https://crm.hawkins.es/api/eleven-labs/enviar-presupuesto-pdf \
  -H "Content-Type: application/json" \
  -d '{
    "budget_id": 789,
    "email": "cliente@empresa.com",
    "message": "Estimado cliente, adjunto nuestro presupuesto"
  }'
```

---

## 🚨 **Códigos de Error**

### **Errores Comunes:**

**400 - Bad Request:**
- Datos de entrada inválidos
- Campos obligatorios faltantes
- Formato de email incorrecto

**404 - Not Found:**
- Cliente no encontrado
- Usuario no encontrado
- Presupuesto no encontrado

**500 - Internal Server Error:**
- Error en la base de datos
- Error generando PDF
- Error enviando email

### **Ejemplo de respuesta de error:**
```json
{
  "success": false,
  "message": "Datos de entrada inválidos",
  "errors": {
    "client_id": ["El campo client_id es obligatorio"],
    "conceptos": ["Debe incluir al menos un concepto"]
  }
}
```

---

## 🔐 **Seguridad y Consideraciones**

- **Sin autenticación:** Los endpoints son públicos para ElevenLabs
- **Validación estricta:** Todos los datos son validados antes del procesamiento
- **Logs completos:** Todas las operaciones quedan registradas
- **Archivos encriptados:** Los PDFs se guardan con nombres encriptados
- **Emails seguros:** BCC automático a administración para auditoría

---

## 📈 **Integración con ElevenLabs**

Estos endpoints están diseñados específicamente para ser utilizados por el agente de ElevenLabs, permitiendo:

1. **Crear presupuestos** basados en conversaciones con clientes
2. **Enviar automáticamente** los presupuestos por email
3. **Generar alertas** para seguimiento del equipo comercial
4. **Mantener trazabilidad** completa de todas las operaciones

**¡Los endpoints están listos para ser utilizados por ElevenLabs!** 🚀
