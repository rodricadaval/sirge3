<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Models\Beneficiario;
use App\Models\ErrorPadronSisa;
use App\Models\Excepciones;
use App\Models\InscriptosPadronSisa;
use DB;
use Exception;
use GuzzleHttp;
use Illuminate\Http\Request;
use Schema;
use SimpleXMLElement;

class WebServicesController extends Controller {
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index() {
		//
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create() {
		return new GuzzleHttp\Client(['base_uri' => 'https://sisa.msal.gov.ar/sisa/services/rest/cmdb/obtener']);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function createLocationIQ() {
		//return new GuzzleHttp\Client(['base_uri' => 'https://locationiq.org/v1/']);
		return new GuzzleHttp\Client(['base_uri' => 'https://us1.locationiq.org/v1/']);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request) {
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id) {
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id) {
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id) {
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id) {
		//
	}

	/**
	 * Devuelve una respuesta con los parámetros consultados
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function makeRequest($id) {
		//
	}

	/**
	 * Devuelve una respuesta enviando los parámetros a consultar en siisa
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function siisaXMLRequest($nrdoc, $sexo = null) {
		$client = $this->create();

		$url = 'https://sisa.msal.gov.ar/sisa/services/rest/cmdb/obtener?nrodoc='.$nrdoc.'&usuario=fnunez&clave=fernandonunez';

		if ($sexo) {
			$url = $url.'&sexo='.$sexo;
		}

		$response = $client->get($url);

		/*echo $response->getStatusCode();

		echo '</br></br>';*/

		$datos = get_object_vars(new SimpleXMLElement($response->getBody()));

		echo json_encode($datos);
	}

	/**
	 * Devuelve una respuesta enviando los parámetros a consultar en siisa
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function cruceSiisaXMLRequest($nrdoc, $client) {

		if (!InscriptosPadronSisa::find($nrdoc)) {
			$url = 'https://sisa.msal.gov.ar/sisa/services/rest/cmdb/obtener?nrodoc='.$nrdoc.'&usuario=fnunez&clave=fernandonunez';

			try {
				//throw new Exception("Error Processing Request", 1);
				$response = $client->get($url);
			} catch (Exception $e) {
				return json_encode(array('error' => 'SI', 'mensaje' => 'Error Code '.$e->getCode().': '.$e->getMessage()));
			}

			$datos = get_object_vars(new SimpleXMLElement($response->getBody()));
			return json_encode($datos);
		} else {
			return null;
		}
	}

	/**
	 * Devuelve una respuesta enviando los parámetros a consultar en siisa
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function convertirEnTexto($valor) {
		if (gettype($valor) == "object") {
			if (isset($valor->{'0'})) {
				if ($valor->{'0'} == ' ' || $valor->{'0'} == '') {
					return null;
				} else {
					return (string) $valor->{'0'};
				}
			} else {
				return null;
			}
		} else {
			if ($valor == 'NULL') {
				return null;
			} else {
				return (string) $valor;
			}
		}
	}

	/**
	 * Busca los documentos de los beneficiarios que no están cruzados con siisa y guarda sus datos.
	 *
	 * @return "Resultado"
	 */
	public function cruzarBeneficiariosConSiisa() {

		$ch = curl_init();
		set_time_limit(0);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30000);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
		curl_close($ch);

		$start = microtime(true);

		$client = $this->create();

		$cantidad = 1000;

		$dt = \DateTime::createFromFormat('Ym', date('Ym'));
		$dt->modify('-2 months');
		$periodo = intval($dt->format('Ym'));

		DB::statement("CREATE TABLE IF NOT EXISTS siisa.temporal_migracion_siisa(numero_documento character varying(14) PRIMARY KEY);");

		$documentos = Beneficiario::join('beneficiarios.geografico as g', 'beneficiarios.beneficiarios.clave_beneficiario', '=', 'g.clave_beneficiario')
			->join('beneficiarios.periodos as p', function ($join) use ($periodo) {
				$join->on('beneficiarios.beneficiarios.clave_beneficiario', '=', 'p.clave_beneficiario');
				$join->where('p.periodo', '=', $periodo);
			})
			->leftjoin('siisa.inscriptos_padron as i', 'beneficiarios.beneficiarios.numero_documento', '=', 'i.nrodocumento')
			->leftjoin('siisa.error_padron_siisa as e', 'beneficiarios.beneficiarios.numero_documento', '=', 'e.numero_documento')
			->leftjoin('siisa.temporal_migracion_siisa as t', 'beneficiarios.beneficiarios.numero_documento', '=', 't.numero_documento')
			->where('id_provincia_alta', '09')
			->where('clase_documento', 'P')
		//->where('g.id_departamento', 370)
			->whereNull('i.nrodocumento')
			->whereNull('t.numero_documento')
			->whereNull('e.numero_documento')
			->take($cantidad)
			->select('beneficiarios.beneficiarios.numero_documento')
			->groupBy('beneficiarios.beneficiarios.numero_documento')
			->get()
			->toArray();

		if (count($documentos) == 0) {
			if (DB::table('siisa.temporal_migracion_siisa')->count() == 0) {
				Schema::dropIfExists('siisa.temporal_migracion_siisa');
			}
			die("No quedan más beneficiarios a procesar de dicha provincia");
		}

		try {
			DB::table("siisa.temporal_migracion_siisa")->insert($documentos);
		} catch (Exception $e) {
			$excepcion         = new Excepciones();
			$excepcion->clase  = (string) get_class();
			$excepcion->metodo = (string) __FUNCTION__;
			$excepcion->error  = json_encode(array("codigo" => $e->getCode(), "mensaje" => $e->getMessage()));
			$excepcion->save();
			unset($excepcion);
		}

		foreach ($documentos as $documento) {
			$datos_benef = $this->cruceSiisaXMLRequest((string) $documento['numero_documento'], $client);
			if ($datos_benef && $datos_benef <> '{}') {
				$data               = (array) json_decode($datos_benef);
				$data               = (object) $data;
				$data->nrodocumento = $documento['numero_documento'];
				if (isset($data->resultado)) {
					if ($data->resultado == 'OK') {
						$resultado[] = $this->guardarDatos($data);
						if (sizeof($resultado)%1000 == 0) {
							try {
								InscriptosPadronSisa::insert($resultado);
							} catch (Exception $e) {
								$excepcion         = new Excepciones();
								$excepcion->clase  = (string) get_class();
								$excepcion->metodo = (string) __FUNCTION__;
								$excepcion->error  = json_encode(array("codigo" => $e->getCode(), "mensaje" => $e->getMessage()));
								$excepcion->save();
								unset($excepcion);
							}
							unset($resultado);
							$resultado = [];
						}
					} else {
						$devolucion = $this->guardarError($data, $documento['numero_documento']);
						if ($devolucion) {
							$error[] = $devolucion;
						}
						unset($devolucion);
					}
				} else {
					$devolucion = $this->guardarError($data, $documento['numero_documento']);
					if ($devolucion) {
						$error[] = $devolucion;
					}
					unset($devolucion);
				}
			}
			unset($datos_benef);
			unset($data);
		}
		if (isset($resultado)?sizeof($resultado):false) {
			try {
				InscriptosPadronSisa::insert($resultado);
			} catch (Exception $e) {
				$excepcion         = new Excepciones();
				$excepcion->clase  = (string) get_class();
				$excepcion->metodo = (string) __FUNCTION__;
				$excepcion->error  = json_encode(array("codigo" => $e->getCode(), "mensaje" => $e->getMessage()));
				$excepcion->save();
				unset($excepcion);
			}
			unset($resultado);
		}

		if (isset($error)?sizeof($error):false) {
			try {
				ErrorPadronSisa::insert($error);
			} catch (Exception $e) {
				$excepcion         = new Excepciones();
				$excepcion->clase  = (string) get_class();
				$excepcion->metodo = (string) __FUNCTION__;
				$excepcion->error  = json_encode(array("codigo" => $e->getCode(), "mensaje" => $e->getMessage()));
				$excepcion->save();
				unset($excepcion);
			}
			unset($error);
		}
		unset($documento);
		unset($documentos);

		$end = microtime(true)-$start;

		if (DB::table('siisa.temporal_migracion_siisa')->count() <= $cantidad) {
			Schema::dropIfExists('siisa.temporal_migracion_siisa');
		}

		DB::statement("INSERT INTO siisa.tiempo_proceso (fecha,tiempo, cantidad) VALUES (now(), ?, ?)", [$end, $cantidad]);

		echo "Los beneficiarios se han insertado correctamente. Tiempo: ".$end."\n";
	}

