<?php

namespace App\Models\Beneficiarios;

use Illuminate\Database\Eloquent\Model;

class Bajas extends Model
{
    /**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'beneficiarios.beneficiarios_bajas';

	/**
	 * Primary key asociated with the table.
	 *
	 * @var string
	 */
	//protected $primaryKey = 'clave_beneficiario';

	/**
	 * Indicates if the model should be timestamped.
	 *
	 * @var bool
	 */
	public $timestamps = false;

	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['clave_beneficiario','periodo','motivo','mensaje'];	
}