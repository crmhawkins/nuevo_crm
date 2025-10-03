# 🤖 **ENDPOINTS COMPLETOS PARA ELEVENLABS**

## 📋 **Descripción General**

Documentación completa de todos los endpoints API disponibles para el agente de ElevenLabs en el CRM Hawkins. Estos endpoints permiten al agente gestionar citas, clientes, peticiones y presupuestos de forma autónoma.

---

## 🔗 **ENDPOINTS DISPONIBLES**

### **1. GESTIÓN DE CITAS**

#### **1.1 Obtener Citas Disponibles**
**GET** `/api/eleven-labs/citas-disponibles`

**Descripción:** Obtiene los horarios disponibles para agendar citas en un rango de fechas.

**Parámetros Query:**
- `fecha_inicio` (opcional) - Fecha inicio en formato YYYY-MM-DD (por defecto: hoy)
- `fecha_fin` (opcional) - Fecha fin en formato YYYY-MM-DD (por defecto: +7 días)

**Respuesta exitosa:**
```json
{
  "success": true,
  "message": "Horarios disponibles obtenidos exitosamente",
  "data": {
    "horarios_disponibles": [
      {
        "fecha": "2025-01-15",
        "hora": "09:30",
        "disponible": true
      },
      {
        "fecha": "2025-01-15",
        "hora": "10:00",
        "disponible": true
      }
    ],
    "total_horarios": 45,
    "rango_fechas": {
      "inicio": "2025-01-15",
      "fin": "2025-01-22"
    }
  }
}
```

#### **1.2 Agendar Cita**
**POST** `/api/eleven-labs/agendar-cita`

**Descripción:** Agenda una nueva cita para un cliente con un gestor específico.

**Parámetros de entrada:**
```json
{
  "cliente_id": 123,
  "gestor_id": 456,
  "fecha": "2025-01-15",
  "hora": "10:00",
  "tipo": "Consulta comercial",
  "descripcion": "Reunión para discutir proyecto web"
}
```

**Campos obligatorios:**
- `cliente_id` - ID del cliente
- `gestor_id` - ID del gestor
- `fecha` - Fecha de la cita (YYYY-MM-DD)
- `hora` - Hora de la cita (HH:MM)

**Respuesta exitosa:**
```json
{
  "success": true,
  "message": "Cita agendada exitosamente",
  "data": {
    "cita_id": 789,
    "cliente_nombre": "Juan Pérez",
    "gestor_nombre": "María García",
    "fecha_hora": "2025-01-15 10:00:00",
    "tipo": "Consulta comercial"
  }
}
```

#### **1.3 Obtener Citas Existentes**
**GET** `/api/eleven-labs/citas`

**Descripción:** Obtiene las citas existentes en un rango de fechas.

**Parámetros Query:**
- `fecha_inicio` (opcional) - Fecha inicio (por defecto: hoy)
- `fecha_fin` (opcional) - Fecha fin (por defecto: +30 días)
- `gestor_id` (opcional) - Filtrar por gestor específico

**Respuesta exitosa:**
```json
{
  "success": true,
  "message": "Citas obtenidas exitosamente",
  "data": [
    {
      "id": 789,
      "cliente_nombre": "Juan Pérez",
      "gestor_nombre": "María García",
      "fecha": "2025-01-15",
      "hora": "10:00",
      "tipo": "Consulta comercial",
      "estado": "Confirmada"
    }
  ]
}
```

---

### **2. GESTIÓN DE CLIENTES**

#### **2.1 Obtener Lista de Clientes**
**GET** `/api/eleven-labs/clientes`

**Descripción:** Obtiene la lista completa de clientes activos.

**Respuesta exitosa:**
```json
{
  "success": true,
  "message": "Clientes obtenidos exitosamente",
  "data": [
    {
      "id": 123,
      "name": "Juan Pérez",
      "company": "Empresa ABC S.L.",
      "email": "juan@empresa.com",
      "phone": "666123456",
      "is_client": 1
    }
  ]
}
```

#### **2.2 Buscar Cliente**
**GET** `/api/eleven-labs/buscar-cliente`

**Descripción:** Busca clientes por nombre, email o teléfono.

**Parámetros Query:**
- `query` (obligatorio) - Término de búsqueda

**Respuesta exitosa:**
```json
{
  "success": true,
  "message": "Búsqueda completada",
  "data": [
    {
      "id": 123,
      "name": "Juan Pérez",
      "company": "Empresa ABC S.L.",
      "email": "juan@empresa.com",
      "phone": "666123456"
    }
  ]
}
```

#### **2.3 Crear Cliente**
**POST** `/api/eleven-labs/crear-cliente`

