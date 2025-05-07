const allowedKeys = {
    interlocuteur: ["lastname", "name", "societe", "email", "phone"],
    société: [
        "name",
        "adress",
        "boss_name",
        "boss_phone",
        "recep_phone",
        "main_name",
    ],
};

const csrfToken =
    document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute("content") || "";

function getEntityFromDataset(dataset) {
    const entity = {};
    for (const key in dataset) entity[key] = dataset[key];
    return entity;
}

function addEntityToSelection(entity) {
    let selectedEntities = JSON.parse(
        localStorage.getItem("selectedEntities") || "[]"
    );

    // Si déjà présente, ne rien faire
    if (
        selectedEntities.some(
            (item) => item.id === entity.id && item.model === entity.model
        )
    ) {
        showSelectedEntitiesCard(selectedEntities);
        return;
    }

    // Si on ajoute un interlocuteur et qu'il y a déjà une société + un interlocuteur
    if (
        entity.model === "interlocuteur" &&
        selectedEntities.length === 2 &&
        selectedEntities[0].model === "société" &&
        selectedEntities[1].model === "interlocuteur"
    ) {
        // Remplace l'interlocuteur (position 1)
        selectedEntities[1] = entity;
    } else if (
        entity.model === "interlocuteur" &&
        selectedEntities.length === 2 &&
        selectedEntities[1].model === "société" &&
        selectedEntities[0].model === "interlocuteur"
    ) {
        // Remplace l'interlocuteur (position 0)
        selectedEntities[0] = entity;
    } else if (
        entity.model === "interlocuteur" &&
        selectedEntities.length === 1 &&
        selectedEntities[0].model === "société"
    ) {
        // Ajoute l'interlocuteur après la société
        selectedEntities.push(entity);
    } else if (
        entity.model === "interlocuteur" &&
        selectedEntities.length === 1 &&
        selectedEntities[0].model === "interlocuteur"
    ) {
        // Remplace l'interlocuteur unique
        selectedEntities[0] = entity;
    } else {
        // Cas standard : si déjà 2 entités, retire la première
        if (selectedEntities.length >= 2) {
            selectedEntities.shift();
        }
        selectedEntities.push(entity);
    }

    localStorage.setItem("selectedEntities", JSON.stringify(selectedEntities));
    showSelectedEntitiesCard(selectedEntities);
}

document.addEventListener("DOMContentLoaded", function () {
    // --- Ajout dynamique du lien de création ---
    const select = document.getElementById("add-model-select");
    const link = document.getElementById("add-model-link");
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
                    addEntityToSelection(entity);
                });
                const results = document.getElementById("user-search-results");
                if (results) results.innerHTML = "";
        }
    });

    // Affiche la sélection au chargement si elle existe
    const selectedEntities = JSON.parse(
        localStorage.getItem("selectedEntities") || "[]"
    );
    showSelectedEntitiesCard(selectedEntities);
});