	/**
	 * Guarda los datos encontrados en el webservice del siisa
	 *
	 * @param  object  $datos
	 * @return json_encode($datos)
	 */
	public function guardarDatos($datos) {

		//die(var_dump($datos));

		//$inscripto = new InscriptosPadronSisa();
		$inscripto['id']                  = $this->convertirEnTexto($datos->id);
		$inscripto['codigosisa']          = $this->convertirEnTexto($datos->codigoSISA);
		$inscripto['identificadorenaper'] = $this->convertirEnTexto($datos->identificadoRenaper);
		$inscripto['padronsisa']          = $this->convertirEnTexto($datos->PadronSISA);
		$inscripto['tipodocumento']       = $this->convertirEnTexto($datos->tipoDocumento);
		$inscripto['nrodocumento']        = intval($this->convertirEnTexto($datos->nroDocumento));
		$inscripto['apellido']            = $this->convertirEnTexto($datos->apellido);
		$inscripto['nombre']              = $this->convertirEnTexto($datos->nombre);
		$inscripto['sexo']                = $this->convertirEnTexto($datos->sexo);
		$inscripto['fechanacimiento']     = $this->convertirEnTexto($datos->fechaNacimiento);
		$inscripto['estadocivil']         = $this->convertirEnTexto($datos->estadoCivil);
		$inscripto['provincia']           = $this->convertirEnTexto($datos->provincia);
		$inscripto['departamento']        = $this->convertirEnTexto($datos->departamento);
		$inscripto['localidad']           = $this->convertirEnTexto($datos->localidad);
		$inscripto['domicilio']           = $this->convertirEnTexto($datos->domicilio);
		$inscripto['pisodpto']            = $this->convertirEnTexto($datos->pisoDpto);
		$inscripto['codigopostal']        = $this->convertirEnTexto($datos->codigoPostal);
		$inscripto['paisnacimiento']      = $this->convertirEnTexto($datos->paisNacimiento);
		$inscripto['provincianacimiento'] = $this->convertirEnTexto($datos->provinciaNacimiento);
		$inscripto['localidadnacimiento'] = $this->convertirEnTexto($datos->localidadNacimiento);
		$inscripto['nacionalidad']        = $this->convertirEnTexto($datos->nacionalidad);
		$inscripto['fallecido']           = $this->convertirEnTexto($datos->fallecido);
		$inscripto['fechafallecido']      = $this->convertirEnTexto($datos->fechaFallecido);
		$inscripto['donante']             = $this->convertirEnTexto($datos->donante);
		$inscripto['created_at']          = date('Y-m-d H:i:s');
		$inscripto['updated_at']          = date('Y-m-d H:i:s');
		return $inscripto;
	}

