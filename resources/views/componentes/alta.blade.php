@extends('content')
@section('content')
<style type="text/css">
.navi li{
    text-align: center;
    padding: 2px;
    width: 150px;
    display:inline-block;
}
.error {
	color:red;
}
</style>
<div class="row">
	<form id="alta-efector">
		<div class="col-md-12">
			<div class="box box-info">
				<div class="box-header">
					<h2 class="box-title">Complete todos los campos</h2>
				</div>
				<div class="box-body">
					<div class="alert alert-danger" id="errores-div">
				        <ul id="errores-form">
				        </ul>
				    </div>
					<div id="rootwizard">
						<div class="navbar navbar-static-top">
							<div class="navbar-inner">
						    	<div class="container navi">
									<ul>
						  				<li><a href="#generales" data-toggle="tab">Generales</a></li>
										<li><a href="#planificacion" data-toggle="tab">Planificado</a></li>
										<li><a href="#observado" data-toggle="tab">Observado</a></li>				
									</ul>
						 		</div>
						  	</div>
						</div>
						<div class="progress progress-xxs">
							<div class="progress-bar progress-bar-red" role="progressbar" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100"></div>
						</div>
						<div class="tab-content">
						    <div class="tab-pane" id="generales">
						    	<div class="row">
						    		<div class="col-md-4">
										<div class="form-group">
							    			<label for="provincia" class="col-sm-3 control-label">Provincia</label>
							    			<div class="col-sm-9">
								    			<select id="provincia" name="provincia" class="form-control">
								    				<option value="">Seleccione ...</option>
								    				@foreach($provincias as $provincia)   					
								    						<option value="{{ $provincia->id_provincia }}">{{ $provincia->descripcion }}</option>	
								    				@endforeach
								    			</select>
							    			</div>
						    			</div>
						    		</div>
						    		<div class="col-md-8">
						    			<div class="form-group">
							    			<label for="indicador" class="col-sm-3 control-label">Indicador</label>
							    			<div class="col-sm-9">
								    			<select id="indicador" name="indicador" class="form-control">
								    				<option value="">Seleccione ...</option>
								    				@foreach($odp as $unOdp)		    						
								    				<option value="{{ $unOdp->id_indicador }}">{{$unOdp->odp . $unOdp->tipo .  ' - ' . $unOdp->descripcion }}</option>
								    													    					
								    				@endforeach
								    			</select>
							    			</div>
						    			</div>
						    		</div>						    		
						    	</div>
						    	<br />
						    	<div class="row">
						    		<div class="col-md-4">
										<div class="form-group">
							    			<label for="year" class="col-sm-3 control-label">A&ntilde;o</label>
							    			<div class="col-sm-9">
								    			<input type="text" name="year" class="form-control" id="year-datepicker">
							    			</div>
						    			</div>
						    		</div>						    								    	
						    	</div>						    							    	
						    </div>
						    <div class="tab-pane" id="planificacion">
						    						    						    	
						    </div>
							<div class="tab-pane" id="observado">
								   	
						    </div>							
							<ul class="pager wizard">
								<li class="previous"><a href="javascript:;">Anterior</a></li>
							  	<li class="next"><a href="javascript:;">Siguiente</a></li>
							</ul>
						</div>	
					</div>
				</div>
				<div class="box-footer">
					<div class="btn-group " role="group">					 	
					 	<button type="submit" class="finish btn btn-warning">Cargar datos</button>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>
<div class="modal fade modal-info">
	<div class="modal-dialog">
		<div class="modal-content">
      		<div class="modal-header">
        		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
    			<h4 class="modal-title">Atención!</h4>
      		</div>
  			<div class="modal-body">
  				<p id="modal-text"></p>
      		</div>
      		<div class="modal-footer">
        		<button type="button" class="btn btn-outline pull-left" data-dismiss="modal">Cerrar</button>
      		</div>
    	</div><!-- /.modal-content -->
  	</div><!-- /.modal-dialog -->
</div>
<script type="text/javascript">
$(document).ready(function() {
	
	$('.finish').hide();
	$('#errores-div').hide();

	var $validator = $('form').validate({
		rules : {			
			provincia : {
				required : true
			},
			indicador : {
				required : true
			},
			year : {				
				number : true,
				required : true
			},
			linea_base : {				
				number : true
			},
			@foreach(range(1,88) as $ind)
			'{{$ind}}' : {						
				number : true
			},
			@endforeach
		},
		messages: {
			provincia : {
				required : 'La provincia es requerida'
			},
			indicador : {
				required : 'Debe seleccionar un ODP'
			},
			@foreach(range(1,88) as $ind)
			'{{$ind}}' : {						
				number : 'Sólo puede ingresar numeros'
			},
			@endforeach				
		},
		submitHandler : function(form){
			$.ajax({
				method : 'post',
				url : 'carga-odp',
				data : $(form).serialize() + '&provincia=' + $('#provincia').val() + '&id_tipo_meta=' + $('#indicador').val(),
				success : function(data){
					console.log(data);
					$('#modal-text').html(data);
					$('.modal').modal();					
				},
				error : function(data){
					var html = '';
					var e = JSON.parse(data.responseText);
					$.each(e , function (key , value){
						html += '<li>' + value[0] + '</li>';
					});
					$('#errores-form').html(html);
					$('#errores-div').show();
				}
			})
			$('.navbar-inner a[href="#generales"]').tab('show');
			$('form').trigger('reset');
		}
	});
	

	$('#indicador,#provincia,#year-datepicker').on('change',function(){		
		var indicador = $('#indicador').val();
		var provincia = $('#provincia').val();	
		var year = $('#year-datepicker').val();	
		console.log(indicador + ' - ' + provincia + ' - ' + year);

		if($('#provincia').val() != '' && $('#indicador').val() != '' && $('#year-datepicker').val() != ''){
			$.get('metas-odp-indicador/' + indicador + '/' + provincia + '/planificado' + '/' + year, function(data){			
				console.log(data);
				$('#planificacion').html(data);
			});
			$.get('metas-odp-indicador/' + indicador + '/' + provincia + '/observado' + '/' + year, function(data){
				$('#observado').html(data);			
			});
		}		
	});
	
  	$('#rootwizard').bootstrapWizard({
  		onTabShow: function(tab, navigation, index) {
			var $total = navigation.find('li').length;
			var $current = index+1;
			var $percent = ($current/$total) * 100;
			$('.progress-bar').css({width:$percent+'%'});

			if($current >= $total) {
				$('.finish').show()
			} else {
				$('.finish').hide()
			}

		},
		onTabClick : function(tab, navigation, index){
			return false;
		},
		onNext : function(tab, navigation, index){
			var $valid = $('form').valid();
  			if(!$valid) {
  				$validator.focusInvalid();
  				return false;
  			}
		}
	});

	$("#year-datepicker").datepicker({
	    format: "yyyy",
	    viewMode: "years", 
	    minViewMode: "years"
	}).on('changeDate', function(e){
	    $(this).datepicker('hide');
	});
});
</script>
@endsection