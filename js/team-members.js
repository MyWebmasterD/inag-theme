jQuery(document).ready(function($) {

    $(".tmm .tmm_member .tmm_show_more_btn").click(function () {
        $(this).toggleClass("close").siblings(".tmm .tmm_member .tmm_textblock").toggleClass("show_more");
    });

});