(function ($) {
  const bakckgroundcolor = $("#mapWrapper").attr("data-backgroundColor");
  const countryColor = $("#mapWrapper").attr("data-country");
  const hoverColor = $("#mapWrapper").attr("data-hover");
  const scoreColor = $("#mapWrapper").attr("data-score");
  $(function () {
    map = new jvm.WorldMap({
      map: "world_mill",
      container: $("#map"),
      zoomOnScroll: false,
      regionStyle: {
        initial: {
          stroke: "black",
          "stroke-width": 0.2,
          fill: countryColor,
        },
        hover: {
          fill: hoverColor,
          "fill-opacity": 1,
        },
        selected: {
          fill: scoreColor,
        },
      },
      backgroundColor: bakckgroundcolor,
      values: null,
      scale: ["#C8EEFF", "#0071A4"],
      series: {
        regions: [
          {
            attribute: "fill",
          },
        ],
      },
    });
  });

  function generateColors(elements) {
    color = {};
    const element = elements.toUpperCase();
    color[element] = scoreColor;
    return color;
  }

  function zoomMapTo(name) {
    var mapObject = $("#map").vectorMap("get", "mapObject");
    //mapObject.setFocus("CN"); // zoom to a specific country using the code
    //mapObject.setFocus(2,0.5,0.5);  // (scale, x, y) where scale = 1,2,3,4,etc. and x/y are values from 0-1
    switch (name) {
      case "africa":
        mapObject.setFocus(2, 0.5, 0.5);
        break;
      case "asia":
        mapObject.setFocus(2.4, 0.85, 0.5);
        break;
      case "europe":
        mapObject.setFocus(4, 0.52, 0.3);
        break;
      case "northamerica":
        mapObject.setFocus(2, 0.1, 0.3);
        break;
      case "southamerica":
        mapObject.setFocus(2.5, 0.18, 0.9);
        break;
      default:
        mapObject.setFocus(1, 0.5, 0.5); //alert('world');
    }
    //mapObject.setFocus("CN"); // zoom to a specific country using the code
  }
  function highlightCountry(code) {
    var mapObject = $("#map").vectorMap("get", "mapObject");
    if (mapObject.series.regions[0].value == undefined) {
      // mapObject.clearSelectedRegions();
      mapObject.setSelectedRegions(generateColors(code));
    } else {
      mapObject.series.regions[0].setValues(generateColors(code));
      // console.log(mapObject.series.regions[0]);
      // console.log(code);

      // console.log(mapObject.series.regions[0]);
      // // map.regions[code].element.config.style.selected.fill = "#333333";
    }
  }

  function highlightMarker(name) {
    name = name.toUpperCase();
    $("#" + name).css("background-color", scoreColor);
    $("#" + name).css("border-color", scoreColor);
  }

  function tryWorldGuess(guess) {
    //var guess = $('#guess').val();
    if (guess.length >= 2) {
      var code = convertCountryNameToJVMCode(guess);
      //convertCountryNameToJVMCode match the country name regex with JVM Code like: India => IN
      if (isValidJVMCountry(code)) {
        //Match the Guess country name to Valid JVM country name
        highlightCountry(code);
        highlightSpecialCases(code); //Special cases are used to merge some JVM Countries which are explicitly not remarkable
        $("#guess").val("");
      } else if (isValidMiniCountry(guess)) {
        //add little countries merge in one and return true, when typing there name, country color will not get change
        highlightMarker(guess.replace(/ /g, "_")); // switch spaces to underscores
        $("#guess").val("");
      }
    }
  }
  var correctanswer = new Array();
  var score = 0;
  calc1 = function () {
    document.getElementById("guess").placeholder = "";
    //value is 196 so, guesses=196
    var guesses = document.form1.numberguesses.value;
    for (i = 1; i <= guesses; i++) {
      var guess = document
        .getElementById("guess")
        .value.toLowerCase()
        .replace(/[^\w\s]/gi, "")
        .replace(/\s/g, "");
      var answer = document
        .getElementById("answer" + i)
        .value.toLowerCase()
        .replace(/[^\w\s]/gi, "")
        .replace(/\s/g, "");
      //196 country name is in answer

      if (guess != "" && guess == answer) {
        // if (document.getElementById("mapquiz")) {
        //   tryGuess();
        // }
        if (document.getElementById("worldmapquiz")) {
          //it will go for country validation and do necessary adjustment if the country value is correct
          tryWorldGuess(answer);
        }
        //alert(answer);

        correctanswer[i] = "correct";
        //for starting with blank
        document.getElementById("show" + i).style.display = "";
        //correct Answer got blue
        document.getElementById("show" + i).style.color = "blue";
        document.getElementById("guess" + i).style.display = "none";

        document.getElementById("guess").value = "";
        score += 1;
        document.getElementById("score").innerHTML =
          "Score: " + score + "/" + guesses;
      } else {
      }

      //if anyone can make all the answers correct
      if (score == guesses) {
        document.getElementById("reloadpage").style.display = "";
        document.getElementById("starttyping").style.display = "none";
        document.getElementById("guess").style.display = "none";
        document.getElementById("showanswersbutton").style.display = "none";
        stopTimer = 1;
      }
    }
  };
  function highlightSpecialCases(code) {
    switch (code) {
      case "CY":
        highlightCountry("_1");
        break; //--> include Northern Cyprus with Cyprus
      case "DK":
        highlightCountry("GL");
        break; //--> include Greenland with Denmark
      case "FR":
        highlightCountry("NC");
        highlightCountry("TF"); //--> include New Caledonia and French Southern and Antartic Lands with France
      case "MA":
        highlightCountry("_2");
        break; //--> include Western Sahara with Morocco
      case "IL":
        highlightCountry("PS");
        break; //--> include West Bank with Israel
      case "SO":
        highlightCountry("_3");
        break; //--> include Somaliland with Somalia
      case "GB":
        highlightCountry("FK");
        break; //--> include Falkland Islands with United Kindom
      case "US":
        highlightCountry("PR");
        break; //--> include Puerto Rico with United States
    }
  }

  function isValidJVMCountry(guess) {
    for (var country in jvm.WorldMap.maps["world_mill"].paths) {
      //if (guess == jvm.WorldMap.maps['world_mill_en'].paths[country].name)
      if (guess == country) return true;
    }
    return false;
  }

  function isValidMiniCountry(guess) {
    var miniCountries = [
      "ANDORRA",
      "ANTIGUA AND BARBUDA",
      "BAHRAIN",
      "BARBADOS",
      "CAPE VERDE",
      "COMOROS",
      "DOMINICA",
      "FEDERATED STATES OF MICRONESIA",
      "GRENADA",
      "KIRIBATI",
      "LIECHTENSTEIN",
      "MALDIVES",
      "MALTA",
      "MARSHALL ISLANDS",
      "MAURITIUS",
      "MONACO",
      "NAURU",
      "PALAU",
      "SAINT KITTS AND NEVIS",
      "SAINT LUCIA",
      "SAINT VINCENT AND THE GRENADINES",
      "SAMOA",
      "SAN MARINO",
      "SAO TOME AND PRINCIPE",
      "SEYCHELLES",
      "SINGAPORE",
      "TONGA",
      "TUVALU",
      "VATICAN CITY",
    ];
    if ($.inArray(guess.toUpperCase(), miniCountries) >= 0) return true;
    else return false;
  }

  function convertJVMCountryCodeToName(code) {
    for (var country in jvm.WorldMap.maps["world_mill"].paths) {
      if (code == country)
        return jvm.WorldMap.maps["world_mill"].paths[country].name;
    }
    return "";
  }

  function convertJVMCountryNameToCode(name) {
    for (var country in jvm.WorldMap.maps["world_mill"].paths) {
      if (
        name.toUpperCase() ==
        String(
          jvm.WorldMap.maps["world_mill"].paths[country].name
        ).toUpperCase()
      )
        return country;
    }
    return "";
  }

  clearWorldMapSelection = function () {
    var mapObject = $("#map").vectorMap("get", "mapObject");
    for (var country in jvm.WorldMap.maps["world_mill"].paths) {
      //var code = "US-" + states[i];
      var json = {};
      json[country] = "white";
      //make all world countries color white
      // mapObject.series.regions[0].setValues(json);
      mapObject.clearSelectedRegions();
      stopTimer = 1;
    }

    var points = document.getElementById("points");
    while (points.firstChild) points.removeChild(points.firstChild);
    $("#chkShowMiniCountries").prop("checked", false);
  };

  function toggleMiniNations() {
    if ($("input[name=chkShowMiniCountries]").is(":checked")) {
      $("#points").css("visibility", "visible");
      drawMiniNations();
    } else {
      $("#points").css("visibility", "hidden");
    }
  }

  function drawMiniNations() {
    // top and left positioning is based on a map size of 950px by 475px
    var data = {
      countries: [
        {
          country: "ANDORRA",
          left: "-455",
          top: "230",
        },
        {
          country: "ANTIGUA AND BARBUDA",
          left: "280",
          top: "305",
        },
        {
          country: "BAHRAIN",
          left: "-570",
          top: "280",
        },
        {
          country: "BARBADOS",
          left: "294",
          top: "318",
        },
        {
          country: "CAPE VERDE",
          left: "387",
          top: "301",
        },
        {
          country: "COMOROS",
          left: "559",
          top: "380",
        },
        {
          country: "DOMINICA",
          left: "287",
          top: "310",
        },
        {
          country: "FEDERATED STATES OF MICRONESIA",
          left: "853",
          top: "338",
        },
        {
          country: "GRENADA",
          left: "284",
          top: "323",
        },
        {
          country: "KIRIBATI",
          left: "887",
          top: "341",
        },
        {
          country: "LIECHTENSTEIN",
          left: "-100",
          top: "100",
        },
        {
          country: "MALDIVES",
          left: "630",
          top: "333",
        },
        {
          country: "MALTA",
          left: "478",
          top: "254",
        },
        {
          country: "MARSHALL ISLANDS",
          left: "873",
          top: "338",
        },
        {
          country: "MAURITIUS",
          left: "589",
          top: "405",
        },
        {
          country: "MONACO",
          left: "-110",
          top: "150",
        },
        {
          country: "NAURU",
          left: "863",
          top: "358",
        },
        {
          country: "PALAU",
          left: "800",
          top: "338",
        },
        {
          country: "SAINT KITTS AND NEVIS",
          left: "273",
          top: "300",
        },
        {
          country: "SAINT LUCIA",
          left: "287",
          top: "320",
        },
        {
          country: "SAINT VINCENT AND THE GRENADINES",
          left: "287",
          top: "324",
        },
        {
          country: "SAMOA",
          left: "936",
          top: "386",
        },
        {
          country: "SAN MARINO",
          left: "-110",
          top: "220",
        },
        {
          country: "SAO TOME AND PRINCIPE",
          left: "459",
          top: "349",
        },
        {
          country: "SEYCHELLES",
          left: "576",
          top: "354",
        },
        {
          country: "SINGAPORE",
          left: "-110",
          top: "250",
        },
        {
          country: "TONGA",
          left: "928",
          top: "398",
        },
        {
          country: "TUVALU",
          left: "882",
          top: "364",
        },
        {
          country: "VATICAN CITY",
          left: "-110",
          top: "280",
        },
      ],
    };

    for (var i in data.countries) {
      if (data.countries[i].left >= 0)
        $("#points").append(
          '<div id="' +
            data.countries[i].country.replace(/ /g, "_") +
            '" class="p" style="position:absolute;left:' +
            data.countries[i].left +
            "px;top:" +
            data.countries[i].top +
            'px">&nbsp;</div>'
        );
    }
  }

  function convertCountryNameToJVMCode(guess) {
    var data = {
      countries: [
        {
          country: "AFGHANISTAN",
          code: "AF",
        },
        {
          country: "ALBANIA",
          code: "AL",
        },
        {
          country: "ALGERIA",
          code: "DZ",
        },
        {
          country: "ANGOLA",
          code: "AO",
        },
        {
          country: "ARGENTINA",
          code: "AR",
        },
        {
          country: "ARMENIA",
          code: "AM",
        },
        {
          country: "AUSTRALIA",
          code: "AU",
        },
        {
          country: "AUSTRIA",
          code: "AT",
        },
        {
          country: "AZERBAIJAN",
          code: "AZ",
        },
        {
          country: "BAHAMAS",
          code: "BS",
        },
        {
          country: "BANGLADESH",
          code: "BD",
        },
        {
          country: "BELARUS",
          code: "BY",
        },
        {
          country: "BELGIUM",
          code: "BE",
        },
        {
          country: "BELIZE",
          code: "BZ",
        },
        {
          country: "BENIN",
          code: "BJ",
        },
        {
          country: "BHUTAN",
          code: "BT",
        },
        {
          country: "BOLIVIA",
          code: "BO",
        },
        {
          country: "BOSNIA AND HERZEGOVINA",
          code: "BA",
        },
        {
          country: "BOTSWANA",
          code: "BW",
        },
        {
          country: "BRAZIL",
          code: "BR",
        },
        {
          country: "BRUNEI",
          code: "BN",
        },
        {
          country: "BULGARIA",
          code: "BG",
        },
        {
          country: "BURKINA FASO",
          code: "BF",
        },
        {
          country: "BURMA",
          code: "MM",
        },
        {
          country: "BURUNDI",
          code: "BI",
        },
        {
          country: "CAMBODIA",
          code: "KH",
        },
        {
          country: "CAMEROON",
          code: "CM",
        },
        {
          country: "CANADA",
          code: "CA",
        },
        {
          country: "CENTRAL AFRICAN REPUBLIC",
          code: "CF",
        },
        {
          country: "CHAD",
          code: "TD",
        },
        {
          country: "CHILE",
          code: "CL",
        },
        {
          country: "CHINA",
          code: "CN",
        },
        {
          country: "COLOMBIA",
          code: "CO",
        },
        {
          country: "COSTA RICA",
          code: "CR",
        },
        {
          country: "COTE DIVOIRE",
          code: "CI",
        },
        {
          country: "CROATIA",
          code: "HR",
        },
        {
          country: "CUBA",
          code: "CU",
        },
        {
          country: "CYPRUS",
          code: "CY",
        },
        {
          country: "CZECH REPUBLIC",
          code: "CZ",
        },
        {
          country: "DEMOCRATIC REPUBLIC OF THE CONGO",
          code: "CD",
        },
        {
          country: "DENMARK",
          code: "DK",
        },
        {
          country: "DJIBOUTI",
          code: "DJ",
        },
        {
          country: "DOMINICAN REPUBLIC",
          code: "DO",
        },
        {
          country: "EAST TIMOR",
          code: "TL",
        },
        {
          country: "ECUADOR",
          code: "EC",
        },
        {
          country: "EGYPT",
          code: "EG",
        },
        {
          country: "EL SALVADOR",
          code: "SV",
        },
        {
          country: "EQUATORIAL GUINEA",
          code: "GQ",
        },
        {
          country: "ERITREA",
          code: "ER",
        },
        {
          country: "ESTONIA",
          code: "EE",
        },
        {
          country: "ETHIOPIA",
          code: "ET",
        },
        {
          country: "FIJI",
          code: "FJ",
        },
        {
          country: "FINLAND",
          code: "FI",
        },
        {
          country: "FRANCE",
          code: "FR",
        },
        {
          country: "GABON",
          code: "GA",
        },
        {
          country: "GAMBIA",
          code: "GM",
        },
        {
          country: "GEORGIA",
          code: "GE",
        },
        {
          country: "GERMANY",
          code: "DE",
        },
        {
          country: "GHANA",
          code: "GH",
        },
        {
          country: "GREECE",
          code: "GR",
        },
        {
          country: "GUATEMALA",
          code: "GT",
        },
        {
          country: "GUINEA",
          code: "GN",
        },
        {
          country: "GUINEABISSAU",
          code: "GW",
        },
        {
          country: "GUYANA",
          code: "GY",
        },
        {
          country: "HAITI",
          code: "HT",
        },
        {
          country: "HONDURAS",
          code: "HN",
        },
        {
          country: "HUNGARY",
          code: "HU",
        },
        {
          country: "ICELAND",
          code: "IS",
        },
        {
          country: "INDIA",
          code: "IN",
        },
        {
          country: "INDONESIA",
          code: "ID",
        },
        {
          country: "IRAN",
          code: "IR",
        },
        {
          country: "IRAQ",
          code: "IQ",
        },
        {
          country: "IRELAND",
          code: "IE",
        },
        {
          country: "ISRAEL",
          code: "IL",
        },
        {
          country: "ITALY",
          code: "IT",
        },
        {
          country: "JAMAICA",
          code: "JM",
        },
        {
          country: "JAPAN",
          code: "JP",
        },
        {
          country: "JORDAN",
          code: "JO",
        },
        {
          country: "KAZAKHSTAN",
          code: "KZ",
        },
        {
          country: "KENYA",
          code: "KE",
        },
        {
          country: "KOSOVO",
          code: "_1",
        },
        {
          country: "KUWAIT",
          code: "KW",
        },
        {
          country: "KYRGYZSTAN",
          code: "KG",
        },
        {
          country: "LAOS",
          code: "LA",
        },
        {
          country: "LATVIA",
          code: "LV",
        },
        {
          country: "LEBANON",
          code: "LB",
        },
        {
          country: "LESOTHO",
          code: "LS",
        },
        {
          country: "LIBERIA",
          code: "LR",
        },
        {
          country: "LIBYA",
          code: "LY",
        },
        {
          country: "LITHUANIA",
          code: "LT",
        },
        {
          country: "LUXEMBOURG",
          code: "LU",
        },
        {
          country: "MACEDONIA",
          code: "MK",
        },
        {
          country: "MADAGASCAR",
          code: "MG",
        },
        {
          country: "MALAWI",
          code: "MW",
        },
        {
          country: "MALAYSIA",
          code: "MY",
        },
        {
          country: "MALI",
          code: "ML",
        },
        {
          country: "MAURITANIA",
          code: "MR",
        },
        {
          country: "MEXICO",
          code: "MX",
        },
        {
          country: "MOLDOVA",
          code: "MD",
        },
        {
          country: "MONGOLIA",
          code: "MN",
        },
        {
          country: "MONTENEGRO",
          code: "ME",
        },
        {
          country: "MOROCCO",
          code: "MA",
        },
        {
          country: "MOZAMBIQUE",
          code: "MZ",
        },
        {
          country: "NAMIBIA",
          code: "NA",
        },
        {
          country: "NEPAL",
          code: "NP",
        },
        {
          country: "NETHERLANDS",
          code: "NL",
        },
        {
          country: "NEW ZEALAND",
          code: "NZ",
        },
        {
          country: "NICARAGUA",
          code: "NI",
        },
        {
          country: "NIGER",
          code: "NE",
        },
        {
          country: "NIGERIA",
          code: "NG",
        },
        {
          country: "NORTH KOREA",
          code: "KP",
        },
        {
          country: "NORWAY",
          code: "NO",
        },
        {
          country: "OMAN",
          code: "OM",
        },
        {
          country: "PAKISTAN",
          code: "PK",
        },
        {
          country: "PANAMA",
          code: "PA",
        },
        {
          country: "PAPUA NEW GUINEA",
          code: "PG",
        },
        {
          country: "PARAGUAY",
          code: "PY",
        },
        {
          country: "PERU",
          code: "PE",
        },
        {
          country: "PHILIPPINES",
          code: "PH",
        },
        {
          country: "POLAND",
          code: "PL",
        },
        {
          country: "PORTUGAL",
          code: "PT",
        },
        {
          country: "QATAR",
          code: "QA",
        },
        {
          country: "REPUBLIC OF THE CONGO",
          code: "CG",
        },
        {
          country: "ROMANIA",
          code: "RO",
        },
        {
          country: "RUSSIA",
          code: "RU",
        },
        {
          country: "RWANDA",
          code: "RW",
        },
        {
          country: "SAUDI ARABIA",
          code: "SA",
        },
        {
          country: "SENEGAL",
          code: "SN",
        },
        {
          country: "SERBIA",
          code: "RS",
        },
        {
          country: "SIERRA LEONE",
          code: "SL",
        },
        {
          country: "SLOVAKIA",
          code: "SK",
        },
        {
          country: "SLOVENIA",
          code: "SI",
        },
        {
          country: "SOLOMON ISLANDS",
          code: "SB",
        },
        {
          country: "SOMALIA",
          code: "SO",
        },
        {
          country: "SOUTH AFRICA",
          code: "ZA",
        },
        {
          country: "SOUTH KOREA",
          code: "KR",
        },
        {
          country: "SOUTH SUDAN",
          code: "SS",
        },
        {
          country: "SPAIN",
          code: "ES",
        },
        {
          country: "SRI LANKA",
          code: "LK",
        },
        {
          country: "SUDAN",
          code: "SD",
        },
        {
          country: "SURINAME",
          code: "SR",
        },
        {
          country: "SWAZILAND",
          code: "SZ",
        },
        {
          country: "SWEDEN",
          code: "SE",
        },
        {
          country: "SWITZERLAND",
          code: "CH",
        },
        {
          country: "SYRIA",
          code: "SY",
        },
        {
          country: "TAIWAN",
          code: "TW",
        },
        {
          country: "TAJIKISTAN",
          code: "TJ",
        },
        {
          country: "TANZANIA",
          code: "TZ",
        },
        {
          country: "THAILAND",
          code: "TH",
        },
        {
          country: "TOGO",
          code: "TG",
        },
        {
          country: "TRINIDAD AND TOBAGO",
          code: "TT",
        },
        {
          country: "TUNISIA",
          code: "TN",
        },
        {
          country: "TURKEY",
          code: "TR",
        },
        {
          country: "TURKMENISTAN",
          code: "TM",
        },
        {
          country: "UGANDA",
          code: "UG",
        },
        {
          country: "UKRAINE",
          code: "UA",
        },
        {
          country: "UNITED ARAB EMIRATES",
          code: "AE",
        },
        {
          country: "UNITED KINGDOM",
          code: "GB",
        },
        {
          country: "UNITED STATES",
          code: "US",
        },
        {
          country: "URUGUAY",
          code: "UY",
        },
        {
          country: "UZBEKISTAN",
          code: "UZ",
        },
        {
          country: "VANUATU",
          code: "VU",
        },
        {
          country: "VENEZUELA",
          code: "VE",
        },
        {
          country: "VIETNAM",
          code: "VN",
        },
        {
          country: "YEMEN",
          code: "YE",
        },
        {
          country: "ZAMBIA",
          code: "ZM",
        },
        {
          country: "ZIMBABWE",
          code: "ZW",
        },
      ],
    };
    for (var i in data.countries) {
      //alert(data.countries[i].Country);
      if (data.countries[i].country.replace(/\s/g, "") == guess.toUpperCase()) {
        // alert("Matched " + guess + " to " + data.countries[i].code);
        return data.countries[i].code;
      }
    }
    return "wrong";
  }
})(jQuery);
