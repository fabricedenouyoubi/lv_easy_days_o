<?php

namespace Modules\RhFeuilleDeTempsConfig\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Budget\Models\AnneeFinanciere;
use Modules\RhFeuilleDeTempsConfig\Models\ConfigurationCodeDeTravail;

class AnneeFinanciereService
{
    protected $feuilleDeTempsGenerator;
    protected $jourFerieGenerator;

    public function __construct(
        FeuilleDeTempsGeneratorService $feuilleDeTempsGenerator,
        JourFerieGeneratorService $jourFerieGenerator
    ) {
        $this->feuilleDeTempsGenerator = $feuilleDeTempsGenerator;
        $this->jourFerieGenerator = $jourFerieGenerator;
    }

    /**
     * Processus complet de clôture d'année financière
     */
    public function cloturerAnneeFinanciere(AnneeFinanciere $oldAnneeFinanciere)
    {
        Log::info("Début clôture de l'année financière...");

        return DB::transaction(function () use ($oldAnneeFinanciere) {
            // 1. Créer la nouvelle année financière
            $newAnneeFinanciere = $this->creerNouvelleAnneeFinanciere($oldAnneeFinanciere);
            Log::info("Sauvegarde de l'année financière: " . $newAnneeFinanciere->debut->year);

            // 2. Désactiver toutes les autres années financières
            Log::info("Désactivation des années financières précédentes...");
            AnneeFinanciere::where('id', '!=', $newAnneeFinanciere->id)
                          ->update(['actif' => false]);

            // 3. Désactiver toutes les feuilles de temps des années précédentes
            Log::info("Désactivation des feuilles de temps des années financières précédentes...");
            $this->feuilleDeTempsGenerator->deactivateAllFeuillesDeTemps();

            // 4. Générer les nouveaux jours fériés
            Log::info("Génération des nouveaux jours fériés...");
            $this->jourFerieGenerator->generateJourFerie($newAnneeFinanciere);

            // 5. Générer les nouvelles feuilles de temps
            Log::info("Génération des nouvelles feuilles de temps...");
            $this->feuilleDeTempsGenerator->generateFeuillesDeTemps($newAnneeFinanciere);

            // 6. Transférer les codes de travail de l'ancienne à la nouvelle année
            Log::info("Transfert des codes de travail de l'ancienne à la nouvelle année...");
            $this->transfererCodeTravailVersNouvelleAnnee($newAnneeFinanciere, $oldAnneeFinanciere);

            // 7. Mettre à jour l'année par défaut dans le système
            Log::info("Mise à jour de l'année par défaut dans le système...");
            $this->updateAnneeFinanciereSessionData($newAnneeFinanciere);

            Log::info("Clôture terminée avec succès");

            return $newAnneeFinanciere;
        });
    }

    /**
     * Créer la nouvelle année financière
     */
    private function creerNouvelleAnneeFinanciere(AnneeFinanciere $oldAnneeFinanciere)
    {
        $anneeFin = $oldAnneeFinanciere->fin->year;
        $dateDebut = \Carbon\Carbon::create($anneeFin, 4, 1);
        $dateFin = \Carbon\Carbon::create($anneeFin + 1, 3, 31);

        return AnneeFinanciere::create([
            'debut' => $dateDebut,
            'fin' => $dateFin,
            'actif' => true
        ]);
    }

    /**
     * Transférer les codes de travail vers la nouvelle année
     */
    public function transfererCodeTravailVersNouvelleAnnee(
        AnneeFinanciere $newAnneeFinanciere,
        AnneeFinanciere $oldAnneeFinanciere
    ) {
        // Récupérer les anciens codes de travail avec employés
        $anciensCodeTravail = ConfigurationCodeDeTravail::parAnneeFinanciere($oldAnneeFinanciere->id)
                                                       ->employeConfigurations()
                                                       ->get();

        Log::info("Nous avons " . $anciensCodeTravail->count() . " anciens codes de travail à transférer");

        $nouveauxCodeDeTravail = $this->copyDataFromOldValues($anciensCodeTravail, $newAnneeFinanciere);

        Log::info("Nous avons au total " . count($nouveauxCodeDeTravail) . " nouveaux codes de travail à enregistrer");

        if (!empty($nouveauxCodeDeTravail)) {
            ConfigurationCodeDeTravail::insert($nouveauxCodeDeTravail);
            Log::info("Les nouveaux codes de travail sont créés");
        }

        return $this;
    }

