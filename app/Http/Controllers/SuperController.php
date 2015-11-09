<?php

namespace App\Http\Controllers;

use ErrorException;
use Illuminate\Database\QueryException;
use Validator;
use Auth;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Rechazo;
use App\Models\Lote;
use App\Models\Subida;
use App\Models\PUCO\Super;

class SuperController extends Controller
{
    private 
		$_rules = [
			'cuil_beneficiario' => 'digits:11',
			'tipo_documento' => 'required|in:DU,LI,LC,LE,PA,01,02,03,04,05,06,07,08,09,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24',
			'numero_documento' => 'required',
			'nombre_apellido' => 'required|max:255',
			'sexo' => 'required|in:F,M',
			'fecha_nacimiento' => 'date_format:dmY',
			'codigo_os' => 'required',
			'ultimo_aporte' => 'required',
		],
		$_data = [
			'cuil_beneficiario',
			'tipo_documento',
			'numero_documento',
			'nombre_apellido',
			'sexo',
			'fecha_nacimiento',
			'tipo_beneficiario',
			'codigo_parentezco',
			'codigo_postal',
			'id_provincia',
			'cuil_titular',
			'codigo_os',
			'ultimo_aporte',
			'cuil_valido',
			'cuit_empleador',
			'lote'
		],
		$_resumen = [
			'insertados' => 0,
			'rechazados' => 0,
			'modificados' => 0
		],
		$_error = [
			'lote' => '',
			'registro' => '',
			'motivos' => ''
		];

	/**
     * Create a new authentication controller instance.
     *
     * @return void
     */
	public function __construct(){
		$this->middleware('auth');
	}

	/**
	 * Crea un nuevo lote
	 * @param int $id_subida
	 *
	 * @return int
	 */
	protected function nuevoLote($id_subida){
		$l = new Lote;
		$l->id_subida = $id_subida;
		$l->id_usuario = Auth::user()->id_usuario;
		$l->id_provincia = Auth::user()->id_provincia;
		$l->registros_in = 0;
		$l->registros_out = 0;
		$l->registros_mod = 0;
		$l->id_estado = 1;
		$l->save();
		return $l->lote;
	}

	/**
	 * Actualiza el lote con los datos procesados
	 * @param int $lote
	 * @param array $resumen
	 *
	 * @return bool
	 */
	protected function actualizaLote($lote , $resumen) {
		$l = Lote::findOrFail($lote);
		$l->registros_in = $resumen['insertados'];
		$l->registros_out = $resumen['rechazados'];
		$l->registros_mod = $resumen['modificados'];
		$l->fin = 'now';
		return $l->save();
	}

	/**
	 * Actualiza el archivo con los datos procesados
	 * @param int $id
	 *
	 * @return bool
	 */
	protected function actualizaSubida($subida) {
		$s = Subida::findOrFail($subida);
		$s->id_estado = 2;
		return $s->save();
	}

	/**
	 * Abre un archivo y devuelve un handler
	 * @param int $id
	 *
	 * @return resource
	 */
	protected function abrirArchivo($id){
		$info = Subida::findOrFail($id);
		try {
			$fh = fopen ('../storage/uploads/sss/' . $info->nombre_actual , 'r');
		} catch (ErrorException $e) {
			return false;
		}
		return $fh;
	}

	/**
	 * Limpia el tipo de documento
	 * @param string $tipo
	 *
	 * @return string
	 */
	protected function sanitizeTipoDoc($tipo){
		$cedulas = ['01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24'];
		$tipos = ['DU','PA'];

		if (in_array($tipo, $cedulas)){
			return 'C' . $tipo;
		} else if (in_array($tipo, $tipos)){
			return $tipo == 'DU' ? 'DNI' : 'PAS';
		} else {
			return $tipo;
		}
	}

	/**
	 * Procesa el archivo de prestaciones
	 * @param int $id
	 *
	 * @return json
	 */
	public function procesarArchivo($id){
		$lote = $this->nuevoLote($id);
		$fh = $this->abrirArchivo($id);
		$bulk = [];
		
		if (!$fh){
			return response('Error' , 422);
		}

		while (!feof($fh)){
			$linea = explode ('|' , trim(fgets($fh) , "\r\n"));
			if (count($linea) == 15) {
				array_push($linea, $lote);
				$sss_raw = array_combine($this->_data, $linea);
				$v = Validator::make($sss_raw , $this->_rules);
				if ($v->fails()) {
					$this->_resumen['rechazados'] ++;
					$this->_error['lote'] = $lote;
					$this->_error['registro'] = json_encode($sss_raw);
					$this->_error['motivos'] = json_encode($v->errors());
					Rechazo::insert($this->_error);
				} else {
					$sss_raw['tipo_documento'] = $this->sanitizeTipoDoc($sss_raw['tipo_documento']);
					$sss_raw['fecha_nacimiento'] = \DateTime::createFromFormat('dmY' , $sss_raw['fecha_nacimiento']);

					$limite_inferior = new \DateTime();
					$limite_inferior->modify('-4 months');
					$periodo_reportado = \DateTime::createFromFormat('Ym' , $sss_raw['ultimo_aporte']);

					if ($limite_inferior >= $periodo_reportado || (int)$sss_raw['codigo_os'] == 500807){
						$this->_resumen['insertados'] ++;
						$bulk[] = $sss_raw;
						if (sizeof($bulk) % 4000 == 0){
							Super::insert($bulk);
							unset($bulk);
							$bulk = [];
						}
					} else {
						$this->_resumen['rechazados'] ++;
						$this->_error['lote'] = $lote;
						$this->_error['registro'] = json_encode($sss_raw);
						$this->_error['motivos'] = '{"periodo invalido" : ["El ultimo periodo reportado es mayor a 4 meses"]}';
						Rechazo::insert($this->_error);		
					}
				 }
			} else {
				$this->_resumen['rechazados'] ++;
				$this->_error['lote'] = $lote;
				$this->_error['registro'] = json_encode($linea);
				$this->_error['motivos'] = '{"registro invalido" : ["El número de campos es incorrecto"]}';
				Rechazo::insert($this->_error);
			}
		}

		if (sizeof($bulk)){
			Super::insert($bulk);
			unset($bulk);
			$bulk = [];
		}

		$this->actualizaLote($lote , $this->_resumen);
		$this->actualizaSubida($id);
		return response()->json($this->_resumen);
	}
}
