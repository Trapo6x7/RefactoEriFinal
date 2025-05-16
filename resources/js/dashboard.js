const allowedKeys = {
    interlocuteur: [
        "lastname",
        "name",
        "email",
        "phone_fix",
        "phone_mobile",
        "id_teamviewer",
    ],
    societe: [
        "name",
        "boss_name",
        "boss_phone",
        "recep_phone",
        "address",
        "main_name",
    ],
};

let selectedEntities = JSON.parse(
    localStorage.getItem("selectedEntities") || "[]"
);
selectedEntities = normalizeSelectedEntities(selectedEntities);

const csrfToken =
    document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute("content") || "";

function getEntityFromDataset(dataset) {
    const entity = {};
    for (const key in dataset) entity[key] = dataset[key];
    return entity;
}

// Fonction pour garantir l'ordre societe/interlocuteur
function normalizeSelectedEntities(entities) {
    let societe = entities.find((e) => e.model === "societe");
    let interlocuteur = entities.find((e) => e.model === "interlocuteur");
    let result = [];
    if (societe) result.push(societe);
    if (interlocuteur) result.push(interlocuteur);
    return result;
}

function formatServiceInfo(raw) {
    if (!raw) return "";
    // 1. Remplace les retours à la ligne par <br>
    let formatted = raw.replace(/\r?\n/g, "<br>");
    // 2. Mets en gras les mots-clés courants (à adapter selon tes besoins)
    formatted = formatted.replace(
        /([A-Za-zÀ-ÿ0-9_\-\.\/'’\(\)\[\] ]+?)\s*:/g,
        '<span class="font-bold">$&</span>'
    );
    // 2bis. Supprime tous les tirets/puces en début de ligne (même multiples)
    formatted = formatted.replace(/(<br>|^)\s*[-–—•]+(\s*)/g, "$1");
    // 3. Transforme les listes commençant par - ou • en <li> (en ignorant les tirets déjà supprimés)
    formatted = formatted.replace(/(?:^|<br>)(.*?)(?=<br>|$)/g, (m, p1) => {
        if (p1.trim().length === 0) return m;
        return `<li>${p1.trim()}</li>`;
    });
    // 4. Si on a des <li>, entoure d'une <ul>
    if (formatted.includes("<li>")) {
        formatted = formatted.replace(/(<li>.*<\/li>)/gs, "<ul>$1</ul>");
    }
    return formatted;
}

function addEntityToSelection(entity) {
    // Ne pas stocker dans le localStorage, garder uniquement en mémoire
    // On veut societe uniquement sur card 1/2, interlocuteur uniquement sur card 3/4

    // Si déjà présente, ne rien faire
    if (
        selectedEntities.some(
            (item) => item.id === entity.id && item.model === entity.model
        )
    ) {
        selectedEntities = normalizeSelectedEntities(selectedEntities);
        showSelectedEntitiesCard(selectedEntities);
        return;
    }

    if (entity.model === "societe") {
        // Remplace la societe (card 1/2)
        selectedEntities = selectedEntities.filter(
            (e) => e.model !== "societe"
        );
        selectedEntities.unshift(entity);
    } else if (entity.model === "interlocuteur") {
        // Remplace l'interlocuteur (card 3/4)
        selectedEntities = selectedEntities.filter(
            (e) => e.model !== "interlocuteur"
        );
        // On garde la societe si présente en premier
        if (
            selectedEntities.length &&
            selectedEntities[0].model === "societe"
        ) {
            selectedEntities = [selectedEntities[0], entity];
        } else {
            selectedEntities = [entity];
        }
    }

    selectedEntities = normalizeSelectedEntities(selectedEntities);
    showSelectedEntitiesCard(selectedEntities);
}

function afficherRechercheProblemeGlobaleAjax(containerId) {
    const liste = document.getElementById(containerId);
    if (!liste) return;
    liste.innerHTML = `
        <div class="flex justify-center gap-2 mb-4 px-12 w-full">
            <input type="text" id="search-problemes-global" placeholder="Rechercher un probleme..." 
                class="p-2 border text-lg rounded max-w-xs w-1/2" />
            <select id="filter-tool" class="p-2 text-lg border rounded w-1/5">
                <option value="">Tous les outils</option>
            </select>
            <select id="filter-env" class="p-2 border text-lg rounded w-1/5">
                <option value="">Tous les env...</option>
            </select>
            <select id="filter-societe" class="p-2 border text-lg rounded w-1/5">
                <option value="">Toutes les soc...</option>
            </select>
        </div>
        <div id="problemes-list-inner-global"></div>
    `;

    const renderProblemes = (
        problemes,
        query = "",
        env = "",
        tool = "",
        societe = ""
    ) => {
        const container = document.getElementById(
            "problemes-list-inner-global"
        );
        if (!query && !env && !tool && !societe) {
            container.innerHTML = `
            <div class="mb-2 px-8 py-1 text-primary-grey font-semibold text-lg text-left">
                Commencez à taper pour rechercher un probleme...
            </div>
        `;
            return;
        }
        container.innerHTML = `
        <div class="flex flex-col items-start w-full">
        ${
            problemes.length
                ? problemes
                      .map(
                          (p, i) =>
                              `<article class="mb-2 px-8 py-1 bg-off-white rounded text-lg w-full max-w-2xl text-left">
                                <button 
                                    class="w-full text-left font-semibold text-blue-accent hover:text-blue-hover problem-title-btn flex items-center gap-2"
                                    data-idx="${i}">
                                    <h3 class="text-left">${p.title || ""}</h3>
                                </button>
                            </article>`
                      )
                      .join("")
                : '<div class="mb-2 px-8 py-1 text-primary-grey font-semibold text-lg text-left">Aucun probleme trouvé.</div>'
        }
        </div>
    `;

        // Ajoute l'événement pour afficher la solution dans problemes-list2
        container.querySelectorAll(".problem-title-btn").forEach((btn) => {
            btn.addEventListener("click", function () {
                const idx = btn.getAttribute("data-idx");
                const problem = problemes[idx];
                const solutionContainer =
                    document.getElementById("problemes-list2");
                const isAdmin =
                    window.currentUserRole &&
                    ["admin", "superadmin"].includes(
                        window.currentUserRole.toLowerCase()
                    );
                if (problem && solutionContainer) {
                    solutionContainer.innerHTML = `
                <div class="bg-white text-lg rounded p-4">
                    <h2 class="font-bold text-blue-accent mb-2">${
                        problem.title || ""
                    }</h2>
                    <div 
                        class="text-primary-grey editable-problem-solution"
                        ${isAdmin ? 'contenteditable="true"' : ""}
                        data-problem-id="${problem.id || ""}"
                        style="min-height:2em;"
                    >${
                        problem.description
                            ? formatServiceInfo(problem.description)
                            : "<em>Aucune solution enregistrée.</em>"
                    }</div>
                </div>
            `;

                    // Si admin, ajoute la sauvegarde auto au blur
                    if (isAdmin) {
                        const editableDiv = solutionContainer.querySelector(
                            ".editable-problem-solution"
                        );
                        editableDiv.addEventListener("blur", function () {
                            const newValue = this.innerText.trim();
                            const problemId = this.dataset.problemId;
                            fetch(
                                `/problemes/update-description/${problemId}`,
                                {
                                    method: "POST",
                                    headers: {
                                        "Content-Type": "application/json",
                                        "X-CSRF-TOKEN": csrfToken,
                                        Accept: "application/json",
                                    },
                                    body: JSON.stringify({
                                        description: newValue,
                                    }),
                                }
                            )
                                .then((res) => res.json())
                                .then(() => {
                                    this.style.background = "#678BD8";
                                    setTimeout(
                                        () => (this.style.background = ""),
                                        500
                                    );
                                })
                                .catch(() => {
                                    this.style.background = "#DB7171";
                                    setTimeout(
                                        () => (this.style.background = ""),
                                        1000
                                    );
                                });
                        });
                    }
                }
            });
        });
    };

    // Fonction pour charger les problemes avec filtres
    function fetchAndRenderProblems() {
        const q = document
            .getElementById("search-problemes-global")
            .value.trim();
        const tool = document.getElementById("filter-tool").value;
        const env = document.getElementById("filter-env").value;
        const societe = document.getElementById("filter-societe").value;
        const params = new URLSearchParams();
        if (q) params.append("q", q);
        if (tool) params.append("tool", tool);
        if (env) params.append("env", env);
        if (societe) params.append("societe", societe);

        fetch("/problemes/search?" + params.toString())
            .then((res) => res.json())
            .then((data) => {
                renderProblemes(data.problems, q, env, tool, societe);
                // Remplir les filtres au premier chargement
                if (!document.getElementById("filter-tool").dataset.loaded) {
                    const toolSelect = document.getElementById("filter-tool");
                    data.tools.forEach((t) => {
                        toolSelect.innerHTML += `<option value="${t.id}">${t.name}</option>`;
                    });
                    toolSelect.dataset.loaded = "1";
                }
                if (!document.getElementById("filter-env").dataset.loaded) {
                    const envSelect = document.getElementById("filter-env");
                    data.envs.forEach((e) => {
                        envSelect.innerHTML += `<option value="${e.id}">${e.name}</option>`;
                    });
                    envSelect.dataset.loaded = "1";
                }
                if (!document.getElementById("filter-societe").dataset.loaded) {
                    const societeSelect =
                        document.getElementById("filter-societe");
                    data.societies.forEach((s) => {
                        societeSelect.innerHTML += `<option value="${s.id}">${s.name}</option>`;
                    });
                    societeSelect.dataset.loaded = "1";
                }
            });
    }

    // Initial fetch
    fetchAndRenderProblems();

    // Recherche dynamique
    document
        .getElementById("search-problemes-global")
        .addEventListener("input", fetchAndRenderProblems);
    document
        .getElementById("filter-tool")
        .addEventListener("change", fetchAndRenderProblems);
    document
        .getElementById("filter-env")
        .addEventListener("change", fetchAndRenderProblems);
    document
        .getElementById("filter-societe")
        .addEventListener("change", fetchAndRenderProblems);
}

document.addEventListener("DOMContentLoaded", function () {
    // --- Ajout dynamique du lien de création ---
    const select = document.getElementById("add-model-select");
    const link = document.getElementById("add-model-link");
    afficherRechercheProblemeGlobaleAjax("problemes-list1");

    if (select && link) {
        select.addEventListener("change", function () {
            link.href = `/model/${this.value}/create`;
        });
    }

    // --- Autocomplétion et recherche ---
    const form = document.getElementById("user-search-form");
    const results = document.getElementById("user-search-results");
    const input = document.getElementById("user-search-input");
    const tableSelect = document.getElementById("user-search-table");
    const suggestionBox = document.getElementById("autocomplete-results");
    const resetBtn = document.getElementById("reset-search-input");

    if (input && tableSelect && suggestionBox && resetBtn) {
        input.addEventListener("input", function () {
            resetBtn.classList.toggle("hidden", !this.value.length);
            const q = this.value.trim();
            const table = tableSelect.value;
            if (q.length < 2) {
                suggestionBox.innerHTML = "";
                suggestionBox.classList.add("hidden");
                return;
            }
            fetch(
                `/user-suggestions?q=${encodeURIComponent(
                    q
                )}&table=${encodeURIComponent(table)}`
            )
                .then((res) => res.json())
                .then((data) => {
                    suggestionBox.innerHTML = "";
                    const suggestions = data.slice(0, 5);
                    if (suggestions.length) {
                        suggestions.forEach((suggestion) => {
                            let item = document.createElement("button");
                            item.type = "button";
                            item.className =
                                "text-left text-primary-grey px-4 py-2 hover:bg-blue-accent hover:text-off-white cursor-pointer flex items-center justify-between w-full";
                            // Détermine l'icône selon le modèle
                            let iconHtml = '';
                            const model = suggestion.model || tableSelect.value;
                            if (model === "societe") {
                                iconHtml = '<i class="fa-solid fa-building text-blue-accent ml-2"></i>';
                            } else if (model === "interlocuteur") {
                                iconHtml = '<i class="fa-solid fa-user text-blue-accent ml-2"></i>';
                            } else {
                                iconHtml = '';
                            }
                            item.innerHTML = `<span>${suggestion.label ?? suggestion}</span><span>${iconHtml}</span>`;
                            item.onclick = function () {
                                input.value = suggestion.label ?? suggestion;
                                suggestionBox.innerHTML = "";
                                suggestionBox.classList.add("hidden");

                                // Ajoute ceci pour charger et afficher la card directement
                                const model =
                                    suggestion.model || tableSelect.value;
                                const id = suggestion.id;
                                if (model && id) {
                                    fetch(`/model/${model}/show/${id}`, {
                                        headers: { Accept: "application/json" },
                                    })
                                        .then((res) => res.json())
                                        .then((data) => {
                                            const allowed =
                                                allowedKeys[model] || [];
                                            const entity = { model };
                                            allowed.forEach((key) => {
                                                if (data[key] !== undefined)
                                                    entity[key] = data[key];
                                            });
                                            entity.id = data.id;
                                            if (data.fullname)
                                                entity.fullname = data.fullname;
                                            if (data.active_services)
                                                entity.active_services =
                                                    data.active_services;
                                            if (data.societe)
                                                entity.societe = data.societe;
                                            if (data.main_obj)
                                                entity.main_obj = data.main_obj;
                                            if (data.phone_fix)
                                                entity.phone_fix =
                                                    data.phone_fix;
                                            if (data.phone_mobile)
                                                entity.phone_mobile =
                                                    data.phone_mobile;
                                            if (data.id_teamviewer)
                                                entity.id_teamviewer =
                                                    data.id_teamviewer;
                                            if (data.address)
                                                entity.address = data.address;
                                            addEntityToSelection(entity);
                                        });
                                    if (results) results.innerHTML = "";
                                }
                            };
                            suggestionBox.appendChild(item);
                        });
                        suggestionBox.classList.remove("hidden");
                    } else {
                        suggestionBox.classList.add("hidden");
                    }
                });
        });

        resetBtn.addEventListener("click", function () {
            input.value = "";
            input.focus();
            resetBtn.classList.add("hidden");
            suggestionBox.innerHTML = "";
            suggestionBox.classList.add("hidden");
        });

        document.addEventListener("click", function (e) {
            if (
                !input.contains(e.target) &&
                !suggestionBox.contains(e.target)
            ) {
                suggestionBox.innerHTML = "";
                suggestionBox.classList.add("hidden");
            }
        });
    }

    if (form && results) {
        form.addEventListener("submit", function (e) {
            const inputValue = input.value.trim();
            if (!inputValue.length) {
                e.preventDefault();
                results.innerHTML =
                    '<div class="text-red-500 p-4">Veuillez saisir au moins un caractère pour rechercher.</div>';
                input.focus();
                return;
            }
            e.preventDefault();
            const formData = new FormData(form);
            results.innerHTML =
                '<div class="text-gray-400 p-4">Recherche en cours...</div>';
            fetch("/user-search", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": csrfToken,
                    Accept: "text/html",
                },
                body: formData,
            })
                .then((res) => res.text())
                .then((html) => {
                    results.innerHTML = `
                    <div class="relative w-full">
                        <button id="close-search-results" type="button"
                            class="absolute right-2 text-xl text-red-accent hover:text-red-hover font-bold z-10">&times;</button>
                        <div class="pt-6 flex justify-center items-center">${html}</div>
                    </div>
                `;
                    document.getElementById("close-search-results").onclick =
                        function () {
                            results.innerHTML = "";
                        };
                })
                .catch(() => {
                    results.innerHTML =
                        '<div class="text-red-500 p-4">Erreur lors de la recherche.</div>';
                });
        });
    }

    // --- Sélection d'une entité dans les résultats ---
    document.addEventListener("click", function (e) {
        if (e.target.classList.contains("search-result-link")) {
            e.preventDefault();
            const model = e.target.dataset.model;
            const id = e.target.dataset.id;
            fetch(`/model/${model}/show/${id}`, {
                headers: {
                    Accept: "application/json",
                },
            })
                .then((res) => res.json())
                .then((data) => {
                    const allowed = allowedKeys[model] || [];
                    const entity = { model };
                    allowed.forEach((key) => {
                        if (data[key] !== undefined) entity[key] = data[key];
                    });
                    entity.id = data.id;
                    if (data.fullname) entity.fullname = data.fullname;
                    if (data.active_services)
                        entity.active_services = data.active_services;
                    if (data.societe) entity.societe = data.societe;
                    if (data.main_obj) entity.main_obj = data.main_obj;
                    if (data.phone_fix) entity.phone_fix = data.phone_fix;
                    if (data.phone_mobile)
                        entity.phone_mobile = data.phone_mobile;
                    if (data.id_teamviewer)
                        entity.id_teamviewer = data.id_teamviewer;
                    if (data.address) entity.address = data.address;
                    addEntityToSelection(entity);
                });
            const results = document.getElementById("user-search-results");
            if (results) results.innerHTML = "";
        }
    });

    // Affiche la sélection au chargement si elle existe
    let selectedEntities = JSON.parse(
        localStorage.getItem("selectedEntities") || "[]"
    );
    selectedEntities = normalizeSelectedEntities(selectedEntities);
    showSelectedEntitiesCard(selectedEntities);
});

