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
        if (probList) {
            probList.innerHTML = '';
            probList.dataset.loaded = false; // Réinitialise un éventuel état de chargement
        }

        // 5. Cache les sections/cards si besoin
        let cardSection = document.getElementById('selected-entity-card');
        if (cardSection) {
            cardSection.classList.add('hidden');
            cardSection.classList.remove('flex');
        }

        // 6. Réinitialise les variables globales
        if (window.selectedEntities) {
            window.selectedEntities = [];
        }

        // 7. Réinitialise les attributs data-* des éléments
        document.querySelectorAll('[data-entity]').forEach(element => {
            element.removeAttribute('data-entity');
        });

        // 8. Réinitialise les classes ou attributs spécifiques
        document.querySelectorAll('.selected').forEach(element => {
            element.classList.remove('selected');
        });

        // 9. Réinitialise les conteneurs d'interlocuteurs et de sociétés
        const interlocuteurContainer = document.getElementById('interlocuteur-container');
        if (interlocuteurContainer) {
            interlocuteurContainer.innerHTML = ''; // Vide le conteneur des interlocuteurs
        }

        const societeContainer = document.getElementById('societe-container');
        if (societeContainer) {
            societeContainer.innerHTML = ''; // Vide le conteneur des sociétés
        }

        // 10. Réinitialise les données dynamiques si nécessaire
        if (typeof afficherRechercheProblemeGlobaleAjax === 'function') {
            afficherRechercheProblemeGlobaleAjax("problemes-list1");
        }

        // 11. Réinitialise les données stockées dans le localStorage ou sessionStorage
        localStorage.removeItem('selectedEntities');
        sessionStorage.removeItem('selectedEntities');

        // 12. Optionnel : scroll en haut
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
});