<div>
    {{-- Messages de feedback --}}
    <x-alert-messages />


    <div class="row">
        {{-- Colonne principale - Tableau --}}
        <div class="col-lg-8">
            <x-table-card title="Liste des groupes" icon="fas fa-user-friends" button-text="Nouveau groupe"
                button-action="show_add_group_modal">

                {{-- Contenu du tableau --}}
                <div class="table-responsive">
                    <table class="table table-nowrap align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Nom</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($groups as $group)
                                <tr>
                                    <td>
                                        <span>{{ $group->name }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            {{-- Boutons avec composant --}}
                                            @if ($group->name != 'ADMIN')
                                                <x-action-button type="outline-success" icon="fas fa-edit"
                                                    tooltip="Modifier"
                                                    wireClick="show_edit_groupe_modal({{ $group->id }})" />
                                            @endif

                                            <x-action-button type="outline-info" icon="fas fa-ban"
                                                tooltip=" Voir les Permissions"
                                                wireClick="show_group_permission_modal({{ $group->id }},'{{ $group->name }}')" />
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4">
                                        <i class="fas fa-user-friends fa-3x text-muted mb-3"></i>
                                        <p class="text-muted mb-0">Aucun Groupe trouvé</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="mt-3">
                    {{ $groups->links() }}
                </div>
            </x-table-card>
        </div>
        {{-- Colonne latérale - Filtres --}}
        <div class="col-lg-4">
            <x-filter-card filterAction="get_groupes">
                {{-- Filtre par nom --}}
                <div class="mb-3">
                    <label for="name_searched" class="form-label">Nom</label>
                    <input type="text" id="name_searched" class="form-control" placeholder="Rechercher par Nom..."
                        wire:model.defer="name_searched">
                </div>
            </x-filter-card>
        </div>
    </div>

    {{-- Formulaire d'ajout et de modification des groupes --}}
    @if ($show_add_group)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div id="dialog-lg" class="modal-dialog modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-user-friends"></i>
                            {{ $groupId ? 'Modifier le groupe ' : 'Ajouter un groupe' }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                            wire:click="hide_groupe_add_modal"></button>
                    </div>
                    <div class="modal-body">
                        <livewire:groupe-form :groupId="$groupId" />
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Tableau des permisssions d'un group --}}
    @if ($group_permission)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div id="dialog-lg" class="modal-dialog modal-xl w-100" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-ban"></i> Permissions du groupe <span
                                class="fw-bold">{{ $groupName }}</span>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                            wire:click="hide_group_permission_modal"></button>
                    </div>
                    <div class="modal-body">
                        <livewire:group-permission :groupId="$groupId" />
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
