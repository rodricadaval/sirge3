<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style type="text/css">
	
	/*
	.row-even {
		background-color: #abcdef;
	}
	*/

	.table-header {
		font-size: 10px; 
		font-weight: bold; 
		text-align: center;
		background-color: #00c0ef;
	}

	.table-title , .title-sumar{
		color: #ffffff;
		font-size: 12px;
		font-weight: bold;
		background-color: #00c0ef;
	}

	td {
		font-size: 9px;
	}

</style>

<table class="table">
	<tr>
		<td class="title-sumar">PROGRAMA SUMAR</td>
		<td class="title-sumar">Tabla de Rechazos</td>
	</tr>
	<tr>
		<td>Fecha de &uacute;ltima actualizaci&oacute;n: </td>
		<td>{{date('d/m/Y')}} </td>
	</tr>
	<tr>
		<td>Origen de datos: </td>
		<td>SIRGe Web V.3</td>
	</tr>
	<tr></tr>
	<tr>
		<td class="table-header">EFECTOR</th>
		<td class="table-header">FECHA_GASTO</th>
		<td class="table-header">PERIODO</th>
		<td class="table-header">NUMERO_COMPROBANTE_GASTO</th>
		<td class="table-header">CODIGO_GASTO</th>
		<td class="table-header">EFECTOR_SESION</th>
		<td class="table-header">MONTO</th>
		<td class="table-header">CONCEPTO</th>
		<td class="table-header">LOTE</th>
		<td class="table-header">MOTIVOS</th>				
	</tr>
	@foreach($rechazos as $rechazo)
	<tr>		
		<?php $registros = json_decode($rechazo->registro); ?>
		<td>{{ $registros->efector  or '' }}</td>
		<td>{{ $registros->fecha_gasto  or '' }}</td>
		<td>{{ $registros->periodo  or '' }}</td>
		<td>{{ $registros->numero_comprobante_gasto  or '' }}</td>
		<td>{{ $registros->codigo_gasto  or '' }}</td>				
		<td>{{ $registros->efector_sesion  or '' }}</td>
		<td>{{ $registros->monto  or '' }}</td>
		<td>{{ $registros->concepto  or '' }}</td>
		<td>{{ $registros->lote  or '' }}</td>
		<td>{{ $rechazo->motivos or '' }} </td>
	</tr>
	@endforeach
</table>