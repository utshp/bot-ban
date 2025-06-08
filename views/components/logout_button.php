<?php
  use App\Controllers\LanguageController;
?>

<form action="/logout" method="POST">
  <button type="submit" title="<?php echo LanguageController::translate('Logout') ?>" aria-label="<?php echo LanguageController::translate('Logout') ?>" class="text-white text-lg fixed bottom-6 right-6 w-14 h-14 bg-red-800 flex items-center justify-center hover:bg-red-900 rounded-full"><i class="fa-solid fa-right-from-bracket"></i></button>
</form>