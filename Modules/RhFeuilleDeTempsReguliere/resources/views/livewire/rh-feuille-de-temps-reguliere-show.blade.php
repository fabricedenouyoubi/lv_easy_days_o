<div>
    {{-- Messages de feedback --}}
    <x-alert-messages />

    <div class="row mb-3">
        <div class="col-md-8">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('feuille-temps.list') }}">Feuilles de temps</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        Détails semaine {{ $semaine->numero_semaine }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Main content area -->
    <div class="row">
        <!-- Main content - 9 colonnes -->
        <div class="col-12 col-lg-9">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">
                                <i class="mdi mdi-clock-time-four-outline me-1 text-primary"></i>
                                Feuille de temps
                            </h5>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            @php $statut = $this->getStatutFormate(); @endphp
                            <span class="badge {{ $statut['class'] }} rounded-pill px-3 py-2">
                                <i class="{{ $statut['icon'] }} align-middle me-1"></i>
                                {{ $statut['text'] }}
                            </span>
                            
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Informations employé -->
                    <div class="row align-items-center mb-4">
                        <div class="col-auto">
                            <div class="avatar-wrapper">
                                <div class="avatar-placeholder d-flex align-items-center justify-content-center bg-light rounded-circle shadow-sm border border-3 border-white"
                                     style="width: 100px; height: 100px;">
                                    <i class="fa fa-user-circle text-primary" style="font-size: 3rem;"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <h4 class="mb-1">{{ $employe->nom }} {{ $employe->prenom }}</h4>
                            <p class="text-muted mb-0">Semaine {{ $semaine->numero_semaine }} - Du {{ \Carbon\Carbon::parse($semaine->debut)->format('d/m/Y') }} au {{ \Carbon\Carbon::parse($semaine->fin)->format('d/m/Y') }}</p>
                        </div>
                    </div>

                    <!-- Détail des lignes de travail -->
                    <div class="card mt-3 border-0 shadow-sm">
                        <div class="card-header bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="card-title mb-0">
                                Détail de la feuille de temps
                            </h6>

                            <div class="d-flex align-items-center gap-2">
                                    <!-- Boutons d'action déplacés dans le header -->
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('feuille-temps.list') }}" class="btn btn-outline-secondary btn-sm">
                                            <i class="mdi mdi-arrow-left me-1"></i>
                                            Retour
                                        </a>
                                        <!-- Actions selon les permissions -->
                                        @if($canEdit)
                                            <a href="{{ route('feuille-temps.edit', ['semaineId' => $semaineId, 'operationId' => $operationId]) }}" 
                                               class="btn btn-sm btn-outline-warning">
                                                <i class="mdi mdi-square-edit-outline me-1"></i>
                                                Modifier
                                            </a>
                                        @endif
                                        
                                        @if($canSubmit)
                                            <button wire:click="toggleSubmitModal" class="btn btn-sm btn-outline-primary">
                                                <i class="mdi mdi-send-circle-outline me-1"></i>
                                                Soumettre
                                            </button>
                                        @endif
                                        
                                        @if($canRecall)
                                            <button wire:click="toggleRecallModal" class="btn btn-sm btn-outline-warning">
                                                <i class="mdi mdi-backup-restore me-1"></i>
                                                Rappeler
                                            </button>
                                        @endif
                                        
                                        @if($canApprove)
                                            <button wire:click="toggleApproveModal" class="btn btn-sm btn-outline-success">
                                                <i class="mdi mdi-checkbox-marked-circle-outline me-1"></i>
                                                Valider
                                            </button>
                                        @endif
                                        
                                        @if($canReject)
                                            <button wire:click="toggleRejectModal" class="btn btn-sm btn-outline-danger">
                                                <i class="mdi mdi-close-circle-outline me-1"></i>
                                                Rejeter
                                            </button>
                                        @endif
                                        
                                        @if($canReturn)
                                            <button wire:click="toggleReturnModal" class="btn btn-sm btn-outline-warning">
                                                <i class="mdi mdi-reply me-1"></i>
                                                Retourner
                                            </button>
                                        @endif
                                    </div>
                                </div>

                            </div>
                            
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Code de travail</th>
                                            @foreach($joursLabels as $index => $jour)
                                                <th class="text-center">
                                                    {{ $jour }}
                                                    <br>
                                                    <small class="text-muted">
                                                        {{ \Carbon\Carbon::parse($semaine->debut)->addDays($index)->format('d/m') }}
                                                    </small>
                                                </th>
                                            @endforeach
                                            <th class="text-center">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($lignesTravail as $ligne)
                                            <tr class="{{ $ligne['auto_rempli'] ? 'table-warning' : '' }}">
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        @if($ligne['auto_rempli'])
                                                            <i class="mdi mdi-lock text-warning me-2" title="Auto-rempli"></i>
                                                        @endif
                                                        {{ $ligne['code_travail']->libelle }}
                                                    </div>
                                                </td>
                                                @foreach($ligne['jours'] as $jour)
                                                    <td class="text-center">
                                                        @if($jour['duree'] > 0)
                                                            <span class="badge bg-primary rounded-pill fw-bold">
                                                                {{ $jour['duree'] }}h
                                                            </span>
                                                        @else
                                                            <span class="text-muted">—</span>
                                                        @endif
                                                    </td>
                                                @endforeach
                                                <td class="text-center">
                                                    <span class="badge bg-dark rounded-pill">
                                                        {{ $ligne['total'] }}h
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="{{ count($joursLabels) + 2 }}" class="text-center py-4">
                                                    <i class="mdi mdi-clipboard-outline text-muted" style="font-size: 48px;"></i>
                                                    <p class="text-muted mb-0">Aucune ligne de travail enregistrée</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Journal du workflow -->
            <div class="card mt-4 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="mdi mdi-history me-1 text-primary"></i>
                        Journal de la feuille de temps
                    </h6>
                </div>
                <div class="card-body">
                    @if(count($workflowHistory) > 0)
                        <div class="timeline">
                            @foreach($workflowHistory as $entry)
                                <div class="row mb-3">
                                    <div class="col-12 d-flex align-items-center">
                                        <span class="badge bg-info me-2">
                                            <i class="mdi mdi-swap-horizontal"></i>
                                        </span>
                                        <strong>{{ $entry['title'] ?? 'Transition' }}</strong>
                                    </div>
                                    <div class="col-12">
                                        <small class="text-muted">
                                            {{ $entry['date'] ?? '' }} à {{ $entry['time'] ?? '' }}
                                            @if(!empty($entry['user']))
                                                par {{ $entry['user'] }}
                                            @endif
                                        </small>
                                    </div>
                                    @if(!empty($entry['comment']))
                                        <div class="col-12">
                                            <p class="mb-0">{{ $entry['comment'] }}</p>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="mdi mdi-comment-text-outline text-muted" style="font-size: 48px;"></i>
                            <h5>Aucune entrée dans le journal</h5>
                            <p class="text-muted">Les changements d'état apparaîtront ici.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar - 3 colonnes -->
        <div class="col-12 col-lg-3 mt-4 mt-lg-0">
            <!-- Résumé des heures -->
            <x-table-card title="Résumé des heures" icon="fas fa-clock">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted">Formation</span>
                    <span class="badge bg-primary px-3 py-2 rounded-pill">{{ $operation->total_heure_formation ?? 0 }}h</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted">CSN</span>
                    <span class="badge bg-primary px-3 py-2 rounded-pill">{{ $operation->total_heure_csn ?? 0 }}h</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted">Caisse</span>
                    <span class="badge bg-primary px-3 py-2 rounded-pill">{{ $operation->total_heure_caisse ?? 0 }}h</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted">Congé Mobile</span>
                    <span class="badge bg-primary px-3 py-2 rounded-pill">{{ $operation->total_heure_conge_mobile ?? 0 }}h</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted">Déplacement</span>
                    <span class="badge bg-primary px-3 py-2 rounded-pill">{{ $operation->total_heure_deplacement ?? 0 }}h</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted">Régulier</span>
                    <span class="badge bg-primary px-3 py-2 rounded-pill">{{ $operation->total_heure_regulier ?? 0 }}h</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted">Heure Supplémentaire</span>
                    <span class="badge bg-primary px-3 py-2 rounded-pill">{{ $operation->total_heure_supp ?? 0 }}h</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted">Total des heures</span>
                    <span class="badge bg-dark px-3 py-2 rounded-pill">{{ $operation->total_heure ?? 0 }}h</span>
                </div>
            </x-table-card>

            <!-- Banque de temps -->
            <x-table-card title="Banque de temps" icon="fas fa-piggy-bank">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted">Vacances</span>
                    <span class="badge bg-success px-3 py-2 rounded-pill">5h</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted">Banque de temps</span>
                    <span class="badge bg-success px-3 py-2 rounded-pill">10h</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted">Heure CSN</span>
                    <span class="badge bg-success px-3 py-2 rounded-pill">45h</span>
                </div>
                <div class="d-flex justify-content-between align-items-center border-top pt-3">
                    <span class="text-muted">Total en banque</span>
                    <span class="badge bg-dark px-3 py-2 rounded-pill">60h</span>
                </div>
            </x-table-card>
        </div>
    </div>

    {{-- Modals pour les actions workflow --}}
    
    {{-- Modal Soumettre --}}
    @if($showSubmitModal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Soumettre la feuille de temps</h5>
                        <button type="button" wire:click="toggleSubmitModal" class="btn-close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Êtes-vous sûr de vouloir soumettre cette feuille de temps ?</p>
                        <div class="mb-3">
                            <label class="form-label">Commentaire (optionnel)</label>
                            <textarea wire:model="commentaire" class="form-control" rows="3" 
                                      placeholder="Ajouter un commentaire..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" wire:click="toggleSubmitModal" class="btn btn-secondary">Annuler</button>
                        <button type="button" wire:click="soumettre" class="btn btn-primary">Confirmer la soumission</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Rappeler --}}
    @if($showRecallModal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Rappeler la feuille de temps</h5>
                        <button type="button" wire:click="toggleRecallModal" class="btn-close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Êtes-vous sûr de vouloir rappeler cette feuille de temps pour modification ?</p>
                        <div class="mb-3">
                            <label class="form-label">Motif (optionnel)</label>
                            <textarea wire:model="commentaire" class="form-control" rows="3" 
                                      placeholder="Raison du rappel..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" wire:click="toggleRecallModal" class="btn btn-secondary">Annuler</button>
                        <button type="button" wire:click="rappeler" class="btn btn-warning">Confirmer le rappel</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Approuver --}}
    @if($showApproveModal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Valider la feuille de temps</h5>
                        <button type="button" wire:click="toggleApproveModal" class="btn-close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Êtes-vous sûr de vouloir valider cette feuille de temps ?</p>
                        <div class="mb-3">
                            <label class="form-label">Commentaire (optionnel)</label>
                            <textarea wire:model="commentaire" class="form-control" rows="3" 
                                      placeholder="Commentaire de validation..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" wire:click="toggleApproveModal" class="btn btn-secondary">Annuler</button>
                        <button type="button" wire:click="approuver" class="btn btn-success">Confirmer la validation</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Rejeter --}}
    @if($showRejectModal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Rejeter la feuille de temps</h5>
                        <button type="button" wire:click="toggleRejectModal" class="btn-close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Êtes-vous sûr de vouloir rejeter cette feuille de temps ?</p>
                        <div class="mb-3">
                            <label class="form-label">Motif de rejet <span class="text-danger">*</span></label>
                            <textarea wire:model="motifRejet" class="form-control @error('motifRejet') is-invalid @enderror" 
                                      rows="4" placeholder="Expliquez pourquoi vous rejetez cette feuille..." required></textarea>
                            @error('motifRejet')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" wire:click="toggleRejectModal" class="btn btn-secondary">Annuler</button>
                        <button type="button" wire:click="rejeter" class="btn btn-danger">Confirmer le rejet</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Retourner (Admin) --}}
    @if($showReturnModal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Retourner la feuille de temps</h5>
                        <button type="button" wire:click="toggleReturnModal" class="btn-close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Êtes-vous sûr de vouloir retourner cette feuille de temps ?</p>
                        <div class="mb-3">
                            <label class="form-label">Motif du retour <span class="text-danger">*</span></label>
                            <textarea wire:model="motifRejet" class="form-control @error('motifRejet') is-invalid @enderror" 
                                      rows="4" placeholder="Expliquez pourquoi vous retournez cette feuille..." required></textarea>
                            @error('motifRejet')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" wire:click="toggleReturnModal" class="btn btn-secondary">Annuler</button>
                        <button type="button" wire:click="retourner" class="btn btn-warning">Confirmer le retour</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>