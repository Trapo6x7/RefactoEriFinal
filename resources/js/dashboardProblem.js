// Exemple de données
const problemes = [
    { id: 1, titre: "Problème réseau", societe: "Société A", description: "..." },
    { id: 2, titre: "Panne serveur", societe: "Société B", description: "..." },
    { id: 3, titre: "Erreur application", societe: "Société A", description: "..." },
    { id: 4, titre: "Incident sécurité", societe: "Société C", description: "..." }
];

// Fonction pour afficher les cards
function afficherProblemes() {
    const container = document.getElementById('problemes-list');
    container.innerHTML = '';
    problemes.forEach((p, idx) => {
        const card = document.createElement('div');
        card.className = 'probleme-card';
        card.innerHTML = `<strong>${p.titre}</strong> - ${p.societe}<br>${p.description}`;
        card.addEventListener('click', () => toggleBandeau(idx, p.societe));
        container.appendChild(card);

        // Bandeau caché par défaut
        const bandeau = document.createElement('div');
        bandeau.className = 'bandeau-communs';
        bandeau.style.display = 'none';
        bandeau.id = `bandeau-${idx}`;
        container.appendChild(bandeau);
    });
}

// Fonction pour ouvrir/fermer le bandeau et afficher les problèmes communs
function toggleBandeau(idx, societe) {
    // Fermer tous les bandeaux
    document.querySelectorAll('.bandeau-communs').forEach(b => b.style.display = 'none');

    const bandeau = document.getElementById(`bandeau-${idx}`);
    if (bandeau.style.display === 'none') {
        // Chercher les problèmes communs
        const communs = problemes.filter(p => p.societe === societe);
        bandeau.innerHTML = `<strong>Problèmes communs pour ${societe} :</strong><ul>` +
            communs.map(p => `<li>${p.titre}</li>`).join('') + '</ul>';
        bandeau.style.display = 'block';
    } else {
        bandeau.style.display = 'none';
    }
}

// À appeler au chargement de la page
document.addEventListener('DOMContentLoaded', afficherProblemes);
