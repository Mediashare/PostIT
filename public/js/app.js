// HighLight Synthax
document.querySelectorAll('.markdown-body pre code').forEach((block) => {
    hljs.highlightBlock(block);
});

// Avatar upload
$("#file").change(function() {
    file = this.files[0];
    filename = file.name
    $('#filename').html(filename);
    var oFReader = new FileReader();
    oFReader.readAsDataURL(file);
    oFReader.onload = function (oFREvent) {$('#image_preview').attr('src', oFREvent.target.result)};
});

// Verify Password
var password = $('form#edit_profile input#inputPassword');
    passwordRepeat = $('form#edit_profile input#inputPasswordRepeat');

password.on('change', function () {
    if (password.val().length > 0) {
        password.attr('required', 'true');
        passwordRepeat.attr('required', 'true');
    } else if (passwordRepeat.val().length < 1) {
        password.removeAttr('required');
        passwordRepeat.removeAttr('required');
    }        
});
passwordRepeat.on('change', function () {
    console.log(passwordRepeat.val().length);
    if (passwordRepeat.val().length > 0) {
        passwordRepeat.attr('required', 'true');
        password.attr('required', 'true');
    } else if (password.val().length < 1) {
        password.removeAttr('required');
        passwordRepeat.removeAttr('required');
    } 
});

// Menu
function scrollToAnchor(id){
    var Tag = $("a[name='"+ id +"']");
    $('ul#menu_posts').animate({scrollTop: Tag.offset().top - 50},'slow');
}
function searchDisplay() {
    var input = $('#menu_search');
    if (input.attr('data-displayed') == "true") {
        $('#menu_header > a').toggle(500);
        input.attr('data-displayed', "false").addClass('d-none');
        $('.btn-search').removeClass('active');
        input.find('input').val('');
        search();
    } else {
        $('#menu_header > a').toggle(500);
        $('.btn-search').addClass('active');
        input.attr('data-displayed', "true").removeClass('d-none');
        input.find('input').focus();
    }
}
function search() {
    // Declare variables
    var input, filter, ul, li, a, i, txtValue;
    input = document.getElementById('input_search');
    filter = input.value.toUpperCase();
    ul = document.getElementById("menu_posts");
    link = ul.getElementsByTagName('a');

    // Loop through all list items, and hide those who don't match the search query
    for (i = 0; i < link.length; i++) {
        txtValue = link[i].textContent || link[i].innerText;
        if (txtValue.toUpperCase().indexOf(filter) > -1) {
            link[i].style.display = "";
        } else {
            link[i].style.display = "none";
        }
    }
}
function openMenuBurger() {
    $('div#menu_header').css('display', 'flex');
    $('div#menu_header').removeClass('d-none');
    $('div#menu_footer').css('display', 'flex');
    $('div#menu_footer').removeClass('d-none');
    $('ul#menu_posts').css('display', 'flex');
    $('ul#menu_posts').removeClass('d-none');
    $('button.btn-burger-open').addClass('d-none');
    $('button.btn-burger-close').removeClass('d-none');
}
function closeMenuBurger() {
    $('div#menu_header').removeClass('d-flex');
    $('div#menu_header').css('display', 'none');
    $('div#menu_footer').removeClass('d-flex');
    $('div#menu_footer').css('display', 'none');
    $('ul#menu_posts').addClass('d-none');
    $('button.btn-burger-close').addClass('d-none');
    $('button.btn-burger-open').removeClass('d-none');
}