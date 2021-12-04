// Avatar upload
$("#file").change(function() {
    $('#file_error').html('');
    file = this.files[0];
    if(file.size > 5242880){
        $('#file_error').append('La taille de votre image dépace la limite autorisé de 2 Mo.')
        this.value = "";
    } else {
        filename = file.name
        $('#filename').html(filename);
        var oFReader = new FileReader();
        oFReader.readAsDataURL(file);
        oFReader.onload = function (oFREvent) {$('#image_preview').attr('src', oFREvent.target.result)};
    }
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
var current = $('ul#menu_posts a.post_link.active');
if (current.length > 0) {
    $(window).bind('keydown', function(event) {
        if (event.shiftKey && (event.ctrlKey || event.metaKey)) {
            if (event.which === 38) {
                $(current).prev()[0].click();
            } else if (event.which === 40) {
                $(current).next()[0].click();
            }
        }
    });
}
function scrollToAnchor(id){
    var Tag = $("a[name='"+ id +"']");
    $('ul#menu_posts').animate({scrollTop: Tag.offset().top - 50});
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

// Modal for delete
function modalDelete(id, url) {
    $('#' + id + ' a.delete').attr('href', url);
    $('#' + id).modal();
}

// Menu dropdown
function newPost() {
    if ($('#new_post').hasClass('active')) {
        $('#new_post').removeClass('active');
    } else { $('#new_post').addClass('active'); }
    $('#new_post_dropdown').toggle('show');
}

// Post Link
function getLinkMetadata() {
    var url = $('#input_url').val();
    if (validURL(url)) {
        $.post(api_link_metadata, {'url': url}, function (data) {
            if (data.status === 'success') {
                if (data.link.title)
                    $('#title').html(data.link.title);
                    if ($('#input_title').val().length < 1)
                        $('#input_title').val(data.link.title);
                if (data.link.description)
                    console.log(data.link.description)
                    if ($('#description').length < 1) {
                        $('.card_link').append('<div id="description" class="card-body"><p class="text-white">'+ data.link.description +'</p></div>');
                    } else {
                        console.log('hello');
                        $('#description p').html(data.link.description);
                    }
                if (data.link.image)
                    if ($('#image').length < 1) {
                        $('.card_link').append('<div id="image" class="card-footer"><img class="d-flex mx-auto" src="'+ data.link.image +'"></div>');
                    } else {
                        $('#image img').attr('src', data.link.image);
                    }
            }
        });
    }
}

function validURL(str) {
    var pattern = new RegExp('^(https?:\\/\\/)?'+ // protocol
        '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|'+ // domain name
        '((\\d{1,3}\\.){3}\\d{1,3}))'+ // OR ip (v4) address
        '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*'+ // port and path
        '(\\?[;&a-z\\d%_.~+=-]*)?'+ // query string
        '(\\#[-a-z\\d_]*)?$','i'); // fragment locator
    return !!pattern.test(str);
}