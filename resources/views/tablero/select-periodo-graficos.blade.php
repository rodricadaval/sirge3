@extends('content')
@section('content')
<div class="row">
	<form>
		<div class="col-md-8 col-md-offset-2">
			<div class="box box-info">
				<div class="box-header">
					<h2 class="box-title">Ingrese parámetros</h2>
				</div>
				<div class="box-body">
					<div class="form-group">
	      				<label for="provincia" class="col-sm-3 control-label">Provincia</label>
	  					<div class="col-sm-9">
	    					<select id="provincia" name="provincia" class="form-control">
    						@if (Auth::user()->id_menu == 1 && Auth::user()->id_area == 1)
								<option value="99">TODAS</option>
							@endif
							@foreach ($provincias as $provincia)
								@if (Auth::user()->id_provincia == $provincia->id_provincia)
								<option value="{{ $provincia->id_provincia }}" selected>{{ $provincia->descripcion }}</option>
								@else
									@if (Auth::user()->id_entidad == 1)
									<option value="{{ $provincia->id_provincia }}">{{ $provincia->descripcion }}</option>
									@else
									<option value="{{ $provincia->id_provincia }}" disabled>{{ $provincia->descripcion }}</option>
									@endif
								@endif
							@endforeach
							</select>
	  					</div>
	    			</div>
	    			<br />
	    			<div class="form-group">
	      				<label for="periodo" class="col-sm-3 control-label">Período</label>
	  					<div class="col-sm-9">
	    					<input type="text" class="form-control" id="periodo" name="periodo">
	  					</div>
	    			</div>
	    			<br />
	    			<div class="form-group">
	      				<label for="periodo" class="col-sm-3 control-label">Indicador</label>
	  					<div class="col-sm-9">
	    					<select id="indicador" name="indicador" class="form-control">
							@foreach ($indicadores as $main)
									<option value="{{ $main->indicador }}">{{ $main->indicador . ' - ' . $main->descripcion }}</option>
							@endforeach
							</select>
	  					</div>
	    			</div>
				</div>
				<div class="box-footer">
					<div class="btn-group" role="group">
						<button class="send btn btn-info">Ver</button>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>
<div class="modal modal-danger">
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
        <button type="button" class="btn btn-outline" data-dismiss="modal">Cerrar</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div>
<script type="text/javascript">

	$('#periodo').inputmask({
		mask : '9999-99',
		placeholder : 'AAAA-MM'
	});

	$('.send').click(function(){
		$('form').validate({
			rules : {
				provincia : {
					required : true
				},
				periodo : {
					required : true,
					minlength : 7,
					maxlength : 7
				}
			},
			submitHandler : function(form){
				$.get('graficos-tablero/' + $('#periodo').val() + '/' + $('#provincia').val() + '/' + $('#indicador').val(), function(data){
					if(data != 'error'){
						$('.content-wrapper').html(data);
					}
					else{
						$('#modal-text').html("No existe ningún indicador en el periodo");
						$('.modal').modal();
						$('.modal').on('hidden.bs.modal', function (e) {
							$.get('select-graficos-tablero' , function(data){
								$('.content-wrapper').html(data);
							});
						});
					}
				});
			}
		});
	});
</script>
@endsection