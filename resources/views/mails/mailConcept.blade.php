<div style="background-color: #dcdcdb;margin: 0 auto;width: 100%;height: auto;text-align:center;padding-bottom:20px;">
	<h1 style="margin:0;text-align: center;font-size: 32px">Los Creativos de Hawkins</h1>
	<div style="background-color: white;margin-left:15px;margin-right:15px;margin-bottom:15px;text-align: center;font-family: Helvetica;height: auto">
		<h1 style="padding-top: 40px;margin:0;">¡HOLA!</h1>
		<p style="font-size: 18px;margin: 5px;">¡SOLICITAMOS TU MEJOR OFERTA!</p>
		<p>Por favor, me gustaría que me envíes tu mejor precio para: {{$MailConcept->cantidad}} {{$MailConcept->titulo}} con las siguientes características: {{$MailConcept->concepto}}
        </p>
		<p>	Recuerda enviárnoslo a la mayor brevedad posible.
			
			No olvides ponerte en contacto conmigo para cualquier duda o aclaración.
			
		</p>
		<p>Muchas gracias y que tengas un buen día!!</p>
			@if($MailConcept->files)	
				<p>Por favor revise la información adjunta</p>
			@endif
            <hr style="display: block;margin-top: 0.5em;margin-bottom: 0.5em;margin-left: 40px;margin-right: 40px;border-style: inset;border-width: 1px;border-color: #282828">
        <p style="padding-left: 40px;text-align: left;margin:2px;">Responsable Comercial: {{$MailConcept->gestor}}</p>
        <p style="padding-left: 40px;text-align: left;margin:2px;">Correo: {{$MailConcept->gestorMail}}</p>
        <p style="padding-bottom:5px;padding-left: 40px;text-align: left;margin:2px;">Telefono: {{$MailConcept->gestorTel}}</p>
	</div>
	
	<br></br>
</div>