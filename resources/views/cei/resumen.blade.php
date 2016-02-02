@extends('content')
@section('content')
<div class="row">
	<div class="col-md-12">
		<div class="box box-info">
			<div class="box-header">
				<h2 class="box-title">Ingrese los filtros necesarios</h2>
			</div>
			<div class="box-body">
				<div class="form-group">
      				<label for="periodo" class="col-sm-3 control-label">Período</label>
  					<div class="col-sm-9">
    					<input type="text" class="form-control" id="periodo" name="periodo">
  					</div>
    			</div>

    			<div class="form-group">
      				<label for="linea_cuidado" class="col-sm-3 control-label">Grupo Etario</label>
  					<div class="col-sm-9">
    					<input type="text" class="form-control" id="linea_cuidado" name="linea_cuidado">
  					</div>
    			</div>

    			<div class="form-group">
      				<label for="linea_cuidado" class="col-sm-3 control-label">Línea de cuidado</label>
  					<div class="col-sm-9">
    					<input type="text" class="form-control" id="linea_cuidado" name="linea_cuidado">
  					</div>
    			</div>

    			<div class="form-group">
      				<label for="provincia" class="col-sm-3 control-label">Jurisdicción</label>
  					<div class="col-sm-9">
    					<select id="provincia" name="provincia" class="form-control">
    						<option value="99">TODO EL PAÍS</option>
						@foreach ($provincias as $provincia)
							<option value="{{ $provincia->id_provincia }}">{{ $provincia->descripcion }}</option>
						@endforeach
						</select>
  					</div>
    			</div>

			</div>
			<div class="box-footer">
				<div class="btn-group" role="group">
					<button class="go btn btn-info">Ver resumen</button>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection