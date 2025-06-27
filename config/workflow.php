<?php

return [
    'operation_workflow' => [
        'type' => 'workflow',
        'metadata' => [
            'title' => 'Workflow des Opérations'
        ],
        'marking_store' => [
            'type' => 'method',
            'property' => 'workflow_state'
        ],
        'supports' => [
            'Modules\RhFeuilleDeTempsAbsence\Models\Operation'
        ],
        'places' => [
            'brouillon',
            'en_cours', 
            'soumis',
            'valide'
        ],
        'transitions' => [
            'enregistrer' => [
                'from' => ['brouillon', 'en_cours'],
                'to' => 'en_cours'
            ],
            'soumettre' => [
                'from' => 'en_cours',
                'to' => 'soumis'
            ],
            'rappeler' => [
                'from' => 'soumis',
                'to' => 'en_cours'
            ],
            'valider' => [
                'from' => 'soumis',
                'to' => 'valide',
                'guard' => 'App\Guards\ManagerGuard'
            ],
            'rejeter' => [
                'from' => 'soumis', 
                'to' => 'en_cours', 
                'guard' => 'App\Guards\RejetGuard' 
            ]
        ]
    ],

    'demande_absence_workflow' => [
        'type' => 'workflow',
        'metadata' => [
            'title' => 'Workflow des Demandes d\'Absence'
        ],
        'marking_store' => [
            'type' => 'method',
            'property' => 'statut'
        ],
        'supports' => [
            'Modules\RhFeuilleDeTempsAbsence\Models\DemandeAbsence'
        ],
        'places' => [
            'Brouillon',
            'En cours', 
            'Soumis',
            'Validé'
        ],
        'transitions' => [
            'enregistrer' => [
                'from' => ['Brouillon', 'En cours'],
                'to' => 'En cours'
            ],
            'soumettre' => [
                'from' => 'En cours', 
                'to' => 'Soumis'
            ],
            'rappeler' => [
                'from' => 'Soumis',
                'to' => 'En cours'
            ],
            'valider' => [
                'from' => 'Soumis',
                'to' => 'Validé',
                'guard' => 'App\Guards\ManagerGuard'
            ],
            'rejeter' => [
                'from' => 'Soumis', 
                'to' => 'En cours',
                'guard' => 'App\Guards\RejetGuard'
            ]
        ]
    ]
];