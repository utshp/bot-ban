<?php
use App\Controllers\LanguageController;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo LanguageController::translate('Authorize') ?></title>
    <link href="/output.css" rel="stylesheet">
    <script src="/flowbite.min.js"></script>
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
    <div class="min-h-screen bg-gray-900 flex flex-col">
        <div class="px-6 my-auto py-12">
            <div class="max-w-80 w-full mx-auto">
                <h1 class="text-center text-gray-400 font-mono text-3xl mb-6"><i class="fa-solid fa-key"></i> <?php echo LanguageController::translate('Authorize') ?></h1>

                <?php if (isset($_SESSION['accessDenied'])): ?>
                    <div class="text-red-700 text-center font-mono mb-3">
                        <?php echo $_SESSION['accessDenied']; ?>
                    </div>
                    <?php unset($_SESSION['accessDenied']);?>
                <?php endif; ?>

                <form action="/login" method="POST">
                    <input type="text" id="username" name="username" class="bg-gray-950 block border border-gray-600 text-sm rounded-lg w-full p-2.5 placeholder-gray-400 text-white font-mono focus:ring-gray-300 focus:border-gray-300 mb-3" required placeholder="<?php echo LanguageController::translate('Username') ?>" />

                    <input type="password" id="password" name="password" class="bg-gray-950 block border border-gray-600 text-sm rounded-lg w-full p-2.5 placeholder-gray-400 text-white font-mono focus:ring-gray-300 focus:border-gray-300 mb-4" required placeholder="<?php echo LanguageController::translate('Password') ?>" />

                    <button type="submit" class="bg-gray-800 p-2.5 px-5 text-sm rounded-lg border border-gray-600 hover:bg-gray-700 font-mono text-white w-full bg-[url('/img/noise.png')]"><?php echo LanguageController::translate('Sign in') ?></button>
                </form>
            </div>
        </div>
    </div>

    <?php
      require __DIR__ . '/components/footer.php';
    ?>
</body>
</html>