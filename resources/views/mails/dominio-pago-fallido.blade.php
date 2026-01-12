<div style="background-color: #dcdcdb;margin: 0 auto;width: 100%;height: auto;text-align:center;padding-bottom:20px;">
    <h1 style="margin:0;text-align: center;font-size: 32px">Los Creativos de Hawkins</h1>
    <div style="background-color: white;margin-left:15px;margin-right:15px;margin-bottom:15px;text-align: center;font-family: Helvetica;height: auto;padding: 40px 20px;">
        <h1 style="padding-top: 20px;margin:0;color: #d32f2f;">Estimado/a {{ $cliente->name }},</h1>
        
        <div style="margin: 30px 0;text-align: left;max-width: 600px;margin-left: auto;margin-right: auto;">
            <div style="background-color: #ffebee;border-left: 4px solid #d32f2f;padding: 15px;margin: 20px 0;">
                <p style="margin: 0;font-size: 18px;font-weight: bold;color: #c62828;">
                    ⚠️ Pago Fallido - Acción Requerida
                </p>
            </div>
            
            <p style="font-size: 16px;line-height: 1.6;color: #555;">
                Le informamos que el pago automático para la renovación de su dominio 
                <strong style="color: #0066cc;">{{ $dominio->dominio }}</strong> 
                no se ha podido procesar.
            </p>
            
            <div style="background-color: #fff3cd;border-left: 4px solid #ffc107;padding: 15px;margin: 20px 0;">
                <p style="margin: 0;font-size: 14px;color: #856404;">
                    <strong>Razón del fallo:</strong> {{ $razonFallo }}
                </p>
            </div>
            
            <p style="font-size: 16px;line-height: 1.6;color: #555;">
                Para evitar la pérdida de su dominio, es importante que actualice su método de pago lo antes posible.
            </p>
            
            <div style="text-align: center;margin: 30px 0;">
                @if($cliente->token_verificacion_dominios)
                    <a href="{{ route('dominio.pago.formulario', $cliente->token_verificacion_dominios) }}" 
                       style="display: inline-block;background-color: #d32f2f;color: white;padding: 15px 30px;text-decoration: none;border-radius: 5px;font-weight: bold;font-size: 16px;">
                        Actualizar Método de Pago
                    </a>
                @else
                    <p style="color: #d32f2f;font-weight: bold;">
                        Por favor, contacte con soporte para actualizar su método de pago.
                    </p>
                @endif
            </div>
            
            <p style="font-size: 14px;line-height: 1.6;color: #777;margin-top: 30px;">
                Si el problema persiste, por favor contacte con nuestro equipo de soporte.
            </p>
            
            <p style="font-size: 14px;line-height: 1.6;color: #777;">
                <strong>Importante:</strong> Si no actualiza su método de pago, su dominio podría caducar y perder su disponibilidad.
            </p>
        </div>
        
        <hr style="display: block;margin-top: 30px;margin-bottom: 20px;margin-left: 40px;margin-right: 40px;border-style: inset;border-width: 1px;border-color: #ddd">
        
        <p style="font-size: 12px;color: #999;margin: 0;">
            Los Creativos de Hawkins<br>
            Este es un mensaje automático, por favor no responda a este correo.
        </p>
    </div>
</div>
