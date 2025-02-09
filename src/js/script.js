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
// contest-Carousel
// ----------------------------------------------------------------------
$(document).ready(function () {
  $(".contest-carousel").owlCarousel({
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
// Food-menu
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
// like-button
// ----------------------------------------------------------------------
$(document).ready(function () {
  $(".like").click(function (event) {
    event.preventDefault();  // Verhindert das Standardverhalten (Link-Klick)
    event.stopPropagation(); // Stoppt das Event, damit der übergeordnete Link nicht klickt
    $(this).toggleClass("active");
  });
});
