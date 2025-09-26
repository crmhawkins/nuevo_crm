# Configuraciones para Copiar y Pegar en Eleven Labs

## 1. Ver Citas Disponibles

### Configuración Básica
- **Nombre:** `Ver Citas Disponibles`
- **Descripción:** `Obtiene las citas disponibles en un rango de fechas específico. Útil para consultar la disponibilidad del calendario y ver citas programadas. Permite filtrar por gestor específico.`
- **Método:** `GET`
- **URL:** `https://crm.hawkins.es/api/eleven-labs/citas-disponibles`

### Descripción Principal (Para el Campo "Descripción" en Eleven Labs)
```
Esta herramienta permite al agente de llamadas consultar las citas existentes en el sistema. El agente debe extraer de la conversación:

1. FECHA INICIO: Desde qué fecha consultar (formato: YYYY-MM-DD)
2. FECHA FIN: Hasta qué fecha consultar (formato: YYYY-MM-DD)
3. GESTOR: Si se menciona un gestor específico, filtrar por él

Útil para verificar disponibilidad, consultar citas existentes, o informar al cliente sobre citas programadas. El agente puede usar esta información para coordinar mejor las citas y evitar conflictos de horarios.
```

### Parámetros de Consulta
- **fecha_inicio** (string, requerido): Fecha de inicio en formato YYYY-MM-DD
- **fecha_fin** (string, requerido): Fecha de fin en formato YYYY-MM-DD  
- **gestor_id** (integer, opcional): ID del gestor para filtrar citas específicas

---

## 2. Agendar Cita

### Configuración Básica
- **Nombre:** `Agendar Cita`
- **Descripción:** `Crea una nueva cita en el calendario del sistema. Permite agendar reuniones, llamadas, visitas y otros tipos de citas con clientes y gestores. La duración y color se calculan automáticamente según el tipo de cita.`
- **Método:** `POST`
- **URL:** `https://crm.hawkins.es/api/eleven-labs/agendar-cita`

### Descripción Principal (Para el Campo "Descripción" en Eleven Labs)
```
Esta herramienta permite al agente de llamadas crear citas en el sistema de gestión. El agente debe extraer de la conversación:

1. TÍTULO: El asunto o motivo de la cita (ej: "Reunión de seguimiento", "Llamada comercial", "Presentación de servicios")
2. FECHA Y HORA: Cuándo se realizará la cita (formato: YYYY-MM-DD HH:MM:SS)
3. TIPO: Qué tipo de cita es (reunion, llamada, visita, presentacion, seguimiento, otro)
4. CLIENTE: Si se menciona un cliente específico, buscar su ID o crear uno nuevo
5. GESTOR: Asignar a un gestor específico si se menciona
6. DESCRIPCIÓN: Detalles adicionales sobre la cita
7. UBICACIÓN: Dónde se realizará (oficina, remoto, domicilio del cliente, etc.)
8. NOTAS INTERNAS: Información importante para el gestor

La herramienta calculará automáticamente la duración y color según el tipo de cita. Si no se especifica duración, usará valores por defecto: llamadas 30min, reuniones 60min, visitas 120min, etc.
```

### Parámetros del Cuerpo
- **titulo** (string, requerido): Título de la cita (máx. 255 caracteres)
- **descripcion** (string, opcional): Descripción detallada de la cita
- **fecha_inicio** (string, requerido): Fecha y hora de inicio (YYYY-MM-DD HH:MM:SS)
- **duracion_minutos** (integer, opcional): Duración en minutos (15-480). Si no se especifica, se usa duración automática por tipo
- **tipo** (string, requerido): Tipo de cita: reunion, llamada, visita, presentacion, seguimiento, otro
- **cliente_id** (integer, opcional): ID del cliente asociado
- **gestor_id** (integer, opcional): ID del gestor asignado
- **ubicacion** (string, opcional): Ubicación de la cita
- **color** (string, opcional): Color en formato hexadecimal. Si no se especifica, se usa color automático por tipo
- **notas_internas** (string, opcional): Notas internas para el gestor

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

---

## 3. Crear Petición

### Configuración Básica
- **Nombre:** `Crear Petición`
- **Descripción:** `Crea una nueva petición o solicitud de un cliente que será asignada a un gestor específico. Las peticiones generan alertas automáticas para los gestores.`
- **Método:** `POST`
- **URL:** `https://crm.hawkins.es/api/eleven-labs/crear-peticion`

### Descripción Principal (Para el Campo "Descripción" en Eleven Labs)
```
Esta herramienta permite al agente de llamadas crear peticiones o solicitudes de clientes en el sistema. El agente debe extraer de la conversación:

1. CLIENTE: Identificar al cliente que hace la petición (buscar por nombre o crear nuevo)
2. GESTOR: Asignar a un gestor específico que se encargará de la petición
3. NOTA: Descripción detallada de lo que solicita el cliente (servicios, información, soporte, etc.)
4. PRIORIDAD: Nivel de urgencia (baja, media, alta, urgente) basado en el contexto de la conversación

La petición generará una alerta automática para el gestor asignado, quien podrá revisar y gestionar la solicitud del cliente.
```

### Parámetros del Cuerpo
- **cliente_id** (integer, requerido): ID del cliente que hace la petición
- **gestor_id** (integer, requerido): ID del gestor asignado
- **nota** (string, requerido): Descripción de la petición o solicitud
- **prioridad** (string, opcional): Nivel de prioridad: baja, media, alta, urgente

---

## 4. Obtener Gestores

