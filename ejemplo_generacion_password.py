#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Ejemplo de implementación del algoritmo de generación de contraseñas
Este código genera EXACTAMENTE las mismas contraseñas que el sistema Laravel
"""

import hmac
import hashlib
import base64
import os

# ============================================================================
# CONFIGURACIÓN
# ============================================================================

# Prefijo identificador (solo decorativo, NO se usa en el hash)
PREFIJO_IDENTIFICADOR = "H11+&401m$Kva"

# Clave secreta (esta SÍ se usa para el HMAC)
# IMPORTANTE: Esta debe ser la misma que PASSWORD_SECRET_KEY en Laravel
CLAVE_SECRETA = os.getenv('PASSWORD_SECRET_KEY', 'Kv8#mX2$pL9@nR5&tQ7!wZ4%yB6^cD3')


# ============================================================================
# FUNCIÓN DE LIMPIEZA DEL DOMINIO
# ============================================================================

def limpiar_dominio(dominio):
    """
    Limpia el dominio siguiendo EXACTAMENTE el mismo proceso que Laravel.
    
    Orden CRÍTICO:
    1. Minúsculas
    2. Trim (espacios inicio/fin)
    3. Eliminar https://
    4. Eliminar http://
    5. Eliminar www.
    6. Eliminar / final
    
    Args:
        dominio: String con el dominio a limpiar
        
    Returns:
        str: Dominio limpio
    """
    # 1. Minúsculas y trim
    dominio_limpio = dominio.lower().strip()
    
    # 2. Eliminar protocolos (en orden específico)
    dominio_limpio = dominio_limpio.replace('https://', '')
    dominio_limpio = dominio_limpio.replace('http://', '')
    dominio_limpio = dominio_limpio.replace('www.', '')
    
    # 3. Eliminar barra final
    if dominio_limpio.endswith('/'):
        dominio_limpio = dominio_limpio[:-1]
    
    return dominio_limpio


# ============================================================================
# FUNCIÓN PRINCIPAL DE GENERACIÓN
# ============================================================================

def generar_password_dinamica(dominio):
    """
    Genera una contraseña determinista basada en un dominio.
    
    Este algoritmo genera EXACTAMENTE las mismas contraseñas que:
    app/Services/PasswordGeneratorService.php en Laravel
    
    Args:
        dominio: El dominio para el cual generar la contraseña
                (puede incluir https://, http://, www., etc.)
    
    Returns:
        dict: {
            'dominio_limpio': str,  # Dominio después de limpieza
            'password': str         # Contraseña generada
        }
    """
    # PASO 1: Limpieza del dominio
    dominio_limpio = limpiar_dominio(dominio)
    
    # PASO 2: Generar HMAC-SHA256
    # IMPORTANTE: Usar .digest() para obtener bytes binarios, NO .hexdigest()
    mensaje = dominio_limpio.encode('utf-8')
    llave = CLAVE_SECRETA.encode('utf-8')
    
    hash_binario = hmac.new(llave, mensaje, hashlib.sha256).digest()
    
    # PASO 3: Codificar a Base64
    pass_base64 = base64.b64encode(hash_binario).decode('utf-8')
    
    # PASO 4: Reemplazar caracteres especiales
    # Base64 puede generar '+' y '/' que causan problemas
    pass_segura = pass_base64.replace('+', '*').replace('/', '!')
    
    # PASO 5: Extraer sufijo dinámico (primeros 10 caracteres)
    sufijo_dinamico = pass_segura[:10]
    
    # PASO 6: Construcción final
    # Prefijo identificador + sufijo dinámico
    resultado = PREFIJO_IDENTIFICADOR + sufijo_dinamico
    
    return {
        'dominio_limpio': dominio_limpio,
        'password': resultado
    }


# ============================================================================
# EJEMPLO DE USO
# ============================================================================

if __name__ == "__main__":
    print("=" * 60)
    print("GENERADOR DE CONTRASEÑAS DETERMINISTAS")
    print("=" * 60)
    print(f"Prefijo identificador: {PREFIJO_IDENTIFICADOR}")
    print(f"Clave secreta: {'*' * len(CLAVE_SECRETA)}")
    print("-" * 60)
    
    # Ejemplos de prueba
    ejemplos = [
        "hawkins.es",
        "https://www.ejemplo.com/",
        "http://test.com",
        "www.dominio.es/",
    ]
    
    for dominio in ejemplos:
        resultado = generar_password_dinamica(dominio)
        print(f"\nDominio entrada: {dominio}")
        print(f"Dominio limpio:  {resultado['dominio_limpio']}")
        print(f"Contraseña:      {resultado['password']}")
    
    print("\n" + "=" * 60)
    print("NOTA: Para generar la misma contraseña que Laravel,")
    print("      asegúrate de usar la misma PASSWORD_SECRET_KEY")
    print("=" * 60)
