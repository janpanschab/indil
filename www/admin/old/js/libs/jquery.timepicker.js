/* ----------------------------------------------------------------
 @name:     timepicker
 @version:  0.2 alpha
 @release:  2010-09-04
 @type:     jQuery plugin
 @author:   Jan Panschab
---------------------------------------------------------------- */

$(function() {

  $.fn.timepicker = function(options) {
    var opts = $.extend({}, $.fn.timepicker.defaults, options);

    return this.each(function() {
      
      var input = this,
          items = '',
          itemsCount = 24 * 60 / opts.interval;

      $(input)
        .parent()
        .addClass('timepicker');
      $('<div class="ui-datepicker ui-widget ui-widget-content ui-helper-clearfix ui-corner-all" />').insertAfter(this);

      for (i = 0; i < itemsCount; i++) {
        var time = formatMinutes(i * opts.interval);
        items += '<a href="'+ time +'" class="ui-state-default">'+ time +'</a>';
      }
      
      var $box = $('.timepicker .ui-widget');
      $box.append(items).hide();

      $($box).bind('click mouseover mouseout', function(e) {
        var $link = $(e.target);
        if ($link.is('a')) {
          if (e.type == 'click') {
            input.value = $link.attr('href');
            $('a', this).removeClass('ui-state-active');
            $link.addClass('ui-state-active');
            $box.hide();
          } else if (e.type == 'mouseover') {
            $link.addClass('ui-state-hover');
          } else if (e.type == 'mouseout') {
            $link.removeClass('ui-state-hover');
          }
        }
        return false;
      });

      $(input).focus(function() {

        $box.show(); // must be visible before scrolling to active item

        var $link = $box.children('a'),
            linkHeight = $link.height() + parseInt($link.css('border-top-width')) + parseInt($link.css('border-bottom-width')) + parseInt($link.css('padding-top')) + parseInt($link.css('padding-bottom')),
            selectedTime = input.value == '' ? [20, 00] : input.value.split(':'),
            selectedItem = ((selectedTime[0] * 60) - (-selectedTime[1])) / opts.interval, // + selectedTime[1]
            fromTop = linkHeight * selectedItem,
            center = parseInt(($box.height() - linkHeight) / 2);

        $box
          .scrollTop(fromTop - center)
          .children('a').eq(selectedItem).addClass('ui-state-active');

      }); // can`t use blur because of closing box before click event

      // external click
      $(document).click(function(e) {
        if ($(e.target).parents('.timepicker').length === 0) {
          $box.hide();
        }
      });

    });
  };

  function formatMinutes(minutes) {
    var hours = parseInt(minutes / 60),
        mins = minutes - (hours * 60),
        mins = mins == 0 ? '00' : mins,
        time = hours < 10 ? '0'+ hours +':'+ mins : hours +':'+ mins;
    return time;
  }

  // timepicker defaults
  $.fn.timepicker.defaults = {
    interval: '30',
    selected: '20:00'
  };

});