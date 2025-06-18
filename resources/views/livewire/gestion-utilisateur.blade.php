<div>
    {{-- Messages de feedback --}}
    <x-alert-messages />
    {{-- Layout principal --}}
    <div class="row">
        {{-- Colonne principale - Tableau --}}
        <div class="col-lg-8">
            <x-table-card title="Liste des Utilisateurs" icon="fas fa-user">

                {{-- Contenu du tableau --}}
                <div class="table-responsive">
                    <table class="table table-nowrap align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Nom</th>
                                <th>Email</th>
                                <th>Groupe</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($utilisateurs as $utilisateur)
                                <tr>
                                    <td>
                                        <span>{{ $utilisateur->name }}</span>
                                    </td>
                                    <td>
                                        <span>{{ $utilisateur->email }}</span>
                                    </td>
                                    <td>
                                        <span>
                                            <ul class="ps-0">
                                                @forelse ($utilisateur->roles as $group)
                                                    <li class="list-group-item px-0 border-0">
                                                        {{ $group?->name }}
                                                    </li>
                                                @empty
                                                    <li class="list-group-item px-0 border-0">---</li>
                                                @endforelse
                                            </ul>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            {{-- Boutons avec composant --}}
                                            <x-action-button type="outline-info" icon="fas fa-ban"
                                                tooltip="Voir Les Permissions" wireClick="show_user_modal('{{ $utilisateur->name }}', {{ $utilisateur->id }})" />
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4">
                                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                        <p class="text-muted mb-0">Aucun Utilisateur trouvé</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="mt-3">
                    {{ $utilisateurs->links() }}
                </div>
            </x-table-card>
        </div>
        {{-- Colonne latérale - Filtres --}}
        <div class="col-lg-4">
            <x-filter-card filterAction="get_utilisateurs">
                {{-- Filtre par nom --}}
                <div class="mb-3">
                    <label for="name_searched" class="form-label">Nom</label>
                    <input type="text" id="name_searched" class="form-control" placeholder="Rechercher par Nom..."
                        wire:model.defer="name_searched">
                </div>
                {{-- Filtre par email --}}
                <div class="mb-3">
                    <label for="email_searched" class="form-label">Email</label>
                    <input type="text" id="email_searched" class="form-control"
                        placeholder="Rechercher par Email..." wire:model.defer="email_searched">
                </div>
                {{-- Filtre par groupe --}}
                <div class="mb-3">
                    <label for="groupe_searched" class="form-label">Groupe</label>
                    <input type="text" id="groupe_searched" class="form-control"
                        placeholder="Rechercher par Groupe..." wire:model.defer="groupe_searched">
                </div>
            </x-filter-card>
        </div>
    </div>

    @if ($userPermissionModal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div id="dialog-lg" class="modal-dialog modal-xl w-100" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-ban"></i> Permissions <span class="fw-bold">{{ $userName }}</span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                            wire:click="hide_user_modal()"></button>
                    </div>
                    <div class="modal-body">
                        <livewire:permission-utilisateur :userId="$userId" />
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
