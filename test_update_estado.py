# -*- coding: utf-8 -*-
import requests
import sys

if sys.platform == 'win32':
    sys.stdout.reconfigure(encoding='utf-8')

# Test: Actualizar estado de una justificación
justificacion_id = 28
url = f"https://crm.hawkins.es/justificaciones/update-estado/{justificacion_id}"

# Datos a enviar
data = {
    "estado": "completado",
    "mensaje": "Proceso completado exitosamente - 3 PDFs generados"
}

print(f"Actualizando estado de justificación #{justificacion_id}")
print(f"URL: {url}")
print(f"Datos: {data}")

try:
    response = requests.post(url, json=data, timeout=10)
    
    print(f"\nStatus Code: {response.status_code}")
    print(f"Response: {response.text}")
    
    if response.status_code == 200:
        print("\nEXITO! Estado actualizado")
    else:
        print(f"\nERROR: {response.status_code}")
        
except Exception as e:
    print(f"\nERROR: {str(e)}")

