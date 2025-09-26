# Configuración Completa de APIs para Eleven Labs

## URLs Base
- **Base URL:** `http://127.0.0.1:8000/api/eleven-labs`

---

## 1. Ver Citas Disponibles

### Configuración de la Herramienta
- **Nombre:** `Ver Citas Disponibles`
- **Descripción:** `Obtiene las citas disponibles en un rango de fechas específico. Útil para consultar la disponibilidad del calendario y ver citas programadas. Permite filtrar por gestor específico.`
- **Método:** `GET`
- **URL:** `http://127.0.0.1:8000/api/eleven-labs/citas-disponibles`

### Parámetros de Consulta
| Parámetro | Tipo | Requerido | Descripción |
|-----------|------|-----------|-------------|
| `fecha_inicio` | string | Sí | Fecha de inicio en formato YYYY-MM-DD |
| `fecha_fin` | string | Sí | Fecha de fin en formato YYYY-MM-DD |
| `gestor_id` | integer | No | ID del gestor para filtrar citas específicas |

### Ejemplo de Uso
```
GET /api/eleven-labs/citas-disponibles?fecha_inicio=2024-01-01&fecha_fin=2024-01-31&gestor_id=1
```

### Respuesta
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "titulo": "Reunión con cliente",
      "descripcion": "Reunión para discutir proyecto",
      "fecha_inicio": "2024-01-15 10:00:00",
      "fecha_fin": "2024-01-15 11:00:00",
      "estado": "programada",
      "tipo": "reunion",
      "ubicacion": "Oficina principal",
      "cliente": {
        "id": 1,
        "nombre": "Cliente Ejemplo",
        "empresa": "Empresa S.L."
      },
      "gestor": {
        "id": 1,
        "nombre": "Diego Hawkins"
      }
    }
  ],
  "total": 1
}
```

---

## 2. Agendar Cita

### Configuración de la Herramienta
- **Nombre:** `Agendar Cita`
- **Descripción:** `Crea una nueva cita en el calendario del sistema. Permite agendar reuniones, llamadas, visitas y otros tipos de citas con clientes y gestores. La duración y color se calculan automáticamente según el tipo de cita.`
- **Método:** `POST`
- **URL:** `http://127.0.0.1:8000/api/eleven-labs/agendar-cita`

### Parámetros del Cuerpo (JSON)
| Parámetro | Tipo | Requerido | Descripción |
|-----------|------|-----------|-------------|
| `titulo` | string | Sí | Título de la cita (máx. 255 caracteres) |
| `descripcion` | string | No | Descripción detallada de la cita |
| `fecha_inicio` | string | Sí | Fecha y hora de inicio (YYYY-MM-DD HH:MM:SS) |
| `duracion_minutos` | integer | No | Duración en minutos (15-480). Si no se especifica, se usa duración automática por tipo |
| `tipo` | string | Sí | Tipo de cita: reunion, llamada, visita, presentacion, seguimiento, otro |
| `cliente_id` | integer | No | ID del cliente asociado |
| `gestor_id` | integer | No | ID del gestor asignado |
| `ubicacion` | string | No | Ubicación de la cita |
| `color` | string | No | Color en formato hexadecimal. Si no se especifica, se usa color automático por tipo |
| `notas_internas` | string | No | Notas internas para el gestor |

### Ejemplo de Uso
```json
{
  "titulo": "Llamada de seguimiento",
  "descripcion": "Llamada para revisar el progreso del proyecto",
  "fecha_inicio": "2024-01-20 14:00:00",
  "duracion_minutos": 30,
  "tipo": "llamada",
  "cliente_id": 1,
  "gestor_id": 1,
  "ubicacion": "Remoto",
  "notas_internas": "Cliente interesado en ampliar servicios"
}
```

### Duraciones Automáticas por Tipo
- **llamada**: 30 minutos
- **reunion**: 60 minutos (1 hora)
- **visita**: 120 minutos (2 horas)
- **presentacion**: 90 minutos (1.5 horas)
- **seguimiento**: 45 minutos
- **otro**: 60 minutos (1 hora por defecto)

