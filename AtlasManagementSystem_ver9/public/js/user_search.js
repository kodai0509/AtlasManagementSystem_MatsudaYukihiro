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
