// ----------------------------------------------------------------------
// Admin-Menu
// ----------------------------------------------------------------------
$(document).ready(function(){
    const MENU_LINKS = $('#admin_menu a');
    const CONTENT_CONTAINER = $('#admin_content');

    MENU_LINKS.click(function (e) { 
        e.preventDefault();
        let content = $(this).data('content'); // Name aus data-content holen
        CONTENT_CONTAINER.load('index.php?action=loadContent&content=' + content);
    });
});
