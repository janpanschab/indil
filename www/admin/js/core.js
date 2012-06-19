// usage: log('inside coolFunc', this, arguments);
// paulirish.com/2009/log-a-lightweight-wrapper-for-consolelog/
window.log = function f(){log.history = log.history || [];log.history.push(arguments);if(this.console) {var args = arguments, newarr;args.callee = args.callee.caller;newarr = [].slice.call(args);if (typeof console.log === 'object') log.apply.call(console.log, console, newarr); else console.log.apply(console, newarr);}};

// make it safe to use console.log always
(function(a){function b(){}for(var c='assert,count,debug,dir,dirxml,error,exception,group,groupCollapsed,groupEnd,info,log,markTimeline,profile,profileEnd,time,timeEnd,trace,warn'.split(','),d;!!(d=c.pop());){a[d]=a[d]||b;}})
(function(){try{console.log();return window.console;}catch(a){return (window.console={});}}());


yepnope({
    //http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js
    load: '/admin/js/lib/jquery-1.7.1.min.js',
    complete: function () {
        
        /**
         * MarkItUp editor
         */
        yepnope({
            test: $('textarea').length,
            yep: [
                    '/admin/js/lib/markitup/sets/texy/set.js',
                    '/admin/js/lib/markitup/sets/texy/style.css',
                    '/admin/js/lib/markitup/skins/simple/style.css',
                    '/admin/js/lib/markitup/jquery.markitup.js',
                    '/admin/js/lib/bootstrap/bootstrap-modal.js'
            ],
            callback: function(url, result, key) {
                if (key === '3') {
                    $('textarea').markItUp(Indil.markItUp.settings);
                }
            }
        });

        /**
         * Media
         */
        yepnope({
            test: $('body.media').length,
            yep: '/admin/js/media/media.js'
        });
        
        /**
         * Delete folder, file or article
         */
        $('[data-delete]').click(function() {
            var text = 'Smazat "'+ $(this).data('delete') +'"?';
            return confirm(text);
        });
        
        /**
         * File uploader
         */
        yepnope({
            test: $('#fileuploader').length,
            yep: [
                '/admin/js/lib/fileuploader/fileuploader.js',
                '/admin/js/lib/fileuploader/fileuploader.css'
            ],
            callback: function(url, result, key) {
                if (key === '0') {
                    var uploader = new qq.FileUploader({
                        element: document.getElementById('fileuploader'),
                        template: '<div class="qq-uploader">' +
                                    '<div class="qq-upload-drop-area"><span>Sem přetáhni soubory pro nahrání</span></div>' +
                                    '<div class="qq-upload-button icon upload">Nahrát soubory</div>' +
                                    '<ul class="qq-upload-list"></ul>' +
                                '</div>',
                        action: Indil.media.fileuploader,
                        debug: true
                    });
                }
            }
        });
        
        /**
         * Lightbox
         */
        yepnope({
            test: $('.lightbox').length,
            yep: [
                '/admin/js/lib/webbox/jquery.webbox-1.0.1.min.css',
                '/admin/js/lib/webbox/jquery.webbox-1.0.1.min.js'
            ],
            callback: function(url, result, key) {
                if (key === '1') {
                    $('.lightbox').webbox();
                }
            }
        });
        
        /**
         * Datepicker
         */
        yepnope({
            test: $('.datum').length,
            yep: [
                '/admin/js/lib/jquery-ui/jquery-ui-1.8.13.custom.min.js',
                '/admin/js/lib/jquery-ui/excite-bike/jquery-ui-1.8.13.custom.css'
            ],
            callback: function(url, result, key) {
                if (key === '0') {
                    $('.datum').datepicker({
                        dateFormat: 'd. m. yy',
                        showOn: 'both',
                        buttonImage: '/admin/gfx/date.png',
                        buttonImageOnly: true, 
                        duration: 'fast'
                    });
                }
            }
        });
    }
});