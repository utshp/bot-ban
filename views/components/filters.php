<?php
use App\Controllers\LanguageController;
use App\Controllers\TypeController;
?>

<div class="lg:flex lg:justify-center mb-3 lg:mb-6">
    <a href="/" class="block text-center p-2.5 px-5 text-sm rounded-lg border border-gray-600 hover:bg-gray-700 font-mono text-white mb-2 lg:me-3 lg:mb-0  <?php echo $type === null ? 'bg-gray-700' : 'bg-gray-900' ?>" href="#"><?php echo LanguageController::translate('All') ?> (<?php echo $punishmentCount ?>)</a>
    <a href="/bans" class="block text-center p-2.5 px-5 text-sm rounded-lg border border-gray-600 hover:bg-gray-700 font-mono text-white mb-2 lg:me-3 lg:mb-0  <?php echo $type === 'ban' ? 'bg-gray-700' : 'bg-gray-900' ?>" href="#"><i class="fa-solid fa-gavel"></i> <?php echo LanguageController::translate('Bans') ?> (<?php echo $banCount ?>)</a>
    <a href="/mutes" class="block text-center p-2.5 px-5 text-sm rounded-lg border border-gray-600 hover:bg-gray-700 font-mono text-white mb-2 lg:me-3 lg:mb-0  <?php echo $type === 'mute' ? 'bg-gray-700' : 'bg-gray-900' ?>" href="#"><i class="fa-solid fa-comment-slash"></i> <?php echo LanguageController::translate('Mutes') ?> (<?php echo $muteCount ?>)</a>
    <a href="/warnings" class="block text-center p-2.5 px-5 text-sm rounded-lg border border-gray-600 hover:bg-gray-700 font-mono text-white mb-2 lg:me-3 lg:mb-0  <?php echo $type === 'warning' ? 'bg-gray-700' : 'bg-gray-900' ?>" href="#"><i class="fa-solid fa-triangle-exclamation"></i> <?php echo LanguageController::translate('Warnings') ?> (<?php echo $warningCount ?>)</a>
    <a href="/kicks" class="block text-center p-2.5 px-5 text-sm rounded-lg border border-gray-600 hover:bg-gray-700 font-mono text-white mb-2 lg:mb-0  <?php echo $type === 'kick' ? 'bg-gray-700' : 'bg-gray-900' ?>" href="#"><i class="fa-solid fa-right-from-bracket"></i> <?php echo LanguageController::translate('Kicks') ?> (<?php echo $kickCount ?>)</a>
</div>

<form action="" method="GET" class="mb-6 hidden lg:block">
    <div class="flex justify-between">
        <div class="flex me-6">
            <button type="button" onClick="window.location.reload()" class="p-2.5 px-5 text-sm rounded-lg border border-gray-600 hover:bg-gray-700 font-mono text-white me-2 bg-gray-900" title="<?php echo LanguageController::translate('Refresh') ?>" aria-label="<?php echo LanguageController::translate('Refresh') ?>">
                <i class="fa-solid fa-arrows-rotate"></i>
            </button>

            <select id="status" name="status" onchange="this.form.submit()" class="bg-gray-900 block border border-gray-600 text-sm rounded-lg p-2.5 placeholder-gray-400 text-white font-mono focus:ring-gray-300 focus:border-gray-300">
                <option value="" <?php if($status === null) echo 'selected' ?> disabled><?php echo LanguageController::translate('Status') ?></option>
                <option value="all" <?php if($status === 'all') echo 'selected' ?>><?php echo LanguageController::translate('All') ?></option>
                <option value="active" <?php if($status === 'active') echo 'selected' ?>><?php echo LanguageController::translate('Active') ?></option>
                <option value="inactive" <?php if($status === 'inactive') echo 'selected' ?>><?php echo LanguageController::translate('Inactive') ?></option>
            </select>
        </div>

        <div class="flex">
            <div class="relative w-64 me-2">
                <input type="text" id="search" name="search" class="bg-gray-900 block border border-gray-600 text-sm rounded-lg w-full p-2.5 placeholder-gray-400 text-white font-mono focus:ring-gray-300 focus:border-gray-300" placeholder="<?php echo LanguageController::translate('Search by player') ?>" value="<?php echo $search ?>" oninput="toggleClearButton('search', 'clearButton')"/>
                <button type="button" id="clearButton" title="<?php echo LanguageController::translate('Clear') ?>" aria-label="<?php echo LanguageController::translate('Clear') ?>" onclick="clearSearchField('search')" class="absolute inset-y-0 end-0 flex items-center pe-3" style="display: none;">
                    <i class="fa-solid fa-xmark text-gray-400"></i>
                </button>
            </div>
            <button type="submit" class="bg-gray-900 p-2.5 px-5 text-sm rounded-lg border border-gray-600 hover:bg-gray-700 font-mono text-white"><?php echo LanguageController::translate('Show results') ?></button>
        </div>
    </div>
</form>

<div class="mb-6 flex justify-end items-center lg:hidden">
    <?php if (isset($search) || isset($status)) : ?>
        <a href="/<?php echo TypeController::transformType($type, true)?>" class="block font-mono text-gray-400 me-2" title="<?php echo LanguageController::translate('Remove filters') ?>" aria-label="<?php echo LanguageController::translate('Remove filters') ?>"><i class="fa-solid fa-xmark"></i></a>
    <?php endif ?>
    <button type="button" data-modal-target="filters_modal" data-modal-toggle="filters_modal" class="font-mono text-gray-300 hover:underline"><?php echo LanguageController::translate('Filter') ?></button>
</div>