function highlightText(text, query) {
    if (!query) return text;
    const escaped = query.replace(/[.*+?^${}()|[\]\\]/g, "\\$&");
    return text.replace(
        new RegExp(escaped, "gi"),
        (match) =>
            `<mark style="background:#ffe066;color:#222;">${match}</mark>`
    );
}

function updateCardsVisibility(entities) {
    const cardSection = document.getElementById("selected-entity-card");
    const card1 = document.getElementById("card-1");
    const card2 = document.getElementById("card-2");
    const card3 = document.getElementById("card-3");
    const card4 = document.getElementById("card-4");

    // Cherche la societe et l'interlocuteur peu importe l'ordre
    const hasSociete = entities.some((e) => e.model === "societe");
    const hasInterlocuteur = entities.some((e) => e.model === "interlocuteur");

    if (window.innerWidth < 768) {
        // Affiche la section si au moins une entité sélectionnée
        if (hasSociete || hasInterlocuteur) {
            cardSection.classList.remove("hidden");
            cardSection.classList.add("flex");
        } else {
            cardSection.classList.add("hidden");
            cardSection.classList.remove("flex");
        }

        // Correction ici : card-1 doit s'afficher si ent1 existe (même interlocuteur)
        if (card1) card1.style.display = entities[0] ? "" : "none";
        if (card2) card2.style.display = hasSociete ? "" : "none";
        if (card3) card3.style.display = hasInterlocuteur ? "" : "none";
        if (card4) card4.style.display = hasInterlocuteur ? "" : "none";
    } else {
        // Desktop : tout visible, layout classique
        cardSection.classList.remove("hidden");
        cardSection.classList.remove("flex");
        if (card1) card1.style.display = "";
        if (card2) card2.style.display = "";
        if (card3) card3.style.display = "";
        if (card4) card4.style.display = "";
    }
}

