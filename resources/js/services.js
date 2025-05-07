export function addServiceEditListeners(csrfToken) {
    document.querySelectorAll("#card-2 .editable-service-field, #card-4 .editable-service-field").forEach((span) => {
        span.addEventListener("blur", function () {
            const model = this.dataset.model;
            const id = this.dataset.id;
            const serviceLabel = this.dataset.serviceKey;
            const value = this.textContent.trim();
            const field =
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
                body: JSON.stringify({ field, value }),
            })
                .then((res) => res.json())
                .then(() => {
                    this.style.background = "#678BD8";
                    setTimeout(() => (this.style.background = ""), 500);
                })
                .catch(() => {
                    this.style.background = "#DB7171";
                    setTimeout(() => (this.style.background = ""), 1000);
                });
        });
    });
}