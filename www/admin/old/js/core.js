window.log = function(){
    log.history = log.history || [];
    log.history.push(arguments);
    if(this.console){
        console.log( Array.prototype.slice.call(arguments) );
    }
};

jQuery(function($) {
  
    $.cms = {}
  
    /* ---------------------------------------------------------------- 
  	vertikální centrování
  ---------------------------------------------------------------- */
    $.fn.verticalCenter = function() {
        return this.each(function() {
            function center(el) {
                var box = $(el);
                var mTop = ($(window).height() - box.height()) / 2;
                box.css('margin-top', mTop +'px');
            };
            var element = this;
            $(document).bind('ready', function() {
                center(element);
            });
            $(window).bind('resize', function() {
                center(element);
            });
        });
    }
  
    $('#access').verticalCenter();
  
    /* ---------------------------------------------------------------- 
  	pruhované tabulky
  ---------------------------------------------------------------- */
    $('table.grid tr:odd').addClass('odd');
  
  
    /* ---------------------------------------------------------------- 
  	fancybox
  ---------------------------------------------------------------- */
    $('a[rel=lightbox]').fancybox({
        centerOnScroll: false,
        titlePosition: 'over'
    });
    
  
    /* ---------------------------------------------------------------- 
  	datepicker
  ---------------------------------------------------------------- */
    $('input.datum').datepicker({
        dateFormat: 'd. m. yy',
        showOn: 'both',
        buttonImage: '/admin/gfx/date.png',
        buttonImageOnly: true, 
        duration: 'fast'
    });
  
  
    /* ---------------------------------------------------------------- 
  	MarkItUp
  ---------------------------------------------------------------- */
    $('textarea').markItUp(mySettings);


    /* ----------------------------------------------------------------
  	folders
  ---------------------------------------------------------------- */
    $('.folder-add').click(function() {
        var name = window.prompt('Vytvořit složku:');
        if (name != null) { // not canceled
            location.href = this.href +'&name='+ name;
        }
        return false;
    });

    $('[data-folder-delete]').click(function() {
        var text = 'Smazat složku "'+ $(this).data('folderDelete') +'"?';
        return confirm(text);
    });
  
    /* upload */
    $('#folders').change(function() {
        location.href = '?folder='+ this.value;
    });
  
  
    /* ----------------------------------------------------------------
  	media
  ---------------------------------------------------------------- */
    /* delete */
    $('[data-delete]').click(function() {
        var text = 'Smazat "'+ $(this).data('delete') +'"?';
        return confirm(text);
    });
  
    /* rename */
    $('[data-rename]').click(function() {
        var newname = window.prompt('Přejmenovat:', $(this).data('rename'));
        if (newname !== null) { // not canceled
            location.href = this.href +'&newname='+ newname;
        }
        return false;
    });
    
    /* insert image */
    $('#insert-image').click(function() {
        // id, type, [align, [link]]
        parent.$('.ui-dialog-titlebar-close').click();
        
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
        
        parent.$.markItUp({
            replaceWith: placeholder
        });
        return false;
    });
    
    /* insert doc */
    $('.insert-doc').click(function() {
        parent.$('.ui-dialog-titlebar-close').click();
        
        var id = $(this).attr('href'),
            placeholder = '{{doc: '+ id +'}}';
        
        parent.$.markItUp({
            replaceWith: placeholder
        });
        return false;
    });


    /* ----------------------------------------------------------------
  	default action
  ---------------------------------------------------------------- */
    // default action
    $.fn.defaultAction = function(options) {
        var opts = $.extend({}, $.fn.defaultAction.defaults, options);
        return this.each(function() {
            var $scope = $(this);
            $(opts.trigger, this).css('cursor','pointer');
            $scope.delegate(opts.trigger, 'click', function(e) {
                var $innerScope = $(this).parents(opts.innerScope),
                $link = $('a.default', $innerScope),
                $click = $('a.click', $innerScope),
                el = e.target.nodeName.toLowerCase();
                if (el == opts.trigger) {
                    if ($link.length) {
                        document.location = $link.attr('href');
                    } else if ($click.length) {
                        $click.click();
                    }
                }
            });
        });
    };
    $.fn.defaultAction.defaults = {
        trigger: 'td',
        innerScope: 'tr'
    };
    $('.data-grid').defaultAction();
  

    /* ---------------------------------------------------------------- 
  	images
  ---------------------------------------------------------------- */
    $('#resolution').change(function() {
        var dir = $(document).url().param('dir');
        if (dir == null) {
            location.href = '?res='+ $(this).val();
        } else {
            location.href = '?dir='+ dir +'&res='+ $(this).val();
        }
    });
  
    /* rename */
    $('.picture-rename').click(function() {
        var newname = window.prompt('Přejmenovat obrázek:', $(this).attr('rel'));
        if (newname != null) { // not canceled
            location.href = this.href +'&newname='+ newname;
        }
        return false;
    });
  
    /* delete */
    $('.picture-delete').click(function() {
        var text = $(this).attr('rel') == '' ? 'Smazat obrázek?' : 'Smazat obrázek "'+ $(this).attr('rel') +'"?';
        return confirm(text);
    });
  
  
    /* add */
    $('.image-insert').delegate(':radio', 'click', function() {
        var $radio = $(this),
        $urlInput = $radio.siblings('input[id^=url]');
        if ($radio.is('[id^=link]')) {
            $urlInput.show();
        } else {
            $urlInput.hide();
        }
    });
  
  
    /* ---------------------------------------------------------------- 
  	messages
  ---------------------------------------------------------------- */
    $('.success').animate({
        opacity: 1
    }, 3000).fadeOut(1000);
  
  
    /* ---------------------------------------------------------------- 
  	hotkeys
  ---------------------------------------------------------------- */
    function shortcutLink(hotkey, $link) {
        if ($link.length) {
            $(document).bind('keydown', hotkey, function() {
                location.href = $link.attr('href');
            });
        }
    }
    function shortcutButton(hotkey, $button) {
        if ($button.length) {
            $(document).bind('keydown', hotkey, function() {
                $button.click();
            });
        }
    }
    shortcutLink('alt+u', $('a.edit, a.folder-add'));
    shortcutLink('alt+p', $('a.back, a.back-img'));
    shortcutLink('alt+t', $('a.add, a.menu-add, a.con-add, a.gal-add, a.forum-add'));
    shortcutLink('alt+b', $('#imgs a'));
    shortcutLink('alt+k', $('#docs a'));
    shortcutButton('alt+l', $('input[type=submit]'));
  
  
    /* ---------------------------------------------------------------- 
        docs
  ---------------------------------------------------------------- */
    /* delete */
    $('.file-delete').click(function() {
        return confirm('Smazat dokument "'+ $(this).attr('rel') +'"?');
    });
  
    /* rename */
    $('.doc-rename').click(function() {
        var newname = window.prompt('Přejmenovat dokument:', $(this).attr('rel'));
        if (newname != null) { // not canceled
            location.href = this.href +'&newname='+ newname;
        }
        return false;
    });
    

    /* ----------------------------------------------------------------
  	menu
  ---------------------------------------------------------------- */
    /* delete */
    $('.menu-delete').click(function() {
        return confirm('Smazat menu "'+ $(this).attr('rel') +'"?');
    });
  
  
    /* ---------------------------------------------------------------- 
  	articles
  ---------------------------------------------------------------- */
    /* delete */
    $('.article-delete').click(function() {
        return confirm('Smazat článek "'+ $(this).attr('rel') +'"?');
    });


    /* ----------------------------------------------------------------
  	collapser
  ---------------------------------------------------------------- */
    $('form').collapser({
        duration: 200
    });


    /* ----------------------------------------------------------------
  	concerts
  ---------------------------------------------------------------- */
    /* delete */
    $('.concert-delete').click(function() {
        return confirm('Smazat koncert "'+ $(this).attr('rel') +'"?');
    });

    /* places */
    $('#places').dialog({
        autoOpen: false
    });
    $('#open-places').click(function() {
        $('#places').dialog('open');
        return false;
    });
    $('#places a').click(function() {
        var place = $(this).text();
        $.markItUp({
            target: '#frmconcertEdit-place', 
            replaceWith: place
        } );
        return false;
    });


    /* ----------------------------------------------------------------
  	gallery
  ---------------------------------------------------------------- */
    /* delete */
    $('.gallery-delete').click(function() {
        return confirm('Smazat galerii "'+ $(this).attr('rel') +'"?');
    });


    /* ----------------------------------------------------------------
  	forum
  ---------------------------------------------------------------- */
    /* delete */
    $('.forum-delete').click(function() {
        return confirm('Smazat komentář od "'+ $(this).attr('rel') +'"?');
    });


    /* ----------------------------------------------------------------
  	timepicker
  ---------------------------------------------------------------- */
    $('input.cas').timepicker({
        interval: '30'
    });

});
