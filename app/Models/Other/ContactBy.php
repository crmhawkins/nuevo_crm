<?php

namespace App\Models\Other;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactBy extends Model
{
    use HasFactory;
    protected $table = 'contact_by';
    public $timestamps = false;
    /**
     * Atributos asignados en masa.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

     /**
     * Medio Contacto: Visita
     * 
     * @var int
     */
    const CONTACT_VISIT = "Visita a empresa";
    /**
     * Medio Contacto: Oficina
     *
     * @var int
     */
    const CONTACT_OFFICE = "Reunión en oficina";
    /**
     * Medio Contacto: Email
     *
     * @var int
     */
    const CONTACT_MAIL = "Correo";
    /**
     * Medio Contacto: Teléfono
     *
     * @var int
     */
    const CONTACT_PHONE = "Teléfono";
    /**
     * Medio Contacto: Skype
     *
     * @var int
     */
    const CONTACT_SKYPE = "Skype";

}
