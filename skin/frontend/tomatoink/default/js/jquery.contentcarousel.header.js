(function($) {
    var	aux		= {
            // navigates left / right
            navigate	: function( dir, $el, $wrapper, opts, cache ) {

                //customize responsive
                cache.itemW = $('.ti_miniCart_carousel_wrapper').width();

                var scroll		= opts.scroll,
                    factor		= 1,
                    idxClicked	= 0;

                if( cache.expanded ) {
                    scroll		= 1; // scroll is always 1 in full mode
                    factor		= 4; // the width of the expanded item will be 3 times bigger than 1 collapsed item
                    idxClicked	= cache.idxClicked; // the index of the clicked item
                }

                // clone the elements on the right / left and append / prepend them according to dir and scroll
                if( dir === 1 ) {
                    $wrapper.find('div.ti_miniCart_carousel_wrapper:lt(' + scroll + ')').each(function(i) {
                        $(this).clone(true).css( 'left', ( cache.totalItems - idxClicked + i ) * cache.itemW * factor + 'px' ).appendTo( $wrapper );
                    });
                }
                else {
                    var $first	= $wrapper.children().eq(0);

                    $wrapper.find('div.ti_miniCart_carousel_wrapper:gt(' + ( cache.totalItems  - 1 - scroll ) + ')').each(function(i) {
                        // insert before $first so they stay in the right order
                        $(this).clone(true).css( 'left', - ( scroll - i + idxClicked ) * cache.itemW * factor + 'px' ).insertBefore( $first );
                    });
                }

                // animate the left of each item
                // the calculations are dependent on dir and on the cache.expanded value
                $wrapper.find('div.ti_miniCart_carousel_wrapper').each(function(i) {
                    var $item	= $(this);
                    $item.stop().animate({
                        left	:  ( dir === 1 ) ? '-=' + ( cache.itemW * factor * scroll ) + 'px' : '+=' + ( cache.itemW * factor * scroll ) + 'px'
                    }, opts.sliderSpeed, opts.sliderEasing, function() {
                        if( ( dir === 1 && $item.position().left < - idxClicked * cache.itemW * factor ) || ( dir === -1 && $item.position().left > ( ( cache.totalItems - 1 - idxClicked ) * cache.itemW * factor ) ) ) {
                            // remove the item that was cloned
                            $item.remove();
                        }
                        cache.isAnimating	= false;
                    });
                });

            },
            // opens an item (animation) -> opens all the others
            openItem	: function( $wrapper, $item, opts, cache ) {
                cache.idxClicked	= $item.index();//-1

                // the item's position (1, 2, or 3) on the viewport (the visible items)
                cache.winpos		= aux.getWinPos( $item.position().left, cache );//3

                $wrapper.find('div.ti_miniCart_carousel_wrapper').not( $item ).hide();
                $item.find('div.ti_related_content_wrapper').css( 'left', cache.itemW + 'px' ).stop().animate({
                    width	: cache.itemW * 3 + 'px',
                    left	: cache.itemW + 'px'
                }, opts.itemSpeed, opts.itemEasing)
                    .end()
                    .stop()
                    .animate({
                        left	: '0px'
                    }, opts.itemSpeed, opts.itemEasing, function() {
                        cache.isAnimating	= false;
                        cache.expanded		= true;

                        aux.openItems( $wrapper, $item, opts, cache );
                    });

            },
            // opens all the items
            openItems	: function( $wrapper, $openedItem, opts, cache ) {
                var openedIdx	= $openedItem.index();

                $wrapper.find('div.ti_miniCart_carousel_wrapper').each(function(i) {
                    var $item	= $(this),
                        idx		= $item.index();

                    if( idx !== openedIdx ) {
                        $item.css( 'left', - ( openedIdx - idx ) * ( cache.itemW * 4 ) + 'px' ).show().find('div.ti_related_content_wrapper').css({
                            left	: cache.itemW + 'px',
                            width	: cache.itemW * 3 + 'px'
                        });

                        // hide more link
                        aux.toggleMore( $item, false );
                    }
                });
            },
            // show / hide the item's more button
            toggleMore	: function( $item, show ) {
                ( show ) ? $item.find('a.ti-more').show() : $item.find('a.ti-more').hide();
            },
            // close all the items
            // the current one is animated
            closeItems	: function( $wrapper, $openedItem, opts, cache ) {
                var openedIdx	= $openedItem.index();

                $openedItem.find('div.ti_related_content_wrapper').stop().animate({
                    width	: '0px'
                }, opts.itemSpeed, opts.itemEasing)
                    .end()
                    .stop()
                    .animate({
                        left	: cache.itemW * ( cache.winpos - 1 ) + 'px'
                    }, opts.itemSpeed, opts.itemEasing, function() {
                        cache.isAnimating	= false;
                        cache.expanded		= false;
                    });

                // show more link
                aux.toggleMore( $openedItem, true );

                $wrapper.find('div.ti_miniCart_carousel_wrapper').each(function(i) {
                    var $item	= $(this),
                        idx		= $item.index();

                    if( idx !== openedIdx ) {
                        $item.find('div.ti_related_content_wrapper').css({
                            width	: '0px'
                        })
                            .end()
                            .css( 'left', ( ( cache.winpos - 1 ) - ( openedIdx - idx ) ) * cache.itemW + 'px' )
                            .show();

                        // show more link
                        aux.toggleMore( $item, true );
                    }
                });
            },
            // gets the item's position (1, 2, or 3) on the viewport (the visible items)
            // val is the left of the item
            getWinPos	: function( val, cache ) {
                switch( val ) {
                    case 0 					: return 1; break;
                    case cache.itemW 		: return 2; break;
                    case cache.itemW * 2 	: return 3; break;
                    case cache.itemW * 3 	: return 4; break;
                }
            }
        },
        methods = {
            init 		: function( options ) {

                if( this.length ) {

                    var settings = {
                        sliderSpeed		: 500,			// speed for the sliding animation
                        sliderEasing	: 'easeOutExpo',// easing for the sliding animation
                        itemSpeed		: 500,			// speed for the item animation (open / close)
                        itemEasing		: 'easeOutExpo',// easing for the item animation (open / close)
                        scroll			: 1				// number of items to scroll at a time
                    };

                    return this.each(function() {

                        // if options exist, lets merge them with our default settings
                        if ( options ) {
                            $.extend( settings, options );
                        }

                        var $el 			= $(this),
                            $wrapper		= $el.find('div.ti_carousel_header'),
                            $items			= $wrapper.children('div.ti_miniCart_carousel_wrapper'),
                            cache			= {};

                        // save the with of one item
                        cache.itemW			= $items.width();
                        // save the number of total items
                        cache.totalItems	= $items.length;
                        //console.log(cache.totalItems);
                        // add navigation buttons
                        //if( cache.totalItems > 4 )
                            $el.prepend('<div class="ti_miniCart_carousel-nav" style="display:none"><span class="ti_carousel-nav-prev icon-arrow-left2"></span><span class="ti_carousel-nav-next icon-arrow-right2"></span></div>');

                        // control the scroll value
                        if( settings.scroll < 1 )
                            settings.scroll = 1;
                        else if( settings.scroll > 4 )
                            settings.scroll = 4;

                        var $navPrev		= $el.find('span.ti_carousel-nav-prev'),
                            $navNext		= $el.find('span.ti_carousel-nav-next');

                        // hide the items except the first 3
                        $wrapper.css( 'overflow', 'hidden' );

                        // the items will have position absolute
                        // calculate the left of each item
                        $items.each(function(i) {
                            $(this).css({
                                position	: 'absolute',
                                left		: i * cache.itemW + 'px'
                            });
                        });

                        // click to open the item(s)
                        //$el.find('a.ti-more').live('click.contentcarousel', function( event ) {
                        $el.on('click.contentcarouselhd', 'a.ti-more', function( event ) {
                            if( cache.isAnimating ) return false;
                            cache.isAnimating	= true;
                            $(this).hide();
                            var $item	= $(this).closest('div.ti_miniCart_carousel_wrapper');
                            aux.openItem( $wrapper, $item, settings, cache );
                            return false;
                        });

                        // click to close the item(s)
                        //$el.find('a.ti-close').live('click.contentcarousel', function( event ) {
                        $el.on('click.contentcarouselhd', 'a.ti-close', function( event ) {
                            if( cache.isAnimating ) return false;
                            cache.isAnimating	= true;
                            var $item	= $(this).closest('div.ti_miniCart_carousel_wrapper');
                            aux.closeItems( $wrapper, $item, settings, cache );
                            return false;
                        });

                        // navigate left
                        $navPrev.bind('click.contentcarouselhd', function( event ) {
                            if( cache.isAnimating ) return false;
                            cache.isAnimating	= true;
                            aux.navigate( -1, $el, $wrapper, settings, cache );
                        });

                        // navigate right
                        $navNext.bind('click.contentcarouselhd', function( event ) {
                            if( cache.isAnimating ) return false;
                            cache.isAnimating	= true;
                            aux.navigate( 1, $el, $wrapper, settings, cache );
                        });

                        // adds events to the mouse
                        //tempary block this function by Yiyang
                        /*
                        $el.bind('mousewheel.contentcarousel', function(e, delta) {
                            if(delta > 0) {
                                if( cache.isAnimating ) return false;
                                cache.isAnimating	= true;
                                aux.navigate( -1, $el, $wrapper, settings, cache );
                            }
                            else {
                                if( cache.isAnimating ) return false;
                                cache.isAnimating	= true;
                                aux.navigate( 1, $el, $wrapper, settings, cache );
                            }
                            return false;
                        }); */

                    });
                }
            }
        };

    $.fn.contentcarouselhd = function(method) {
        //if (method == 3) {alert("what is it?");}

        if ( methods[method] ) {
            return methods[method].apply( this, Array.prototype.slice.call( arguments, 1 ));
        } else if ( typeof method === 'object' || ! method ) {
            return methods.init.apply( this, arguments );
        } else {
            $.error( 'Method ' +  method + ' does not exist on jQuery.contentcarousel-header' );
        }
    };

})(jQuery);