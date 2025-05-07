export function addEntityToSelection(entity, allowedKeys, showSelectedEntitiesCard) {
    let selectedEntities = JSON.parse(
        localStorage.getItem("selectedEntities") || "[]"
    );

    if (
        selectedEntities.some(
            (item) => item.id === entity.id && item.model === entity.model
        )
    ) {
        showSelectedEntitiesCard(selectedEntities);
        return;
    }

    if (
        entity.model === "interlocuteur" &&
        selectedEntities.length === 2 &&
        selectedEntities[0].model === "société" &&
        selectedEntities[1].model === "interlocuteur"
    ) {
        selectedEntities[1] = entity;
    } else if (
        entity.model === "interlocuteur" &&
        selectedEntities.length === 2 &&
        selectedEntities[1].model === "société" &&
        selectedEntities[0].model === "interlocuteur"
    ) {
        selectedEntities[0] = entity;
    } else if (
        entity.model === "interlocuteur" &&
        selectedEntities.length === 1 &&
        selectedEntities[0].model === "société"
    ) {
        selectedEntities.push(entity);
    } else if (
        entity.model === "interlocuteur" &&
        selectedEntities.length === 1 &&
        selectedEntities[0].model === "interlocuteur"
    ) {
        selectedEntities[0] = entity;
    } else {
        if (selectedEntities.length >= 2) {
            selectedEntities.shift();
        }
        selectedEntities.push(entity);
    }

    localStorage.setItem("selectedEntities", JSON.stringify(selectedEntities));
    showSelectedEntitiesCard(selectedEntities);
}