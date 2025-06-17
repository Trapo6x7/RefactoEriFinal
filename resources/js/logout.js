let inactivityTimeout; // Stocke le timeout d'inactivité
const logoutDelay = 12 * 60 * 60 * 1000; // Temps d'inactivité avant déconnexion (12 heures en millisecondes)

/**
 * Réinitialise le timer d'inactivité.
 */
function resetInactivityTimer() {
    clearTimeout(inactivityTimeout); // Réinitialise le timer
    inactivityTimeout = setTimeout(() => {
        // Déconnecte l'utilisateur après le délai d'inactivité
        alert("Vous avez été déconnecté pour cause d'inactivité.");
        document.getElementById('logout').submit();
    }, logoutDelay);
}

/**
 * Initialise les événements pour détecter l'activité de l'utilisateur.
 */
function initializeAutoLogout() {
    // Écoute les événements d'activité utilisateur
    ["mousemove", "keydown", "click", "scroll", "touchstart"].forEach((event) => {
        window.addEventListener(event, resetInactivityTimer);
    });

    // Démarre le timer d'inactivité
    resetInactivityTimer();
}

// Initialise le logout automatique au chargement de la page
initializeAutoLogout();