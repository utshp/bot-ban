<?php
    use App\Config;
    use App\Controllers\LanguageController;
    use App\Controllers\TypeController;
?>

<header class="bg-center bg-cover bg-no-repeat bg-gray-500 bg-blend-multiply mb-6" style="background-image: url('<?php echo Config::get('BACKGROUND_IMAGE', '/img/background.webp') ?>')">
    <nav>
        <div class="sm:max-w-screen-sm md:max-w-screen-md lg:max-w-screen-lg xl:max-w-screen-xl mx-auto p-6 flex justify-between items-center text-white text-xl md:text-2xl font-mono">
            <a href="/" class="flex items-center">
                <?php if (Config::get('SHOW_SERVER_LOGO', true)) : ?>
                    <img src="<?php echo Config::get('SERVER_LOGO', '') ?>" class="h-8 me-3" alt="Server Logo" />
                <?php endif ?>
                <span><?php echo Config::get('SERVER_NAME', 'A Minecraft Server') ?></span>
            </a>
            
            <?php if (Config::get('SHOW_HOME_LINK', false)) { ?>
                <a href="<?php echo Config::get('HOME_URL', '/') ?>"><?php echo LanguageController::translate('Home') ?></a>
            <?php } ?>
        </div>
    </nav>

    <h1 class="text-white text-center text-5xl font-mono py-40">
        <?php echo TypeController::getTitle($type) ?>
    </h1>
</header>