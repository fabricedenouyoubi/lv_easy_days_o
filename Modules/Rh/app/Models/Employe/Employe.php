<?php

namespace Modules\Rh\Models\Employe;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\Entreprise\Models\Adresse;
use Modules\Entreprise\Models\Entreprise;

// use Modules\Rh\Database\Factories\Employe/EmployeFactory;

class Employe extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
        protected $fillable = [
        'matricule',
        'nom',
        'prenom',
        'date_de_naissance',
        'user_id',
        'entreprise_id',
        'gestionnaire_id',
        'nombre_d_heure_semaine',
        'adresse_id',
        'date_embauche',
    ];

    protected $dates = [
        'date_de_naissance',
        'date_embauche',
    ];


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function entreprise(): BelongsTo
    {
        return $this->belongsTo(Entreprise::class);
    }

    public function gestionnaire(): BelongsTo
    {
        return $this->belongsTo(self::class, 'gestionnaire_id');
    }

    public function adresse(): BelongsTo
    {
        return $this->belongsTo(Adresse::class);
    }

    public function getFullName(): string
    {
        return "{$this->nom} {$this->prenom}";
    }

    public function email(): string
    {
        return User::findOrFail($this->user_id)->email;
    }

    public function employe_groups()
    {
        $user = User::with('roles')->findOrFail($this->user_id);
        return $user->roles;
    }

    public function historiques_gestionnaire_employe()
    {
        return $this->hasMany(HistoriqueGestionnaire::class, 'employe_id');
    }

    public function historiques_gestionnaire_gest()
    {
        return $this->hasMany(HistoriqueGestionnaire::class, 'gestionnaire_id');
    }

    // protected static function newFactory(): Employe/EmployeFactory
    // {
    //     // return Employe/EmployeFactory::new();
    // }
}
