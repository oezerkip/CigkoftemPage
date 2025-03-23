// ----------------------------------------------------------------------
// Product-Datenbank
// ----------------------------------------------------------------------
let product_database = [];
function getProductDatabase(callback) {
  $.ajax({
    url: "index.php?action=getFood", // Die URL zum Controller
    type: "GET", // Oder 'POST', falls gewünscht
    dataType: "json", // Erwartete Antwort als JSON
    success: function (response) {
      product_database = response;
      // console.log("Produkt-Datenbank: ", product_database);
      if (typeof callback === "function") {
        callback();
      }
    },
    error: function (xhr, status, error) {
      console.error("Fehler beim Abrufen der Daten:", error);
    },
  });
}
$(document).ready(function () {
  getProductDatabase();
});

// ----------------------------------------------------------------------
// Contest-Datenbank
// ----------------------------------------------------------------------
let contest_database = [];
function getContest(callback) {
  $.ajax({
    url: "index.php?action=getContest", // Die URL zum Controller
    type: "GET", // Oder 'POST', falls gewünscht
    dataType: "json", // Erwartete Antwort als JSON
    success: function (response) {
      contest_database = response;
      console.log("Contest-Datenbank: ", contest_database);
      if (typeof callback === "function") {
        callback();
      }
    },
    error: function (xhr, status, error) {
      console.error("Fehler beim Abrufen der Daten:", error);
    },
  });
}
$(document).ready(function () {
  getContest(function(){
    contestCarousel();
  });
});

// ----------------------------------------------------------------------
// contest-Carousel
// ----------------------------------------------------------------------
function contestCarousel() {
  const owlContest = $(".contest-carousel");
  if (owlContest.length > 0) {
    owlContest.owlCarousel("destroy").empty();
    owlContest.owlCarousel({
      responsive: {
        0: {
          items: 2,
        },
        600: {
          items: 4,
        },
      },
      center: true,
      margin: 10,
      nav: true,
      dots: false,
      autoplay: true,
      autoplayTimeout: 4000,
      autoplayHoverPause: true,
      navText: [
        '<i class="bi bi-chevron-left"></i>',
        '<i class="bi bi-chevron-right"></i>',
      ],
    });
    contest_database.forEach((element) => {
      let item = `
        <div data-contestid="${element.ID}" class="item text-center">
            <a href="${element.pic_url}" class="image" data-fancybox>
                <img src="${element.pic_url}" class="img-fluid rounded" alt="food" />
            </a>
            <a href="" class="like" onclick="raiseRating(event)">
                <i class="bi bi-heart"></i>
                <i class="bi bi-heart-fill"></i>
            </a>
        </div> 
      `;
      owlContest.trigger("add.owl.carousel", [$(item)]);
    });
    owlContest.trigger("refresh.owl.carousel");
  }
}

// ----------------------------------------------------------------------
// Rating für Contest-Item erhöhen (Like-Button)
// ----------------------------------------------------------------------
function raiseRating(event) {
  event.preventDefault(); // Verhindert das Standardverhalten (Link-Klick)
  event.stopPropagation(); // Stoppt das Event, damit der übergeordnete Link nicht klickt
  const contestItem = $(event.target).closest(".item");
  $.ajax({
    type: "GET",
    url: "index.php?action=raiseRating",
    data: {
      ID: $(contestItem).data("contestid"),
    },
    dataType: "html",
    success: function (response) {
      $(".like", contestItem).toggleClass("selected");
      getContest();
    },
  });
}

// ----------------------------------------------------------------------
// Contest-Upload
// ----------------------------------------------------------------------
function uploadContestImage(event) {
  $.ajax({
    type: "GET",
    url: "index.php?action=uploadContestImage",
    data: {
      imagePath: $("#imagePath", event.target).val(),
    },
    dataType: "html",
    success: function (response) {
      $("#uploadContestImageMessage").html(response);
    },
  });
}
$(document).ready(function () {
  $("#uploadContestImage").on("submit", function (event) {
    event.preventDefault();
    uploadContestImage(event);
  });
});

// ----------------------------------------------------------------------
// Hintergrundfarbe für HEADER wenn gescrollt wird
// ----------------------------------------------------------------------
function headerColor() {
  if ($(window).scrollTop() > 0) {
    $("header").addClass("bg-body");
  } else {
    $("header").removeClass("bg-body");
  }
}
$(document).ready(function () {
  $(window).on("scroll", function () {
    headerColor();
  });
  headerColor();
});

// ----------------------------------------------------------------------
// Food-Carousel
// ----------------------------------------------------------------------
$(document).ready(function () {
  $(".food-carousel").owlCarousel({
    responsive: {
      0: {
        items: 2,
      },
      600: {
        items: 4,
      },
    },
    loop: true,
    center: true,
    margin: 10,
    nav: true,
    dots: false,
    autoplay: true,
    autoplayTimeout: 4000,
    autoplayHoverPause: true,
    navText: [
      '<i class="bi bi-chevron-left"></i>',
      '<i class="bi bi-chevron-right"></i>',
    ],
  });
});

// ----------------------------------------------------------------------
// food-menu
// ----------------------------------------------------------------------
$(document).ready(function () {
  let buttons = $("#foodfilter .btn");
  let items = $("#foodlist .item");
  buttons.click(function () {
    let category = $(this).attr("category");
    buttons.removeClass("active");
    $(this).addClass("active");
    items.removeClass("active");
    if (category === "all") {
      items.addClass("active");
    } else {
      items.filter(`[category="${category}"]`).addClass("active");
    }
  });
});

// ----------------------------------------------------------------------
// food-modal
// ----------------------------------------------------------------------
const myModalEl = document.getElementById("foodmodal");
foodmodal.addEventListener("show.bs.modal", (event) => {
  const food = $(event.relatedTarget);
  $(".modal-title", foodmodal).html($(".title", food).text());
  $(".modal-image", foodmodal).attr("src", $(".image img", food).attr("src"));
  $(".modal-description", foodmodal).html($(".description", food).text());
  $(".modal-price", foodmodal).html($(".price", food).text());
  $(".modal-additives", foodmodal).html($(".additives", food).text());
  $(".modal-calories", foodmodal).html($(".calories", food).text());
  $(".add-to-cart", foodmodal).attr("ean", food.attr("ean"));
});

// ----------------------------------------------------------------------
// Back To Top Button
// ----------------------------------------------------------------------
$(document).ready(function () {
  const backToTopButton = $("#backToTop");

  $(window).scroll(function () {
    if ($(this).scrollTop() > 300) {
      backToTopButton.fadeIn();
    } else {
      backToTopButton.fadeOut();
    }
  });
});

// ----------------------------------------------------------------------
// Fancy-Box
// ----------------------------------------------------------------------
Fancybox.bind("[data-fancybox]", {
  // Your custom options
});
