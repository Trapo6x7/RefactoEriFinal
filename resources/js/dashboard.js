const allowedKeys = {
    interlocuteur: [
        "lastname",
        "name",
        "email",
        "phone_fix",
        "phone_mobile",
        "id_teamviewer",
    ],
    société: [
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

// Fonction pour garantir l'ordre société/interlocuteur
function normalizeSelectedEntities(entities) {
    let societe = entities.find((e) => e.model === "société");
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
        /\b(Login|Mdp|IP|Compte|Rétention|courriel|script|copy|utilisateur|office)\b\s*:/gi,
        '<span>$&</span>'
    );
    // 3. Transforme les listes commençant par - ou • en <li>
    formatted = formatted.replace(/(?:^|<br>)[\-\•]\s?(.*?)(?=<br>|$)/g, '<li>$1</li>');
    // 4. Si on a des <li>, entoure d'une <ul>
    if (formatted.includes("<li>")) {
        formatted = formatted.replace(/(<li>.*<\/li>)/gs, '<ul>$1</ul>');
    }
    return formatted;
}

function addEntityToSelection(entity) {
    // Ne pas stocker dans le localStorage, garder uniquement en mémoire
    // On veut société uniquement sur card 1/2, interlocuteur uniquement sur card 3/4

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

    if (entity.model === "société") {
        // Remplace la société (card 1/2)
        selectedEntities = selectedEntities.filter(e => e.model !== "société");
        selectedEntities.unshift(entity);
    } else if (entity.model === "interlocuteur") {
        // Remplace l'interlocuteur (card 3/4)
        selectedEntities = selectedEntities.filter(e => e.model !== "interlocuteur");
        // On garde la société si présente en premier
        if (selectedEntities.length && selectedEntities[0].model === "société") {
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
            <input type="text" id="search-problemes-global" placeholder="Rechercher un problème..." 
                class="p-2 border text-sm rounded max-w-xs w-1/2" />
            <select id="filter-tool" class="p-2 text-sm border rounded w-1/5">
                <option value="">Tous les outils</option>
            </select>
            <select id="filter-env" class="p-2 border text-sm rounded w-1/5">
                <option value="">Tous les env...</option>
            </select>
            <select id="filter-societe" class="p-2 border text-sm rounded w-1/5">
                <option value="">Toutes les soc...</option>
            </select>
        </div>
        <div id="problemes-list-inner-global"></div>
    `;

    const renderProblemes = (problemes) => {
        document.getElementById("problemes-list-inner-global").innerHTML = `
            <div class="flex flex-col items-start w-full">
            ${
                problemes.length
                    ? problemes
                          .map(
                              (p, i) =>
                                  `<article class="mb-2 px-8 py-1 bg-off-white rounded text-sm w-full max-w-2xl text-left">
                                    <button 
                                        class="w-full text-left font-semibold text-blue-accent hover:text-blue-hover problem-title-btn flex items-center gap-2"
                                        data-idx="${i}">
                                        <h3 class="text-left">${
                                            p.title || ""
                                        }</h3>
                                    </button>
                                </article>`
                          )
                          .join("")
                    : '<div class="mb-2 px-8 py-1 text-primary-grey font-semibold text-sm text-left">Aucun problème trouvé.</div>'
            }
            </div>
        `;

        // Ajoute l'affichage de la solution dans problemes-list2 au clic
        document
            .getElementById("problemes-list-inner-global")
            .querySelectorAll(".problem-title-btn")
            .forEach((btn) => {
                btn.addEventListener("click", function () {
                    const idx = this.dataset.idx;
                    const problem = problemes[idx];
                    document.getElementById("problemes-list2").innerHTML = `
                        <div class="p-4 bg-white rounded max-w-2xl mx-auto relative">
                            <button type="button" 
                                class="absolute top-2 right-2 text-xl text-red-accent hover:text-red-hover font-bold" 
                                id="close-probleme-details"
                                title="Fermer">&times;</button>
                            <h2 class="font-bold text-blue-accent text-lg mb-2">${
                                problem.title || ""
                            }</h2>
                            <div class="text-primary-grey text-sm">${
                                problem.description || ""
                            }</div>
                        </div>
                    `;
                    document.getElementById("close-probleme-details")?.addEventListener("click", function () {
                        document.getElementById("problemes-list2").innerHTML = "";
                    });
                });
            });
    };

    // Fonction pour charger les problèmes avec filtres
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
                renderProblemes(data.problems);
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
                                "text-left text-primary-grey px-4 py-2 hover:bg-blue-accent hover:text-off-white cursor-pointer";
                            item.textContent = suggestion.label ?? suggestion;
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

function showSelectedEntitiesCard(entities) {
    entities = normalizeSelectedEntities(entities);

    for (let i = 1; i <= 4; i++) {
        document.getElementById(`card-${i}`).innerHTML = "";
    }

    const ent1 = entities[0]; // société
    const ent2 = entities[1]; // interlocuteur

    if (ent1) {
        let coordonneesHtml = "";
        (allowedKeys[ent1.model] || []).forEach((key) => {
            if (ent1[key]) {
                coordonneesHtml += `
                    <div class="my-4 pr-2 w-full break-words flex flex-col">
                        <p class="font-semibold text-blue-accent">${
                            window.translatedFields[key]
                        } :</p>
                        <span 
                            class="editable-field" 
                            data-model="${ent1.model}" 
                            data-id="${ent1.id}" 
                            data-key="${key}" 
                           contenteditable="${
                               window.currentUserRole &&
                               ["admin", "superadmin"].includes(
                                   window.currentUserRole.toLowerCase()
                               )
                                   ? "true"
                                   : "false"
                           }"
                            style="border-bottom:1px color-secondary-grey #ccc;min-height:1.5em">${
                                ent1[key]
                            }</span>
                    </div>`;
            }
        });
        const maisonMereHtml =
            ent1.model === "société" && ent1.main_obj
                ? `<p class="text-xs text-blue-hover mb-2 maison-mere-link" data-main-id="${ent1.main_obj.id}">Filiale de ${ent1.main_obj.name}</p>`
                : "";
        document.getElementById("card-1").innerHTML = `
            <button type="button" class="absolute top-2 right-2 text-xl text-red-accent hover:text-red-hover font-bold remove-entity-btn" data-idx="0" title="Supprimer">&times;</button>
            <div class="flex flex-col items-center w-full h-full">
                <h2 class="font-bold text-blue-accent text-sm uppercase">
                    ${
                        ent1.model === "société"
                            ? ent1.name
                            : ent1.model === "interlocuteur"
                            ? ent1.fullname
                            : ""
                    }
                </h2>   
                ${
                    ent1.model === "interlocuteur" && ent1.societe
                        ? `<a href="#" 
                              class="text-xs text-blue-accent underline mb-2 voir-societe-link" 
                              data-societe-id="${ent1.societe}">
                              Voir la société
                           </a>`
                        : ""
                }
                ${maisonMereHtml}
                ${coordonneesHtml}
            </div>
        `;
        document
            .getElementById("card-1")
            .setAttribute("data-societe", ent1.societe || ent1.name || "");

        // Ajout du select interlocuteur si ent1 est une société
        if (ent1.model === "société") {
            fetch(`/societe/${ent1.id}/interlocuteurs`, {
                headers: { Accept: "application/json" },
            })
                .then((res) => res.json())
                .then((interlocutors) => {
                    const selectHtml = `
                        <label for="interlocutor-select-1" class="block mt-4 font-semibold text-blue-accent">Sélectionner un interlocuteur :</label>
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
                    `;
                    document
                        .getElementById("card-1")
                        .insertAdjacentHTML("beforeend", selectHtml);

                    document
                        .getElementById("interlocutor-select-1")
                        .addEventListener("change", function () {
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
                                        if (data.phone_fix)
                                            entity.phone_fix = data.phone_fix;
                                        if (data.phone_mobile)
                                            entity.phone_mobile =
                                                data.phone_mobile;
                                        if (data.id_teamviewer)
                                            entity.id_teamviewer =
                                                data.id_teamviewer;
                                        addEntityToSelection(entity);
                                    });
                            }
                        });
                });
        }

        // Services activés avec recherche, surbrillance et édition inline
        if (ent1.active_services) {
            let services = Object.values(ent1.active_services);
            const searchInputId = "services-search-1";
            let servicesHtml = `
                <div class="accordion-services">
                    <input type="text" id="${searchInputId}" placeholder="Rechercher un service..." 
                        class="mb-4 p-2 border rounded w-full" />
                    <div id="services-list-1">
                        ${services
                            .map(
                                (service, idx) => `
                                <div class="mb-2 pr-2 w-full break-words flex flex-col service-item">
                                    <button type="button" 
                                        class="font-semibold text-blue-accent text-left accordion-label w-full flex items-center gap-2 py-1"
                                        data-idx="${idx}"
                                        style="background:none;border:none;outline:none;cursor:pointer;">
                                        <p>${service.label}</p>
                                        <span class="accordion-arrow" style="transition:transform 0.2s;">&#x25BE;</span>
                                    </button>
                                    <div class="accordion-content" style="display:none;">
                                        <span 
                                            class="editable-service-field"
                                            data-model="${ent1.model}"
                                            data-id="${ent1.id}"
                                            data-service-key="${service.label}"
                                            contenteditable="${
                                                window.currentUserRole &&
                                                ["admin", "superadmin"].includes(
                                                    window.currentUserRole.toLowerCase()
                                                )
                                                    ? "true"
                                                    : "false"
                                            }"
                                            style="border-bottom:1px color-secondary-grey #ccc;min-height:1.5em;display:block;margin-top:0.5em;">
                                                ${formatServiceInfo(service.info ?? "Oui")}
                                        </span>
                                    </div>
                                </div>
                            `
                            )
                            .join("")}
                    </div>
                </div>
            `;

            setTimeout(() => {
                // Accordion toggle logic
                document
                    .querySelectorAll("#services-list-1 .accordion-label")
                    .forEach((btn) => {
                        btn.addEventListener("click", function () {
                            const content =
                                this.parentElement.querySelector(
                                    ".accordion-content"
                                );
                            const arrow =
                                this.querySelector(".accordion-arrow");
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
            }, 0);
            document.getElementById("card-2").innerHTML = `
                <div class="flex flex-col w-full h-full">
                    <h2 class="font-bold text-blue-accent text-sm mb-2 uppercase text-center">Services activés</h2>
                    ${servicesHtml}
                </div>
            `;
            setTimeout(() => {
                const input = document.getElementById(searchInputId);
                const list = document.getElementById("services-list-1");
                if (input && list) {
                    input.addEventListener("input", function () {
                        const q = this.value.toLowerCase();
                        list.querySelectorAll(".service-item").forEach(
                            (div) => {
                                const labelElem = div.querySelector("p");
                                const valueElem = div.querySelector("span");
                                const label = labelElem?.textContent || "";
                                const value = valueElem?.textContent || "";
                                const match =
                                    label.toLowerCase().includes(q) ||
                                    value.toLowerCase().includes(q);
                                div.style.display = match ? "" : "none";
                                // Surligne le texte recherché
                                if (match && q) {
                                    labelElem.innerHTML = highlightText(
                                        label,
                                        q
                                    );
                                    valueElem.innerHTML = highlightText(
                                        value,
                                        q
                                    );
                                } else {
                                    labelElem.innerHTML = label;
                                    valueElem.innerHTML = value;
                                }
                            }
                        );
                    });
                }
            }, 0);
        }
    }

    if (ent2) {
        let coordonneesHtml = "";
        (allowedKeys[ent2.model] || []).forEach((key) => {
            if (ent2[key]) {
                coordonneesHtml += `
                    <div class="my-4 pr-2 w-full break-words flex flex-col">
                        <p class="font-semibold text-blue-accent">${
                            window.translatedFields[key]
                        } :</p>
                        <span 
                            class="editable-field" 
                            data-model="${ent2.model}" 
                            data-id="${ent2.id}" 
                            data-key="${key}" 
                          contenteditable="${
                              window.currentUserRole &&
                              ["admin", "superadmin"].includes(
                                  window.currentUserRole.toLowerCase()
                              )
                                  ? "true"
                                  : "false"
                          }"
                            style="border-bottom:1px color-secondary-grey #ccc;min-height:1.5em">${
                                ent2[key]
                            }</span>
                    </div>`;
            }
        });

        const maisonMereHtml2 =
            ent2.model === "société" && ent2.main_obj
                ? `<a href="#" class="text-xs text-blue-hover mb-2 maison-mere-link" data-main-id="${ent2.main_obj.id}">Filiale de ${ent2.main_obj.name}</a>`
                : "";

        document.getElementById("card-3").innerHTML = `
            <button type="button" class="absolute top-2 right-2 text-xl text-red-accent hover:text-red-hover font-bold remove-entity-btn" data-idx="1" title="Supprimer">&times;</button>
            <div class="flex flex-col items-center w-full h-full">
                <h2 class="font-bold text-blue-accent text-sm uppercase">
                    ${
                        ent2.model === "société"
                            ? ent2.name
                            : ent2.model === "interlocuteur"
                            ? ent2.fullname
                            : ""
                    }
                </h2>
                ${
                    ent2.model === "interlocuteur" && ent2.societe
                        ? `<a href="#" 
                              class="text-xs text-blue-accent underline mb-2 voir-societe-link" 
                              data-societe-id="${ent2.societe}">
                              Voir la société
                           </a>`
                        : ""
                }
                ${maisonMereHtml2}
                ${coordonneesHtml}
            </div>
        `;
        document
            .getElementById("card-3")
            .setAttribute("data-societe", ent2.societe || ent2.name || "");

        // Ajout du select interlocuteur si ent2 est une société
        if (ent2.model === "société") {
            fetch(`/societe/${ent2.id}/interlocuteurs`, {
                headers: { Accept: "application/json" },
            })
                .then((res) => res.json())
                .then((interlocutors) => {
                    if (interlocutors.length) {
                        const selectHtml = `
                            <label class="block mt-4 font-semibold text-blue-accent">Sélectionner un interlocuteur :</label>
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
                        document
                            .getElementById("card-3")
                            .insertAdjacentHTML("beforeend", selectHtml);

                        document
                            .getElementById("interlocutor-select-2")
                            .addEventListener("change", function () {
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
                });
        }

        // Services activés avec recherche, surbrillance et édition inline pour ent2
        if (ent2.active_services) {
            let services = Object.values(ent2.active_services);
            const searchInputId = "services-search-2";
            let servicesHtml = `
                <div class="accordion-services">
                    <input type="text" id="${searchInputId}" placeholder="Rechercher un service..." 
                        class="mb-4 p-2 border rounded w-full" />
                    <div id="services-list-2">
                        ${services
                            .map(
                                (service, idx) => `
                                <div class="mb-2 pr-2 w-full break-words flex flex-col service-item">
                                <button type="button" 
                                class="font-semibold text-blue-accent text-left accordion-label w-full flex items-center gap-2 py-1"
                                data-idx="${idx}"
                                style="background:none;border:none;outline:none;cursor:pointer;">
                                <p>${
                                    service.label
                                }  </p>
                                <span class="accordion-arrow" style="transition:transform 0.2s;">&#x25BE;</span>
                              
                                </button>
                                <div class="accordion-content" style="display:none;">
                                        <span 
                                            class="editable-service-field"
                                            data-model="${ent2.model}"
                                            data-id="${ent2.id}"
                                            data-service-key="${service.label}"
                                            contenteditable="${
                                                window.currentUserRole &&
                                                [
                                                    "admin",
                                                    "superadmin",
                                                ].includes(
                                                    window.currentUserRole.toLowerCase()
                                                )
                                                    ? "true"
                                                    : "false"
                                            }"
                                            style="border-bottom:1px color-secondary-grey #ccc;min-height:1.5em;display:block;margin-top:0.5em;">
                                                ${formatServiceInfo(service.info ?? "Oui")}                                
                                        </span>
                                    </div>
                                </div>
                            `
                            )
                            .join("")}
                    </div>
                </div>
            `;

            setTimeout(() => {
                // Accordion toggle logic
                document
                    .querySelectorAll("#services-list-2 .accordion-label")
                    .forEach((btn) => {
                        btn.addEventListener("click", function () {
                            const content =
                                this.parentElement.querySelector(
                                    ".accordion-content"
                                );
                            const arrow =
                                this.querySelector(".accordion-arrow");
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
            }, 0);
            document.getElementById("card-4").innerHTML = `
                <div class="flex flex-col w-full h-full">
                    <h2 class="font-bold text-blue-accent text-sm mb-2 uppercase text-center">Services activés</h2>
                    ${servicesHtml}
                </div>
            `;
            setTimeout(() => {
                const input = document.getElementById(searchInputId);
                const list = document.getElementById("services-list-2");
                if (input && list) {
                    input.addEventListener("input", function () {
                        const q = this.value.toLowerCase();
                        list.querySelectorAll(".service-item").forEach(
                            (div) => {
                                const labelElem = div.querySelector("p");
                                const valueElem = div.querySelector("span");
                                const label = labelElem?.textContent || "";
                                const value = valueElem?.textContent || "";
                                const match =
                                    label.toLowerCase().includes(q) ||
                                    value.toLowerCase().includes(q);
                                div.style.display = match ? "" : "none";
                                // Surligne le texte recherché
                                if (match && q) {
                                    labelElem.innerHTML = highlightText(
                                        label,
                                        q
                                    );
                                    valueElem.innerHTML = highlightText(
                                        value,
                                        q
                                    );
                                } else {
                                    labelElem.innerHTML = label;
                                    valueElem.innerHTML = value;
                                }
                            }
                        );
                    });
                }
            }, 0);
        }
    }

    document.querySelectorAll(".maison-mere-link").forEach((link) => {
        link.addEventListener("click", function (e) {
            e.preventDefault();
            const mainId = this.dataset.mainId;
            fetch(`/model/société/show/${mainId}`, {
                headers: { Accept: "application/json" },
            })
                .then((res) => res.json())
                .then((data) => {
                    const allowed = allowedKeys["société"] || [];
                    const entity = { model: "société" };
                    allowed.forEach((key) => {
                        if (data[key] !== undefined) entity[key] = data[key];
                    });
                    entity.id = data.id;
                    if (data.active_services)
                        entity.active_services = data.active_services;
                    if (data.main_obj) entity.main_obj = data.main_obj;

                    // Ajoute la maison mère en deuxième entité (card-3)
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

    // Edition inline des coordonnées (cards 1 et 3)
    setTimeout(() => {
        if (
            window.currentUserRole &&
            ["admin", "superadmin"].includes(
                window.currentUserRole.toLowerCase()
            )
        ) {
            document
                .querySelectorAll(
                    "#card-1 .editable-field, #card-3 .editable-field"
                )
                .forEach((span) => {
                    span.addEventListener("blur", function () {
                        const model = this.dataset.model;
                        const id = this.dataset.id;
                        const key = this.dataset.key;
                        const value = this.textContent.trim();

                        fetch(`/model/${model}/update-field/${id}`, {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": csrfToken,
                                Accept: "application/json",
                            },
                            body: JSON.stringify({ field: key, value }),
                        })
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
                });

            // Edition inline des services activés (cards 2 et 4)
            document
                .querySelectorAll(
                    "#card-2 .editable-service-field, #card-4 .editable-service-field"
                )
                .forEach((span) => {
                    span.addEventListener("blur", function () {
                        const model = this.dataset.model;
                        const id = this.dataset.id;
                        const serviceLabel = this.dataset.serviceKey;
                        const value = this.textContent.trim();
                        const key =
                            "infos_" +
                            serviceLabel
                                .toLowerCase()
                                .normalize("NFD")
                                .replace(/[\u0300-\u036f]/g, "")
                                .replace(/ /g, "_");

                        fetch(`/model/${model}/update-field/${id}`, {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": csrfToken,
                                Accept: "application/json",
                            },
                            body: JSON.stringify({ key, value }),
                        })
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
                });
        } else {
            // Si pas admin, rendre les champs non éditables
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
            let selectedEntities = JSON.parse(
                localStorage.getItem("selectedEntities") || "[]"
            );
            selectedEntities.splice(idx, 1);
            localStorage.setItem(
                "selectedEntities",
                JSON.stringify(selectedEntities)
            );
            showSelectedEntitiesCard(selectedEntities);
        });
    });

    // if (ent1 && ent1.model === "société") {
    //     afficherRechercheProblemeGlobaleAjax("problemes-list1");
    // } else {
    //     document.getElementById("problemes-list1").innerHTML = "";
    // }
}