    /**
     * Copier les données des anciennes valeurs
     */
    private function copyDataFromOldValues($anciensCodeTravail, AnneeFinanciere $nouvelleAnnee)
    {
        $nouveauxCodeTravail = [];

        foreach ($anciensCodeTravail as $ancienCodeTravail) {
            $heureDisponible = 0;
            if ($ancienCodeTravail->solde_heure_annee_precedente && $ancienCodeTravail->nombre_d_heure_restant) {
                $heureDisponible = $ancienCodeTravail->nombre_d_heure_restant + $ancienCodeTravail->solde_heure_annee_precedente;
            }

            $nouveauxCodeTravail[] = [
                'libelle' => $ancienCodeTravail->libelle,
                'code_de_travail_id' => $ancienCodeTravail->code_de_travail_id,
                'annee_financiere_id' => $nouvelleAnnee->id,
                'nombre_d_heure' => $ancienCodeTravail->nombre_d_heure,
                'jour' => $ancienCodeTravail->jour,
                'nombre_d_heure_restant' => $heureDisponible,
                'nombre_d_heure_pris' => 0,
                'solde_heure_annee_precedente' => 0,
                'quantite_heure_annee_courante' => $heureDisponible,
                'employe_id' => $ancienCodeTravail->employe_id,
                'description' => $ancienCodeTravail->description,
                'debut' => $ancienCodeTravail->debut,
                'fin' => $ancienCodeTravail->fin,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        return $nouveauxCodeTravail;
    }

    /**
     * Mettre à jour les données de session de l'année financière
     */
    public function updateAnneeFinanciereSessionData(AnneeFinanciere $annee)
    {
        session([
            'annee_debut' => $annee->debut->format('d-m-Y'),
            'annee_fin' => $annee->fin->format('d-m-Y'),
            'annee_financiere_id' => $annee->id
        ]);

        return $this;
    }

    /**
     * Obtenir les statistiques complètes d'une année financière
     */
    public function getAnneeFinanciereStats(AnneeFinanciere $anneeFinanciere)
    {
        $feuilleStats = $this->feuilleDeTempsGenerator->getGenerationStats($anneeFinanciere);
        $ferieStats = $this->jourFerieGenerator->getGenerationStats($anneeFinanciere);
        
        $configStats = [
            'total_configurations' => ConfigurationCodeDeTravail::parAnneeFinanciere($anneeFinanciere->id)->count(),
            'configurations_employes' => ConfigurationCodeDeTravail::parAnneeFinanciere($anneeFinanciere->id)
                                                                  ->employeConfigurations()
                                                                  ->count(),
            'configurations_globales' => ConfigurationCodeDeTravail::parAnneeFinanciere($anneeFinanciere->id)
                                                                   ->globalConfigurations()
                                                                   ->count()
        ];

        return array_merge($feuilleStats, $ferieStats, $configStats);
    }

    /**
     * Initialiser une nouvelle année financière complètement
     */
    public function initialiserNouvelleAnnee(AnneeFinanciere $anneeFinanciere)
    {
        return DB::transaction(function () use ($anneeFinanciere) {
            // Générer les jours fériés
            $this->jourFerieGenerator->generateJourFerie($anneeFinanciere);
            
            // Générer les feuilles de temps
            $this->feuilleDeTempsGenerator->generateFeuillesDeTemps($anneeFinanciere);
            
            // Mettre à jour la session
            $this->updateAnneeFinanciereSessionData($anneeFinanciere);

            return $anneeFinanciere;
        });
    }

    public function handle() {}
}
