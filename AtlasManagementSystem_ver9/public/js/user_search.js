$(function () {
    $(".search_conditions").click(function () {
        $(".search_conditions_inner").slideToggle();
    });

    $(".subject_edit_btn").click(function () {
        $(".subject_inner").slideToggle();
    });
});

const sc = document.querySelector(".search_conditions");
sc.addEventListener("click", () => {
    sc.classList.toggle("open");
});

function toggleSubjectForm(el) {
    const wrapper = el.closest(".subject_edit_btn_wrapper");
    const subjectInner =
        el.nextElementSibling || el.parentElement.nextElementSibling;
    wrapper.classList.toggle("open");
    if (subjectInner && subjectInner.classList.contains("subject_inner")) {
        const isVisible = subjectInner.style.display === "block";
        subjectInner.style.display = isVisible ? "none" : "block";
    }
}
