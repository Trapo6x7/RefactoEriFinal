document.addEventListener('DOMContentLoaded', function() {
    const btnRAZ = document.getElementById('btn-raz');
    if (!btnRAZ) return;

    btnRAZ.addEventListener('click', function() {
        // 1. Vide tous les inputs de recherche
        document.querySelectorAll('input[type="text"], input[type="search"]').forEach(input => input.value = '');

        // 2. Vide les selects (remet à la première option)
        document.querySelectorAll('select').forEach(select => select.selectedIndex = 0);

        // 3. Vide les cards (suppose que tes cards ont des ids card-1, card-2, etc)
        for (let i = 1; i <= 4; i++) {
            let card = document.getElementById(`card-${i}`);
            if (card) card.innerHTML = '';
        }

        // 4. Vide les listes de problèmes (adapte l'id si besoin)
        let probList = document.getElementById('problemes-list2');
        if (probList) probList.innerHTML = '';

        // 5. Cache les sections/cards si besoin
        let cardSection = document.getElementById('selected-entity-card');
        if (cardSection) {
            cardSection.classList.add('hidden');
            cardSection.classList.remove('flex');
        }

        // 6. Si tu utilises des variables JS globales pour l'état, reset-les ici
        // Ex: window.selectedEntities = [];

        // 7. Si tu utilises des fonctions d'affichage dynamique, relance-les ici
        // Ex: afficherRechercheProblemeGlobaleAjax("problemes-list1");

        // 8. Si tu veux aussi reset d'autres éléments, ajoute-les ici

        // Optionnel : scroll en haut
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
});