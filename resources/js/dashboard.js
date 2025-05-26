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

function onCkeditor5Ready(callback) {
    if (window.CKEDITOR) {
        callback();
    } else {
        const interval = setInterval(() => {
            if (window.CKEDITOR) {
                clearInterval(interval);
                callback();
            }
        }, 100);
    }
}

function autoLink(text) {
    // Remplace les URLs par des liens cliquables avec style
    return text.replace(/((https?:\/\/|www\.)[^\s<]+)/gi, function (url) {
        let href = url;
        if (!href.match(/^https?:\/\//)) {
            href = "http://" + href;
        }
        return `<a href="${href}" target="_blank" rel="noopener noreferrer" class="underline text-blue-accent cursor-pointer">${url}</a>`;
    });
}

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
    // Si aucun des deux, mais il y a une entité (ex: interlocuteur seul)
    if (!societe && !interlocuteur && entities.length) result.push(entities[0]);
    return result;
}

/**
 * Active la navigation clavier sur une liste d'autocomplétion.
 * @param {HTMLInputElement} input - L'input de recherche.
 * @param {HTMLElement} suggestionBox - Le conteneur des suggestions (doit contenir des <button>).
 */
function enableAutocompleteKeyboardNavigation(input, suggestionBox) {
    let selectedIndex = -1;

    input.addEventListener("keydown", function (e) {
        const items = suggestionBox.querySelectorAll("button");
        if (!items.length || suggestionBox.classList.contains("hidden")) return;

        if (e.key === "ArrowDown") {
            e.preventDefault();
            selectedIndex = (selectedIndex + 1) % items.length;
            items.forEach((item, idx) => {
                item.classList.toggle("bg-blue-accent", idx === selectedIndex);
                item.classList.toggle("text-off-white", idx === selectedIndex);
            });
            items[selectedIndex].scrollIntoView({ block: "nearest" });
        } else if (e.key === "ArrowUp") {
            e.preventDefault();
            selectedIndex = (selectedIndex - 1 + items.length) % items.length;
            items.forEach((item, idx) => {
                item.classList.toggle("bg-blue-accent", idx === selectedIndex);
                item.classList.toggle("text-off-white", idx === selectedIndex);
            });
            items[selectedIndex].scrollIntoView({ block: "nearest" });
        } else if (e.key === "Enter") {
            if (selectedIndex >= 0 && selectedIndex < items.length) {
                e.preventDefault();
                items[selectedIndex].click();
            }
        } else if (e.key === "Escape") {
            selectedIndex = -1;
            items.forEach((item) => {
                item.classList.remove("bg-blue-accent", "text-off-white");
                suggestionBox.classList.add("hidden");
            });
        }
    });

    // Remet à zéro l'index à chaque nouvelle recherche
    input.addEventListener("input", function () {
        selectedIndex = -1;
    });
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
        <div class="flex flex-col 2xl:flex-row justify-center gap-2 mb-4 px-4 w-full relative">
            <div class="lg:flex-row lg:flex">
            <select id="all-problems-select" class="lg:w-1/3 appearance-none border-2 border-blue-accent rounded-lg px-4 py-2 bg-white text-blue-accent focus:outline-none focus:ring-2 focus:ring-blue-accent transition w-full 2xl:w-auto 2xl:ml-2">
                <option value="">Tous les problèmes...</option>
            </select>
            <div class="relative w-full lg:w-1/3 2xl:w-auto 2xl:flex-1">
                <input type="text" id="search-problemes-global"
                    placeholder="Rechercher un probleme..."
                    class="appearance-none border-2 border-blue-accent rounded-lg px-4 py-2 bg-white text-blue-accent focus:outline-none focus:ring-2 focus:ring-blue-accent transition w-full" />
                <button type="button" id="reset-search-input2"
                    class="absolute right-2 top-1/2 -translate-y-1/2 text-red-accent hover:text-red-accent text-3xl hidden z-10"
                    aria-label="Effacer">
                    &times;
                </button>
            </div>
            </div>
            <div class="flex">
            <select id="filter-tool" class="appearance-none border-2 border-blue-accent rounded-lg py-2 w-full bg-white text-blue-accent focus:outline-none focus:ring-2 focus:ring-blue-accent transition text-lg">
                <option value="">Outils</option>
            </select>
            <select id="filter-env" class="appearance-none border-2 border-blue-accent rounded-lg py-2 w-full bg-white text-blue-accent focus:outline-none focus:ring-2 focus:ring-blue-accent transition text-lg">
                <option value="">Env</option>
            </select>
            <select id="filter-societe" class="appearance-none border-2 border-blue-accent rounded-lg py-2 w-full bg-white text-blue-accent focus:outline-none focus:ring-2 focus:ring-blue-accent transition text-lg">
                <option value="">Société</option>
            </select>
            </div>
        </div>
        <div id="problemes-list-inner-global" class="w-full px-8"></div>
    `;

    const input2 = document.getElementById("search-problemes-global");
    const resetBtn = document.getElementById("reset-search-input2");

    if (input2 && resetBtn) {
        input2.addEventListener("input", function () {
            resetBtn.classList.remove("hidden", !this.value.length);
            resetBtn.classList.add("flex", !this.value.length);
        });
        resetBtn.addEventListener("click", function () {
            input2.value = "";
            input2.focus();
            resetBtn.classList.add("hidden");
            resetBtn.classList.remove("flex");
            // Optionnel : relancer la recherche ou vider les résultats
            fetchAndRenderProblems();
        });
    }

    const allProblemsSelect = document.getElementById("all-problems-select");
    fetch("/problemes/search")
        .then((res) => res.json())
        .then((data) => {
            const problems = data.problems || [];
            problems.forEach((p) => {
                const opt = document.createElement("option");
                opt.value = p.id;
                opt.textContent = p.title || "(Sans titre)";
                allProblemsSelect.appendChild(opt);
            });
        });
    allProblemsSelect.addEventListener("change", function () {
        const problemId = this.value;
        if (!problemId) return;
        fetch(`/model/probleme/show/${problemId}`, {
            headers: { Accept: "application/json" },
        })
            .then((res) => res.json())
            .then((problem) => {
                const solutionContainer =
                    document.getElementById("problemes-list2");
                if (solutionContainer) {
                    solutionContainer.innerHTML = `
    <div class="bg-white text-lg rounded p-4">
        <div id="pbTitle" class="flex items-center justify-between mb-2">
            <h2 class="font-bold text-blue-accent mb-2 uppercase">${
                problem.title || ""
            }</h2>
            <button id="edit-description-btn" class="ml-2 px-2 py-1 text-blue-accent rounded hover:text-blue-hover" title="Éditer la description">
                <i class="fa-solid fa-pen"></i>
            </button>
        </div>
        <div id="problem-description" class="text-primary-grey" style="min-height:2em;">
            ${
                problem.description
                    ? autoLink(problem.description)
                    : "<em>Aucune solution enregistrée.</em>"
            }
        </div>
    </div>
`;
                    const editBtn = document.getElementById(
                        "edit-description-btn"
                    );
                    const descDiv = document.getElementById(
                        "problem-description"
                    );
                    if (editBtn && descDiv) {
                        editBtn.onclick = function () {
                            // Crée la modale si besoin
                            let modal =
                                document.getElementById("ckeditor-modal");
                            if (!modal) {
                                modal = document.createElement("div");
                                modal.id = "ckeditor-modal";
                                modal.className =
                                    "fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40";
                                modal.innerHTML = `
                <div class="bg-white rounded-lg p-6 max-w-xl w-full flex flex-col items-center">
                    <textarea id="ckeditor-area" style="width:100%;min-height:20%;"></textarea>
                    <div class="flex gap-2 mt-4">
                        <button id="ckeditor-save" class="px-4 py-2 bg-blue-accent text-white rounded hover:bg-blue-hover">Sauvegarder</button>
                        <button id="ckeditor-cancel" class="px-4 py-2 bg-secondary-grey text-primary-grey rounded hover:bg-red-accent hover:text-white">Annuler</button>
                    </div>
                </div>
            `;
                                document.body.appendChild(modal);
                            }
                            modal.style.display = "flex";
                            document.getElementById("ckeditor-area").value =
                                descDiv.innerHTML;

                            // Charge CKEditor 5 si besoin
                            if (window.CKEDITOR5_INSTANCE) {
                                window.CKEDITOR5_INSTANCE.destroy();
                            }
                            ClassicEditor.create(
                                document.getElementById("ckeditor-area"),
                                {
                                    toolbar: [
                                        "bold",
                                        "italic",
                                        "link",
                                        "bulletedList",
                                        "numberedList",
                                        "undo",
                                        "redo",
                                    ],
                                }
                            )
                                .then((editor) => {
                                    window.CKEDITOR5_INSTANCE = editor;
                                    editor.setData(descDiv.innerHTML);
                                })
                                .catch((error) => {
                                    console.error(error);
                                });

                            document.getElementById("ckeditor-save").onclick =
                                function () {
                                    if (window.CKEDITOR5_INSTANCE) {
                                        const value =
                                            window.CKEDITOR5_INSTANCE.getData();
                                        fetch(
                                            `/problemes/update-description/${problem.id}`,
                                            {
                                                method: "POST",
                                                headers: {
                                                    "Content-Type":
                                                        "application/json",
                                                    "X-CSRF-TOKEN": csrfToken,
                                                    Accept: "application/json",
                                                },
                                                body: JSON.stringify({
                                                    description: value,
                                                }),
                                            }
                                        )
                                            .then((res) => res.json())
                                            .then(() => {
                                                descDiv.innerHTML = value;
                                                descDiv.innerHTML = autoLink(
                                                    descDiv.innerHTML
                                                );
                                                modal.style.display = "none";
                                                window.CKEDITOR5_INSTANCE.destroy();
                                                window.CKEDITOR5_INSTANCE =
                                                    null;
                                            })
                                            .catch(() => {
                                                modal.style.display = "none";
                                                window.CKEDITOR5_INSTANCE.destroy();
                                                window.CKEDITOR5_INSTANCE =
                                                    null;
                                            });
                                    }
                                };
                            document.getElementById("ckeditor-cancel").onclick =
                                function () {
                                    modal.style.display = "none";
                                    if (window.CKEDITOR5_INSTANCE) {
                                        window.CKEDITOR5_INSTANCE.destroy();
                                        window.CKEDITOR5_INSTANCE = null;
                                    }
                                };
                        };
                    }
                }
            });
    });
    function renderProblemes(
        problemes,
        query = "",
        env = "",
        tool = "",
        societe = ""
    ) {
        const container = document.getElementById(
            "problemes-list-inner-global"
        );
        if (!query && !env && !tool && !societe) {
            container.innerHTML = `
            <div class="mb-2 px-8 py-1 text-primary-grey font-semibold text-lg text-left">
                Commencer a rechercher...
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
                                        <h3 class="text-left uppercase">${
                                            p.title || ""
                                        }</h3>
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
                            <div id="pbTitle" class="flex items-center justify-between mb-2">
                                <h2 class="font-bold text-blue-accent mb-2 uppercase">${
                                    problem.title || ""
                                }</h2>
                                <span class="edit-lock-btn-placeholder"></span>
                            </div>
                            <input 
                                type="text" 
                                id="search-problemes-values" 
                                placeholder="Rechercher..." 
                                class="appearance-none border-2 border-blue-accent rounded-lg px-4 py-2 w-full bg-white text-blue-accent focus:outline-none focus:ring-2 focus:ring-blue-accent transition"
                            />
                            <div 
                                class="text-primary-grey editable-problem-solution"
                                data-problem-id="${problem.id || ""}"
                                contenteditable="false"
                                style="min-height:2em;"
                            >${
                                problem.description
                                    ? problem.description
                                    : "<em>Aucune solution enregistrée.</em>"
                            }</div>
                        </div>
                    `;

                    const valueSearchInput = document.getElementById(
                        "search-problemes-values"
                    );
                    const descDiv = document.querySelector(
                        "#problemes-list2 .editable-problem-solution"
                    );

                    if (valueSearchInput && descDiv) {
                        valueSearchInput.addEventListener("input", function () {
                            const q = this.value.trim().toLowerCase();
                            const original =
                                descDiv.getAttribute("data-original") ||
                                descDiv.innerHTML;

                            // Toujours afficher la description
                            descDiv.parentElement.style.display = "";

                            // Stocke l'original si besoin
                            if (!descDiv.getAttribute("data-original")) {
                                descDiv.setAttribute(
                                    "data-original",
                                    descDiv.innerHTML
                                );
                            }

                            // Highlight uniquement si trouvé, sinon affiche sans highlight
                            const plainText = descDiv.textContent.toLowerCase();
                            if (q && plainText.includes(q)) {
                                descDiv.innerHTML = highlightText(original, q);
                            } else {
                                descDiv.innerHTML = original;
                            }
                        });
                    }

                    // Ajout du formulaire d'upload sous la description
                    if (descDiv) {
                        descDiv.insertAdjacentHTML(
                            "afterend",
                            `
                            <div id="file-upload-section" class="mt-1 flex flex-col w-full items-center justify-between">
                                <form id="upload-form" enctype="multipart/form-data" class="flex flex-col md:flex-row items-center gap-2 w-full">
                                    <label class="block font-semibold mb-0 md:mb-0 md:w-auto w-full text-left">Ajouter un fichier :</label>
                                    <input 
                                        type="file" 
                                        name="file" 
                                        id="file-input" 
                                        class="rounded-lg px-4 py-2 mb-0 md:w-auto w-full" 
                                    />
                                    <button 
                                        type="submit" 
                                        class="px-3 py-1 bg-blue-accent text-white rounded hover:bg-blue-hover md:w-auto w-full"
                                    >Uploader</button>
                                </form>
                                <div id="upload-result" class="mt-2 text-blue-accent"></div>
                            </div>
                            `
                        );

                        const h2 = solutionContainer.querySelector("#pbTitle");

                        // Affiche le bouton "Voir les images" seulement s'il y a des images
                        fetch(
                            `/model/probleme/files/${descDiv.dataset.problemId}`
                        )
                            .then((res) => res.json())
                            .then((data) => {
                                if (data.files && data.files.length) {
                                    h2.insertAdjacentHTML(
                                        "afterend",
                                        `
                            <button id="show-images-btn" class="mb-2 px-3 py-1 bg-blue-accent text-white rounded hover:bg-blue-hover">Voir les images</button>
                            <div id="images-modal" class="w-full fixed inset-0 z-50  flex items-center justify-center bg-black bg-opacity-40 ml-auto mr-auto">                            
                                <div class="bg-white w-1/2 rounded-lg p-6 max-w-2xl flex flex-col items-center relative">
                                    <button id="close-images-modal" class="absolute top-4 right-4 text-red-accent text-3xl">&times;</button>
                                    <div id="images-carousel" class="relative w-full flex flex-col items-center">
                                        <button id="carousel-prev" class="absolute left-0 top-1/2 -translate-y-1/2 bg-white rounded-full shadow px-2 py-1 z-10">&lt;</button>
                                        <img id="carousel-image" src="" alt="image" class="max-h-60 rounded shadow mx-auto" style="display:none;" />
                                        <button id="carousel-next" class="absolute right-0 top-1/2 -translate-y-1/2 bg-white rounded-full shadow px-2 py-1 z-10">&gt;</button>
                                        <div id="carousel-indicator" class="mt-2 text-sm text-gray-500"></div>
                                    </div>
                                </div>
                            </div>
                            `
                                    );

                                    // Carrousel JS
                                    const showImagesBtn =
                                        document.getElementById(
                                            "show-images-btn"
                                        );
                                    const imagesModal =
                                        document.getElementById("images-modal");
                                    const closeImagesModal =
                                        document.getElementById(
                                            "close-images-modal"
                                        );

                                    if (
                                        showImagesBtn &&
                                        imagesModal &&
                                        closeImagesModal
                                    ) {
                                        showImagesBtn.onclick = function () {
                                            imagesModal.classList.remove(
                                                "hidden"
                                            );
                                            imagesModal.classList.add("flex");

                                            const images = data.files || [];
                                            const carouselImg =
                                                document.getElementById(
                                                    "carousel-image"
                                                );
                                            const prevBtn =
                                                document.getElementById(
                                                    "carousel-prev"
                                                );
                                            const nextBtn =
                                                document.getElementById(
                                                    "carousel-next"
                                                );
                                            const indicator =
                                                document.getElementById(
                                                    "carousel-indicator"
                                                );
                                            let idx = 0;

                                            function showImage(i) {
                                                if (!images.length) {
                                                    carouselImg.style.display =
                                                        "none";
                                                    indicator.textContent =
                                                        "Aucun fichier trouvé.";
                                                    prevBtn.style.display =
                                                        "none";
                                                    nextBtn.style.display =
                                                        "none";
                                                    return;
                                                }
                                                const url = images[i];
                                                const ext = url
                                                    .split(".")
                                                    .pop()
                                                    .toLowerCase();
                                                const isImage = [
                                                    "jpg",
                                                    "jpeg",
                                                    "png",
                                                    "gif",
                                                    "webp",
                                                    "bmp",
                                                ].includes(ext);
                                                // Supprime tout lien précédent
                                                carouselImg.parentElement
                                                    .querySelector(".file-link")
                                                    ?.remove();
                                                if (isImage) {
                                                    carouselImg.src = url;
                                                    carouselImg.style.display =
                                                        "block";
                                                } else {
                                                    carouselImg.style.display =
                                                        "none";
                                                    let fileLink =
                                                        document.createElement(
                                                            "a"
                                                        );
                                                    fileLink.className =
                                                        "file-link text-blue-accent underline mt-4";
                                                    fileLink.href = url;
                                                    fileLink.target = "_blank";
                                                    fileLink.innerHTML = `<i class="fa-solid fa-file mr-2"></i> Télécharger le fichier`;
                                                    carouselImg.parentElement.appendChild(
                                                        fileLink
                                                    );
                                                }
                                                indicator.textContent = `${
                                                    i + 1
                                                } / ${images.length}`;
                                                prevBtn.style.display =
                                                    images.length > 1
                                                        ? "block"
                                                        : "none";
                                                nextBtn.style.display =
                                                    images.length > 1
                                                        ? "block"
                                                        : "none";
                                            }

                                            prevBtn.onclick = function () {
                                                idx =
                                                    (idx - 1 + images.length) %
                                                    images.length;
                                                showImage(idx);
                                            };
                                            nextBtn.onclick = function () {
                                                idx = (idx + 1) % images.length;
                                                showImage(idx);
                                            };

                                            idx = 0;
                                            showImage(idx);
                                        };

                                        closeImagesModal.onclick = function () {
                                            imagesModal.classList.add("hidden");
                                            imagesModal.classList.remove(
                                                "flex"
                                            );
                                        };
                                    }
                                }
                            });

                        // Gestion de l'upload
                        const uploadForm =
                            document.getElementById("upload-form");
                        if (uploadForm) {
                            uploadForm.addEventListener("submit", function (e) {
                                e.preventDefault();
                                const fileInput =
                                    document.getElementById("file-input");
                                if (!fileInput.files.length) return;
                                const formData = new FormData();
                                formData.append("file", fileInput.files[0]);
                                fetch(
                                    `/model/probleme/upload/${descDiv.dataset.problemId}`,
                                    {
                                        method: "POST",
                                        headers: {
                                            "X-CSRF-TOKEN": document
                                                .querySelector(
                                                    'meta[name="csrf-token"]'
                                                )
                                                .getAttribute("content"),
                                        },
                                        body: formData,
                                    }
                                )
                                    .then((res) => res.json())
                                    .then((data) => {
                                        if (data.success) {
                                            document.getElementById(
                                                "upload-result"
                                            ).innerHTML = `Fichier uploadé : <a href="${data.url}" target="_blank">${data.url}</a>`;
                                        } else {
                                            document.getElementById(
                                                "upload-result"
                                            ).innerText =
                                                data.message ||
                                                "Erreur lors de l'upload";
                                        }
                                    })
                                    .catch(() => {
                                        document.getElementById(
                                            "upload-result"
                                        ).innerText = "Erreur lors de l'upload";
                                    });
                            });
                        }
                    }

                    if (isAdmin) {
                        const placeholder = solutionContainer.querySelector(
                            ".edit-lock-btn-placeholder"
                        );
                        if (placeholder && descDiv) {
                            // Bouton édition enrichie (CKEditor)
                            const btnCk = document.createElement("button");
                            btnCk.type = "button";
                            btnCk.className =
                                "edit-ckeditor-btn ml-2 text-blue-accent";
                            btnCk.title = "Édition enrichie";
                            btnCk.innerHTML = '<i class="fa-solid fa-pen"></i>';
                            placeholder.appendChild(btnCk);

                            btnCk.onclick = function (event) {
                                event.stopPropagation();
                                let tryCount = 0;
                                const maxTries = 30; // 3 secondes max (30 x 100ms)

                                function openCkeditor5() {
                                    if (
                                        typeof window.ClassicEditor ===
                                        "undefined"
                                    ) {
                                        tryCount++;
                                        if (tryCount > maxTries) {
                                            alert(
                                                "CKEditor 5 n'est pas chargé après plusieurs tentatives. Vérifiez votre connexion ou rechargez la page."
                                            );
                                            return;
                                        }
                                        setTimeout(openCkeditor5, 100);
                                        return;
                                    }
                                    let modal =
                                        document.getElementById(
                                            "ckeditor-modal"
                                        );
                                    if (!modal) {
                                        modal = document.createElement("div");
                                        modal.id = "ckeditor-modal";
                                        modal.className =
                                            "fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40";
                                        modal.innerHTML = `
                <div class="bg-white rounded-lg p-6 max-w-xl w-full flex flex-col items-center">
                    <textarea id="ckeditor-area" style="width:100%;min-height:20%;"></textarea>
                    <div class="flex gap-2 mt-4">
                        <button id="ckeditor-save" class="px-4 py-2 bg-blue-accent text-white rounded hover:bg-blue-hover">Sauvegarder</button>
                        <button id="ckeditor-cancel" class="px-4 py-2 bg-secondary-grey text-primary-grey rounded hover:bg-red-accent hover:text-white">Annuler</button>
                    </div>
                </div>
            `;
                                        document.body.appendChild(modal);
                                    }
                                    modal.style.display = "flex";
                                    document.getElementById(
                                        "ckeditor-area"
                                    ).value = descDiv.innerHTML;

                                    if (window.CKEDITOR5_INSTANCE) {
                                        window.CKEDITOR5_INSTANCE.destroy();
                                    }
                                    ClassicEditor.create(
                                        document.getElementById(
                                            "ckeditor-area"
                                        ),
                                        {
                                            toolbar: [
                                                "bold",
                                                "italic",
                                                "link",
                                                "bulletedList",
                                                "numberedList",
                                                "undo",
                                                "redo",
                                            ],
                                        }
                                    )
                                        .then((editor) => {
                                            window.CKEDITOR5_INSTANCE = editor;
                                            editor.setData(descDiv.innerHTML);
                                        })
                                        .catch((error) => {
                                            console.error(error);
                                        });

                                    document.getElementById(
                                        "ckeditor-save"
                                    ).onclick = function () {
                                        if (window.CKEDITOR5_INSTANCE) {
                                            const value =
                                                window.CKEDITOR5_INSTANCE.getData();
                                            fetch(
                                                `/problemes/update-description/${descDiv.dataset.problemId}`,
                                                {
                                                    method: "POST",
                                                    headers: {
                                                        "Content-Type":
                                                            "application/json",
                                                        "X-CSRF-TOKEN":
                                                            csrfToken,
                                                        Accept: "application/json",
                                                    },
                                                    body: JSON.stringify({
                                                        description: value,
                                                    }),
                                                }
                                            )
                                                .then((res) => res.json())
                                                .then(() => {
                                                    descDiv.innerHTML = value;
                                                    descDiv.innerHTML =
                                                        autoLink(
                                                            descDiv.innerHTML
                                                        );
                                                    modal.style.display =
                                                        "none";
                                                    window.CKEDITOR5_INSTANCE.destroy();
                                                    window.CKEDITOR5_INSTANCE =
                                                        null;
                                                })
                                                .catch(() => {
                                                    modal.style.display =
                                                        "none";
                                                    window.CKEDITOR5_INSTANCE.destroy();
                                                    window.CKEDITOR5_INSTANCE =
                                                        null;
                                                });
                                        }
                                    };
                                    document.getElementById(
                                        "ckeditor-cancel"
                                    ).onclick = function () {
                                        modal.style.display = "none";
                                        if (window.CKEDITOR5_INSTANCE) {
                                            window.CKEDITOR5_INSTANCE.destroy();
                                            window.CKEDITOR5_INSTANCE = null;
                                        }
                                    };
                                }

                                // Utilise la fonction utilitaire pour attendre CKEditor 5
                                function onCkeditor5Ready(callback) {
                                    if (
                                        typeof window.ClassicEditor !==
                                        "undefined"
                                    ) {
                                        callback();
                                    } else {
                                        const interval = setInterval(() => {
                                            if (
                                                typeof window.ClassicEditor !==
                                                "undefined"
                                            ) {
                                                clearInterval(interval);
                                                callback();
                                            }
                                        }, 100);
                                    }
                                }
                                onCkeditor5Ready(openCkeditor5);
                            };

                            // Bouton lock/save (édition inline)
                            const btnLock = document.createElement("button");
                            btnLock.type = "button";
                            btnLock.className =
                                "edit-lock-btn ml-2 text-blue-accent";
                            btnLock.title = "Déverrouiller pour éditer";
                            btnLock.innerHTML =
                                '<i class="fa-solid fa-lock"></i>';
                            placeholder.appendChild(btnLock);

                            handleLockSaveButton({
                                editableElem: descDiv,
                                btn: btnLock,
                                fetchUrl: `/problemes/update-description/${descDiv.dataset.problemId}`,
                                fetchBody: (value) => ({ description: value }),
                                onSuccess: (el) => {
                                    el.style.background = "#678BD8";
                                    setTimeout(
                                        () => (el.style.background = ""),
                                        500
                                    );
                                },
                                onError: (el) => {
                                    el.style.background = "#DB7171";
                                    setTimeout(
                                        () => (el.style.background = ""),
                                        1000
                                    );
                                },
                                getValue: (el) => el.innerHTML,
                                setValue: (el, val) => {
                                    el.innerHTML = val;
                                },
                            });
                        }
                    }
                }
            });
        });
    }

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
                // Filtrage JS côté client (si besoin)
                let filtered = data.problems;
                if (q) {
                    filtered = filtered.filter((p) =>
                        (p.title || "").toLowerCase().includes(q.toLowerCase())
                    );
                }
                // Les filtres tool, env, societe sont déjà appliqués côté serveur via params
                renderProblemes(filtered, q, env, tool, societe);
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

    const input = document.getElementById("search-problemes-global");

    // Crée le conteneur suggestions si pas déjà là
    let suggestionBox = document.getElementById(
        "search-problemes-global-suggestions"
    );
    if (!suggestionBox) {
        suggestionBox = document.createElement("div");
        suggestionBox.id = "search-problemes-global-suggestions";
        suggestionBox.className =
            "absolute z-50 bg-white border rounded shadow w-1/2 max-w-xs hidden";
        // Ajoute une hauteur fixe et scroll vertical
        suggestionBox.style.maxHeight = "320px";
        suggestionBox.style.overflowY = "auto";
        input.parentNode.appendChild(suggestionBox);
    } else {
        // Si déjà présent, applique aussi le style
        suggestionBox.style.maxHeight = "320px";
        suggestionBox.style.overflowY = "auto";
    }

    let allProblems = [];
    let selectedIndex = -1;

    // Récupère tous les problèmes une seule fois
    fetch("/problemes/search")
        .then((res) => res.json())
        .then((data) => {
            allProblems = data.problems || [];
        });

    // Navigation clavier
    input.addEventListener("keydown", function (ev) {
        const items = suggestionBox.querySelectorAll(".suggestion-item");
        if (!items.length || suggestionBox.classList.contains("hidden")) return;
        if (ev.key === "ArrowDown") {
            ev.preventDefault();
            selectedIndex = (selectedIndex + 1) % items.length;
            items.forEach((item, idx) => {
                item.classList.toggle("bg-blue-accent", idx === selectedIndex);
                item.classList.toggle("text-white", idx === selectedIndex);
            });
            items[selectedIndex].scrollIntoView({ block: "nearest" });
        } else if (ev.key === "ArrowUp") {
            ev.preventDefault();
            selectedIndex = (selectedIndex - 1 + items.length) % items.length;
            items.forEach((item, idx) => {
                item.classList.toggle("bg-blue-accent", idx === selectedIndex);
                item.classList.toggle("text-white", idx === selectedIndex);
            });
            items[selectedIndex].scrollIntoView({ block: "nearest" });
        } else if (ev.key === "Enter") {
            if (selectedIndex >= 0 && selectedIndex < items.length) {
                ev.preventDefault();
                items[selectedIndex].click();
            }
        } else if (ev.key === "Escape") {
            suggestionBox.classList.add("hidden");
        }
    });

    // Clic sur une suggestion : affiche dans problemes-list2
    suggestionBox.addEventListener("click", function (e) {
        if (e.target.classList.contains("suggestion-item")) {
            const idx = e.target.getAttribute("data-idx");
            const problem = allProblems[idx];
            suggestionBox.classList.add("hidden");
            // Réutilise le rendu déjà présent dans renderProblemes
            const solutionContainer =
                document.getElementById("problemes-list2");
            if (problem && solutionContainer) {
                solutionContainer.innerHTML = `
    <div class="bg-white text-lg rounded p-4">
        <div id="pbTitle" class="flex items-center justify-between mb-2">
            <h2 class="font-bold text-blue-accent mb-2 uppercase">${
                problem.title || ""
            }</h2>
            <span class="edit-lock-btn-placeholder"></span>
        </div>
        <input type="text" id="search-problemes-values" placeholder="Rechercher..." class="px-4 py-4 border text-lg rounded w-full" />
        <div 
            class="text-primary-grey editable-problem-solution"
            data-problem-id="${problem.id || ""}"
            contenteditable="false"
            style="min-height:2em;"
        >${
            problem.description
                ? problem.description
                : "<em>Aucune solution enregistrée.</em>"
        }</div>
    </div>
            `;
                // (Optionnel) Ajoutez ici la logique pour la recherche dans la description, upload, etc.
            }
        }
    });

    // Ferme la box si clic ailleurs
    document.addEventListener("click", function (e) {
        if (!suggestionBox.contains(e.target) && e.target !== input) {
            suggestionBox.classList.add("hidden");
        }
    });
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

    // --- Synchronisation des selects manuels Société/Interlocuteur avec les cards ---
    const societeSelect = document.getElementById("societe-select");
    const interlocuteurSelect = document.getElementById("interlocuteur-select");
    if (societeSelect) {
        societeSelect.addEventListener("change", function () {
            const val = this.value;
            if (val && val.startsWith("societe-")) {
                const id = val.replace("societe-", "");
                fetch(`/model/societe/show/${id}`, {
                    headers: { Accept: "application/json" },
                })
                    .then((res) => res.json())
                    .then((data) => {
                        const allowed = allowedKeys["societe"] || [];
                        const entity = { model: "societe" };
                        allowed.forEach((key) => {
                            if (data[key] !== undefined)
                                entity[key] = data[key];
                        });
                        entity.id = data.id;
                        if (data.active_services)
                            entity.active_services = data.active_services;
                        if (data.main_obj) entity.main_obj = data.main_obj;
                        addEntityToSelection(entity);
                    });
            }
        });
    }

    if (interlocuteurSelect) {
        interlocuteurSelect.addEventListener("change", function () {
            const val = this.value;
            if (val && val.startsWith("interlocuteur-")) {
                const id = val.replace("interlocuteur-", "");
                fetch(`/model/interlocuteur/show/${id}`, {
                    headers: { Accept: "application/json" },
                })
                    .then((res) => res.json())
                    .then((data) => {
                        const allowed = allowedKeys["interlocuteur"] || [];
                        const entity = { model: "interlocuteur" };
                        allowed.forEach((key) => {
                            if (data[key] !== undefined)
                                entity[key] = data[key];
                        });
                        entity.id = data.id;
                        if (data.fullname) entity.fullname = data.fullname;
                        if (data.active_services)
                            entity.active_services = data.active_services;
                        if (data.societe) entity.societe = data.societe;
                        addEntityToSelection(entity);
                    });
            }
        });
    }

    if (input && tableSelect && suggestionBox && resetBtn) {
        input.addEventListener("input", function () {
            resetBtn.classList.toggle("hidden", !this.value.length);
            const q = this.value.trim();
            const table = tableSelect.value;
            if (q.length < 1) {
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
                    const suggestions = data.slice(0, 10);
                    if (suggestions.length) {
                        suggestions.forEach((suggestion) => {
                            let item = document.createElement("button");
                            item.type = "button";
                            item.className =
                                "flex items-center gap-2 text-left text-primary-grey px-4 py-2 hover:bg-blue-accent hover:text-off-white cursor-pointer";
                            // Ajoute l’icône selon le type
                            let icon = "";
                            if (
                                (suggestion.model || tableSelect.value) ===
                                "societe"
                            ) {
                                icon =
                                    '<i class="fa-solid fa-building text-blue-accent"></i>';
                            } else if (
                                (suggestion.model || tableSelect.value) ===
                                "interlocuteur"
                            ) {
                                icon =
                                    '<i class="fa-solid fa-user text-blue-accent"></i>';
                            }
                            item.innerHTML = `${icon}<span>${
                                suggestion.label ?? suggestion
                            }</span>`;
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
                    enableAutocompleteKeyboardNavigation(input, suggestionBox);
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
                            class="absolute right-2 text-3xl text-red-accent hover:text-red-hover font-bold z-10">&times;</button>
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
        (match) => `<mark style="color:#678BD8;">${match}</mark>`
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
    const hasAny = entities.length > 0;

    if (window.innerWidth <= 1023) {
        // Affiche la section si au moins une entité sélectionnée
        if (hasAny) {
            cardSection.classList.remove("hidden");
            cardSection.classList.add("flex");
        } else {
            cardSection.classList.add("hidden");
            cardSection.classList.remove("flex");
        }

        if (card1) card1.classList.toggle("hidden", !hasAny);
        if (card2)
            card2.classList.toggle(
                "hidden",
                !(entities[0] && entities[0].active_services)
            );
        if (card3)
            card3.classList.toggle(
                "hidden",
                !hasInterlocuteur || entities.length < 2
            );
        if (card4)
            card4.classList.toggle(
                "hidden",
                !hasInterlocuteur ||
                    entities.length < 2 ||
                    !entities[1].active_services
            );
    } else {
        // Desktop : tout visible, layout classique
        cardSection.classList.remove("hidden");
        cardSection.classList.remove("flex");
        if (card1) card1.classList.remove("hidden");
        if (card2) card2.classList.remove("hidden");
        if (card3) card3.classList.remove("hidden");
        if (card4) card4.classList.remove("hidden");
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

    const ent1 = entities[0]; // societe OU interlocuteur
    const ent2 = entities[1]; // interlocuteur si présent ET société déjà en 1

    // --- CARD 1 ---
    if (ent1) {
        const card1 = document.getElementById("card-1");
        let coordonneesHtml = "";
        (allowedKeys[ent1.model] || []).forEach((key) => {
            if (ent1[key]) {
                let value = ent1[key];
                let displayValue = value;
                // Ajout mailto et tel
                if (key === "email") {
                    displayValue = `<a href="mailto:${value}" class="text-blue-accent underline">${value}</a>`;
                } else if (
                    key === "phone_fix" ||
                    key === "phone_mobile" ||
                    key === "boss_phone" ||
                    key === "recep_phone"
                ) {
                    // Nettoie le numéro pour le lien tel
                    const tel = value.replace(/[^+\d]/g, "");
                    displayValue = `<a href="tel:${tel}" class="text-blue-accent underline">${value}</a>`;
                }
                coordonneesHtml += `
        <div class="my-1 pr-2 w-full break-words flex flex-col">
            <div class="flex items-center justify-between w-full">
                <p class="font-semibold text-blue-accent mb-0">${
                    window.translatedFields[key]
                } :</p>
                <span class="edit-lock-btn-placeholder ml-auto"></span>
            </div>
            <span class="editable-field" data-model="${ent1.model}" data-id="${
                    ent1.id
                }" data-key="${key}" contenteditable="${
                    window.currentUserRole &&
                    ["admin", "superadmin"].includes(
                        window.currentUserRole.toLowerCase()
                    )
                        ? "true"
                        : "false"
                }" style="border-bottom:1px color-secondary-grey #ccc;min-height:1.5em">${displayValue}</span>
        </div>`;
            }
        });
        const maisonMereHtml =
            ent1.model === "societe" && ent1.main_obj
                ? `<p class="text-xs text-blue-hover mb-2 maison-mere-link" data-main-id="${ent1.main_obj.id}">Filiale de ${ent1.main_obj.name}</p>`
                : '<p class="mb-6"></p>';
        card1.innerHTML = `
            <button type="button" class="absolute top-2 right-2 text-3xl text-red-accent hover:text-red-hover font-bold remove-entity-btn" data-idx="0" title="Supprimer">&times;</button>
            <div id="card1-content" class="flex flex-col items-center w-full h-full">
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
                    interlocutors.sort((a, b) => {
                        const nameA = (
                            a.fullname ||
                            a.name ||
                            ""
                        ).toLowerCase();
                        const nameB = (
                            b.fullname ||
                            b.name ||
                            ""
                        ).toLowerCase();
                        return nameA.localeCompare(nameB);
                    });
                    const selectHtml = `
                        <div class="sticky bottom-0 z-10 bg-white w-full pt-2 pb-2">
                            <label for="interlocutor-select-1" class="block font-semibold text-blue-accent">Sélectionner un interlocuteur :</label>
                            <select id="interlocutor-select-1"
                                class="appearance-none border-2 border-blue-accent rounded-lg px-4 py-2 bg-white text-blue-accent focus:outline-none focus:ring-2 focus:ring-blue-accent transition max-w-xs w-full">
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
                    const card1Content =
                        document.getElementById("card1-content");
                    if (card1Content) {
                        card1Content.insertAdjacentHTML(
                            "beforeend",
                            selectHtml
                        );
                        const select1 = document.getElementById(
                            "interlocutor-select-1"
                        );
                        if (select1) {
                            select1.addEventListener("change", function () {
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

    // --- CARD 2 (services de l'entité sélectionnée, société OU interlocuteur) ---
    if (ent1 && ent1.active_services) {
        let services = Object.values(ent1.active_services);
        const searchInputId = "services-search-1";
        let servicesHtml = `
        <div class="accordion-services">
        <input type="text" id="${searchInputId}" placeholder="Rechercher un service..." 
            class="appearance-none border-2 border-blue-accent rounded-lg px-4 py-2 w-full bg-white text-blue-accent focus:outline-none focus:ring-2 focus:ring-blue-accent transition mb-4" />
        <div id="services-list-1">
            ${services
                .map(
                    (service, idx) => `
            <div class="mb-2 pr-2 w-full break-words flex flex-col service-item">
                <button type="button" class="font-semibold text-blue-accent text-left accordion-label w-full flex items-center gap-2 py-1" data-idx="${idx}" style="background:none;border:none;outline:none;cursor:pointer;">
                <p>${service.label}</p>
                <div class="flex items-center justify-between w-full">
                <span class="accordion-arrow" style="transition:transform 0.2s;">&#x25BE;</span>
                <span class="edit-lock-btn-placeholder ml-auto"></span>
                </div>
                </button>
                <div class="accordion-content" style="display:none;">
                <span class="editable-service-field" data-model="${
                    ent1.model
                }" data-id="${ent1.id}" data-service-key="${
                        service.label
                    }" contenteditable="false" style="border-bottom:1px color-secondary-grey #ccc;min-height:1.5em;display:block;margin-top:0.5em;">
                    ${service.info ?? "Oui"}
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
            document
                .querySelectorAll("#services-list-1 .accordion-label")
                .forEach((btn) => {
                    btn.addEventListener("click", function () {
                        const content =
                            this.parentElement.querySelector(
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
        }, 0);

        document.getElementById("card-2").innerHTML = `
        <div class="flex flex-col w-full h-full">
            <h2 class="font-bold text-blue-accent text-lg mb-2 uppercase text-center">Services activés</h2>
            <div class="flex justify-center items-center mb-4">
            <button id="manage-services-btn1" class="w-1/2 mb-4 px-3 py-1 bg-blue-accent text-white rounded hover:bg-blue-hover">Gérer les services</button>
            </div>
            ${servicesHtml}
        </div>
    `;

        setTimeout(() => {
            const manageBtn = document.getElementById("manage-services-btn1");
            if (manageBtn) {
                manageBtn.onclick = function () {
                    showManageServicesModal(ent1); // ent1 = société ou interlocuteur
                };
            }
        }, 0);

        setTimeout(() => {
            const input = document.getElementById(searchInputId);
            const list = document.getElementById("services-list-1");
            if (input && list) {
                input.addEventListener("input", function () {
                    const q = this.value.toLowerCase();
                    list.querySelectorAll(".service-item").forEach((div) => {
                        const labelElem = div.querySelector("p");
                        const valueElem = div.querySelector(
                            ".editable-service-field"
                        );
                        const accordionContent =
                            div.querySelector(".accordion-content");
                        const arrow = div.querySelector(".accordion-arrow");
                        const label = labelElem?.textContent || "";
                        const value = valueElem?.innerText || "";
                        const match =
                            label.toLowerCase().includes(q) ||
                            value.toLowerCase().includes(q);
                        div.style.display = match ? "" : "none";
                        if (match && q) {
                            labelElem.innerHTML = highlightText(label, q);
                            valueElem.innerHTML = highlightText(value, q);
                            // Ouvre l'accordéon si match
                            if (accordionContent && arrow) {
                                accordionContent.style.display = "block";
                                arrow.style.transform = "rotate(180deg)";
                            }
                        } else {
                            labelElem.innerHTML = label;
                            valueElem.innerHTML = value;
                            // Ferme l'accordéon si pas de match
                            if (accordionContent && arrow) {
                                accordionContent.style.display = "none";
                                arrow.style.transform = "rotate(0deg)";
                            }
                        }
                    });
                });
            }
        }, 0);
    }

    // --- CARD 3 ---
    if (ent2) {
        const card3 = document.getElementById("card-3");
        let coordonneesHtml = "";
        (allowedKeys[ent2.model] || []).forEach((key) => {
            if (ent2[key]) {
                let value = ent2[key];
                let displayValue = value;
                // Ajout mailto et tel
                if (key === "email") {
                    displayValue = `<a href="mailto:${value}" class="text-blue-accent underline">${value}</a>`;
                } else if (
                    key === "phone_fix" ||
                    key === "phone_mobile" ||
                    key === "boss_phone" ||
                    key === "recep_phone"
                ) {
                    const tel = value.replace(/[^+\d]/g, "");
                    displayValue = `<a href="tel:${tel}" class="text-blue-accent underline">${value}</a>`;
                }
                coordonneesHtml += `
        <div class="my-1 pr-2 w-full break-words flex flex-col">
            <div class="flex items-center justify-between w-full">
                <p class="font-semibold text-blue-accent mb-0">${
                    window.translatedFields[key]
                } :</p>
                <span class="edit-lock-btn-placeholder ml-auto"></span>
            </div>
            <span class="editable-field" data-model="${ent2.model}" data-id="${
                    ent2.id
                }" data-key="${key}" contenteditable="${
                    window.currentUserRole &&
                    ["admin", "superadmin"].includes(
                        window.currentUserRole.toLowerCase()
                    )
                        ? "true"
                        : "false"
                }" style="border-bottom:1px color-secondary-grey #ccc;min-height:1.5em">${displayValue}</span>
        </div>`;
            }
        });
        const maisonMereHtml2 =
            ent2.model === "societe" && ent2.main_obj
                ? `<a href="#" class="text-xs text-blue-hover mb-2 maison-mere-link" data-main-id="${ent2.main_obj.id}">Filiale de ${ent2.main_obj.name}</a>`
                : "";
        card3.innerHTML = `
            <button type="button" class="absolute top-2 right-2 text-3xl text-red-accent hover:text-red-hover font-bold remove-entity-btn" data-idx="1" title="Supprimer">&times;</button>
            <div id="card3-content" class="flex flex-col items-center w-full h-full">
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
                            <div class="sticky bottom-0 z-10 bg-white w-full pt-2 pb-2">
                                <label for="interlocutor-select-2" class="block font-semibold text-blue-accent">Sélectionner un interlocuteur :</label>
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
                            </div>
                        `;
                        const oldSelect = document.getElementById(
                            "interlocutor-select-2"
                        );
                        if (oldSelect) oldSelect.parentElement.remove();
                        const card3Content =
                            document.getElementById("card3-content");
                        if (card3Content) {
                            card3Content.insertAdjacentHTML(
                                "beforeend",
                                selectHtml
                            );
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
                                                    allowedKeys[
                                                        "interlocuteur"
                                                    ];
                                                const entity = {
                                                    model: "interlocuteur",
                                                };
                                                allowed.forEach((key) => {
                                                    if (data[key] !== undefined)
                                                        entity[key] = data[key];
                                                });
                                                entity.id = data.id;
                                                if (data.fullname)
                                                    entity.fullname =
                                                        data.fullname;
                                                if (data.active_services)
                                                    entity.active_services =
                                                        data.active_services;
                                                if (data.societe)
                                                    entity.societe =
                                                        data.societe;
                                                addEntityToSelection(entity);
                                            });
                                    }
                                });
                            }
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
            <input type="text" id="${searchInputId}" placeholder="Rechercher un service..." 
                class="appearance-none border-2 border-blue-accent rounded-lg px-4 py-2 w-full bg-white text-blue-accent focus:outline-none focus:ring-2 focus:ring-blue-accent transition mb-4" />
            <div id="services-list-2">
                ${services
                    .map(
                        (service, idx) => `
                <div class="mb-2 pr-2 w-full break-words flex flex-col service-item">
                    <button type="button" class="font-semibold text-blue-accent text-left accordion-label w-full flex items-center gap-2 py-1" data-idx="${idx}" style="background:none;border:none;outline:none;cursor:pointer;">
                    <p>${service.label}</p>
                    <div class="flex items-center justify-between w-full">
                    <span class="accordion-arrow" style="transition:transform 0.2s;">&#x25BE;</span>
                    <span class="edit-lock-btn-placeholder ml-auto"></span>
                    </div>
                    </button>
                    <div class="accordion-content" style="display:none;">
                    <span class="editable-service-field" data-model="${
                        ent2.model
                    }" data-id="${ent2.id}" data-service-key="${
                            service.label
                        }" contenteditable="false" style="border-bottom:1px color-secondary-grey #ccc;min-height:1.5em;display:block;margin-top:0.5em;">
                        ${service.info ?? "Oui"}
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
            document
                .querySelectorAll("#services-list-2 .accordion-label")
                .forEach((btn) => {
                    btn.addEventListener("click", function () {
                        const content =
                            this.parentElement.querySelector(
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
        }, 0);

        document.getElementById("card-4").innerHTML = `
            <div class="flex flex-col w-full h-full">
                <h2 class="font-bold text-blue-accent text-lg mb-2 uppercase text-center">Services activés</h2>
                <div class="flex justify-center items-center mb-4">
                <button id="manage-services-btn2" class="w-1/2 mb-4 px-3 py-1 bg-blue-accent text-white rounded hover:bg-blue-hover">Gérer les services</button>
                </div>
                ${servicesHtml}
            </div>
        `;

        setTimeout(() => {
            const manageBtn = document.getElementById("manage-services-btn2");
            if (manageBtn) {
                manageBtn.onclick = function () {
                    showManageServicesModal(ent2); // ent2 =  interlocuteur
                };
            }
        }, 0);

        setTimeout(() => {
            const input = document.getElementById(searchInputId);
            const list = document.getElementById("services-list-2");
            if (input && list) {
                input.addEventListener("input", function () {
                    const q = this.value.toLowerCase();
                    list.querySelectorAll(".service-item").forEach((div) => {
                        const labelElem = div.querySelector("p");
                        const valueElem = div.querySelector(
                            ".editable-service-field"
                        );
                        const accordionContent =
                            div.querySelector(".accordion-content");
                        const arrow = div.querySelector(".accordion-arrow");
                        const label = labelElem?.textContent || "";
                        const value = valueElem?.innerText || "";
                        const match =
                            label.toLowerCase().includes(q) ||
                            value.toLowerCase().includes(q);
                        div.style.display = match ? "" : "none";
                        if (match && q) {
                            labelElem.innerHTML = highlightText(label, q);
                            valueElem.innerHTML = highlightText(value, q);
                            // Ouvre l'accordéon si match
                            if (accordionContent && arrow) {
                                accordionContent.style.display = "block";
                                arrow.style.transform = "rotate(180deg)";
                            }
                        } else {
                            labelElem.innerHTML = label;
                            valueElem.innerHTML = value;
                            // Ferme l'accordéon si pas de match
                            if (accordionContent && arrow) {
                                accordionContent.style.display = "none";
                                arrow.style.transform = "rotate(0deg)";
                            }
                        }
                    });
                });
            }
        }, 0);
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

    // Edition inline des coordonnées (cards 1 et 3)
    setTimeout(() => {
        if (
            window.currentUserRole &&
            ["admin", "superadmin"].includes(
                window.currentUserRole.toLowerCase()
            )
        ) {
            // Inline classique pour coordonnées (cards 1 et 3)
            document
                .querySelectorAll(
                    "#card-1 .editable-field, #card-3 .editable-field"
                )
                .forEach((span) => {
                    span.setAttribute("contenteditable", "false");
                    let placeholder = span.parentElement.querySelector(
                        ".edit-lock-btn-placeholder"
                    );
                    if (!placeholder && span.closest(".service-item")) {
                        placeholder = span
                            .closest(".service-item")
                            .querySelector(".edit-lock-btn-placeholder");
                    }
                    if (
                        (!placeholder &&
                            (!span.nextElementSibling ||
                                !span.nextElementSibling.classList.contains(
                                    "edit-lock-btn"
                                ))) ||
                        (placeholder &&
                            !placeholder.querySelector(".edit-lock-btn"))
                    ) {
                        const btn = document.createElement("button");
                        btn.type = "button";
                        btn.className = "edit-lock-btn ml-2 text-blue-accent";
                        btn.title = "Déverrouiller pour éditer";
                        btn.innerHTML = '<i class="fa-solid fa-lock"></i>';
                        if (placeholder) placeholder.appendChild(btn);
                        else span.after(btn);
                        handleLockSaveButton({
                            editableElem: span,
                            btn,
                            fetchUrl: `/model/${span.dataset.model}/update-field/${span.dataset.id}`,
                            fetchBody: (value) => ({
                                field: span.dataset.key,
                                value,
                            }),
                            onSuccess: (el) => {
                                el.style.background = "#678BD8";
                                setTimeout(
                                    () => (el.style.background = ""),
                                    500
                                );
                            },
                            onError: (el) => {
                                el.style.background = "#DB7171";
                                setTimeout(
                                    () => (el.style.background = ""),
                                    1000
                                );
                            },
                        });
                    }
                });

            // Edition enrichie pour services (cards 2 et 4)
            document
                .querySelectorAll(
                    "#card-2 .editable-service-field, #card-4 .editable-service-field"
                )
                .forEach((span) => {
                    // Ajoute le bouton édition enrichie si pas déjà présent
                    let placeholder = span.parentElement.querySelector(
                        ".edit-lock-btn-placeholder"
                    );
                    if (!placeholder && span.closest(".service-item")) {
                        placeholder = span
                            .closest(".service-item")
                            .querySelector(".edit-lock-btn-placeholder");
                    }
                    if (
                        (!placeholder &&
                            (!span.nextElementSibling ||
                                !span.nextElementSibling.classList.contains(
                                    "edit-ckeditor-btn"
                                ))) ||
                        (placeholder &&
                            !placeholder.querySelector(".edit-ckeditor-btn"))
                    ) {
                        const btnCk = document.createElement("button");
                        btnCk.type = "button";
                        btnCk.className =
                            "edit-ckeditor-btn ml-2 text-blue-accent";
                        btnCk.title = "Édition enrichie";
                        btnCk.innerHTML = '<i class="fa-solid fa-pen"></i>';
                        if (placeholder) placeholder.appendChild(btnCk);
                        else span.after(btnCk);

                        btnCk.onclick = function (event) {
                            event.stopPropagation();
                            function openCkeditor5() {
                                let modal =
                                    document.getElementById("ckeditor-modal");
                                if (!modal) {
                                    modal = document.createElement("div");
                                    modal.id = "ckeditor-modal";
                                    modal.className =
                                        "fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40";
                                    modal.innerHTML = `
                                    <div class="bg-white rounded-lg p-6 max-w-xl w-full flex flex-col items-center">
                                        <textarea id="ckeditor-area" style="width:100%;min-height:20%;"></textarea>
                                        <div class="flex gap-2 mt-4">
                                            <button id="ckeditor-save" class="px-4 py-2 bg-blue-accent text-white rounded hover:bg-blue-hover">Sauvegarder</button>
                                            <button id="ckeditor-cancel" class="px-4 py-2 bg-secondary-grey text-primary-grey rounded hover:bg-red-accent hover:text-white">Annuler</button>
                                        </div>
                                    </div>
                                `;
                                    document.body.appendChild(modal);
                                }
                                modal.style.display = "flex";
                                document.getElementById("ckeditor-area").value =
                                    span.innerHTML;

                                if (window.CKEDITOR5_INSTANCE) {
                                    window.CKEDITOR5_INSTANCE.destroy();
                                }
                                ClassicEditor.create(
                                    document.getElementById("ckeditor-area"),
                                    {
                                        toolbar: [
                                            "bold",
                                            "italic",
                                            "link",
                                            "bulletedList",
                                            "numberedList",
                                            "undo",
                                            "redo",
                                        ],
                                    }
                                )
                                    .then((editor) => {
                                        window.CKEDITOR5_INSTANCE = editor;
                                        editor.setData(span.innerHTML);
                                    })
                                    .catch((error) => {
                                        console.error(error);
                                    });

                                document.getElementById(
                                    "ckeditor-save"
                                ).onclick = function () {
                                    if (window.CKEDITOR5_INSTANCE) {
                                        const value =
                                            window.CKEDITOR5_INSTANCE.getData();
                                        fetch(
                                            `/model/${span.dataset.model}/update-field/${span.dataset.id}`,
                                            {
                                                method: "POST",
                                                headers: {
                                                    "Content-Type":
                                                        "application/json",
                                                    "X-CSRF-TOKEN": csrfToken,
                                                    Accept: "application/json",
                                                },
                                                body: JSON.stringify({
                                                    field:
                                                        "infos_" +
                                                        span.dataset.serviceKey
                                                            .toLowerCase()
                                                            .normalize("NFD")
                                                            .replace(
                                                                /[\u0300-\u036f]/g,
                                                                ""
                                                            )
                                                            .replace(/ /g, "_"),
                                                    value: value,
                                                }),
                                            }
                                        )
                                            .then((res) => res.json())
                                            .then(() => {
                                                span.innerHTML = value;
                                                modal.style.display = "none";
                                                window.CKEDITOR5_INSTANCE.destroy();
                                                window.CKEDITOR5_INSTANCE =
                                                    null;
                                            })
                                            .catch(() => {
                                                modal.style.display = "none";
                                                window.CKEDITOR5_INSTANCE.destroy();
                                                window.CKEDITOR5_INSTANCE =
                                                    null;
                                            });
                                    }
                                };
                                document.getElementById(
                                    "ckeditor-cancel"
                                ).onclick = function () {
                                    modal.style.display = "none";
                                    if (window.CKEDITOR5_INSTANCE) {
                                        window.CKEDITOR5_INSTANCE.destroy();
                                        window.CKEDITOR5_INSTANCE = null;
                                    }
                                };
                            }

                            function onCkeditor5Ready(callback) {
                                if (
                                    typeof window.ClassicEditor !== "undefined"
                                ) {
                                    callback();
                                } else {
                                    const interval = setInterval(() => {
                                        if (
                                            typeof window.ClassicEditor !==
                                            "undefined"
                                        ) {
                                            clearInterval(interval);
                                            callback();
                                        }
                                    }, 100);
                                }
                            }
                            onCkeditor5Ready(openCkeditor5);
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

function showConfirmModal(message = "Confirmer ?") {
    return new Promise((resolve) => {
        // Crée la modale si elle n'existe pas déjà
        let modal = document.getElementById("confirm-modal");
        if (!modal) {
            modal = document.createElement("div");
            modal.id = "confirm-modal";
            modal.className =
                "fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40";
            modal.innerHTML = `
                <div class="bg-white rounded-lg p-6 max-w-xs w-full flex flex-col items-center">
                    <div class="mb-4 text-center text-primary-grey">${message}</div>
                    <div class="flex gap-2">
                        <button id="confirm-yes" class="px-4 py-2 bg-blue-accent text-white rounded hover:bg-blue-hover">Oui</button>
                        <button id="confirm-no" class="px-4 py-2 bg-secondary-grey text-primary-grey rounded hover:bg-red-accent hover:text-white">Non</button>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);
        } else {
            modal.querySelector("div.mb-4").textContent = message;
            modal.style.display = "flex";
        }

        modal.querySelector("#confirm-yes").onclick = () => {
            modal.style.display = "none";
            resolve(true);
        };
        modal.querySelector("#confirm-no").onclick = () => {
            modal.style.display = "none";
            resolve(false);
        };
    });
}

function showManageServicesModal(entity) {
    // entity doit contenir model et id
    fetch(`/model/${entity.model}/services/${entity.id}`, {
        headers: { Accept: "application/json" },
    })
        .then((res) => res.json())
        .then((data) => {
            // data.services doit être un tableau [{label, actif, id}]
            let modal = document.getElementById("manage-services-modal");
            if (!modal) {
                modal = document.createElement("div");
                modal.id = "manage-services-modal";
                modal.className =
                    "fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40";
                document.body.appendChild(modal);
            }
            modal.innerHTML = `
                <div class="bg-white rounded-lg p-6 max-w-lg w-1/4 flex flex-col items-center">
                    <h2 class="font-bold text-blue-accent text-lg mb-4 uppercase">Gérer les services</h2>
                    <form id="manage-services-form" class="w-full flex flex-col gap-2">
                        <div class="flex flex-col gap-2">
                        ${data.services
                            .map(
                                (s) => `
                            <label class="flex items-center gap-2 px-4 py-2 rounded border border-blue-accent bg-off-white hover:bg-blue-accent/10 transition">
                                <input type="checkbox" name="services[]" value="${
                                    s.id
                                }" class="accent-blue-accent w-5 h-5" ${
                                    s.actif ? "checked" : ""
                                } />
                                <span class="text-blue-accent font-semibold">${
                                    s.label
                                }</span>
                            </label>
                        `
                            )
                            .join("")}
                        </div>
                        <div class="flex gap-2 mt-6 justify-end">
                            <button type="submit" class="px-4 py-2 bg-blue-accent text-white rounded hover:bg-blue-hover transition">Enregistrer</button>
                            <button type="button" id="close-manage-services" class="px-4 py-2 bg-secondary-grey text-primary-grey rounded hover:bg-red-accent hover:text-white transition">Annuler</button>
                        </div>
                    </form>
                </div>
            `;
            modal.style.display = "flex";

            document.getElementById("close-manage-services").onclick =
                function () {
                    modal.style.display = "none";
                };

            document.getElementById("manage-services-form").onsubmit =
                function (e) {
                    e.preventDefault();
                    const checked = Array.from(
                        this.querySelectorAll('input[type="checkbox"]:checked')
                    ).map((cb) => cb.value);
                    fetch(`/model/${entity.model}/services/${entity.id}`, {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": csrfToken,
                            Accept: "application/json",
                        },
                        body: JSON.stringify({ services: checked }),
                    })
                        .then((res) => res.json())
                        .then(() => {
                            modal.style.display = "none";
                            // Recharge les données fraiches de l'entité avant d'afficher la card
                            fetch(`/model/${entity.model}/show/${entity.id}`, {
                                headers: { Accept: "application/json" },
                            })
                                .then((res) => res.json())
                                .then((data) => {
                                    const allowed =
                                        allowedKeys[entity.model] || [];
                                    const newEntity = { model: entity.model };
                                    allowed.forEach((key) => {
                                        if (data[key] !== undefined)
                                            newEntity[key] = data[key];
                                    });
                                    newEntity.id = data.id;
                                    if (data.active_services)
                                        newEntity.active_services =
                                            data.active_services;
                                    if (data.main_obj)
                                        newEntity.main_obj = data.main_obj;
                                    if (data.fullname)
                                        newEntity.fullname = data.fullname;
                                    if (data.societe)
                                        newEntity.societe = data.societe;

                                    // Met à jour selectedEntities à la bonne position
                                    selectedEntities = selectedEntities.map(
                                        (e) =>
                                            e.model === newEntity.model
                                                ? newEntity
                                                : e
                                    );
                                    showSelectedEntitiesCard(selectedEntities);
                                });
                        });
                };
        });
}

// Utilitaire pour gérer le lock/save sur un champ éditable
function handleLockSaveButton({
    editableElem,
    btn,
    fetchUrl,
    fetchBody,
    onSuccess,
    onError,
    getValue = (el) => el.innerText.trim(),
    setValue = (el, val) => {
        el.innerText = val;
    },
    lockTitle = "Déverrouiller pour éditer",
    saveTitle = "Sauvegarder",
}) {
    let originalValue = getValue(editableElem);
    btn.onclick = function () {
        if (editableElem.isContentEditable) {
            // Utilise Promise.resolve pour garantir le comportement asynchrone
            Promise.resolve(
                showConfirmModal("Sauvegarder la modification ?")
            ).then((confirmed) => {
                if (confirmed) {
                    const value = getValue(editableElem);
                    fetch(fetchUrl, {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": csrfToken,
                            Accept: "application/json",
                        },
                        body: JSON.stringify(fetchBody(value)),
                    })
                        .then((res) => res.json())
                        .then(() => {
                            if (onSuccess) onSuccess(editableElem);
                        })
                        .catch(() => {
                            if (onError) onError(editableElem);
                        });
                    originalValue = value;
                } else {
                    setValue(editableElem, originalValue);
                }
                editableElem.contentEditable = "false";
                btn.innerHTML = '<i class="fa-solid fa-lock"></i>';
                btn.title = lockTitle;
            });
        } else {
            originalValue = getValue(editableElem);
            editableElem.contentEditable = "true";
            editableElem.focus();
            btn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i>';
            btn.title = saveTitle;
        }
    };
}