### Configuración Básica
- **Nombre:** `Obtener Gestores`
- **Descripción:** `Obtiene la lista de gestores disponibles en el sistema para asignar citas y peticiones. Solo incluye gestores activos con nivel de acceso apropiado.`
- **Método:** `GET`
- **URL:** `https://crm.hawkins.es/api/eleven-labs/gestores`

### Descripción Principal (Para el Campo "Descripción" en Eleven Labs)
```
Esta herramienta permite al agente de llamadas obtener la lista de gestores disponibles en el sistema. Útil para:

1. CONSULTAR GESTORES: Ver qué gestores están disponibles para asignar citas o peticiones
2. ASIGNAR RESPONSABLES: Seleccionar el gestor apropiado para una tarea específica
3. VERIFICAR DISPONIBILIDAD: Confirmar que un gestor existe antes de asignarle trabajo

El agente puede usar esta información para asignar citas y peticiones a los gestores correctos del sistema.
```

---

## 5. Obtener Clientes

### Configuración Básica
- **Nombre:** `Obtener Clientes`
- **Descripción:** `Obtiene la lista completa de clientes disponibles en el sistema para asociar con citas y peticiones. Incluye información básica de contacto.`
- **Método:** `GET`
- **URL:** `https://crm.hawkins.es/api/eleven-labs/clientes`

### Descripción Principal (Para el Campo "Descripción" en Eleven Labs)
```
Esta herramienta permite al agente de llamadas obtener la lista completa de clientes del sistema. Útil para:

1. CONSULTAR CLIENTES: Ver todos los clientes disponibles en el sistema
2. VERIFICAR EXISTENCIA: Confirmar si un cliente específico ya existe
3. OBTENER INFORMACIÓN: Acceder a datos de contacto de clientes existentes
4. ASIGNAR CITAS: Seleccionar clientes para asociar con citas o peticiones

El agente puede usar esta información para identificar clientes existentes y evitar duplicados.
```

---

## 6. Buscar Cliente

### Configuración Básica
- **Nombre:** `Buscar Cliente`
- **Descripción:** `Busca clientes existentes por nombre, empresa o email. Útil para encontrar clientes específicos antes de crear citas o peticiones. La búsqueda es flexible y encuentra coincidencias parciales.`
- **Método:** `GET`
- **URL:** `https://crm.hawkins.es/api/eleven-labs/buscar-cliente`

### Descripción Principal (Para el Campo "Descripción" en Eleven Labs)
```
Esta herramienta permite al agente de llamadas buscar clientes específicos en el sistema. El agente debe extraer de la conversación:

1. TÉRMINO DE BÚSQUEDA: Nombre, empresa o email del cliente mencionado en la conversación
2. IDENTIFICAR CLIENTE: Encontrar al cliente correcto entre los resultados
3. OBTENER ID: Usar el ID del cliente encontrado para otras operaciones

La búsqueda es flexible y encuentra coincidencias parciales, útil cuando el cliente menciona solo parte del nombre o empresa.
```

### Parámetros de Consulta
- **busqueda** (string, requerido): Término de búsqueda (mín. 2 caracteres)

---

## 7. Crear Cliente

### Configuración Básica
- **Nombre:** `Crear Cliente`
- **Descripción:** `Crea un nuevo cliente en el sistema. Útil cuando un cliente no existe y necesita ser registrado antes de crear citas o peticiones. Asigna automáticamente un gestor si se especifica.`
- **Método:** `POST`
- **URL:** `https://crm.hawkins.es/api/eleven-labs/crear-cliente`

### Descripción Principal (Para el Campo "Descripción" en Eleven Labs)
```
Esta herramienta permite al agente de llamadas crear nuevos clientes en el sistema. El agente debe extraer de la conversación:

1. NOMBRE: Nombre completo del cliente
2. EMPRESA: Nombre de la empresa (si es cliente corporativo)
3. EMAIL: Dirección de correo electrónico del cliente
4. TELÉFONO: Número de teléfono de contacto
5. GESTOR: Asignar a un gestor específico si se menciona

Útil cuando un cliente nuevo llama y necesita ser registrado antes de crear citas o peticiones. El sistema asignará automáticamente un gestor por defecto si no se especifica uno.
```

### Parámetros del Cuerpo
- **name** (string, requerido): Nombre del cliente
- **company** (string, opcional): Nombre de la empresa
- **email** (string, opcional): Email del cliente
- **phone** (string, opcional): Teléfono del cliente
- **gestor_id** (integer, opcional): ID del gestor asignado

---

## Flujo de Trabajo Recomendado

### Para Cliente Existente:
1. Buscar cliente con "Buscar Cliente"
2. Usar el ID del cliente en "Agendar Cita" o "Crear Petición"

### Para Cliente Nuevo:
1. Crear cliente con "Crear Cliente"
2. Usar el ID del cliente en "Agendar Cita" o "Crear Petición"

### Para Consultas:
1. Usar "Ver Citas Disponibles" para consultar calendario
2. Usar "Obtener Gestores" para ver gestores disponibles
3. Usar "Obtener Clientes" para ver todos los clientes

---

## Notas Importantes

- **No requiere autenticación** para el agente de Eleven Labs
- **Validación robusta** en todos los endpoints
- **Alertas automáticas** para gestores cuando se crean citas o peticiones
- **Formato de fechas:** YYYY-MM-DD HH:MM:SS
- **Duración automática** si no se especifica
- **Color automático** si no se especifica
- **Búsqueda limitada** a 10 resultados para optimizar rendimiento
