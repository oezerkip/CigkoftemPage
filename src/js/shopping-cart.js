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
