<div>
    {{-- Messages de feedback --}}
    <x-alert-messages />

    <!-- Onglets de navigation -->
    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <ul class="nav nav-tabs card-header-tabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab === 'feuilles' ? 'active' : '' }}" 
                            wire:click="setActiveTab('feuilles')" type="button">
                        <i class="mdi mdi-clock-time-four-outline me-1"></i>
                        Feuilles de temps ({{ $stats['feuilles_en_attente'] }})
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab === 'absences' ? 'active' : '' }}" 
                            wire:click="setActiveTab('absences')" type="button">
                        <i class="mdi mdi-calendar-clock me-1"></i>
                        Demandes d'absence ({{ $stats['absences_en_attente'] }})
                    </button>
                </li>
            </ul>
        </div>

        <div class="card-body">
            <div class="tab-content">
                {{-- Onglet Feuilles de temps --}}
                @if($activeTab === 'feuilles')
                    <div class="tab-pane fade show active">
                        @if($feuilles_attente->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Employé</th>
                                            <th>Semaine</th>
                                            <th>Période</th>
                                            <th>Total heures</th>
                                            <th>Soumis le</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($feuilles_attente as $feuille)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm bg-light rounded-circle text-center me-2">
                                                            <span class="text-primary">
                                                                {{ substr($feuille->employe->prenom, 0, 1) }}{{ substr($feuille->employe->nom, 0, 1) }}
                                                            </span>
                                                        </div>
                                                        <div>
                                                            <strong>{{ $feuille->employe->prenom }} {{ $feuille->employe->nom }}</strong>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-primary">{{ $feuille->anneeSemaine->numero_semaine }}</span>
                                                </td>
                                                <td>
                                                    Du {{ \Carbon\Carbon::parse($feuille->anneeSemaine->debut)->format('d/m') }}
                                                    au {{ \Carbon\Carbon::parse($feuille->anneeSemaine->fin)->format('d/m') }}
                                                </td>
                                                <td>
                                                    <span class="fw-bold">{{ $feuille->total_heure ?? 0 }}h</span>
                                                </td>
                                                <td>
                                                    <small class="text-muted">
                                                        {{ $feuille->updated_at->format('d/m/Y H:i') }}
                                                    </small>
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex gap-1 justify-content-center">
                                                        <x-action-button 
                                                            type="outline-info" 
                                                            size="sm"
                                                            icon="mdi mdi-eye"
                                                            tooltip="Consulter"
                                                            href="{{ route('feuille-temps.show', ['semaineId' => $feuille->anneeSemaine->id, 'operationId' => $feuille->id]) }}"
                                                        />
                                                        <x-action-button 
                                                            type="outline-success" 
                                                            size="sm"
                                                            icon="mdi mdi-check"
                                                            tooltip="Validation rapide"
                                                            wireClick="approuverFeuille({{ $feuille->id }})"
                                                        />
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            {{-- Pagination --}}
                            {{ $feuilles_attente->links() }}
                        @else
                            <div class="text-center py-5">
                                <i class="mdi mdi-clock-check-outline text-muted" style="font-size: 48px;"></i>
                                <h5 class="mt-3">Aucune feuille en attente</h5>
                                <p class="text-muted">Toutes les feuilles de temps ont été traitées.</p>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- Onglet Demandes d'absence --}}
                @if($activeTab === 'absences')
                    <div class="tab-pane fade show active">
                        @if($absences_attente->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Employé</th>
                                            <th>Type d'absence</th>
                                            <th>Période</th>
                                            <th>Durée</th>
                                            <th>Soumis le</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($absences_attente as $absence)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm bg-light rounded-circle text-center me-2">
                                                            <span class="text-primary">
                                                                {{ substr($absence->employe->prenom, 0, 1) }}{{ substr($absence->employe->nom, 0, 1) }}
                                                            </span>
                                                        </div>
                                                        <div>
                                                            <strong>{{ $absence->employe->prenom }} {{ $absence->employe->nom }}</strong>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info">{{ $absence->codeTravail->libelle ?? 'N/A' }}</span>
                                                </td>
                                                <td>
                                                    Du {{ $absence->date_debut->format('d/m') }}
                                                    au {{ $absence->date_fin->format('d/m') }}
                                                </td>
                                                <td>
                                                    <span class="fw-bold">{{ $absence->total_heure ?? 0 }}h</span>
                                                </td>
                                                <td>
                                                    <small class="text-muted">
                                                        {{ $absence->updated_at->format('d/m/Y H:i') }}
                                                    </small>
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex gap-1 justify-content-center">
                                                        <x-action-button 
                                                            type="outline-info" 
                                                            size="sm"
                                                            icon="fas fa-eye"
                                                            tooltip="Consulter"
                                                            href="{{ route('absence.show', $absence->id) }}"
                                                        />
                                                        <x-action-button 
                                                            type="outline-success" 
                                                            size="sm"
                                                            icon="fas fa-check"
                                                            tooltip="Validation rapide"
                                                            wireClick="approuverAbsence({{ $absence->id }})"
                                                        />
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            {{-- Pagination --}}
                            {{ $absences_attente->links() }}
                        @else
                            <div class="text-center py-5">
                                <i class="mdi mdi-calendar-check-outline text-muted" style="font-size: 48px;"></i>
                                <h5 class="mt-3">Aucune demande en attente</h5>
                                <p class="text-muted">Toutes les demandes d'absence ont été traitées.</p>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
