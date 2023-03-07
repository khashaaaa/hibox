/*
 * SliceMenu jQuery plugin for trimming the menu list and adding the "More" element to the end.
 * Copyright OpenTrade Commerce
 */
(function($) {
    $.fn.sliceMenu = function(options) {
        return this.each(function() {
            if (! $(this).find('li').length) {
                return;
            }

            var defaultOptions = {
                title: 'More...',
                button: '<li class="slice-menu-more"><a href="javascript:void(0)">{{title}}</a></li>'
            };
            options = $.extend(defaultOptions, options);

            var $menu = $(this),
                moreButton = options.button.replace('{{title}}', options.title);

            slice($menu, moreButton);

            $(window).on('resize', function () {
                unSlice($menu);
                slice($menu, moreButton);
            });
        });
    };

    /**************************************************************
     **************************** Utils ***************************
     **************************************************************/

    var slice = function($menu, moreButton) {
        // find invisible items
        var startPositionTop = $menu.find('li').eq(0).position().top,
            $sortedLi = sortList($menu.find('li'), startPositionTop);

        if ($sortedLi.invisible.length > 0) {
            addMoreButton($menu, $sortedLi, moreButton, startPositionTop);
        }

        $menu.addClass('slice-menu').css({'overflow': 'visible'});
    };

    var unSlice = function($menu) {
        var $liElements = $menu.find('li:not(.slice-menu-more)');
        $menu
            .empty()
            .append($liElements)
            .removeClass('slice-menu')
            .css({'overflow': 'hidden'});
    };

    var sortList = function($listLi, startPositionTop) {
        var visible = [],
            invisible = [];
        $listLi.each(function () {
            var $li = $(this);

            if ($li.position().top > startPositionTop) {
                invisible.push($li);
            } else {
                visible.push($li);
            }
        });

        return {"visible":visible, "invisible":invisible};
    };

    var addMoreButton = function($menu, $sortedLi, moreButton, startPositionTop) {
        // add more button
        var $subMenu = $(moreButton)
            .appendTo($menu);

        // add invisible item to new list
        $subMenu = $('<ul>').appendTo($subMenu);
        $.each($sortedLi.invisible, function(i, item) {
            $subMenu.append(item);
        });

        // hide the last visible element if the button has not yet fit into the line.
        if ($subMenu.closest('li').position().top > startPositionTop) {
            $subMenu.prepend($sortedLi.visible.pop());
        }
    };
})(jQuery);
