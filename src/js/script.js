// ----------------------------------------------------------------------
// Product-Datenbank
// ----------------------------------------------------------------------
let product_database = [];
function updateProductDatabase() {
  $.getJSON("src/pseudo-articles.json", function (data) {
    product_database = data;
    console.log("Produkt-Datenbank: ", product_database);
  });
}
$(document).ready(function () {
  // Produkte aus JSON laden
  updateProductDatabase();
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
// like-button
// ----------------------------------------------------------------------
$(document).ready(function () {
  $(".like").click(function (event) {
    event.preventDefault(); // Verhindert das Standardverhalten (Link-Klick)
    event.stopPropagation(); // Stoppt das Event, damit der übergeordnete Link nicht klickt
    $(this).toggleClass("selected");
  });
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
// Shopping Cart
// ----------------------------------------------------------------------
const cart = [];

// Produkt in Warenkorb hinzufügen
function addToCart(ean) {
  const product = product_database.find((p) => p.ean === ean);
  if (!product) return;

  const item = cart.find((item) => item.ean === product.ean);
  if (item) {
    item.quantity++;
  } else {
    cart.push({ ...product, quantity: 1 });
  }
  updateCart();
}

// Produkt aus Warenkorb entfernen
function removeFromCart(ean) {
  console.log(ean);
  const item = cart.find((item) => item.ean === ean);
  if (item) {
    if (item.quantity > 1) {
      item.quantity--;
    } else {
      const index = cart.findIndex((item) => item.ean === ean);
      if (index !== -1) {
        cart.splice(index, 1);
      }
    }
  }
  updateCart();
}

// Produkt-Anzahl aktualisieren
function updateQuantity(ean, quantity) {
  const index = cart.findIndex((item) => item.ean === ean);
  if (index !== -1) {
    if (quantity > 0) {
      cart[index].quantity = quantity; // Menge aktualisieren
    } else {
      cart.splice(index, 1); // Produkt aus Warenkorb entfernen
    }
  }
  updateCart(); // Warenkorb neu rendern
}

// Anzahl aller Produkte im Warenkorb (badge)
function getTotalQuantity() {
  return cart.reduce((total, item) => total + item.quantity, 0);
}

// Gesamt-Preis aller Produkte im Warenkorb
function getTotalPrice() {
  return cart
    .reduce((total, item) => {
      const price = parseFloat(item.price.replace(",", ".")); // Umwandlung für Berechnung
      return total + price * item.quantity;
    }, 0)
    .toFixed(2)
    .replace(".", ","); // Zurück zu Komma für Ausgabe
}

// Warenkorb aktualisieren
function updateCart() {
  const totalQuantity = getTotalQuantity();
  const totalPrice = getTotalPrice();
  if (totalQuantity > 0) {
    $("#shoppingcart_button").removeClass("disabled");
    $("#shoppingcart_counter").text(totalQuantity);
  } else {
    $("#shoppingcart_button").addClass("disabled");
    $("#shoppingcart_counter").text("");
  }
  $("#shoppingcart").empty();
  $("#shoppingcart").append(`
    <thead>
      <tr>
        <th><span>Nr.</span></th>
        <th><span>Name</span></th>
        <th><span>Preis</span></th>
        <th><span>Menge</span></th>
      </tr>
    </thead>
  `);
  $("#shoppingcart").append('<tbody class="table-group-divider">');
  cart.forEach((item) => {
    const iconClass = item.quantity === 1 ? "bi-x" : "bi-dash";
    $("#shoppingcart").append(`
      <tr>
        <td><span>${item.ean}</span></td>
        <td><span>${item.name}</span></td>
        <td><span>${item.price} €</span></td>
        <td><span>${item.quantity}</span></td>
        <td class="text-end">
          <div class="btn-group">
            <button class="btn btn-outline-danger" onclick="updateQuantity('${
              item.ean
            }', ${item.quantity - 1})">
              <i class="bi ${iconClass}"></i>
            </button>
            <button class="btn btn-outline-success bi-plus" onclick="updateQuantity('${
              item.ean
            }', ${item.quantity + 1})">
              <i class="bi "></i>
            </button>
          </div>
        </td>
      </tr>
    `);
  });
  $("#shoppingcart").append("</tbody>");
  $("#shoppingcart").append(`
    <tfoot>
      <tr class="border-top">
        <th colspan="2"><b>Summe: </b></th>
        <td colspan="3"><b>${totalPrice} €</b></td>
      </tr>
    </tfoot>  
  `);
}

// Buttons für das Hinzufügen von Produkten in den Warenkorb
$(document).ready(function () {
  $(".add-to-cart").click(function () {
    addToCart($(this).attr("ean"));
  });
});

// Bestellung senden mit WhatApp
$(document).ready(function () {
  const MODAL_SHOPPINGCART = $("#modal_shoppingcart");
  $("form", MODAL_SHOPPINGCART).on("submit", function (event) {
    event.preventDefault();
    if (cart.length <= 0) {
      return alert("Mindestbestellwert noch nicht erreicht!");
    }
    let fullname = $("input[name=fullname]", MODAL_SHOPPINGCART).val();
    let fulladdress = $("input[name=fulladdress]", MODAL_SHOPPINGCART).val();
    let text = "*Bestellung:*";
    text += `\n`;
    text += `\n`;
    cart.forEach((element) => {
      text += `[${element.ean}] `;
      text += `${element.quantity}x `;
      text += `${element.name}`;
      text += `\n`;
      text += `\n`;
    });
    text += `*Kunde:*`;
    text += `\n`;
    text += `\n`;
    text += `${fullname}\n`;
    text += `${fulladdress}`;
    let tel = "4915228807319";
    let url = `https://wa.me/${tel}?text=${encodeURIComponent(text)}`;
    console.log(text);
    window.open(url, "_blank");
  });
});

// ----------------------------------------------------------------------
// Admin-Menu
// ----------------------------------------------------------------------
$(document).ready(function () {
  const MENU_LINKS = $("#admin_menu a");
  const CONTENT_CONTAINER = $("#admin_content");

  MENU_LINKS.click(function (e) {
    e.preventDefault();
    let content = $(this).data("content"); // Name aus data-content holen
    CONTENT_CONTAINER.load("index.php?action=loadContent&content=" + content);
  });
});

// ----------------------------------------------------------------------
// Kundenverwaltung
// ----------------------------------------------------------------------
function searchCustomer(event) {
  event.preventDefault(); // Verhindert das Absenden des Formulars

  let searchMail = $("#searchEmail").val().trim();
  console.log("Suche nach:", searchMail);

  // JSON-Datei laden
  $.getJSON("src/pseudo-customer.json", function (data) {
    let customer = data.find((c) => c.email === searchMail);
    console.log("Kunde: ", customer);

    if (customer) {
      // Alle Inputs im #showCustomer-Container durchgehen
      $("#showCustomer input").each(function () {
        let fieldName = $(this).attr("name"); // Name-Attribut des Inputs holen

        // Falls das JSON-Objekt diesen Schlüssel hat, setzen wir den Wert
        if (customer.hasOwnProperty(fieldName)) {
          $(this).val(customer[fieldName]);
        }
      });
      $("#showCustomer button[type=submit]").attr("data-id", customer.id);
    } else {
      alert("Kein Kunde mit dieser E-Mail gefunden.");
    }
  }).fail(function () {
    alert("Fehler beim Laden der Kundendaten.");
  });
}

// ----------------------------------------------------------------------
// Food-Manager
// ----------------------------------------------------------------------
function updateFoodSelect() {
  const SELECT = $("select[name=productSelect]");
  const SELECT_VALUE = SELECT.val();
  SELECT.html('<option value="newProduct">Neuer Artikel</option>');
  product_database.forEach((product) => {
    if (product.ean === SELECT_VALUE) {
      SELECT.append(
        `<option value="${product.ean}" selected>${product.name}</option>`
      );
    } else {
      SELECT.append(`<option value="${product.ean}">${product.name}</option>`);
    }
  });
}

function selectFood() {
  const button_addArticle = $("#addArticle");
  const button_updateArticle = $("#updateArticle");
  const button_deleteArticle = $("#deleteArticle");
  const FOOD_MASK = $("#articleMask");
  const SELECT_VALUE = $("select[name=productSelect]").val();
  const SELECTED_FOOD = product_database.find(
    (product) => product.ean === SELECT_VALUE
  );
  if (SELECTED_FOOD) {
    $("#ean").val(SELECTED_FOOD.ean);
    $("#category").val(SELECTED_FOOD.category);
    $("#name").val(SELECTED_FOOD.name);
    $("#special").prop("checked", SELECTED_FOOD.special == 1);
    $("#description").val(SELECTED_FOOD.description);
    $("#additives").val(SELECTED_FOOD.additives);
    $("#calories").val(SELECTED_FOOD.calories);
    $("#price").val(SELECTED_FOOD.price);
    $("#image").val(SELECTED_FOOD.image);

    button_addArticle
      .prop("disabled", true)
      .addClass("btn-secondary")
      .removeClass("btn-success");
    button_updateArticle
      .prop("disabled", false)
      .addClass("btn-info")
      .removeClass("btn-secondary");
    button_deleteArticle
      .prop("disabled", false)
      .addClass("btn-danger")
      .removeClass("btn-secondary");
  } else {
    // Leert alle Textinput-, Textarea- und Select-Felder
    FOOD_MASK.find("input, textarea, select").val("");
    // Setzt alle Checkboxen und Radiobuttons zurück
    FOOD_MASK.find("input[type=checkbox], input[type=radio]").prop("checked", false);
    
    button_addArticle
      .prop("disabled", false)
      .addClass("btn-success")
      .removeClass("btn-secondary");
    button_updateArticle
      .prop("disabled", true)
      .addClass("btn-secondary")
      .removeClass("btn-info");
    button_deleteArticle
      .prop("disabled", true)
      .addClass("btn-secondary")
      .removeClass("btn-danger");
  }
}

// ----------------------------------------------------------------------
// Fancy-Box
// ----------------------------------------------------------------------
Fancybox.bind("[data-fancybox]", {
  // Your custom options
});
