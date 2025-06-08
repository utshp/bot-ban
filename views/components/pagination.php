<?php
use App\Controllers\LanguageController;
?>

<div class="flex flex-col lg:flex-row lg:justify-between items-end lg:items-center">
    <div class="text-gray-400 text-mono mb-2 lg:mb-0">
        <?php echo LanguageController::translate('Showing') ?> <span class="font-bold"><?php echo $meta['offset'] + 1 ?></span> <?php echo LanguageController::translate('to') ?> <span class="font-bold"><?php echo $meta['offset'] + $meta['result_count'] ?></span> <?php echo LanguageController::translate('of') ?> <span class="font-bold"><?php echo $meta['total_results'] ?></span> <?php echo LanguageController::translate('results') ?>
    </div>

    <nav aria-label="<?php echo LanguageController::translate('Page navigation') ?>">
        <ul class="inline-flex -space-x-px text-base h-10">
            <li>
                <?php if ($meta['page'] > 1) : ?>
                    <a href="?page=<?php echo $meta['page'] - 1 ?><?php if (isset($status)) echo "&status=$status"; if (isset($search)) echo "&search=$search" ?>" class="flex items-center justify-center px-4 h-10 ms-0 leading-tight font-mono text-white bg-gray-900 border border-e-0 border-gray-600 rounded-s-lg hover:bg-gray-700"><?php echo LanguageController::translate('Previous') ?></a>
                <?php else : ?>
                    <a class="flex items-center justify-center px-4 h-10 ms-0 leading-tight font-mono text-gray-500 bg-gray-900 border border-e-0 border-gray-600 rounded-s-lg"><?php echo LanguageController::translate('Previous') ?></a>
                <?php endif ?>
            </li>

            <div class="hidden md:flex">
                <?php
                $MAX_PAGES_DISPLAYED = 5;
                $start_page = max(1, min($meta['page'] - floor($MAX_PAGES_DISPLAYED / 2), $meta['total_pages'] - $MAX_PAGES_DISPLAYED + 1));
                $end_page = min($start_page + $MAX_PAGES_DISPLAYED - 1, $meta['total_pages']);

                for ($i = $start_page; $i <= $end_page; $i++) :
                ?>
                    <li>
                        <a href="?page=<?php echo $i ?><?php if (isset($status)) echo "&status=$status"; if (isset($search)) echo "&search=$search" ?>" <?php if ($meta['page'] == $i) echo 'aria-current="page"' ?> class="flex items-center font-mono justify-center px-4 h-10 text-white <?php echo ($meta['page'] == $i) ? 'bg-gray-700' : 'bg-gray-900 leading-tight'; ?> border border-e-0 border-gray-600 hover:bg-gray-700"><?php echo $i ?></a>
                    </li>
                <?php endfor ?>
            </div>

            <li>
                <?php if ($meta['page'] < $meta['total_pages']) : ?>
                    <a href="?page=<?php echo $meta['page'] + 1 ?><?php if (isset($status)) echo "&status=$status"; if (isset($search)) echo "&search=$search" ?>" class="flex items-center justify-center px-4 h-10 ms-0 leading-tight font-mono text-white bg-gray-900 border border-gray-600 rounded-e-lg hover:bg-gray-700"><?php echo LanguageController::translate('Next') ?></a>
                <?php else : ?>
                    <a class="flex items-center justify-center px-4 h-10 ms-0 leading-tight font-mono text-gray-500 bg-gray-900 border border-gray-600 rounded-e-lg"><?php echo LanguageController::translate('Next') ?></a>
                <?php endif ?>
            </li>
        </ul>
    </nav>

</div>