function showSelectedEntitiesCard(entities, { reset = true } = {}) {
    entities = normalizeSelectedEntities(entities);
    updateCardsVisibility(entities);

    if (reset) {
        for (let i = 1; i <= 4; i++) {
            document.getElementById(`card-${i}`).innerHTML = "";
        }
    }

    const ent1 = entities[0]; // societe ou interlocuteur
    const ent2 = entities[1]; // interlocuteur si présent

    // --- CARD 1 ---
    if (ent1) {
        const card1 = document.getElementById("card-1");
        let coordonneesHtml = "";
        (allowedKeys[ent1.model] || []).forEach((key) => {
            if (ent1[key]) {
                coordonneesHtml += `
                    <div class="my-2 pr-2 w-full break-words flex flex-col">
                        <div class="flex items-center justify-between w-full">
                            <p class="font-semibold text-blue-accent mb-0">${window.translatedFields[key]} :</p>
                            <span class="edit-lock-btn-placeholder"></span>
                        </div>
                        <span class="editable-field" data-model="${ent1.model}" data-id="${ent1.id}" data-key="${key}" contenteditable="${window.currentUserRole && ["admin", "superadmin"].includes(window.currentUserRole.toLowerCase()) ? "true" : "false"}" style="border-bottom:1px color-secondary-grey #ccc;min-height:1.5em;margin-top:2px;">${getClickableValue(key, ent1[key])}</span>
                    </div>`;
            }
        });
        const maisonMereHtml =
            ent1.model === "societe" && ent1.main_obj
                ? `<p class="text-xs text-blue-hover mb-2 maison-mere-link" data-main-id="${ent1.main_obj.id}">Filiale de ${ent1.main_obj.name}</p>`
                : "";
        card1.innerHTML = `
            <div class="relative flex flex-col items-center w-full h-full">
                <button type="button" class="absolute top-2 right-2 text-xl text-red-accent hover:text-red-hover font-bold remove-entity-btn" data-idx="0" title="Supprimer">&times;</button>
                <h2 class="font-bold text-blue-accent text-lg uppercase">
                    ${
                        ent1.model === "societe"
                            ? ent1.name
                            : ent1.fullname || ""
                    }
                </h2>
                ${
                    ent1.model === "interlocuteur" && ent1.societe
                        ? `<a href="#" class="text-xs text-blue-accent underline mb-2 voir-societe-link" data-societe-id="${ent1.societe}">Voir la societe</a>`
                        : ""
                }
                ${maisonMereHtml}
                ${coordonneesHtml}
            </div>
        `;
        card1.setAttribute("data-societe", ent1.societe || ent1.name || "");

        // Ajout du select interlocuteur si ent1 est une societe
        if (ent1.model === "societe") {
            fetch(`/societe/${ent1.id}/interlocuteurs`, {
                headers: { Accept: "application/json" },
            })
                .then((res) => res.json())
                .then((interlocutors) => {
                    const selectHtml = `
                        <div style="position:sticky;bottom:0;z-index:2;background:white;">
                            <label for="interlocutor-select-1" class="block mt-2 font-semibold text-blue-accent">Sélectionner un interlocuteur :</label>
                            <select id="interlocutor-select-1" class="mt-1 p-2 border rounded w-full">
                                <option value="">-- Choisir --</option>
                                ${interlocutors
                                    .map(
                                        (i) =>
                                            `<option value="${i.id}">${
                                                i.fullname || i.name
                                            }</option>`
                                    )
                                    .join("")}
                            </select>
                        </div>
                    `;
                    const oldSelect = document.getElementById(
                        "interlocutor-select-1"
                    );
                    if (oldSelect) oldSelect.parentElement.remove();
                    card1.insertAdjacentHTML("beforeend", selectHtml);
                    const select1 = document.getElementById(
                        "interlocutor-select-1"
                    );
                    if (select1) {
                        select1.addEventListener("change", function () {
                            const interlocutorId = this.value;
                            if (interlocutorId) {
                                fetch(
                                    `/model/interlocuteur/show/${interlocutorId}`,
                                    { headers: { Accept: "application/json" } }
                                )
                                    .then((res) => res.json())
                                    .then((data) => {
                                        const allowed =
                                            allowedKeys["interlocuteur"];
                                        const entity = {
                                            model: "interlocuteur",
                                        };
                                        allowed.forEach((key) => {
                                            if (data[key] !== undefined)
                                                entity[key] = data[key];
                                        });
                                        entity.id = data.id;
                                        if (data.fullname)
                                            entity.fullname = data.fullname;
                                        if (data.active_services)
                                            entity.active_services =
                                                data.active_services;
                                        if (data.societe)
                                            entity.societe = data.societe;
                                        addEntityToSelection(entity);
                                    });
                            }
                        });
                    }
                });
        }
    }

    // --- CARD 2 (services societe) ---
    if (ent1 && ent1.active_services) {
        let services = Object.values(ent1.active_services);
        const searchInputId = "services-search-1";
        let servicesHtml = `
            <div class="accordion-services">
                <input type="text" id="${searchInputId}" placeholder="Rechercher un service..." class="mb-4 p-2 border rounded w-full" />
                <div id="services-list-1">
                    ${services
                        .map(
                            (service, idx) => `
                    <div class="mb-1 pr-2 w-full break-words flex flex-col service-item">
                      <button type="button" class="accordion-label flex items-center justify-between w-full px-0 py-1 font-semibold text-blue-accent bg-transparent border-0 focus:outline-none" style="cursor:pointer;">
                        <span>${service.label}</span>
                        <span class="flex items-center gap-2">
                          <span class="accordion-arrow transition-transform duration-200" style="display:inline-block;">
                            <i class="fa-solid fa-chevron-down"></i>
                          </span>
                          <span class="edit-lock-btn-placeholder"></span>
                        </span>
                      </button>
                      <div class="accordion-content" style="display:none;padding-left:0.5em;">
                        <span class="editable-service-field" data-model="${ent1.model}" data-id="${ent1.id}" data-service-key="${service.label}" contenteditable="${window.currentUserRole && ["admin", "superadmin"].includes(window.currentUserRole.toLowerCase()) ? "true" : "false"}" style="margin-top:2px; border-bottom:1px color-secondary-grey #ccc;min-height:1.5em;display:block;">${formatServiceInfo(service.info ?? "Oui")}</span>
                      </div>
                    </div>
                `)
                        .join("")}
                </div>
            </div>
        `;
        setTimeout(() => {
            document
                .querySelectorAll("#services-list-1 .accordion-label")
                .forEach((btn) => {
                    btn.addEventListener("click", function () {
                        const content = this.parentElement.querySelector(
                            ".accordion-content"
                        );
                        const arrow = this.querySelector(".accordion-arrow");
                        if (
                            content.style.display === "none" ||
                            !content.style.display
                        ) {
                            content.style.display = "block";
                            arrow.style.transform = "rotate(180deg)";
                        } else {
                            content.style.display = "none";
                            arrow.style.transform = "rotate(0deg)";
                        }
                    });
                });
            const input = document.getElementById(searchInputId);
            const list = document.getElementById("services-list-1");
            if (input && list) {
                input.addEventListener("input", function () {
                    const q = this.value.toLowerCase();
                    list.querySelectorAll(".service-item").forEach((div) => {
                        const labelElem = div.querySelector(".accordion-label span");
                        const valueElem = div.querySelector(".editable-service-field");
                        const label = labelElem?.textContent || "";
                        const value = valueElem?.textContent || "";
                        const match =
                            label.toLowerCase().includes(q) ||
                            value.toLowerCase().includes(q);
                        div.style.display = match ? "" : "none";
                        if (match && q) {
                            labelElem.innerHTML = highlightText(label, q);
                            valueElem.innerHTML = highlightText(value, q);
                        } else {
                            labelElem.innerHTML = label;
                            valueElem.innerHTML = value;
                        }
                    });
                });
            }
        }, 0);
        document.getElementById("card-2").innerHTML = `
            <div class="flex flex-col w-full h-full">
                <h2 class="font-bold text-blue-accent text-lg mb-2 uppercase text-center">Services activés</h2>
                ${servicesHtml}
            </div>
        `;
    }

    // --- CARD 3 ---
    if (ent2) {
        const card3 = document.getElementById("card-3");
        let coordonneesHtml = "";
        (allowedKeys[ent2.model] || []).forEach((key) => {
            if (ent2[key]) {
                coordonneesHtml += `
                    <div class="my-2 pr-2 w-full break-words flex flex-col">
                        <div class="flex items-center justify-between w-full">
                            <p class="font-semibold text-blue-accent mb-0">${window.translatedFields[key]} :</p>
                            <span class="edit-lock-btn-placeholder"></span>
                        </div>
                        <span class="editable-field" data-model="${ent2.model}" data-id="${ent2.id}" data-key="${key}" contenteditable="${window.currentUserRole && ["admin", "superadmin"].includes(window.currentUserRole.toLowerCase()) ? "true" : "false"}" style="border-bottom:1px color-secondary-grey #ccc;min-height:1.5em;margin-top:2px;">${getClickableValue(key, ent2[key])}</span>
                    </div>`;
            }
        });
        const maisonMereHtml2 =
            ent2.model === "societe" && ent2.main_obj
                ? `<a href="#" class="text-xs text-blue-hover mb-2 maison-mere-link" data-main-id="${ent2.main_obj.id}">Filiale de ${ent2.main_obj.name}</a>`
                : "";
        card3.innerHTML = `
            <div class="relative flex flex-col items-center w-full h-full">
                <button type="button" class="absolute top-2 right-2 text-xl text-red-accent hover:text-red-hover font-bold remove-entity-btn" data-idx="1" title="Supprimer">&times;</button>
                <h2 class="font-bold text-blue-accent text-lg uppercase">
                    ${
                        ent2.model === "societe"
                            ? ent2.name
                            : ent2.fullname || ""
                    }
                </h2>
                ${
                    ent2.model === "interlocuteur" && ent2.societe
                        ? `<a href="#" class="text-xs text-blue-accent underline mb-2 voir-societe-link" data-societe-id="${ent2.societe}">Voir la societe</a>`
                        : ""
                }
                ${maisonMereHtml2}
                ${coordonneesHtml}
            </div>
        `;
        card3.setAttribute("data-societe", ent2.societe || ent2.name || "");

        // Ajout du select interlocuteur si ent2 est une societe
        if (ent2.model === "societe") {
            fetch(`/societe/${ent2.id}/interlocuteurs`, {
                headers: { Accept: "application/json" },
            })
                .then((res) => res.json())
                .then((interlocutors) => {
                    if (interlocutors.length) {
                        const selectHtml = `
                            <label for="interlocutor-select-2" class="block mt-4 font-semibold text-blue-accent">Sélectionner un interlocuteur :</label>
                            <select id="interlocutor-select-2" class="mt-1 p-2 border rounded w-full">
                                <option value="">-- Choisir --</option>
                                ${interlocutors
                                    .map(
                                        (i) =>
                                            `<option value="${i.id}">${
                                                i.fullname || i.name
                                            }</option>`
                                    )
                                    .join("")}
                            </select>
                        `;
                        const oldSelect = document.getElementById(
                            "interlocutor-select-2"
                        );
                        if (oldSelect) oldSelect.parentElement.remove();
                        card3.insertAdjacentHTML("beforeend", selectHtml);
                        const select2 = document.getElementById(
                            "interlocutor-select-2"
                        );
                        if (select2) {
                            select2.addEventListener("change", function () {
                                const interlocutorId = this.value;
                                if (interlocutorId) {
                                    fetch(
                                        `/model/interlocuteur/show/${interlocutorId}`,
                                        {
                                            headers: {
                                                Accept: "application/json",
                                            },
                                        }
                                    )
                                        .then((res) => res.json())
                                        .then((data) => {
                                            const allowed =
                                                allowedKeys["interlocuteur"];
                                            const entity = {
                                                model: "interlocuteur",
                                            };
                                            allowed.forEach((key) => {
                                                if (data[key] !== undefined)
                                                    entity[key] = data[key];
                                            });
                                            entity.id = data.id;
                                            if (data.fullname)
                                                entity.fullname = data.fullname;
                                            if (data.active_services)
                                                entity.active_services =
                                                    data.active_services;
                                            if (data.societe)
                                                entity.societe = data.societe;
                                            addEntityToSelection(entity);
                                        });
                                }
                            });
                        }
                    }
                });
        }
    }

    // --- CARD 4 (services interlocuteur) ---
    if (ent2 && ent2.active_services) {
        let services = Object.values(ent2.active_services);
        const searchInputId = "services-search-2";
        let servicesHtml = `
            <div class="accordion-services">
                <input type="text" id="${searchInputId}" placeholder="Rechercher un service..." class="mb-4 p-2 border rounded w-full" />
                <div id="services-list-2">
                    ${services
                        .map(
                            (service, idx) => `
                    <div class="mb-1 pr-2 w-full break-words flex flex-col service-item">
                      <button type="button" class="accordion-label flex items-center justify-between w-full px-0 py-1 font-semibold text-blue-accent bg-transparent border-0 focus:outline-none" style="cursor:pointer;">
                        <span>${service.label}</span>
                        <span class="flex items-center gap-2">
                          <span class="accordion-arrow transition-transform duration-200" style="display:inline-block;">
                            <i class="fa-solid fa-chevron-down"></i>
                          </span>
                          <span class="edit-lock-btn-placeholder"></span>
                        </span>
                      </button>
                      <div class="accordion-content" style="display:none;padding-left:0.5em;">
                        <span class="editable-service-field" data-model="${ent2.model}" data-id="${ent2.id}" data-service-key="${service.label}" contenteditable="${window.currentUserRole && ["admin", "superadmin"].includes(window.currentUserRole.toLowerCase()) ? "true" : "false"}" style="margin-top:2px; border-bottom:1px color-secondary-grey #ccc;min-height:1.5em;display:block;">${formatServiceInfo(service.info ?? "Oui")}</span>
                      </div>
                    </div>
                `)
                        .join("")}
                </div>
            </div>
        `;
        setTimeout(() => {
            document
                .querySelectorAll("#services-list-2 .accordion-label")
                .forEach((btn) => {
                    btn.addEventListener("click", function () {
                        const content = this.parentElement.querySelector(
                            ".accordion-content"
                        );
                        const arrow = this.querySelector(".accordion-arrow");
                        if (
                            content.style.display === "none" ||
                            !content.style.display
                        ) {
                            content.style.display = "block";
                            arrow.style.transform = "rotate(180deg)";
                        } else {
                            content.style.display = "none";
                            arrow.style.transform = "rotate(0deg)";
                        }
                    });
                });
            const input = document.getElementById(searchInputId);
            const list = document.getElementById("services-list-2");
            if (input && list) {
                input.addEventListener("input", function () {
                    const q = this.value.toLowerCase();
                    list.querySelectorAll(".service-item").forEach((div) => {
                        const labelElem = div.querySelector(".accordion-label span");
                        const valueElem = div.querySelector(".editable-service-field");
                        const label = labelElem?.textContent || "";
                        const value = valueElem?.textContent || "";
                        const match =
                            label.toLowerCase().includes(q) ||
                            value.toLowerCase().includes(q);
                        div.style.display = match ? "" : "none";
                        if (match && q) {
                            labelElem.innerHTML = highlightText(label, q);
                            valueElem.innerHTML = highlightText(value, q);
                        } else {
                            labelElem.innerHTML = label;
                            valueElem.innerHTML = value;
                        }
                    });
                });
            }
        }, 0);
        document.getElementById("card-4").innerHTML = `
            <div class="flex flex-col w-full h-full">
                <h2 class="font-bold text-blue-accent text-lg mb-2 uppercase text-center">Services activés</h2>
                ${servicesHtml}
            </div>
        `;
    }

    // --- Listeners dynamiques ---

    // Lien "Voir la societe" (card-1 et card-3)
    document.querySelectorAll(".voir-societe-link").forEach((link) => {
        link.addEventListener("click", function (e) {
            e.preventDefault();
            const societeId = this.dataset.societeId;
            fetch(`/model/societe/show/${societeId}`, {
                headers: { Accept: "application/json" },
            })
                .then((res) => res.json())
                .then((data) => {
                    const allowed = allowedKeys["societe"] || [];
                    const entity = { model: "societe" };
                    allowed.forEach((key) => {
                        if (data[key] !== undefined) entity[key] = data[key];
                    });
                    entity.id = data.id;
                    if (data.active_services)
                        entity.active_services = data.active_services;
                    if (data.main_obj) entity.main_obj = data.main_obj;
                    addEntityToSelection(entity);
                });
        });
    });

    // Lien "maison mère"
    document.querySelectorAll(".maison-mere-link").forEach((link) => {
        link.addEventListener("click", function (e) {
            e.preventDefault();
            const mainId = this.dataset.mainId;
            fetch(`/model/societe/show/${mainId}`, {
                headers: { Accept: "application/json" },
            })
                .then((res) => res.json())
                .then((data) => {
                    const allowed = allowedKeys["societe"] || [];
                    const entity = { model: "societe" };
                    allowed.forEach((key) => {
                        if (data[key] !== undefined) entity[key] = data[key];
                    });
                    entity.id = data.id;
                    if (data.active_services)
                        entity.active_services = data.active_services;
                    if (data.main_obj) entity.main_obj = data.main_obj;
                    let selectedEntities = JSON.parse(
                        localStorage.getItem("selectedEntities") || "[]"
                    );
                    if (selectedEntities.length === 0) {
                        selectedEntities.push(entity);
                    } else if (selectedEntities.length === 1) {
                        selectedEntities.push(entity);
                    } else {
                        selectedEntities[1] = entity;
                    }
                    localStorage.setItem(
                        "selectedEntities",
                        JSON.stringify(selectedEntities)
                    );
                    showSelectedEntitiesCard(selectedEntities);
                });
        });
    });

    // Edition inline des coordonnées (cards 1 et 3) et services (cards 2 et 4) avec lock/délock et modale
    setTimeout(() => {
        if (
            window.currentUserRole &&
            ["admin", "superadmin"].includes(
                window.currentUserRole.toLowerCase()
            )
        ) {
            document
                .querySelectorAll(
                    "#card-1 .editable-field, #card-3 .editable-field, #card-2 .editable-service-field, #card-4 .editable-service-field"
                )
                .forEach((span) => {
                    span.setAttribute("contenteditable", "false");
                    // Pour les services, le placeholder est dans le bouton accordéon, donc on cherche dans le .service-item parent
                    let placeholder = span.parentElement.querySelector('.edit-lock-btn-placeholder');
                    if (!placeholder && span.closest('.service-item')) {
                        placeholder = span.closest('.service-item').querySelector('.edit-lock-btn-placeholder');
                    }
                    if (
                        (!placeholder && (!span.nextElementSibling || !span.nextElementSibling.classList.contains("edit-lock-btn"))) ||
                        (placeholder && !placeholder.querySelector('.edit-lock-btn'))
                    ) {
                        const btn = document.createElement("button");
                        btn.type = "button";
                        btn.className = "edit-lock-btn ml-2 text-blue-accent";
                        btn.title = "Déverrouiller pour éditer";
                        btn.innerHTML = '<i class="fa-solid fa-lock"></i>';
                        if (placeholder) placeholder.appendChild(btn);
                        else span.after(btn);
                        let originalValue = span.textContent;
                        btn.onclick = async function () {
                            if (span.isContentEditable) {
                                // On veut sauvegarder
                                const confirmed = await showConfirmModal(
                                    "Voulez-vous enregistrer la modification ?"
                                );
                                if (confirmed) {
                                    // Save (reprend la logique existante)
                                    const model = span.dataset.model;
                                    const id = span.dataset.id;
                                    const key =
                                        span.dataset.key ||
                                        "infos_" +
                                            span.dataset.serviceKey
                                                ?.toLowerCase()
                                                .normalize("NFD")
                                                .replace(/[\u0300-\u036f]/g, "")
                                                .replace(/ /g, "_");
                                    const value = span.textContent.trim();
                                    fetch(
                                        `/model/${model}/update-field/${id}`,
                                        {
                                            method: "POST",
                                            headers: {
                                                "Content-Type":
                                                    "application/json",
                                                "X-CSRF-TOKEN": csrfToken,
                                                Accept: "application/json",
                                            },
                                            body: JSON.stringify({
                                                field: key,
                                                value,
                                            }),
                                        }
                                    )
                                        .then((res) => res.json())
                                        .then(() => {
                                            span.style.background = "#678BD8";
                                            setTimeout(
                                                () =>
                                                    (span.style.background =
                                                        ""),
                                                500
                                            );
                                        })
                                        .catch(() => {
                                            span.style.background = "#DB7171";
                                            setTimeout(
                                                () =>
                                                    (span.style.background =
                                                        ""),
                                                1000
                                            );
                                        });
                                    originalValue = value;
                                } else {
                                    // Annule la modif
                                    span.textContent = originalValue;
                                }
                                span.setAttribute("contenteditable", "false");
                                btn.innerHTML =
                                    '<i class="fa-solid fa-lock"></i>';
                                btn.title = "Déverrouiller pour éditer";
                            } else {
                                // On veut déverrouiller
                                originalValue = span.textContent;
                                span.setAttribute("contenteditable", "true");
                                span.focus();
                                btn.innerHTML =
                                    '<i class="fa-solid fa-floppy-disk"></i>';
                                btn.title = "Sauvegarder";
                            }
                        };
                    }
                });
        } else {
            document
                .querySelectorAll(".editable-field, .editable-service-field")
                .forEach((span) => {
                    span.setAttribute("contenteditable", "false");
                });
        }
    }, 0);

    // Gestion de la suppression via la croix
    document.querySelectorAll(".remove-entity-btn").forEach((btn) => {
        btn.addEventListener("click", function () {
            let idx = parseInt(this.dataset.idx, 10);
            if (idx === 1 && selectedEntities.length > 1) {
                selectedEntities = [selectedEntities[0]];
            } else {
                selectedEntities.splice(idx, 1);
            }
            selectedEntities = normalizeSelectedEntities(selectedEntities);
            localStorage.setItem(
                "selectedEntities",
                JSON.stringify(selectedEntities)
            );
            showSelectedEntitiesCard(selectedEntities);
        });
    });

    // Responsive : met à jour la visibilité des cards au resize
    window.addEventListener("resize", () => {
        selectedEntities = normalizeSelectedEntities(selectedEntities);
        updateCardsVisibility(selectedEntities);
    });
}

