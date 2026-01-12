<div style="background-color: #dcdcdb;margin: 0 auto;width: 100%;height: auto;text-align:center;padding-bottom:20px;">
    <h1 style="margin:0;text-align: center;font-size: 32px">Los Creativos de Hawkins</h1>
    <div style="background-color: white;margin-left:15px;margin-right:15px;margin-bottom:15px;text-align: center;font-family: Helvetica;height: auto;padding: 40px 20px;">
        <h1 style="padding-top: 20px;margin:0;color: #333;">Estimado/a {{ $cliente->name }},</h1>
        
        <div style="margin: 30px 0;text-align: left;max-width: 600px;margin-left: auto;margin-right: auto;">
            <p style="font-size: 16px;line-height: 1.6;color: #555;">
                Le informamos que su dominio <strong style="color: #0066cc;">{{ $dominio->dominio }}</strong> 
                está próximo a caducar.
            </p>
            
            <div style="background-color: #fff3cd;border-left: 4px solid #ffc107;padding: 15px;margin: 20px 0;">
                <p style="margin: 0;font-size: 18px;font-weight: bold;color: #856404;">
                    Fecha de caducidad: {{ \Carbon\Carbon::parse($fechaCaducidad)->format('d/m/Y') }}
                </p>
            </div>
            
            <p style="font-size: 16px;line-height: 1.6;color: #555;">
                Para asegurar la continuidad de su servicio y evitar la pérdida de su dominio, 
                necesitamos que configure un método de pago válido:
            </p>
            
            <ul style="font-size: 16px;line-height: 1.8;color: #555;text-align: left;margin: 20px 0;">
                <li><strong>IBAN para domiciliación SEPA:</strong> Configure su cuenta bancaria para pagos automáticos</li>
                <li><strong>Tarjeta de crédito mediante Stripe:</strong> Añada su tarjeta de forma segura para pagos recurrentes</li>
            </ul>
            
            <div style="text-align: center;margin: 30px 0;">
                <a href="{{ $urlPago }}" 
                   style="display: inline-block;background-color: #0066cc;color: white;padding: 15px 30px;text-decoration: none;border-radius: 5px;font-weight: bold;font-size: 16px;">
                    Configurar Método de Pago
                </a>
            </div>
            
            <p style="font-size: 14px;line-height: 1.6;color: #777;margin-top: 30px;">
                Este enlace es personalizado y seguro. Expirará en 30 días por motivos de seguridad.
            </p>
            
            <p style="font-size: 14px;line-height: 1.6;color: #777;">
                Si tiene alguna pregunta o necesita asistencia, no dude en contactarnos.
            </p>
        </div>
        
        <hr style="display: block;margin-top: 30px;margin-bottom: 20px;margin-left: 40px;margin-right: 40px;border-style: inset;border-width: 1px;border-color: #ddd">
        
        <p style="font-size: 12px;color: #999;margin: 0;">
            Los Creativos de Hawkins<br>
            Este es un mensaje automático, por favor no responda a este correo.
        </p>
    </div>
</div>
