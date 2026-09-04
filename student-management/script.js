$(document).ready(function () {

    // casher la side bar 
    $('#sidebar').hide();

    // Effet Toggle  de la Navbar
    $('#sidebarCollapse').on('click', function () {

        // Alterne la classe .active qui réduit ou agrandit la sidebar
        $('#sidebar').toggle();
    });
});