### Colores Automáticos por Tipo
- **llamada**: Verde (#10b981)
- **reunion**: Azul (#3b82f6)
- **visita**: Amarillo (#f59e0b)
- **presentacion**: Púrpura (#8b5cf6)
- **seguimiento**: Cian (#06b6d4)
- **otro**: Gris (#6b7280)

### Respuesta
```json
{
  "success": true,
  "message": "Cita agendada exitosamente",
  "data": {
    "id": 2,
    "titulo": "Llamada de seguimiento",
    "fecha_inicio": "2024-01-20 14:00:00",
    "fecha_fin": "2024-01-20 14:30:00",
    "estado": "programada"
  }
}
```

---

## 3. Crear Petición

### Configuración de la Herramienta
- **Nombre:** `Crear Petición`
- **Descripción:** `Crea una nueva petición o solicitud de un cliente que será asignada a un gestor específico. Las peticiones generan alertas automáticas para los gestores.`
- **Método:** `POST`
- **URL:** `http://127.0.0.1:8000/api/eleven-labs/crear-peticion`

### Parámetros del Cuerpo (JSON)
| Parámetro | Tipo | Requerido | Descripción |
|-----------|------|-----------|-------------|
| `cliente_id` | integer | Sí | ID del cliente que hace la petición |
| `gestor_id` | integer | Sí | ID del gestor asignado |
| `nota` | string | Sí | Descripción de la petición o solicitud |
| `prioridad` | string | No | Nivel de prioridad: baja, media, alta, urgente |

### Ejemplo de Uso
```json
{
  "cliente_id": 1,
  "gestor_id": 1,
  "nota": "El cliente solicita información sobre los nuevos servicios de hosting y precios actualizados. También quiere agendar una reunión para la próxima semana.",
  "prioridad": "media"
}
```

### Respuesta
```json
{
  "success": true,
  "message": "Petición creada exitosamente",
  "data": {
    "id": 1,
    "cliente": "Cliente Ejemplo",
    "gestor": "Diego Hawkins",
    "nota": "El cliente solicita información sobre los nuevos servicios...",
    "estado": "Pendiente",
    "fecha_creacion": "2024-01-15 10:30:00"
  }
}
```

---

## 4. Obtener Gestores

### Configuración de la Herramienta
- **Nombre:** `Obtener Gestores`
- **Descripción:** `Obtiene la lista de gestores disponibles en el sistema para asignar citas y peticiones. Solo incluye gestores activos con nivel de acceso apropiado.`
- **Método:** `GET`
- **URL:** `http://127.0.0.1:8000/api/eleven-labs/gestores`

### Respuesta
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Diego Hawkins",
      "email": "diego@empresa.com"
    },
    {
      "id": 2,
      "name": "Gestor Principal",
      "email": "gestor@empresa.com"
    }
  ]
}
```

---

## 5. Obtener Clientes

### Configuración de la Herramienta
- **Nombre:** `Obtener Clientes`
- **Descripción:** `Obtiene la lista completa de clientes disponibles en el sistema para asociar con citas y peticiones. Incluye información básica de contacto.`
- **Método:** `GET`
- **URL:** `http://127.0.0.1:8000/api/eleven-labs/clientes`

### Respuesta
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Cliente Corporativo",
      "company": "Empresa S.L.",
      "email": "cliente@empresa.com",
      "phone": "123456789"
    },
    {
      "id": 2,
      "name": "Cliente Importante",
      "company": "Importante S.A.",
      "email": "importante@empresa.com",
      "phone": "987654321"
    }
  ]
}
```

---

## 6. Buscar Cliente

### Configuración de la Herramienta
- **Nombre:** `Buscar Cliente`
- **Descripción:** `Busca clientes existentes por nombre, empresa o email. Útil para encontrar clientes específicos antes de crear citas o peticiones. La búsqueda es flexible y encuentra coincidencias parciales.`
- **Método:** `GET`
- **URL:** `http://127.0.0.1:8000/api/eleven-labs/buscar-cliente`

### Parámetros de Consulta
| Parámetro | Tipo | Requerido | Descripción |
|-----------|------|-----------|-------------|
| `busqueda` | string | Sí | Término de búsqueda (mín. 2 caracteres) |

### Ejemplo de Uso
```
GET /api/eleven-labs/buscar-cliente?busqueda=empresa
```

### Respuesta
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Cliente Corporativo",
      "company": "Empresa S.L.",
      "email": "cliente@empresa.com",
      "phone": "123456789"
    }
  ],
  "total": 1
}
```

---

## 7. Crear Cliente

### Configuración de la Herramienta
- **Nombre:** `Crear Cliente`
- **Descripción:** `Crea un nuevo cliente en el sistema. Útil cuando un cliente no existe y necesita ser registrado antes de crear citas o peticiones. Asigna automáticamente un gestor si se especifica.`
- **Método:** `POST`
- **URL:** `http://127.0.0.1:8000/api/eleven-labs/crear-cliente`

