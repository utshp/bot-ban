function toggleClearButton(searchId, clearButtonId) {
    var searchInput = document.getElementById(searchId);
    var clearButton = document.getElementById(clearButtonId);

    if (searchInput.value.trim() !== '') {
        clearButton.style.display = 'block';
    } else {
        clearButton.style.display = 'none';
    }
}

function clearSearchField(searchId) {
    var searchInput = document.getElementById(searchId);
    searchInput.value = '';
    toggleClearButton(searchId, 'clearButton'); // Používáme 'clearButton' pro oba inputy
    searchInput.focus();
}

document.addEventListener('DOMContentLoaded', function() {
    toggleClearButton('search', 'clearButton'); // Zpracování obou inputů při načtení stránky
    toggleClearButton('search2', 'clearButton2'); // Zpracování obou inputů při načtení stránky
});
