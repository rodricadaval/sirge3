@extends('content')
@section('content')
<div class="row">

	<div class="col-md-8">
		<div class="box box-info">
			<div class="box-header">
				<h2 class="box-title">Listado de DDJJ generadas</h2>
			</div>
			<div class="box-body">
				<table class="table table-hover" id="ddjj-table">
	                <thead>
	                  <tr>
	                    <th>Provincia</th>
	                    <th>Periodo</th>
	                    <th>Versión</th>
	                    <th>Motivo</th>
	                    <th>Fecha</th>
	                    <th></th>
	                  </tr>
	                </thead>
	            </table>
			</div>
		</div>
	</div>
	
	<div class="col-md-4">

		<div class="row">
			<div class="col-md-12">
				<div class="info-box bg-yellow">
					<span class="info-box-icon"><i class="fa fa-book"></i></span>
					<div class="info-box-content">
						<span class="info-box-text">Jurisdicciones reportantes</span>
						<span class="info-box-number">{{ $numero }}</span>
						<div class="progress">
							<div class="progress-bar" style="width: {{ $porcentaje }}%"></div>
						</div>
						<span class="progress-description">
							<?php setlocale(LC_TIME, 'es_ES.UTF-8'); ?>
							{{ ucwords(strftime("%B %Y")) }}
						</span>
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-12">
				<div class="box box-info">
					<div class="box-header">
						<h2 class="box-title">Generar</h2>
					</div>
					<div class="box-body">
						@if (session('status'))
						    <div class="alert alert-danger">
						        {{ session('status') }}
						    </div>
						@endif
						Desde esta opción usted podrá generar la DDJJ correspondiente al backup del periodo deseado.
					</div>
					<div class="box-footer">
						<div class="btn-group" role="group">
							<button class="periodo btn btn-primary">Ingresar</button>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-12">
				<div class="box box-info">
					<div class="box-header">
						<h2 class="box-title">Consolidado</h2>
					</div>
					<div class="box-body">
						Aquí podrá ver el estado de la generación del Backup por jurisdicción.
					</div>
					<div class="box-footer">
						<div class="btn-group" role="group">
							<button class="consolidado btn btn-primary">Ver</button>
						</div>
					</div>
				</div>
			</div>
		</div>

	</div>
</div>
<script type="text/javascript">
$(document).ready(function(){

	var dt1 = $('#ddjj-table').DataTable({
		processing: true,
        serverSide: true,
        ajax : 'ddjj-backup-table',
        columns: [
            { data: 'provincia.descripcion' , name: 'id_provincia'},
            { data: 'periodo_reportado' , name: 'periodo_reportado'},
            { data: 'version'},
            { data: 'motivo_reimpresion'},
            { data: 'fecha_impresion'},
            { data: 'action'}
            
        ],
        order : [[1,'desc']]
	});

	$('.periodo').click(function(){
		$.get('ddjj-periodo/backup' , function(data){
			$('.content-wrapper').html(data);
		});
	});

	$('.consolidado').click(function(){
		$.get('ddjj-backup-consolidado' , function(data){
			$('.content-wrapper').html(data);
		});
	});

});
</script>
@endsection