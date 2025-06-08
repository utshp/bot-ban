<?php
use App\Controllers\LanguageController;
?>

<div id="filters_modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-2xl max-h-full">
        <div class="relative rounded-lg shadow bg-gray-800">
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-gray-600">
                <h3 class="text-xl font-mono text-white">
                    <?php echo LanguageController::translate('Filter') ?>
                </h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-600 hover:text-white rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center" data-modal-hide="filters_modal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>
                    <span class="sr-only"><?php echo LanguageController::translate('Close modal') ?></span>
                </button>
            </div>
            <form action="" method="GET">
                <!-- Modal body -->
                <div class="p-4 md:p-5">
                    <select id="status" name="status" class="bg-gray-900 block border border-gray-600 text-sm rounded-lg p-2.5 placeholder-gray-400 text-white mb-3 font-mono focus:ring-gray-300 focus:border-gray-300">
                        <option value="" <?php if ($status === null) echo 'selected' ?> disabled><?php echo LanguageController::translate('Status') ?></option>
                        <option value="all" <?php if ($status === 'all') echo 'selected' ?>><?php echo LanguageController::translate('All') ?></option>
                        <option value="active" <?php if ($status === 'active') echo 'selected' ?>><?php echo LanguageController::translate('Active') ?></option>
                        <option value="inactive" <?php if ($status === 'inactive') echo 'selected' ?>><?php echo LanguageController::translate('Inactive') ?></option>
                    </select>

                    <div class="relative w-64 me-2">
                        <input type="text" id="search2" name="search" class="bg-gray-900 block border border-gray-600 text-sm rounded-lg w-full p-2.5 placeholder-gray-400 text-white font-mono focus:ring-gray-300 focus:border-gray-300" placeholder="<?php echo LanguageController::translate('Search by player') ?>" value="<?php echo $search ?>" oninput="toggleClearButton('search2', 'clearButton2')"/>
                        <button type="button" id="clearButton2" title="<?php echo LanguageController::translate('Clear') ?>" onclick="clearSearchField('search2')" class="absolute inset-y-0 end-0 flex items-center pe-3" style="display: none;">
                            <i class="fa-solid fa-xmark text-gray-400"></i>
                        </button>
                    </div>
                </div>
                <!-- Modal footer -->
                <div class="flex items-center p-4 md:p-5 border-t rounded-b border-gray-600">
                    <button type="submit" class="py-2.5 px-5 text-sm font-mono text-white bg-gray-700 hover:bg-gray-600 rounded-lg border border-gray-600 me-3"><?php echo LanguageController::translate('Confirm') ?></button>
                    <button data-modal-hide="filters_modal" type="button" class="py-2.5 px-5 text-sm font-mono text-white bg-gray-700 hover:bg-gray-600 rounded-lg border border-gray-600"><?php echo LanguageController::translate('Cancel') ?></button>
                </div>
            </form>
        </div>
    </div>
</div>