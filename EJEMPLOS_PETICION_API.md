# Ejemplos de Petición a la API - Obtener Cliente por Dominio

## URL Base
```
https://crm.hawkins.es/api/crm/cliente/por-dominio
```

## Ejemplo 1: GET (Query Parameter)

### cURL
```bash
curl -X GET "https://crm.hawkins.es/api/crm/cliente/por-dominio?dominio=academia-britannia.com"
```

### JavaScript (Fetch)
```javascript
fetch('https://crm.hawkins.es/api/crm/cliente/por-dominio?dominio=academia-britannia.com')
  .then(response => response.json())
  .then(data => {
    if (data.error) {
      console.error('Error:', data.error);
    } else {
      console.log('Cliente encontrado:', data);
      console.log('Nombre:', data.nombre);
      console.log('Email:', data.email);
    }
  })
  .catch(error => console.error('Error de red:', error));
```

### PHP (Guzzle)
```php
use GuzzleHttp\Client;

$client = new Client();
$response = $client->get('https://crm.hawkins.es/api/crm/cliente/por-dominio', [
    'query' => ['dominio' => 'academia-britannia.com']
]);

$data = json_decode($response->getBody(), true);

if (isset($data['error'])) {
    echo "Error: " . $data['error'];
} else {
    echo "Cliente: " . $data['nombre'];
    echo "Email: " . $data['email'];
}
```

### Python (requests)
```python
import requests

response = requests.get('https://crm.hawkins.es/api/crm/cliente/por-dominio', 
                        params={'dominio': 'academia-britannia.com'})

data = response.json()

if 'error' in data:
    print(f"Error: {data['error']}")
else:
    print(f"Cliente: {data['nombre']}")
    print(f"Email: {data['email']}")
```

---

## Ejemplo 2: POST (JSON Body)

### cURL
```bash
curl -X POST "https://crm.hawkins.es/api/crm/cliente/por-dominio" \
  -H "Content-Type: application/json" \
  -d '{"dominio":"academia-britannia.com"}'
```

### JavaScript (Fetch)
```javascript
fetch('https://crm.hawkins.es/api/crm/cliente/por-dominio', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
  },
  body: JSON.stringify({
    dominio: 'academia-britannia.com'
  })
})
  .then(response => response.json())
  .then(data => {
    if (data.error) {
      console.error('Error:', data.error);
    } else {
      console.log('Cliente encontrado:', data);
      console.log('ID Cliente:', data.idCliente);
      console.log('Nombre:', data.nombre);
      console.log('Email:', data.email);
      console.log('Teléfono:', data.telefono);
      console.log('NIF:', data.nif);
      console.log('Dirección:', data.direccion);
      console.log('Ciudad:', data.ciudad);
      console.log('Provincia:', data.provincia);
      console.log('Código Postal:', data.codigoPostal);
      console.log('País:', data.pais);
    }
  })
  .catch(error => console.error('Error de red:', error));
```

### PHP (Guzzle)
```php
use GuzzleHttp\Client;

$client = new Client();
$response = $client->post('https://crm.hawkins.es/api/crm/cliente/por-dominio', [
    'json' => [
        'dominio' => 'academia-britannia.com'
    ]
]);

$data = json_decode($response->getBody(), true);

if (isset($data['error'])) {
    echo "Error: " . $data['error'];
} else {
    echo "ID Cliente: " . $data['idCliente'] . "\n";
    echo "Nombre: " . $data['nombre'] . "\n";
    echo "Email: " . $data['email'] . "\n";
    echo "Teléfono: " . ($data['telefono'] ?? 'N/A') . "\n";
    echo "NIF: " . ($data['nif'] ?? 'N/A') . "\n";
    echo "Dirección: " . ($data['direccion'] ?? 'N/A') . "\n";
    echo "Ciudad: " . ($data['ciudad'] ?? 'N/A') . "\n";
    echo "Provincia: " . ($data['provincia'] ?? 'N/A') . "\n";
    echo "Código Postal: " . ($data['codigoPostal'] ?? 'N/A') . "\n";
    echo "País: " . ($data['pais'] ?? 'N/A') . "\n";
}
```

