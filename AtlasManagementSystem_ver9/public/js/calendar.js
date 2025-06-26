$(function () {
    const reserveForm = document.querySelector("#reserveParts");
    if (!reserveForm) return;

    reserveForm.addEventListener("submit", function (e) {
        const existingHiddens = reserveForm.querySelectorAll(
            'input[type="hidden"].added-by-js'
        );
        existingHiddens.forEach((h) => h.remove());

        const selectedSelects = document.querySelectorAll(
            'select[name^="reserve_parts["]'
        );
        selectedSelects.forEach((el) => {
            if (!el.value) return;

            const nameMatch = el.name.match(/reserve_parts\[(.*?)\]/);
            if (!nameMatch) return;

            const date = nameMatch[1];
            const part = el.value;

            const hidden = document.createElement("input");
            hidden.type = "hidden";
            hidden.name = `reserve_parts[${date}]`;
            hidden.value = part;
            hidden.classList.add("added-by-js");
            reserveForm.appendChild(hidden);
        });
    });
});
