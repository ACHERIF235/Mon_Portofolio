<?php
function admin_sections(): array
{
    return [
        'skills' => [
            'name' => 'Compétences',
            'table' => 'skills',
            'fields' => [
                ['name' => 'category', 'label' => 'Catégorie', 'type' => 'text'],
                ['name' => 'name_fr', 'label' => 'Nom (FR)', 'type' => 'text'],
                ['name' => 'name_en', 'label' => 'Nom (EN)', 'type' => 'text'],
                ['name' => 'level', 'label' => 'Niveau', 'type' => 'text'],
                ['name' => 'order_index', 'label' => 'Ordre', 'type' => 'number'],
            ],
        ],
        'projects' => [
            'name' => 'Projets',
            'table' => 'projects',
            'fields' => [
                ['name' => 'title_fr', 'label' => 'Titre (FR)', 'type' => 'text'],
                ['name' => 'title_en', 'label' => 'Titre (EN)', 'type' => 'text'],
                ['name' => 'description_fr', 'label' => 'Description (FR)', 'type' => 'textarea'],
                ['name' => 'description_en', 'label' => 'Description (EN)', 'type' => 'textarea'],
                ['name' => 'link', 'label' => 'Lien', 'type' => 'text'],
                ['name' => 'order_index', 'label' => 'Ordre', 'type' => 'number'],
            ],
        ],
        'education' => [
            'name' => 'Formations',
            'table' => 'education',
            'fields' => [
                ['name' => 'title_fr', 'label' => 'Diplôme / Titre (FR)', 'type' => 'text'],
                ['name' => 'title_en', 'label' => 'Diploma / Title (EN)', 'type' => 'text'],
                ['name' => 'organization', 'label' => 'Établissement', 'type' => 'text'],
                ['name' => 'period', 'label' => 'Période', 'type' => 'text'],
                ['name' => 'description_fr', 'label' => 'Description (FR)', 'type' => 'textarea'],
                ['name' => 'description_en', 'label' => 'Description (EN)', 'type' => 'textarea'],
                ['name' => 'order_index', 'label' => 'Ordre', 'type' => 'number'],
            ],
        ],
        'associations' => [
            'name' => 'Associations',
            'table' => 'associations',
            'fields' => [
                ['name' => 'title_fr', 'label' => 'Titre / Rôle (FR)', 'type' => 'text'],
                ['name' => 'title_en', 'label' => 'Title / Role (EN)', 'type' => 'text'],
                ['name' => 'organization', 'label' => 'Organisation', 'type' => 'text'],
                ['name' => 'period', 'label' => 'Période', 'type' => 'text'],
                ['name' => 'description_fr', 'label' => 'Description (FR)', 'type' => 'textarea'],
                ['name' => 'description_en', 'label' => 'Description (EN)', 'type' => 'textarea'],
                ['name' => 'order_index', 'label' => 'Ordre', 'type' => 'number'],
            ],
        ],
        'languages' => [
            'name' => 'Langues',
            'table' => 'languages',
            'fields' => [
                ['name' => 'name_fr', 'label' => 'Langue (FR)', 'type' => 'text'],
                ['name' => 'name_en', 'label' => 'Language (EN)', 'type' => 'text'],
                ['name' => 'level', 'label' => 'Niveau', 'type' => 'text'],
                ['name' => 'order_index', 'label' => 'Ordre', 'type' => 'number'],
            ],
        ],
        'contacts' => [
            'name' => 'Contacts',
            'table' => 'contacts',
            'fields' => [
                ['name' => 'type', 'label' => 'Type', 'type' => 'text'],
                ['name' => 'label_fr', 'label' => 'Libellé (FR)', 'type' => 'text'],
                ['name' => 'label_en', 'label' => 'Label (EN)', 'type' => 'text'],
                ['name' => 'value', 'label' => 'Valeur', 'type' => 'text'],
                ['name' => 'order_index', 'label' => 'Ordre', 'type' => 'number'],
            ],
        ],
        'inbox' => [
            'name' => 'Messages',
            'table' => 'inbox_messages',
            'fields' => [],
        ],
    ];
}

function admin_section(string $type): ?array
{
    $sections = admin_sections();
    return $sections[$type] ?? null;
}

function sanitize_admin_input(string $value): string
{
    return trim($value);
}
