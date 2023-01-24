(function ($) {
  $(document).ready(function () {
    // document.getElementById("guess").focus();
    startingtimer();
    calc1();

    $(window).keydown(function (event) {
      if (event.keyCode == 13) {
        event.preventDefault();
        return false;
      }
    });
    if ($(document).height() < 1000) {
      $(".navbar").removeAttr("data-spy");
    }
    $("table").click(function () {
      $("#guess").focus();
    });
  });

  var counter = setInterval(timer, 1000); //1000 will run it every 1 second
  var stopTimer = 0;
  var count = 180;

  function startingtimer() {
    var guesses = document.form1.numberguesses.value;
    document.getElementById("score").innerHTML = "Score: 0/" + guesses;
    count = document.getElementById("quiztime").value;

    if (count == 0) {
      count = 100000000;
      stopTimer = 2;
      document.getElementById("timer").innerHTML = "";
      document.getElementById("notimer").style.display = "none";
    }
    timer();
  }

  stopInputTime = function () {
    stopTimer = 2;
    document.getElementById("notimer").style.display = "none";
  };

  function timer() {
    if ((count <= 0) | (stopTimer == 1) | (stopTimer == 2)) {
      if (count <= 0) {
        document.getElementById("timer").innerHTML = "Time!";
        showallanswers();
        return;
      } else if (stopTimer == 1) {
        showallanswers();
        return;
      } else if (stopTimer == 2) {
        //it means stop the timer
        document.getElementById("timer").innerHTML = "";
        return;
      }
    } else {
      count = count - 1;
    }

    // makes it min:second format

    var minutes = 0;
    seconds = count;
    while (seconds >= 60) {
      minutes += 1;
      seconds -= 60;
    }
    if (seconds < 10) {
      //when second is less than 10, need a 0 before the number
      seconds = "0" + seconds;
    }

    document.getElementById("timer").innerHTML = minutes + ":" + seconds;
  }

  var correctanswer = new Array();
  var score = 0;

  showallanswers = function () {
    document.getElementById("reloadpage").style.display = "";
    document.getElementById("starttyping").style.display = "none";
    document.getElementById("showanswersbutton").style.display = "none";
    var guesses = document.form1.numberguesses.value;
    for (n = 1; n <= guesses; n++) {
      //display '' means for making display:none it will not be seen
      document.getElementById("show" + n).style.display = "";
    }
    stopTimer = 1;
  };

  reloadedpage = function () {
    if (document.getElementById("mapquiz")) {
      clearUSMapSelection();
    }

    if (document.getElementById("worldmapquiz")) {
      clearWorldMapSelection();
    }

    var guesses = document.form1.numberguesses.value;

    document.getElementById("showanswersbutton").style.display = "";
    document.getElementById("starttyping").style.display = "";
    document.getElementById("reloadpage").style.display = "none";
    document.getElementById("guess").style.display = "";
    document.getElementById("guess").placeholder = "type answers here";
    document.getElementById("guess").focus();

    score = 0;
    document.getElementById("score").innerHTML =
      "Score: " + score + "/" + guesses;
    for (i = 1; i <= guesses; i++) {
      correctanswer[i] = "notcorrect";
      document.getElementById("guess" + i).style.display = "";
      document.getElementById("show" + i).style.display = "none";
      document.getElementById("show" + i).style.color = "red";
    }

    document.getElementById("notimer").style.display = "";

    count = document.getElementById("quiztime").value;

    stopTimer = 0;
    startingtimer();
  };
})(jQuery);
