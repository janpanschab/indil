/**
 * Create new folder
 */
$('#folder-add').click(function() {
    var name = window.prompt('Vytvořit složku:');
    if (name != null) { // not canceled
        location.href = this.href +'&name='+ name;
    }
    return false;
});

/**
 * Rename folder or file
 */
$('[data-rename]').click(function() {
    var newname = window.prompt('Přejmenovat: ', $(this).data('rename'));
    if (newname !== null) { // not canceled
        location.href = this.href +'&newname='+ newname;
    }
    return false;
});

/**
 * Insert image into content editor
 */
$('#insert-image').click(function() {
    // id, type, [align, [link]]

    var $wrapper = $('#content'),
        id = $(this).attr('href'),
        type = $wrapper.find(':radio[name=type]:checked').val(),
        align = $wrapper.find(':radio[name=align]:checked').val(),
        link = $wrapper.find(':radio[name=link]:checked').val(),
        placeholder;

    if (link === 'lightbox' && align === '=') {
        placeholder = '{{img: '+ id +', '+ type +'}} ';
    } else if (link === 'lightbox') {
        placeholder = '{{img: '+ id +', '+ type +', '+ align +'}} ';
    } else {
        placeholder = '{{img: '+ id +', '+ type +', '+ align +', '+ link +'}} ';
    }
    
    parent.$('.modal').modal('hide');

    parent.$.markItUp({
        replaceWith: placeholder
    });
    return false;
});

/**
 * Insert document into content editor
 */
$('#content').on('click', '.insert-doc', function() {
    var id = $(this).attr('href'),
        placeholder = '{{doc: '+ id +'}} ';
    
    parent.$('.modal').modal('hide');

    parent.$.markItUp({
        replaceWith: placeholder
    });
    return false;
});