// Helper pour la modale de confirmation
function showConfirmModal(message) {
    return new Promise((resolve) => {
        let modal = document.getElementById("confirm-modal");
        if (!modal) {
            modal = document.createElement("div");
            modal.id = "confirm-modal";
            modal.style.position = "fixed";
            modal.style.top = "0";
            modal.style.left = "0";
            modal.style.width = "100vw";
            modal.style.height = "100vh";
            modal.style.background = "rgba(0,0,0,0.3)";
            modal.style.display = "flex";
            modal.style.alignItems = "center";
            modal.style.justifyContent = "center";
            modal.style.zIndex = 9999;
            modal.innerHTML = `
                <div style="background:white;padding:2em;border-radius:8px;min-width:300px;box-shadow:0 2px 8px #0002;text-align:center;">
                    <div id="confirm-modal-message" class="mb-4"></div>
                    <button id="confirm-modal-yes" class="bg-blue-accent text-white px-4 py-2 rounded mr-2">Oui</button>
                    <button id="confirm-modal-no" class="bg-gray-300 px-4 py-2 rounded">Non</button>
                </div>
            `;
            document.body.appendChild(modal);
        }
        modal.querySelector("#confirm-modal-message").textContent = message;
        modal.style.display = "flex";
        modal.querySelector("#confirm-modal-yes").onclick = () => {
            modal.style.display = "none";
            resolve(true);
        };
        modal.querySelector("#confirm-modal-no").onclick = () => {
            modal.style.display = "none";
            resolve(false);
        };
    });
}

// Helper pour rendre les champs cliquables (mailto/tel)
function getClickableValue(key, value) {
    if (!value) return "";
    if (key === "email") {
        return `<a href="mailto:${value}" class="text-blue-accent">${value}</a>`;
    }
    if (["phone_fix", "phone_mobile", "boss_phone", "recep_phone"].includes(key)) {
        const tel = value.replace(/[^+\d]/g, "");
        return `<a href="tel:${tel}" class="text-blue-accent">${value}</a>`;
    }
    return value;
}
