<?php
use App\Controllers\LanguageController;
use App\Config;
use App\Controllers\TypeController;
?>

<!DOCTYPE html>
<html lang="<?php echo LanguageController::getLocale() ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo TypeController::getTitle($type) . ' - ' . Config::get('SERVER_NAME') ?></title>
    <link href="/output.css" rel="stylesheet">
    <link href="/fontawesome/css/fontawesome.css" rel="stylesheet" />
    <link href="/fontawesome/css/solid.css" rel="stylesheet" />
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#1e293b">
    <meta name="msapplication-TileColor" content="#1e293b">
    <meta name="theme-color" content="#1e293b">
</head>

<body>
    <div class="min-h-screen bg-gray-800 pb-12 overflow-hidden relative">
        <?php require __DIR__ . '/components/header.php' ?>

        <div class="sm:max-w-screen-sm md:max-w-screen-md lg:max-w-screen-lg xl:max-w-screen-xl mx-auto px-6">
            <?php require __DIR__ . '/components/filters.php' ?>

            <div class="overflow-auto mb-6">
                <?php require __DIR__ . '/components/table.php' ?>
            </div>

            <?php if (isset($connectionError)) : ?>
                <div class="mb-6">
                    <div class="mb-2 text-center text-red-700 font-mono"><?php echo LanguageController::translate('An error occurred while trying to connect to the database.') ?></div>
                    <?php if (Config::get('DEBUG', false)) : ?><div class="text-center text-gray-400 font-mono"><?php echo $connectionError ?></div><?php endif ?>
                </div>
            <?php elseif (isset($databaseError)) : ?>
                <div class="mb-6">
                    <div class="mb-2 text-center text-red-700 font-mono"><?php echo LanguageController::translate('An error occurred while requesting the database.') ?></div>
                    <?php if (Config::get('DEBUG', false)) : ?><div class="text-center text-gray-400 font-mono"><?php echo $databaseError ?></div><?php endif ?>
                </div>
            <?php elseif ($meta['result_count'] === 0) : ?>
                <div class="mb-6 text-center text-gray-400 font-mono"><?php echo LanguageController::translate('No results found.') ?></div>
            <?php endif ?>

            <?php if (!isset($connectionError) && !isset($databaseError) && $meta['total_results'] > $perPage) { require __DIR__ . '/components/pagination.php'; } ?>
        </div>
    </div>

    <?php
    require __DIR__ . '/components/footer.php';
    require __DIR__ . '/components/filters_modal.php';
    ?>

    <?php if (Config::get('ALLOW_AUTHORIZATION', true)) require __DIR__ . '/components/logout_button.php' ?>

    <script src="/flowbite.min.js"></script>
    <script src="/searchbar.js"></script>

</body>

</html>