function showSelectedEntitiesCard(entities) {
    for (let i = 1; i <= 4; i++) {
        document.getElementById(`card-${i}`).innerHTML = "";
    }

    // Affiche la première entité (société ou interlocuteur)
    const ent1 = entities[0];
    if (ent1) {
        let coordonneesHtml = "";
        (allowedKeys[ent1.model] || []).forEach((key) => {
            if (ent1[key]) {
                coordonneesHtml += `
                    <div class="my-4 pr-2 w-full break-words flex flex-col">
                        <p class="font-semibold text-blue-accent">${window.translatedFields[key]} :</p>
                        <span 
                            class="editable-field" 
                            data-model="${ent1.model}" 
                            data-id="${ent1.id}" 
                            data-key="${key}" 
                            contenteditable="true"
                            style="border-bottom:1px color-secondary-grey #ccc;min-height:1.5em">${ent1[key]}</span>
                    </div>`;
            }
        });
        const maisonMereHtml =
            ent1.model === "société" && ent1.main_obj
                ? `<p class="text-xs text-blue-hover mb-2">Filiale de ${ent1.main_obj.name}</p>`
                : "";
        document.getElementById("card-1").innerHTML = `
            <button type="button" class="absolute top-2 right-2 text-xl text-red-accent hover:text-red-hover font-bold remove-entity-btn" data-idx="0" title="Supprimer">&times;</button>
            <div class="flex flex-col items-center w-full h-full">
                <h2 class="font-bold text-blue-accent text-lg uppercase">
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

        // Ajout du select interlocuteur si ent1 est une société
        if (ent1.model === "société") {
            fetch(`/societe/${ent1.id}/interlocuteurs`, {
                headers: { Accept: "application/json" },
            })
                .then((res) => res.json())
                .then((interlocutors) => {
                    const selectHtml = `
                        <label class="block mt-4 font-semibold text-blue-accent">Sélectionner un interlocuteur :</label>
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
                                        addEntityToSelection(entity);
                                    });
                            }
                        });
                });
        }

        // Services activés avec recherche et édition inline
        if (ent1.active_services) {
            let services = Object.values(ent1.active_services);
            const searchInputId = "services-search-1";
            let servicesHtml = `
                <input type="text" id="${searchInputId}" placeholder="Rechercher un service..." 
                    class="mb-4 p-2 border rounded w-full" />
                <div id="services-list-1">
                    ${services
                        .map(
                            (service) => `
                        <div class="mb-2 pr-2 w-full break-words flex flex-col service-item">
                            <p class="font-semibold text-blue-accent">${
                                service.label
                            } :</p>
                            <span 
                                class="editable-service-field"
                                data-model="${ent1.model}"
                                data-id="${ent1.id}"
                                data-service-key="${service.label}"
                                contenteditable="true"
                                style="border-bottom:1px color-secondary-grey #ccc;min-height:1.5em">
                                ${service.info ?? "Oui"}
                            </span>
                        </div>
                    `
                        )
                        .join("")}
                </div>
            `;
            document.getElementById("card-2").innerHTML = `
                <div class="flex flex-col w-full h-full">
                    <h2 class="font-bold text-blue-accent text-lg mb-2 uppercase text-center">Services activés</h2>
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
                                div.style.display = div.textContent
                                    .toLowerCase()
                                    .includes(q)
                                    ? ""
                                    : "none";
                            }
                        );
                    });
                }
            }, 0);
        }
    }

    // Affiche la deuxième entité (société ou interlocuteur)
    const ent2 = entities[1];
    if (ent2) {
        let coordonneesHtml = "";
        (allowedKeys[ent2.model] || []).forEach((key) => {
            if (ent2[key]) {
                coordonneesHtml += `
                    <div class="my-4 pr-2 w-full break-words flex flex-col">
                        <p class="font-semibold text-blue-accent">${window.translatedFields[key]} :</p>
                        <span 
                            class="editable-field" 
                            data-model="${ent2.model}" 
                            data-id="${ent2.id}" 
                            data-key="${key}" 
                            contenteditable="true"
                            style="border-bottom:1px color-secondary-grey #ccc;min-height:1.5em">${ent2[key]}</span>
                    </div>`;
            }
        });

        const maisonMereHtml2 =
            ent2.model === "société" && ent2.main_obj
                ? `<p class="text-xs text-blue-hover mb-2">Filiale de ${ent2.main_obj.name}</p>`
                : "";

        document.getElementById("card-3").innerHTML = `
            <button type="button" class="absolute top-2 right-2 text-xl text-red-accent hover:text-red-hover font-bold remove-entity-btn" data-idx="1" title="Supprimer">&times;</button>
            <div class="flex flex-col items-center w-full h-full">
                <h2 class="font-bold text-blue-accent text-lg uppercase">
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

        // Services activés avec recherche et édition inline pour ent2
        if (ent2.active_services) {
            let services = Object.values(ent2.active_services);
            const searchInputId = "services-search-2";
            let servicesHtml = `
                <input type="text" id="${searchInputId}" placeholder="Rechercher un service..." 
                    class="mb-4 p-2 border rounded w-full" />
                <div id="services-list-2">
                    ${services
                        .map(
                            (service) => `
                        <div class="mb-2 pr-2 w-full break-words flex flex-col service-item">
                            <p class="font-semibold text-blue-accent">${
                                service.label
                            } :</p>
                            <span 
                                class="editable-service-field"
                                data-model="${ent2.model}"
                                data-id="${ent2.id}"
                                data-service-key="${service.label}"
                                contenteditable="true"
                                style="border-bottom:1px color-secondary-grey #ccc;min-height:1.5em">
                                ${service.info ?? "Oui"}
                            </span>
                        </div>
                    `
                        )
                        .join("")}
                </div>
            `;
            document.getElementById("card-4").innerHTML = `
                <div class="flex flex-col w-full h-full">
                    <h2 class="font-bold text-blue-accent text-lg mb-2 uppercase text-center">Services activés</h2>
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
                                div.style.display = div.textContent
                                    .toLowerCase()
                                    .includes(q)
                                    ? ""
                                    : "none";
                            }
                        );
                    });
                }
            }, 0);
        }
    }

    // Ajoute le comportement "Voir la société" sur les deux cards
    document.querySelectorAll('.voir-societe-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const societeId = this.dataset.societeId;
            fetch(`/model/société/show/${societeId}`, {
                headers: { Accept: "application/json" }
            })
            .then(res => res.json())
            .then(data => {
                const allowed = allowedKeys["société"] || [];
                const entity = { model: "société" };
                allowed.forEach((key) => {
                    if (data[key] !== undefined) entity[key] = data[key];
                });
                entity.id = data.id;
                if (data.active_services) entity.active_services = data.active_services;
                if (data.main_obj) entity.main_obj = data.main_obj;
                addEntityToSelection(entity);
            });
        });
    });

    // Edition inline des coordonnées (cards 1 et 3)
    setTimeout(() => {
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
                        body: JSON.stringify({ key, value }),
                    })
                        .then((res) => res.json())
                        .then(() => {
                            this.style.background = "#678BD8";
                            setTimeout(() => (this.style.background = ""), 500);
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
                    // Génère la clé du champ infos_XYZ
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
                            setTimeout(() => (this.style.background = ""), 500);
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
}