### Python (requests)
```python
import requests
import json

url = 'https://crm.hawkins.es/api/crm/cliente/por-dominio'
payload = {
    'dominio': 'academia-britannia.com'
}

response = requests.post(url, json=payload)

data = response.json()

if 'error' in data:
    print(f"Error: {data['error']}")
else:
    print(f"ID Cliente: {data['idCliente']}")
    print(f"Nombre: {data['nombre']}")
    print(f"Email: {data['email']}")
    print(f"Teléfono: {data.get('telefono', 'N/A')}")
    print(f"NIF: {data.get('nif', 'N/A')}")
    print(f"Dirección: {data.get('direccion', 'N/A')}")
    print(f"Ciudad: {data.get('ciudad', 'N/A')}")
    print(f"Provincia: {data.get('provincia', 'N/A')}")
    print(f"Código Postal: {data.get('codigoPostal', 'N/A')}")
    print(f"País: {data.get('pais', 'N/A')}")
```

---

## Respuesta Exitosa (200)

```json
{
  "idCliente": "1034",
  "nombre": "SOLEDAD MOYANO ESTERO",
  "email": "abritannia@yahoo.es",
  "telefono": null,
  "nif": "31856534Q",
  "direccion": "Algeciras",
  "codigoPostal": "11203",
  "ciudad": "C/Salvador Cabrera, Edif Aurora II, Local 83-8",
  "provincia": "Cádiz",
  "pais": "España"
}
```

## Respuesta de Error (404)

```json
{
  "error": "No se encontró cliente para este dominio",
  "dominio_buscado": "dominio-inexistente.com"
}
```

---

## Ejemplo Completo con Manejo de Errores (JavaScript)

```javascript
async function obtenerClientePorDominio(dominio) {
  try {
    const response = await fetch('https://crm.hawkins.es/api/crm/cliente/por-dominio', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ dominio: dominio })
    });

    const data = await response.json();

    if (!response.ok || data.error) {
      throw new Error(data.error || 'Error al obtener cliente');
    }

    return {
      exito: true,
      cliente: {
        id: data.idCliente,
        nombre: data.nombre,
        email: data.email,
        telefono: data.telefono,
        nif: data.nif,
        direccion: data.direccion,
        ciudad: data.ciudad,
        provincia: data.provincia,
        codigoPostal: data.codigoPostal,
        pais: data.pais
      }
    };
  } catch (error) {
    return {
      exito: false,
      error: error.message
    };
  }
}

// Uso
obtenerClientePorDominio('academia-britannia.com')
  .then(resultado => {
    if (resultado.exito) {
      console.log('Cliente:', resultado.cliente);
    } else {
      console.error('Error:', resultado.error);
    }
  });
```

---

## Ejemplo Completo con Manejo de Errores (PHP)

```php
<?php

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

function obtenerClientePorDominio($dominio) {
    $client = new Client();
    
    try {
        $response = $client->post('https://crm.hawkins.es/api/crm/cliente/por-dominio', [
            'json' => ['dominio' => $dominio],
            'timeout' => 30
        ]);
        
        $data = json_decode($response->getBody(), true);
        
        if (isset($data['error'])) {
            return [
                'exito' => false,
                'error' => $data['error']
            ];
        }
        
        return [
            'exito' => true,
            'cliente' => $data
        ];
        
    } catch (RequestException $e) {
        return [
            'exito' => false,
            'error' => 'Error de conexión: ' . $e->getMessage()
        ];
    }
}

// Uso
$resultado = obtenerClientePorDominio('academia-britannia.com');

if ($resultado['exito']) {
    echo "Cliente: " . $resultado['cliente']['nombre'] . "\n";
    echo "Email: " . $resultado['cliente']['email'] . "\n";
} else {
    echo "Error: " . $resultado['error'] . "\n";
}
```

---

## Prueba Rápida con cURL (Copia y Pega)

```bash
# GET
curl "https://crm.hawkins.es/api/crm/cliente/por-dominio?dominio=academia-britannia.com"

# POST
curl -X POST "https://crm.hawkins.es/api/crm/cliente/por-dominio" \
  -H "Content-Type: application/json" \
  -d '{"dominio":"academia-britannia.com"}'
```

---

## Notas Importantes

1. **URL**: Usa `https://crm.hawkins.es` (no `http://127.0.0.1:8000` en producción)
2. **Dominio**: Envía exactamente `academia-britannia.com` (sin espacios, sin www)
3. **Content-Type**: Para POST, siempre incluye `Content-Type: application/json`
4. **Manejo de nulls**: Algunos campos pueden ser `null`, verifica antes de usar
5. **Errores**: Siempre verifica si hay un campo `error` en la respuesta
