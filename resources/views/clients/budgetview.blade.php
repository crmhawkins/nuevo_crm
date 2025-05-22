<html>

<head>
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	<style type="text/css">

		.custom-desktop{
			display: block;
		}

		.custom-mobile{
			display: none;
		}

		/*
		  ##Device = Tablets, Ipads (portrait)
		  ##Screen = B/w 768px to 1024px
		*/

		@media (min-width: 768px) and (max-width: 1024px) {

		  	/* CSS */
		  	.custom-mobile{
				display: block;
				text-align: center;
			}

			.custom-desktop{
				display: none;
			}

		}

		/*
		  ##Device = Tablets, Ipads (landscape)
		  ##Screen = B/w 768px to 1024px
		*/

		@media (min-width: 768px) and (max-width: 1024px) and (orientation: landscape) {

		  /* CSS */
		  	.custom-mobile{
				display: block;
				text-align: center;
			}

			.custom-desktop{
				display: none;
			}

		}

		/*
		  ##Device = Low Resolution Tablets, Mobiles (Landscape)
		  ##Screen = B/w 481px to 767px
		*/

		@media (min-width: 481px) and (max-width: 767px) {

		  /* CSS */
		  	.custom-mobile{
				display: block;
				text-align: center;
			}

			.custom-desktop{
				display: none;
			}

		}

		/*
		  ##Device = Most of the Smartphones Mobiles (Portrait)
		  ##Screen = B/w 320px to 479px
		*/

		@media (min-width: 320px) and (max-width: 480px) {

		  /* CSS */
		  	.custom-mobile{
				display: block;
				text-align: center;
			}

			.custom-desktop{
				display: none;
			}

		}

	</style>

</head>

<body>

	<div class="container-fluid">
		<div class="row h-100 justify-content-md-center">
			<div class="col-12 custom-desktop ">
				<object id='objectPdf' data="{{ asset('storage/assets/budgets/' .$filename)}}" width='100%' height="100%" type='application/pdf' style='float:left;width:100%;height: 100%;'>
				    <embed src="{{ asset('storage/assets/budgets/' .$filename)}}" type='application/pdf' width='100%' height="100%"/>
				</object>
			</div>
			<div class="col-12 custom-mobile mt-5">
				<a class="btn btn-primary" id='pdfdown' href="{{ asset('storage/assets/budgets/' .$filename)}}" data-usage="{{ asset('storage/assets/budgets/' .$filename)}}" download>

					<h1 style="text-align: center;">Ver presupuesto</h1>
				</a>
			</div>
		</div>
	</div>

<body>
<footer>

<div style='top: 30% !important;' class='modal' id='myModal' role='dialog'>
	<div class='modal-dialog'>
	   <div class='modal-content'>
	    	<div class='modal-header'>
	    		<h4 class='modal-title'>Terminos</h4>
	    	</div>
	   <div class='modal-body'>
	    	<p>Acepte los terminos, por favor.</p>
	    	<input id='checkterminos' type='checkbox'> Aceptar los <a href="{{ asset('assets/politica_de_privacidad_presupuestos.pdf') }}" target='_blank'>terminos y condiciones</a><br>
	   </div>
	    	<div class='modal-footer'>
	    		<button type='button' class='btn btn-defaul aceptar' data-dismiss='modal'>Aceptar</button>
	    	</div>
	   </div>
	</div>
</div>

<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

</footer>

<script>

	$(document).ready(function () {
		var accept = "{{$budget_val->acceptance_conds}}";
		if(accept != 1){
			$("#myModal").modal({backdrop: 'static', keyboard: false});
			$(".aceptar").prop('disabled', true);
		}

	});


	$('object').attr({'width':$( window ).width()});
	$('object').attr({'height':$( window ).height()});


	$('.aceptar').click(function () {

		$("#pdfdown").get(0).click();

		$.ajax({
			type: "POST",
			url: "/budget/acceptance",
			headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            },
			data:{
				"id" : "{{ $budget_val->id }}"
			},
			dataType: "json",
			success: function (data){
				console.log(data);
			}
		});

	});


	$( '#checkterminos' ).on( 'click', function() {
	    if( $(this).is(':checked') ){
	    	$(".aceptar").prop('disabled', false);
	    } else {
	    	$(".aceptar").prop('disabled', true);
	    }
	});



</script>
</html>
