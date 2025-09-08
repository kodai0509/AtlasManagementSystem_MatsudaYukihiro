$(function () {
    // いいね
    $(document).on("click", ".like_btn", function (e) {
        e.preventDefault();

        var $btn = $(this);
        if ($btn.prop("disabled")) return;
        $btn.prop("disabled", true);
        $btn.addClass("un_like_btn").removeClass("like_btn");
        var post_id = $btn.attr("post_id");
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
                $btn.removeClass("un_like_btn").addClass("like_btn");
            })
            .always(function () {
                $btn.prop("disabled", false);
            });
    });

    // いいね解除
    $(document).on("click", ".un_like_btn", function (e) {
        e.preventDefault();

        var $btn = $(this);
        if ($btn.prop("disabled")) return;
        $btn.prop("disabled", true);

        $btn.removeClass("un_like_btn").addClass("like_btn");
        var post_id = $btn.attr("post_id");
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
                $(".like_counts" + post_id).text(Math.max(countInt - 1, 0)); // 0未満防止
            })
            .fail(function () {
                $btn.removeClass("like_btn").addClass("un_like_btn");
            })
            .always(function () {
                $btn.prop("disabled", false);
            });
    });

    $(".edit-modal-open").on("click", function () {
        $(".js-modal").fadeIn();
        const title = $(this).data("post-title");
        const body = $(this).data("post-body");
        const id = $(this).data("post-id");
        $('.modal-inner-title  input[name="post_title"]').val(title);
        $('.modal-inner-body   textarea[name="post_body"]').val(body);
        $(".edit-modal-hidden").val(id);
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
        $(".delete-form").attr("action", "/bulletin_board/post/" + post_id);
    });

    $(".js-delete-modal-close").on("click", function (e) {
        e.preventDefault();
        $(".delete-modal").fadeOut();
    });

    // 検索
    $("#main_category").on("change", function () {
        var selectedId = $(this).val();
        $(".sub_categories_list").hide();
        $('.sub_categories_list[data-category-id="' + selectedId + '"]').show();
    });

    const $select = $("#main_category");
    const $wrapper = $select.closest(".select_wrapper");

    $select.on("focus", function () {
        $wrapper.addClass("open");
    });

    $select.on("blur", function () {
        $wrapper.removeClass("open");
    });
    $(".main_categories").on("click", function (e) {
        e.preventDefault(); // 念のため追加

        var category_id = $(this).attr("category_id");

        // 自分のサブカテゴリーだけ開閉
        $(".category_num" + category_id)
            .stop(true, true)
            .slideToggle();

        // 矢印切り替え
        $(this).toggleClass("open");
    });
});
