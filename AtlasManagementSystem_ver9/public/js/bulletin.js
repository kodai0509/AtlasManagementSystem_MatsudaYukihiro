$(function () {
    $(".main_categories").click(function () {
        var category_id = $(this).attr("category_id");
        $(".category_num" + category_id).slideToggle();
    });

    $(document).on("click", ".like_btn", function (e) {
        e.preventDefault();
        $(this).addClass("un_like_btn").removeClass("like_btn");
        var post_id = $(this).attr("post_id");
        var count = $(".like_counts" + post_id).text();
        var countInt = Number(count);
        $.ajax({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            method: "post",
            url: "/like/post/" + post_id,
            data: { post_id: post_id },
        })
            .done(function (res) {
                console.log(res);
                $(".like_counts" + post_id).text(countInt + 1);
            })
            .fail(function () {
                console.log("fail");
            });
    });

    $(document).on("click", ".un_like_btn", function (e) {
        e.preventDefault();
        $(this).removeClass("un_like_btn").addClass("like_btn");
        var post_id = $(this).attr("post_id");
        var count = $(".like_counts" + post_id).text();
        var countInt = Number(count);

        $.ajax({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            method: "post",
            url: "/unlike/post/" + post_id,
            data: { post_id: post_id },
        })
            .done(function () {
                $(".like_counts" + post_id).text(countInt - 1);
            })
            .fail(function () {});
    });

    $(".edit-modal-open").on("click", function () {
        $(".js-modal").fadeIn();
        var post_title = $(this).data("post_title");
        var post_body = $(this).data("post_body");
        var post_id = $(this).data("post_id");
        $(".modal-inner-title input").val(post_title);
        $(".modal-inner-body textarea").val(post_body);
        $(".edit-modal-hidden").val(post_id);
        return false;
    });
    $(".js-modal-close").on("click", function () {
        $(".js-modal").fadeOut();
        return false;
    });

    // 削除モーダル
    $(document).on("click", ".delete-modal-open", function (e) {
        e.preventDefault();
        var post_id = $(this).data("post-id");
        $(".delete-modal").fadeIn();
        $(".delete-form").attr("action", "/posts/" + post_id);
    });

    $(".js-delete-modal-close").on("click", function (e) {
        e.preventDefault();
        $(".delete-modal").fadeOut();
    });
});