**Descripción:** Crea un nuevo cliente en el sistema.

**Parámetros de entrada:**
```json
{
  "name": "Juan Pérez",
  "company": "Empresa ABC S.L.",
  "email": "juan@empresa.com",
  "phone": "666-123-456",
  "address": "Calle Mayor 123",
  "city": "Madrid",
  "province": "Madrid",
  "zipcode": "28001",
  "admin_user_id": 456
}
```

**Campos obligatorios:**
- `name` - Nombre del cliente
- `email` - Email del cliente
- `phone` - Teléfono del cliente
- `admin_user_id` - ID del gestor asignado

**Respuesta exitosa:**
```json
{
  "success": true,
  "message": "Cliente creado exitosamente",
  "data": {
    "client_id": 124,
    "name": "Juan Pérez",
    "email": "juan@empresa.com",
    "phone": "666123456",
    "gestor_asignado": "María García"
  }
}
```

---

### **3. GESTIÓN DE GESTORES**

#### **3.1 Obtener Lista de Gestores**
**GET** `/api/eleven-labs/gestores`

**Descripción:** Obtiene la lista de gestores activos disponibles.

**Respuesta exitosa:**
```json
{
  "success": true,
  "message": "Gestores obtenidos exitosamente",
  "data": [
    {
      "id": 456,
      "name": "María García",
      "surname": "López",
      "email": "maria@hawkins.es",
      "phone": "666789012",
      "departamento": "Comercial"
    }
  ]
}
```

---

### **4. GESTIÓN DE PETICIONES**

#### **4.1 Crear Petición**
**POST** `/api/eleven-labs/crear-peticion`

**Descripción:** Crea una nueva petición/solicitud en el sistema.

**Parámetros de entrada:**
```json
{
  "client_id": 123,
  "admin_user_id": 456,
  "title": "Solicitud de presupuesto web",
  "description": "El cliente necesita una página web corporativa con tienda online",
  "priority_id": 2,
  "gestoria_id": 1
}
```

**Campos obligatorios:**
- `client_id` - ID del cliente
- `admin_user_id` - ID del gestor
- `title` - Título de la petición
- `description` - Descripción detallada

**Respuesta exitosa:**
```json
{
  "success": true,
  "message": "Petición creada exitosamente",
  "data": {
    "petition_id": 789,
    "title": "Solicitud de presupuesto web",
    "client_name": "Juan Pérez",
    "gestor_name": "María García",
    "priority": "Media",
    "status": "Pendiente"
  }
}
```

---

### **5. GESTIÓN DE PRESUPUESTOS**

#### **5.1 Obtener Proyectos/Campañas**
**GET** `/api/eleven-labs/proyectos`

**Descripción:** Obtiene la lista de proyectos/campañas disponibles.

