const allowedKeys = {
    interlocuteur: ["lastname", "name", "society_id", "email", "phone"],
    société: [
        "name",
        "adress",
        "boss_name",
        "boss_phone",
        "recep_phone",
        "main_name",
    ],
};

// Récupère le token CSRF depuis le meta tag
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
    if (
        !selectedEntities.some(
            (item) => item.id === entity.id && item.model === entity.model
        )
    ) {
        selectedEntities.push(entity);
        localStorage.setItem(
            "selectedEntities",
            JSON.stringify(selectedEntities)
        );
    }
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
                                "text-left px-4 py-2 hover:bg-blue-accent hover:text-off-white cursor-pointer";
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
                            class="absolute right-36 top-2 text-xl text-red-accent hover:text-red-hover font-bold z-10">&times;</button>
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
            const entity = getEntityFromDataset(e.target.dataset);
            addEntityToSelection(entity);
        }
    });

    // Affiche la sélection au chargement si elle existe
    const selectedEntities = JSON.parse(
        localStorage.getItem("selectedEntities") || "[]"
    );
    showSelectedEntitiesCard(selectedEntities);
});

function showSelectedEntitiesCard(entities) {
    const card = document.getElementById("selected-entity-card");

    if (!card) return;
    if (!entities.length) {
        card.classList.add("hidden");
        card.innerHTML = "";
        return;
    }
    card.classList.remove("hidden");
    const fields = window.translatedFields || {};
    let html = '<div class="flex flex-wrap gap-4 justify-center items-center">';
    entities.forEach((entity, idx) => {
        html += `
        <div class="relative bg-white rounded-lg shadow-md p-6 min-w-[260px] max-w-xs mb-4 h-80 flex flex-col" data-entity-idx="${idx}">
            <button type="button" class="absolute top-2 right-2 text-xl text-red-accent hover:text-red-hover font-bold remove-entity-btn" data-idx="${idx}" title="Supprimer">&times;</button>
<div class="font-bold text-blue-accent text-lg mb-2">${
            entity.model === "société"
                ? entity.name || ""
                : entity.fullname || ""
        }</div>
     ${
         entity.model === "interlocuteur" && entity.society_id
             ? `<button type="button" class="text-xs text-blue-accent underline open-society-btn text-left" data-society-id="${entity.society_id}">
                        Voir la société liée
                   </button>`
             : ""
     }
            <div class="text-xs uppercase text-primary-grey mb-2">${
                entity.type || entity.model || ""
            }</div>
            ${
                entity.main_name
                    ? `<div class="mb-2 text-xs text-blue-600 font-semibold">Filliale de ${entity.main_name}</div>`
                    : ""
            }
            <div class="interlocutor-select-placeholder"></div>
            <div class="flex-1 min-h-0">
                <ul class="mb-2 overflow-y-auto max-h-32 pr-2 h-full">
        `;
        (allowedKeys[entity.model] || []).forEach((key) => {
            if (
                entity[key] !== undefined &&
                entity[key] !== "" &&
                entity[key] !== null
            ) {
                html += `<li class="mb-1"><span class="font-semibold">${
                    fields[key] || key
                } :</span> ${entity[key]}</li>`;
            }
        });
        html += `
                </ul>
            </div>
            <div class="text-xs text-gray-400 mt-2">ID : ${
                entity.id || ""
            }</div>
        </div>
        `;
    });
    html += "</div>";
    card.innerHTML = html;

    // Ajoute le select interlocuteur dans la card société
    entities.forEach((entity, idx) => {
        if (entity.model === "société") {
            fetch(`/societe/${entity.id}/interlocuteurs`)
                .then((res) => res.json())
                .then((interlocutors) => {
                    const cardDiv = card.querySelector(
                        `[data-entity-idx="${idx}"] .interlocutor-select-placeholder`
                    );
                    if (!cardDiv) return;
                    if (!interlocutors.length) {
                        cardDiv.innerHTML =
                            '<div class="text-gray-500">Aucun interlocuteur pour cette société.</div>';
                        return;
                    }
                    let selectHtml = `<label class="block mb-1 text-blue-accent font-semibold">Interlocuteur :</label>
                    <select class="border rounded px-4 py-2 w-full mb-2 interlocutor-select">
                        <option value="">Sélectionner...</option>`;
                    interlocutors.forEach((i) => {
                        selectHtml += `<option value="${i.id}">${
                            i.fullname || i.name
                        }</option>`;
                    });
                    selectHtml += `</select>`;
                    cardDiv.innerHTML = selectHtml;

                    // Ajoute l'interlocuteur à la sélection lors du choix
                    cardDiv
                        .querySelector(".interlocutor-select")
                        .addEventListener("change", function () {
                            const selectedId = this.value;
                            if (!selectedId) return;
                            const link = document.querySelector(
                                `a[data-model="interlocuteur"][data-id="${selectedId}"]`
                            );
                            if (link) {
                                link.click();
                            } else {
                                fetch(
                                    `/model/interlocuteur/show/${selectedId}`,
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
                                        entity.id = data.id;
                                        entity.fullname = data.fullname; // <-- ajoute cette ligne
                                        allowed.forEach((key) => {
                                            if (data[key] !== undefined)
                                                entity[key] = data[key];
                                        });
                                        addEntityToSelection(entity);
                                    });
                            }
                        });
                });
        }
    });

    // Ajoute l'écouteur pour chaque croix
    card.querySelectorAll(".remove-entity-btn").forEach((btn) => {
        btn.addEventListener("click", function (e) {
            let selectedEntities = JSON.parse(
                localStorage.getItem("selectedEntities") || "[]"
            );
            selectedEntities.splice(Number(this.dataset.idx), 1);
            localStorage.setItem(
                "selectedEntities",
                JSON.stringify(selectedEntities)
            );
            showSelectedEntitiesCard(selectedEntities);
        });
    });

    // Ajoute l'écouteur pour chaque bouton "Voir la société liée"
    card.querySelectorAll(".open-society-btn").forEach((btn) => {
        btn.addEventListener("click", function () {
            const societyId = this.dataset.societyId;
            let selectedEntities = JSON.parse(
                localStorage.getItem("selectedEntities") || "[]"
            );
            if (
                selectedEntities.some(
                    (item) => item.id == societyId && item.model === "société"
                )
            ) {
                return;
            }
            fetch(`/model/société/show/${societyId}`, {
                headers: {
                    Accept: "application/json",
                },
            })
                .then((res) => res.json())
                .then((data) => {
                    const allowed = allowedKeys["société"];
                    const entity = { model: "société" };
                    allowed.forEach((key) => {
                        if (data[key] !== undefined) entity[key] = data[key];
                    });
                    entity.id = data.id;
                    addEntityToSelection(entity);
                });
        });
    });
}
