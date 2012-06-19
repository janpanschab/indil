/* ---------------------------------------------------------------- 
 @name:     collapser - plugin na otevírání a zavírání boxů
 @version:  0.2 
 @release:  2009-09-23
 @type:     jQuery plugin
 @author:   Jan Panschab
---------------------------------------------------------------- */
 
jQuery(function($) {
  
  $.fn.collapser = function(options) {
    var opts = $.extend({}, $.fn.collapser.defaults, options);
    
    $.fn.log = function (msg) {
      if (opts.debug && window.console && window.console.log) {
        console.log('[collapser] %s: %o', msg, this);
      }
      return this;
    };
    
    return this.each(function() {
      
      var $this = $(this);
      var $triggers = $(opts.trigger).log('triggers');
      
      // inicializace
      $triggers.next().css('width',opts.width).hide(); // všechno se zavře
      if (opts.openOnLoad != false) { // otevře se box podle selectoru
        $triggers.filter(opts.openOnLoad).log('open on load').addClass(opts.openClass).next().show();
      }
      if (document.location.hash) { // otevře se hash z adresy
        $(document.location.hash).log('open by hash').addClass(opts.openClass).next().show();
      }
      
      // click on trigger
      $triggers.click(function() {
        if (opts.autoClose) { // automaticky se zavírají ostatní boxy
          var $actualTrigger = $(this);
          $actualTrigger.toggleClass(opts.openClass).next().slideToggle(opts.duration);
          $triggers.not($actualTrigger).removeClass(opts.openClass).next().slideUp(opts.duration);
        }
        else { // může být otevřeno i více boxů
          $(this).toggleClass(opts.openClass).next().slideToggle(opts.duration);
        }
        return false;
      });
      
    });
  };
  // collapser defaults
  $.fn.collapser.defaults = {
    trigger: '.collapser', // [jQuery selector] třída triggeru
    openClass: 'topen', // [string] třída triggeru při otevřeném boxu
    autoClose: false, // [boolean] otevírání jednoho/více boxů
    openOnLoad: false, // [false|jQuery selector] co se otevře při načtení stránky
    duration: 500, // [string(slow, normal, fast)|number] rychlost otevírání boxů
    width: '', // [number] šířka otevíraného boxu - skákající bug
    debug: false // [boolean] výpis debugovacích hlášek do konzole
  };
 
});