**Respuesta exitosa:**
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
        "name": "Empresa ABC S.L.",
        "company": "Empresa ABC S.L."
      }
    }
  ]
}
```

#### **5.2 Crear Presupuesto**
**POST** `/api/eleven-labs/crear-presupuesto`

**Descripción:** Crea un nuevo presupuesto completo con conceptos.

**Parámetros de entrada:**
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

**Campos obligatorios:**
- `client_id` - ID del cliente
- `project_id` - ID del proyecto/campaña
- `admin_user_id` - ID del gestor
- `concept` - Concepto general (máx. 200 caracteres)
- `conceptos` - Array de conceptos (mínimo 1)

**Respuesta exitosa:**
```json
{
  "success": true,
  "message": "Presupuesto creado exitosamente",
  "data": {
    "budget_id": 789,
    "reference": "2025/01/000001",
    "total": 2420.00,
    "client_name": "Empresa ABC S.L.",
    "project_name": "Campaña Web 2025"
  }
}
```

#### **5.3 Enviar Presupuesto por Email**
**POST** `/api/eleven-labs/enviar-presupuesto-pdf`

**Descripción:** Envía un presupuesto por email en formato PDF.

**Parámetros de entrada:**
```json
{
  "budget_id": 789,
  "email": "cliente@empresa.com",
  "cc": "gerente@empresa.com",
  "cc2": "comercial@hawkins.es",
  "message": "Adjunto encontrará nuestro presupuesto personalizado"
}
```

**Campos obligatorios:**
- `budget_id` - ID del presupuesto
- `email` - Email del destinatario

**Respuesta exitosa:**
```json
{
  "success": true,
  "message": "Presupuesto enviado exitosamente por email",
  "data": {
    "budget_id": 789,
    "reference": "2025/01/000001",
    "email": "cliente@empresa.com",
    "pdf_url": "https://crm.hawkins.es/budget/cliente/encrypted_filename.pdf"
  }
}
```

---

### **6. INFORMACIÓN GENERAL**

#### **6.1 Obtener Información del Día**
**GET** `/api/eleven-labs/dia-hoy`

**Descripción:** Obtiene información completa del día actual.

**Respuesta exitosa:**
```json
{
  "success": true,
  "message": "Información del día de hoy obtenida exitosamente",
  "data": {
    "fecha": "15/01/2025",
    "dia_semana": "miércoles",
    "mes": "enero",
    "año": 2025,
    "hora": "14:30",
    "fecha_completa": "15/01/2025 14:30",
    "timestamp": 1737811800,
    "formato_iso": "2025-01-15T14:30:00.000000Z",
    "zona_horaria": "Europe/Madrid",
    "descripcion": "Hoy es miércoles, 15/01/2025 y son las 14:30"
  }
}
```

---

## 🚨 **CÓDIGOS DE ERROR COMUNES**

### **400 - Bad Request**
```json
{
  "success": false,
  "message": "Datos de entrada inválidos",
  "errors": {
    "client_id": ["El campo client_id es obligatorio"],
    "email": ["El formato del email es inválido"]
  }
}
```

### **404 - Not Found**
```json
{
  "success": false,
  "message": "Cliente no encontrado"
}
```

### **500 - Internal Server Error**
```json
{
  "success": false,
  "message": "Error interno del servidor",
  "error": "Descripción técnica del error"
}
```

---

## 🔐 **AUTENTICACIÓN Y SEGURIDAD**

- **Sin autenticación:** Todos los endpoints son públicos para ElevenLabs
- **Validación estricta:** Todos los datos son validados antes del procesamiento
- **Logs completos:** Todas las operaciones quedan registradas
- **Archivos seguros:** Los PDFs se guardan con nombres encriptados
- **Auditoría:** BCC automático a administración en emails

---

## 📊 **FLUJOS DE TRABAJO RECOMENDADOS**

### **Flujo 1: Agendar Cita**
1. `GET /gestores` - Obtener gestores disponibles
2. `GET /clientes` o `GET /buscar-cliente` - Buscar cliente
3. `GET /citas-disponibles` - Ver horarios disponibles
4. `POST /agendar-cita` - Confirmar la cita

### **Flujo 2: Crear Cliente y Petición**
1. `POST /crear-cliente` - Crear nuevo cliente
2. `POST /crear-peticion` - Crear petición para el cliente

### **Flujo 3: Generar y Enviar Presupuesto**
1. `GET /clientes` - Obtener cliente
2. `GET /proyectos` - Obtener campañas disponibles
3. `GET /gestores` - Obtener gestor
4. `POST /crear-presupuesto` - Crear presupuesto
5. `POST /enviar-presupuesto-pdf` - Enviar por email

---

## 🎯 **EJEMPLOS DE USO CON CURL**

### **Crear Cliente:**
```bash
curl -X POST https://crm.hawkins.es/api/eleven-labs/crear-cliente \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Juan Pérez",
    "email": "juan@empresa.com",
    "phone": "666123456",
    "admin_user_id": 456
  }'
```

### **Agendar Cita:**
```bash
curl -X POST https://crm.hawkins.es/api/eleven-labs/agendar-cita \
  -H "Content-Type: application/json" \
  -d '{
    "cliente_id": 123,
    "gestor_id": 456,
    "fecha": "2025-01-15",
    "hora": "10:00",
    "tipo": "Consulta comercial"
  }'
```

### **Crear Presupuesto:**
```bash
curl -X POST https://crm.hawkins.es/api/eleven-labs/crear-presupuesto \
  -H "Content-Type: application/json" \
  -d '{
    "client_id": 123,
    "project_id": 456,
    "admin_user_id": 789,
    "concept": "Desarrollo Web",
    "conceptos": [
      {
        "title": "Diseño Web",
        "concept": "Diseño responsive",
        "units": 1,
        "sale_price": 800.00
      }
    ]
  }'
```

---

## 📈 **INTEGRACIÓN CON ELEVENLABS**

Estos endpoints están diseñados específicamente para ser utilizados por el agente de ElevenLabs, permitiendo:

1. **Gestión completa de citas** - Consultar disponibilidad y agendar
2. **Administración de clientes** - Buscar, crear y gestionar
3. **Creación de peticiones** - Registrar solicitudes de clientes
4. **Generación de presupuestos** - Crear y enviar presupuestos automáticamente
5. **Seguimiento automatizado** - Alertas y logs para el equipo

**¡Todos los endpoints están listos para ser utilizados por ElevenLabs!** 🚀
