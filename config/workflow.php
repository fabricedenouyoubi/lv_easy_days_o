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
            'valide',
            'rejete'  
        ],
        'transitions' => [
            'enregistrer' => [
                'from' => ['brouillon', 'en_cours', 'rejete'], 
                'to' => 'en_cours'
            ],
            'soumettre' => [
                'from' => ['en_cours', 'rejete'], 
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
                'to' => 'rejete',  
                'guard' => 'App\Guards\RejetGuard' 
            ],
            'retourner' => [
                'from' => ['valide', 'rejete'],  
                'to' => 'en_cours'
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
            'Validé',
            'Rejeté'  
        ],
        'transitions' => [
            'enregistrer' => [
                'from' => ['Brouillon', 'En cours', 'Rejeté'], 
                'to' => 'En cours'
            ],
            'soumettre' => [
                'from' => ['En cours', 'Rejeté'], 
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
                'to' => 'Rejeté',  
                'guard' => 'App\Guards\RejetGuard'
            ],
            'retourner' => [
                'from' => ['Validé', 'Rejeté'],  
                'to' => 'En cours'
            ]
        ]
    ]
];