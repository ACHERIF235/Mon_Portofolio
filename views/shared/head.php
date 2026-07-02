<!DOCTYPE html>
<html lang="<?= View::escape($lang ?? 'fr') ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= View::escape($title ?? 'Portfolio') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= View::escape($basePath ?? '') ?>assets/css/style.css">
    <meta name="description" content="Portfolio personnel moderne et responsive.">
    <style>:root { --accent: <?= View::escape($accent ?? '#c9a227') ?>; }</style>
    <?php
    $faviconUrl = ($basePath ?? '') . 'assets/images/favicon.ico';
    if (isset($settings['profile_photo']) && !empty($settings['profile_photo'])) {
        $faviconUrl = $settings['profile_photo'];
    } elseif (class_exists('SettingModel')) {
        $photo = SettingModel::get('profile_photo');
        if ($photo) $faviconUrl = $photo;
    }
    ?>
    <link rel="icon" href="<?= View::escape($faviconUrl) ?>" type="image/x-icon">
</head>
<body>
