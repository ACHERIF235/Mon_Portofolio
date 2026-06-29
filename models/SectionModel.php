<?php
class SectionModel
{
    private static array $sections = [
        'skills' => [
            'name' => 'Compétences',
            'table' => 'skills',
            'fields' => [
                ['name' => 'category', 'label' => 'Catégorie'],
                ['name' => 'name_fr', 'label' => 'Nom (FR)'],
                ['name' => 'name_en', 'label' => 'Nom (EN)'],
                ['name' => 'level', 'label' => 'Niveau'],
                ['name' => 'order_index', 'label' => 'Ordre'],
            ],
        ],
        'projects' => [
            'name' => 'Projets',
            'table' => 'projects',
            'fields' => [
                ['name' => 'title_fr', 'label' => 'Titre (FR)'],
                ['name' => 'title_en', 'label' => 'Titre (EN)'],
                ['name' => 'description_fr', 'label' => 'Description (FR)'],
                ['name' => 'description_en', 'label' => 'Description (EN)'],
                ['name' => 'link', 'label' => 'Lien'],
                ['name' => 'order_index', 'label' => 'Ordre'],
            ],
        ],
        'education' => [
            'name' => 'Formations',
            'table' => 'education',
            'fields' => [
                ['name' => 'title_fr', 'label' => 'Diplôme / Titre (FR)'],
                ['name' => 'title_en', 'label' => 'Diploma / Title (EN)'],
                ['name' => 'organization', 'label' => 'Établissement'],
                ['name' => 'period', 'label' => 'Période'],
                ['name' => 'description_fr', 'label' => 'Description (FR)'],
                ['name' => 'description_en', 'label' => 'Description (EN)'],
                ['name' => 'order_index', 'label' => 'Ordre'],
            ],
        ],
        'associations' => [
            'name' => 'Associations',
            'table' => 'associations',
            'fields' => [
                ['name' => 'title_fr', 'label' => 'Titre / Rôle (FR)'],
                ['name' => 'title_en', 'label' => 'Title / Role (EN)'],
                ['name' => 'organization', 'label' => 'Organisation'],
                ['name' => 'period', 'label' => 'Période'],
                ['name' => 'description_fr', 'label' => 'Description (FR)'],
                ['name' => 'description_en', 'label' => 'Description (EN)'],
                ['name' => 'order_index', 'label' => 'Ordre'],
            ],
        ],
        'languages' => [
            'name' => 'Langues',
            'table' => 'languages',
            'fields' => [
                ['name' => 'name_fr', 'label' => 'Langue (FR)'],
                ['name' => 'name_en', 'label' => 'Language (EN)'],
                ['name' => 'level', 'label' => 'Niveau'],
                ['name' => 'order_index', 'label' => 'Ordre'],
            ],
        ],
        'contacts' => [
            'name' => 'Contacts',
            'table' => 'contacts',
            'fields' => [
                ['name' => 'type', 'label' => 'Type'],
                ['name' => 'label_fr', 'label' => 'Libellé (FR)'],
                ['name' => 'label_en', 'label' => 'Label (EN)'],
                ['name' => 'value', 'label' => 'Valeur'],
                ['name' => 'order_index', 'label' => 'Ordre'],
            ],
        ],
        'inbox' => [
            'name' => 'Messages',
            'table' => 'inbox_messages',
            'fields' => [
                ['name' => 'name', 'label' => 'Nom'],
                ['name' => 'email', 'label' => 'Email'],
                ['name' => 'created_at', 'label' => 'Date'],
                ['name' => 'message', 'label' => 'Message']
            ],
        ],
    ];

    public static function getSections(): array
    {
        return self::$sections;
    }

    public static function getSection(string $key): ?array
    {
        return self::$sections[$key] ?? null;
    }

    public static function findAll(string $type): array
    {
        $section = self::getSection($type);
        if (!$section) {
            return [];
        }
        $orderFields = array_column($section['fields'], 'name');
        $orderClause = in_array('order_index', $orderFields, true)
            ? 'ORDER BY order_index ASC, id ASC'
            : 'ORDER BY id ASC';
        return db_fetchAll('SELECT * FROM ' . $section['table'] . ' ' . $orderClause);
    }

    public static function find(string $type, int $id): ?array
    {
        $section = self::getSection($type);
        if (!$section) {
            return null;
        }
        return db_fetch('SELECT * FROM ' . $section['table'] . ' WHERE id = :id LIMIT 1', ['id' => $id]);
    }

    public static function save(string $type, array $data, ?int $id = null): bool
    {
        $section = self::getSection($type);
        if (!$section) {
            return false;
        }

        $fields = array_column($section['fields'], 'name');
        $payload = [];
        foreach ($fields as $fieldName) {
            $payload[$fieldName] = $data[$fieldName] ?? '';
        }

        if ($id !== null) {
            $setParts = [];
            foreach ($payload as $key => $value) {
                $setParts[] = "`$key` = :$key";
            }
            $payload['id'] = $id;
            db_query('UPDATE ' . $section['table'] . ' SET ' . implode(', ', $setParts) . ' WHERE id = :id', $payload);
            return true;
        }

        $columns = implode(', ', array_map(fn($field) => "`$field`", array_keys($payload)));
        $placeholders = implode(', ', array_map(fn($field) => ":$field", array_keys($payload)));
        db_query('INSERT INTO ' . $section['table'] . ' (' . $columns . ') VALUES (' . $placeholders . ')', $payload);
        return true;
    }

    public static function delete(string $type, int $id): bool
    {
        $section = self::getSection($type);
        if (!$section) {
            return false;
        }
        db_query('DELETE FROM ' . $section['table'] . ' WHERE id = :id', ['id' => $id]);
        return true;
    }
}
