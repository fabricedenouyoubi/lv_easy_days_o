<?php

namespace Modules\Rh\Models\Employe;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\Entreprise\Models\Adresse;
use Modules\Entreprise\Models\Entreprise;
use Modules\RhFeuilleDeTempsAbsence\Models\Operation;

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
        'adresse_id',
        'date_embauche',
        'est_gestionnaire'
    ];

    protected $dates = [
        'date_de_naissance',
        'date_embauche',
    ];


    //--- relation avec l'utilisateur
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    //--- relation avec l'entreprise
    public function entreprise(): BelongsTo
    {
        return $this->belongsTo(Entreprise::class);
    }

    //--- relation avec le gestionnaire
    public function gestionnaire(): BelongsTo
    {
        return $this->belongsTo(self::class, 'gestionnaire_id');
    }

    //--- relation avec l'adresse
    public function adresse(): BelongsTo
    {
        return $this->belongsTo(Adresse::class);
    }

    //--- recuperer le nom et prenom de l'employe
    public function getFullName(): string
    {
        return "{$this->nom} {$this->prenom}";
    }

    //--- recuperer l'email de l'employe
    public function email(): string
    {
        return User::findOrFail($this->user_id)->email;
    }

    //--- recuperer les groupes de l'employe
    public function employe_groups()
    {
        $user = User::with('roles')->findOrFail($this->user_id);
        return $user->roles;
    }

    //--- relation avec les historiques gestionnaire
    public function historiques_gestionnaire_employe()
    {
        return $this->hasMany(HistoriqueGestionnaire::class, 'employe_id');
    }

    //--- relation avec les historiques gestionnaire
    public function historiques_gestionnaire_gest()
    {
        return $this->hasMany(HistoriqueGestionnaire::class, 'gestionnaire_id');
    }

    //--- relation avec les historiques heures par semainse de l'employe
    public function heure_semaines_employe()
    {
        $heure = HistoriqueHeuresSemaines::where('employe_id', $this->id)->orderByDesc('date_debut')->first();
        return $heure->nombre_d_heure_semaine;
    }

    //--- relation avec les operations
    public function operations()
    {
        return $this->hasMany(Operation::class, 'demande_absence_id');
    }
    // protected static function newFactory(): Employe/EmployeFactory
    // {
    //     // return Employe/EmployeFactory::new();
    // }
}
