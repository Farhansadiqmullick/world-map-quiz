(function ($) {
  $("document").ready(function () {
    //Ajax Calling
    $("#wmq-submit").on("click", function (e) {
      e.preventDefault();
      let inputs = [];
      let inputValue = $("#wmq-quiz-form :input").each(function (
        index,
        indexvalue
      ) {
        const { name, value } = indexvalue;
        inputs.push({ name, value });
      });
      let params = {
        action: "wmq_quiz",
        nonce: wmq_quiz_option.nonce,
        task: inputs,
      };
      $.ajax({
        url: wmq_quiz_option.ajax_url,
        type: "POST",
        data: params,
        success: function (data, status) {
          console.log(data);
          Swal.fire({
            position: "top-end",
            icon: "success",
            title: "Your value has been saved",
            showConfirmButton: false,
            timer: 2500,
          });
        },
        error: function (jqXHR, textStatus, errorThrown) {
          console.log("jqXHR:" + jqXHR);
          console.log("TestStatus: " + textStatus);
          console.log("ErrorThrown: " + errorThrown);
          Swal.fire({
            icon: "error",
            title: "Oops...",
            text: "Something went wrong!",
          });
        },
      });
    });
  });
})(jQuery);
