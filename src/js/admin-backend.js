// ----------------------------------------------------------------------
// Contest-Datenbank (für Admin)
// ----------------------------------------------------------------------
let contest_database_admin = [];
function getContestForAdmin() {
  $.ajax({
    url: "index.php?action=getContestForAdmin", // Die URL zum Controller
    type: "GET", // Oder 'POST', falls gewünscht
    dataType: "json", // Erwartete Antwort als JSON
    success: function (response) {
      contest_database_admin = response;
      console.log("Contest-Datenbank für Admin: ", contest_database_admin);
    },
    error: function (xhr, status, error) {
      console.error("Fehler beim Abrufen der Daten:", error);
    },
  });
}
$(document).ready(function () {
  getContestForAdmin();
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
// Gerichte-Verwaltung
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
      SELECT.append(
        `<option value="${product.ean}">${product.ean} - ${product.name}</option>`
      );
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
    $("#productID").val(SELECTED_FOOD.ID);
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
    FOOD_MASK.find("input[type=checkbox], input[type=radio]").prop(
      "checked",
      false
    );

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

// Gericht zu Datenbank hinzufügen
function addArticleToDB(event) {
  event.preventDefault();
  $.ajax({
    type: "GET",
    url: "index.php?action=addArticleToDB",
    data: {
      ean: $("#ean").val(),
      category: $("#category").val(),
      name: $("#name").val(),
      special: $("#special").prop("checked"),
      description: $("#description").val(),
      additives: $("#additives").val(),
      calories: $("#calories").val(),
      price: $("#price").val(),
      image: $("#image").val(),
    },
    dataType: "html",
    success: function (response) {
      alert("Artikel wurde hinzugefügt!");
      $("#foodManager")[0].reset();
      getProductDatabase();
      selectFood();
    },
    error: function (xhr, status, error) {
      console.error("Fehler:", status, error);
    },
  });
}

// Gericht in Datenbank aktualisieren
function updateArticleAtDB(event) {
  event.preventDefault();
  $.ajax({
    type: "GET",
    url: "index.php?action=updateArticleAtDB",
    data: {
      productID: $("#productID").val(),
      ean: $("#ean").val(),
      category: $("#category").val(),
      name: $("#name").val(),
      special: $("#special").prop("checked"),
      description: $("#description").val(),
      additives: $("#additives").val(),
      calories: $("#calories").val(),
      price: $("#price").val(),
      image: $("#image").val(),
    },
    dataType: "html",
    success: function (response) {
      alert("Artikel wurde aktualisiert!");
      $("#foodManager")[0].reset();
      console.log(response);
      getProductDatabase();
      selectFood();
    },
    error: function (xhr, status, error) {
      console.error("Fehler:", status, error);
    },
  });
}

// Gericht aus Datenbank löschen
function deleteArticleFromDB(event) {
  event.preventDefault();
  $.ajax({
    type: "GET",
    url: "index.php?action=deleteArticleFromDB",
    data: {
      ean: $("#ean").val(),
    },
    dataType: "html",
    success: function (response) {
      alert("Artikel wurde entfernt!");
      $("#foodManager")[0].reset();
      console.log(response);
      getProductDatabase();
      selectFood();
    },
    error: function (xhr, status, error) {
      console.error("Fehler:", status, error);
    },
  });
}

// ----------------------------------------------------------------------
// Kunden-Verwaltung
// ----------------------------------------------------------------------

// Kunde suchen
function getCustomerFromDB(event) {
  event.preventDefault(); // Verhindert das Absenden des Formulars
  $.ajax({
    type: "GET",
    url: "index.php?action=getCustomerFromDB",
    data: {
      searchMail: $("#searchEmail").val().trim(),
    },
    dataType: "json",
    success: function (response) {
      console.log(response); // Überprüfe die Antwort im Browser-Log

      // Setze die Felder mit den Werten aus der Antwort
      if (response && response.name) {
        $("#vorname").val(response.name);
        $("#nachname").val(response.surname);
        $("#email").val(response.e_mail);
        $("#strasse").val(response.street);
        $("#hausnummer").val(response.postal_code); // Falls dies die Hausnummer ist
        $("#plz").val(response.postal_code); // Falls dies der PLZ-Wert ist
        $("#wohnort").val(response.city);
      }
    },
    error: function (xhr, status, error) {
      console.error("Fehler:", status, error);
    },
  });
}

// Kunde löschen
function deleteCustomerFromDB(event) {
  event.preventDefault(); // Verhindert das Absenden des Formulars
  $.ajax({
    type: "GET",
    url: "index.php?action=deleteCustomerFromDB",
    data: {
      customerEmail: $("#email").val().trim(),
    },
    dataType: "html",
    success: function (response) {
      alert("Kunde wurde entfernt!");
      console.log(response);
      $("#searchCustomer")[0].reset();
      $("#showCustomer")[0].reset();
    },
    error: function (xhr, status, error) {
      console.error("Fehler:", status, error);
    },
  });
}

// ----------------------------------------------------------------------
// Konto-Verwaltung
// ----------------------------------------------------------------------

// Admin-Passwort ändern
function updateAdminPassword(event) {
  event.preventDefault(); // Verhindert das Absenden des Formulars
  $.ajax({
    type: "GET",
    url: "index.php?action=updateAdminPassword",
    data: {
      currentPassword: $("#currentPassword").val().trim(),
      newPassword: $("#newPassword").val().trim(),
      newPasswordRepeat: $("#newPasswordRepeat").val().trim(),
    },
    dataType: "html",
    success: function (response) {
      $("#formMessage").html(response);
      $("#adminAccount")[0].reset();
    },
    error: function (xhr, status, error) {
      console.error("Fehler:", status, error);
    },
  });
}

// ----------------------------------------------------------------------
// Contest-Verwaltung
// ----------------------------------------------------------------------

// Zeige alle Contest Items ( ACHTUNG! ===================================================> Hier muss noch das permission geprüft und der Button für accept gesetzt werden)
function getContestItems() {
  const contestForm = $("#contestForm");
  contestForm.html();
  contest_database_admin.forEach((element) => {
    contestForm.append(`
      <div class="row mb-3 justify-content-center">
          <div class="col">
              <input type="text" id="email_${element.ID}" class="form-control" value="${element.email}">
          </div>
          <div class="col">
              <input type="text" id="image_${element.ID}" class="form-control" value="${element.pic_url}">
          </div>
          <div class="col-auto">
              <div class="btn-group" role="group" aria-label="Basic example">
                  <a class="btn btn-info" href="${element.pic_url}" data-fancybox><i class="bi bi-eye"></i></a>
                  <a class="btn btn-success"><i class="bi bi-check-lg"></i></a>
                  <a class="btn btn-danger"><i class="bi bi-trash3"></i></a>
              </div>
          </div>
      </div>
    `);
  });
}

// Function & Logik für -> acceptContestItem()
//...

// Function für -> deleteContestItem()
//... 