### Parámetros del Cuerpo (JSON)
| Parámetro | Tipo | Requerido | Descripción |
|-----------|------|-----------|-------------|
| `name` | string | Sí | Nombre del cliente |
| `company` | string | No | Nombre de la empresa |
| `email` | string | No | Email del cliente |
| `phone` | string | No | Teléfono del cliente |
| `gestor_id` | integer | No | ID del gestor asignado |

### Ejemplo de Uso
```json
{
  "name": "Juan Pérez",
  "company": "Empresa Nueva S.L.",
  "email": "juan@empresanueva.com",
  "phone": "123456789",
  "gestor_id": 1
}
```

### Respuesta
```json
{
  "success": true,
  "message": "Cliente creado exitosamente",
  "data": {
    "id": 3,
    "name": "Juan Pérez",
    "company": "Empresa Nueva S.L.",
    "email": "juan@empresanueva.com",
    "phone": "123456789"
  }
}
```

---

## Flujo de Trabajo Recomendado

### Escenario 1: Cliente Existente
1. **Buscar cliente:** `GET /buscar-cliente?busqueda=nombre`
2. **Obtener ID** del cliente de la respuesta
3. **Agendar cita:** `POST /agendar-cita` (con cliente_id)
4. **Crear petición:** `POST /crear-peticion` (con cliente_id)

### Escenario 2: Cliente Nuevo
1. **Crear cliente:** `POST /crear-cliente`
2. **Obtener ID** del cliente de la respuesta
3. **Agendar cita:** `POST /agendar-cita` (con cliente_id)
4. **Crear petición:** `POST /crear-peticion` (con cliente_id)

### Escenario 3: Solo Consulta
1. **Ver citas:** `GET /citas-disponibles?fecha_inicio=YYYY-MM-DD&fecha_fin=YYYY-MM-DD`
2. **Obtener gestores:** `GET /gestores`
3. **Obtener clientes:** `GET /clientes`

---

## Sistema de Alertas

### Alertas Automáticas
- **Citas:** Se crean alertas automáticas para los gestores cuando se agenda una nueva cita
- **Peticiones:** Se crean alertas automáticas para los gestores cuando se crea una nueva petición
- **Stage IDs:** 
  - Citas: `stage_id = 10`
  - Peticiones: `stage_id = 1`

### Tipos de Alertas
- **Nueva Cita:** "Nueva cita agendada: [título] para [fecha]"
- **Nueva Petición:** "Nueva petición de [cliente]: [resumen de la nota]"

---

## Códigos de Error

### 400 - Bad Request
```json
{
  "success": false,
  "message": "Datos de entrada inválidos",
  "errors": {
    "campo": ["mensaje de error"]
  }
}
```

### 500 - Internal Server Error
```json
{
  "success": false,
  "message": "Error interno del servidor",
  "error": "Descripción del error"
}
```

---

## Notas Importantes

1. **Autenticación:** Las APIs no requieren autenticación para el agente de Eleven Labs
2. **Validación:** Todos los endpoints incluyen validación robusta de datos
3. **Alertas:** Las citas y peticiones generan alertas automáticas para los gestores
4. **Formato de Fechas:** Usar formato ISO 8601 (YYYY-MM-DD HH:MM:SS)
5. **IDs:** Los IDs de gestores y clientes deben existir en la base de datos
6. **Estados:** Las citas se crean con estado "programada" por defecto
7. **Notificaciones:** Los gestores reciben alertas inmediatas cuando se crean citas o peticiones
8. **Búsqueda:** La búsqueda de clientes está limitada a 10 resultados para optimizar rendimiento
9. **Duración:** Si no se especifica duración, se usa la duración automática según el tipo de cita
10. **Color:** Si no se especifica color, se usa el color automático según el tipo de cita

---

## URLs de Prueba

```
✅ Ver Citas: http://127.0.0.1:8000/api/eleven-labs/citas-disponibles?fecha_inicio=2024-01-01&fecha_fin=2024-01-31
✅ Agendar Cita: POST http://127.0.0.1:8000/api/eleven-labs/agendar-cita
✅ Crear Petición: POST http://127.0.0.1:8000/api/eleven-labs/crear-peticion
✅ Buscar Cliente: http://127.0.0.1:8000/api/eleven-labs/buscar-cliente?busqueda=empresa
✅ Crear Cliente: POST http://127.0.0.1:8000/api/eleven-labs/crear-cliente
✅ Gestores: http://127.0.0.1:8000/api/eleven-labs/gestores
✅ Clientes: http://127.0.0.1:8000/api/eleven-labs/clientes
```