	/**
	 * Guarda el error de la búsqueda del beneficiario.
	 *
	 * @param  object $datos
	 * @return bool
	 */
	public function guardarError($datos, $documento) {

		$devolver     = array();
		$noEncontrado = ErrorPadronSisa::where('numero_documento', $documento)->first();

		if ($noEncontrado) {
			$noEncontrado->error = $this->convertirEnTexto($datos->resultado);
			try {
				$noEncontrado->save();
				unset($noEncontrado);
				return false;
			} catch (QueryException $e) {
				echo json_encode($e);
			}
		} else {
			$devolver['numero_documento'] = $documento;
			$devolver['error']            = isset($datos->error)?$datos->mensaje:$this->convertirEnTexto($datos->resultado);
			$devolver['created_at']       = date('Y-m-d H:i:s');
			$devolver['updated_at']       = date('Y-m-d H:i:s');
			return $devolver;
		}
	}

	/**
	 * Borra la tabla temporal
	 *
	 *
	 *
	 */
	public function borrarTablaTemporal() {
		Schema::dropIfExists('siisa.temporal_migracion_siisa');
	}

	/**
	 * Busca los documentos de los beneficiarios que no están cruzados con siisa y guarda sus datos.
	 *
	 * @return "Resultado"
	 */
	public function georeferenciarBeneficiarios() {

		$start = microtime(true);

		$client = $this->createLocationIQ();

		$cantidad = 1000;

		$dt = \DateTime::createFromFormat('Ym', date('Ym'));
		$dt->modify('-2 months');
		$periodo = intval($dt->format('Ym'));

		$beneficiarios = Beneficiario::join('beneficiarios.geografico as g', 'beneficiarios.beneficiarios.clave_beneficiario', '=', 'g.clave_beneficiario')
			->join('beneficiarios.periodos as p', function ($join) use ($periodo) {
				$join->on('beneficiarios.beneficiarios.clave_beneficiario', '=', 'p.clave_beneficiario');
				$join->where('p.periodo', '=', $periodo);
			})
			->join('geo.departamentos', 'g.id_departamento', '=', 'geo.departamentos.id')
			->join('geo.provincias', 'beneficiarios.beneficiarios.id_provincia_alta', '=', 'geo.provincias.id_provincia')
			->where('id_provincia_alta', '09')
			->where('clase_documento', 'P')
		//->where('g.id_departamento', 370)
			->take($cantidad)
			->select('beneficiarios.beneficiarios.numero_documento', 'g.calle', 'g.numero', 'geo.departamentos.nombre_departamento as departamento', 'geo.provincias.descripcion as provincia')
			->get();

		if (count($beneficiarios) == 0) {
			die("Query vacia");
		}

		foreach ($beneficiarios as $beneficiario) {
			$address     = $beneficiario->calle." ".$beneficiario->numero.",".$beneficiario->departamento.",".$beneficiario->provincia.",Argentina";
			$datos_benef = $this->cruceLocationIQ($address, $client, $beneficiario->numero_documento);
			if (is_array($datos_benef)) {
				$datos_benef                   = (object) $datos_benef[0];
				$datos_benef->numero_documento = $beneficiario->numero_documento;
				$datos_benef->calle            = $beneficiario->calle;
				$datos_benef->calle_numero     = $beneficiario->numero;
				$datos_benef->departamento     = $beneficiario->departamento;
				$datos_benef->provincia        = $beneficiario->provincia;
			}

			$show_results[] = $datos_benef;
			/*
		if ($datos_benef && $datos_benef <> '{}') {
		$data               = (array) json_decode($datos_benef);
		$data               = (object) $data;
		$data->nrodocumento = $documento['numero_documento'];
		if (isset($data->resultado)) {
		if ($data->resultado == 'OK') {
		$resultado[] = $this->guardarDatos($data);
		if (sizeof($resultado)%1000 == 0) {
		try {
		InscriptosPadronSisa::insert($resultado);
		} catch (Exception $e) {
		$excepcion         = new Excepciones();
		$excepcion->clase  = (string) get_class();
		$excepcion->metodo = (string) __FUNCTION__;
		$excepcion->error  = json_encode(array("codigo" => $e->getCode(), "mensaje" => $e->getMessage()));
		$excepcion->save();
		unset($excepcion);
		}
		unset($resultado);
		$resultado = [];
		}
		} else {
		$devolucion = $this->guardarError($data, $documento['numero_documento']);
		if ($devolucion) {
		$error[] = $devolucion;
		}
		unset($devolucion);
		}
		} else {
		$devolucion = $this->guardarError($data, $documento['numero_documento']);
		if ($devolucion) {
		$error[] = $devolucion;
		}
		unset($devolucion);
		}
		}
		unset($datos_benef);
		unset($data);
		 */
		}

		$results = collect($show_results);

		echo json_encode($results, JSON_PRETTY_PRINT);
		die("");

		if (isset($resultado)?sizeof($resultado):false) {
			try {
				InscriptosPadronSisa::insert($resultado);
			} catch (Exception $e) {
				$excepcion         = new Excepciones();
				$excepcion->clase  = (string) get_class();
				$excepcion->metodo = (string) __FUNCTION__;
				$excepcion->error  = json_encode(array("codigo" => $e->getCode(), "mensaje" => $e->getMessage()));
				$excepcion->save();
				unset($excepcion);
			}
			unset($resultado);
		}

		if (isset($error)?sizeof($error):false) {
			try {
				ErrorPadronSisa::insert($error);
			} catch (Exception $e) {
				$excepcion         = new Excepciones();
				$excepcion->clase  = (string) get_class();
				$excepcion->metodo = (string) __FUNCTION__;
				$excepcion->error  = json_encode(array("codigo" => $e->getCode(), "mensaje" => $e->getMessage()));
				$excepcion->save();
				unset($excepcion);
			}
			unset($error);
		}
		unset($documento);
		unset($documentos);

		$end = microtime(true)-$start;

		if (DB::table('siisa.temporal_migracion_siisa')->count() <= $cantidad) {
			Schema::dropIfExists('siisa.temporal_migracion_siisa');
		}

		DB::statement("INSERT INTO siisa.tiempo_proceso (fecha,tiempo, cantidad) VALUES (now(), ?, ?)", [$end, $cantidad]);

		echo "Los beneficiarios se han insertado correctamente. Tiempo: ".$end."\n";
	}

	/**
	 * Devuelve una respuesta enviando los parámetros a consultar en siisa
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function cruceLocationIQ($address, $client, $numero_documento) {

		$url = 'search.php?key=9872109901f4e1&q='.$address.'&format=json';

		echo $url."\n";

		try {
			//throw new Exception("Error Processing Request", 1);
			$response = $client->request('GET', $url);
		} catch (\Exception $e) {
			return (object) array("error" => "SI", "numero_documento" => $numero_documento, "uri" => $address, "error" => $e->getCode(), "message" => $e->getMessage());
		}
		return json_decode($response->getBody()->getContents());
	}
}
