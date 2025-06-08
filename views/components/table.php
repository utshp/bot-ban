<?php
use Carbon\Carbon;
use App\Controllers\LanguageController;
use App\Controllers\AvatarController;
use App\Config;
use App\Controllers\TypeController;
use Carbon\Language;

?>

<table class="table-auto w-full text-white font-mono">
    <thead>
        <tr class="bg-gray-700 bg-[url('/img/noise.png')]">
            <th class="py-3 px-6 text-start"><?php echo LanguageController::translate('Player'); ?></th>
            <th class="py-3 px-6 text-start"><?php echo LanguageController::translate('Type'); ?></th>
            <th class="py-3 px-6 text-start"><?php echo LanguageController::translate('Admin'); ?></th>
            <th class="py-3 px-6 text-end"><?php echo LanguageController::translate('Expiration'); ?></th>
            <th class="py-3 px-6 text-end"><?php echo LanguageController::translate('Status'); ?></th>
            <?php if (Config::get('ALLOW_SCOPES', false)) : ?>
                <th class="py-3 px-6 text-end"><?php echo LanguageController::translate('Server'); ?></th>
            <?php endif ?>
            <th class="py-3 px-6 text-start"><?php echo LanguageController::translate('Reason'); ?></th>
        </tr>
    </thead>

    <tbody>
        <?php foreach ($punishments as $key=>$punishment): ?>
            <tr class="<?php echo $key % 2 === 0 ? 'bg-gray-900' : 'bg-gray-800'; ?>">
                <td class="py-3 px-6">
                    <div class="flex items-center">
                        <div class="w-8 h-8 me-2">
                            <img class="w-full h-full" src="
                                <?php
                                if ($punishment->ipban) {
                                    echo Config::get('IP_ADDRESS_IMAGE', '/img/steve.png');
                                } elseif (!isset($punishment->player_name)) {
                                    echo Config::get('DEFAULT_AVATAR_IMAGE', '/img/steve.png');
                                } else {
                                    echo AvatarController::getSource($punishment->player_name);
                                }
                                ?>
                            " alt="
                            <?php
                            if ($punishment->ipban) {
                                echo LanguageController::translate('IP Address');
                            } elseif (!isset($punishment->player_name)) {
                                echo LanguageController::translate('Player avatar');
                            } else {
                                echo $punishment->player_name . LanguageController::translate('\'s') . ' ' . LanguageController::translate('avatar');
                            }
                            ?>
                            ">
                        </div>
                        
                        <div class="whitespace-nowrap">
                            <?php
                            if ($punishment->ipban) {
                                echo LanguageController::translate('IP Address');
                            } elseif (!isset($punishment->player_name)) {
                                echo LanguageController::translate('Unknown name');
                            } else {
                                echo $punishment->player_name;
                            }
                            ?>
                        </div>
                    </div>
                </td>

                <td class="py-3 px-6 whitespace-nowrap">
                    <?php if ($punishment->type === 'ban') : ?>
                        <i class="fa-solid fa-gavel"></i>
                    <?php elseif ($punishment->type === 'mute') : ?>
                        <i class="fa-solid fa-comment-slash"></i>
                    <?php elseif ($punishment->type === 'warning') : ?>
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    <?php elseif ($punishment->type === 'kick') : ?>
                        <i class="fa-solid fa-right-from-bracket"></i>
                    <?php endif ?>

                    <?php echo TypeController::getLabel($punishment->type) ?>
                </td>

                <td class="py-3 px-6">
                    <div class="flex items-center">
                        <div class="w-8 h-8 me-2">
                            <img class="w-full h-full" src="
                                <?php
                                if ($punishment->by_console) {
                                    echo Config::get('CONSOLE_IMAGE', '/img/console.png');
                                } else {
                                    echo AvatarController::getSource($punishment->admin_name);
                                }
                                ?>
                            " alt="
                            <?php
                            if ($punishment->by_console) {
                                echo LanguageController::translate('Console');
                            } else {
                                echo $punishment->admin_name . LanguageController::translate('\'s') . ' ' . LanguageController::translate('avatar');
                            }
                            ?>
                            ">
                        </div>

                        <div>
                            <?php
                            if ($punishment->by_console) {
                                echo LanguageController::translate('Console');
                            } else {
                                echo $punishment->admin_name;
                            }
                            ?>
                        </div>
                    </div>
                </td>

                <td class="py-3 px-6 text-end whitespace-nowrap">
                    <button data-tooltip-target="expiration_tooltip_<?php echo $key ?>" type="button" class="inline">
                        <?php
                        if (isset($punishment->end) && $punishment->type !== 'kick'  ) {
                            echo Carbon::parse($punishment->end)->diffForHumans(Carbon::now());
                        } elseif ($punishment->type === 'kick') {
                            echo LanguageController::translate('Immediate');
                        } else {
                            echo LanguageController::translate('Permanent');
                        }
                        ?>
                    </button>

                    <div id="expiration_tooltip_<?php echo $key ?>" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-mono text-white transition-opacity duration-300 bg-black rounded-lg shadow-sm opacity-0 tooltip">
                        <?php
                        if(isset($punishment->end)) {
                            echo LanguageController::translate('From:') . ' ' . Carbon::parse($punishment->start) . '<br>' . LanguageController::translate("To:") . ' ' . Carbon::parse($punishment->end);
                        } else {
                            echo LanguageController::translate('From:') . ' ' . Carbon::parse($punishment->start);
                        }
                        ?>
                        <div class="tooltip-arrow" data-popper-arrow></div>
                    </div>
                </td>

                <td class="py-3 px-6">
                    <div class="flex justify-end">
                        <button <?php if ($punishment->cancelled) echo 'data-tooltip-target="status_tooltip_' . $key . '"'; ?> class="flex items-center" type="button" <?php if (!$punishment->cancelled) echo 'disabled'; ?>>
                            <?php
                            if ($punishment->active) : ?>
                                <div class="bg-red-500 rounded-full me-3 h-2 w-2"></div>
                            <?php else : ?>
                                <div class="bg-green-500 rounded-full me-3 h-2 w-2"></div>
                            <?php endif ?>

                            <div>
                                <?php
                                if ($punishment->active) {
                                    echo LanguageController::translate('Active');
                                } elseif ($punishment->cancelled) {
                                    echo LanguageController::translate('Cancelled');
                                } else {
                                    echo LanguageController::translate('Expired');
                                }
                                ?>
                            </div>
                        </button>

                        <?php if ($punishment->cancelled) : ?>
                            <div id="status_tooltip_<?php echo $key ?>" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-mono text-white transition-opacity duration-300 bg-black rounded-lg shadow-sm opacity-0 tooltip">
                                <?php echo LanguageController::translate('Cancelled by:') . ' ' . ($punishment->cancelled_by_console ? LanguageController::translate('Console') : $punishment->cancelled_by_name) ?><br>
                                <?php echo LanguageController::translate('Date:') . ' ' . Carbon::parse($punishment->cancelled_by_date) ?><br>
                                <?php echo LanguageController::translate('Reason:') . ' ' . $punishment->cancelled_by_reason ?>
                                <div class="tooltip-arrow" data-popper-arrow></div>
                            </div>
                        <?php endif ?>
                    </div>
                </td>

                <?php if (Config::get('ALLOW_SCOPES', false)) : ?>
                    <td class="py-3 px-6 text-end">
                        <button <?php if (isset($punishment->server_origin)) echo 'data-tooltip-target="scope_tooltip_' . $key . '"'; ?> type="button" class="inline" <?php if (!isset($punishment->server_origin)) echo 'disabled'; ?>>
                            <?php if (!isset($punishment->scope)) : ?>
                                <span class="bg-<?php echo Config::get('GLOBAL_SCOPE_COLOR', 'yellow') ?>-900 text-<?php echo Config::get('GLOBAL_SCOPE_COLOR', 'yellow') ?>-300 text-sm font-medium px-2.5 py-0.5 rounded"><?php echo LanguageController::translate('Global') ?></span>
                            <?php else : ?>
                                <span class="bg-<?php echo $punishment->scope->color ?>-900 text-<?php echo $punishment->scope->color ?>-300 text-sm font-medium px-2.5 py-0.5 rounded"><?php echo $punishment->scope->name ?? $punishment->scope->value ?></span>
                            <?php endif ?>
                        </button>

                        <?php if (isset($punishment->server_origin)) : ?>
                            <div id="scope_tooltip_<?php echo $key ?>" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-mono text-white transition-opacity duration-300 bg-black rounded-lg shadow-sm opacity-0 tooltip">
                                <?php echo LanguageController::translate('Server origin:') . ' ' . ($punishment->server_origin->name ?? $punishment->server_origin->value) ?>
                                <div class="tooltip-arrow" data-popper-arrow></div>
                            </div>
                        <?php endif ?>
                    </td>
                <?php endif ?>

                <td class="py-3 px-6 whitespace-nowrap"><?php echo $punishment->reason ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>