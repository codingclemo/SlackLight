$(".editMsg").click(function () {
    var buttonId = this.id;
    buttonId = buttonId.toString().replace("editMsg_", ""); // reduce to numerical value
    var divId = "form_editMsg_" + buttonId;
    var x = $('#' + divId);
    x.toggle();
});

$(".removeEditMsg").click(function () {
    $(".removeEditMsg").closest("div").hide();
});