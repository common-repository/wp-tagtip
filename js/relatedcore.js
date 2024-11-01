/**
 * WP-TagTip Script (JS CORE)
 * (c) 2009-2010 Eduardo Daniel Sada
 * www.coders.me
**/

document.addEvent('domready', function() {
    var i = 0;
    $$('.relatedspan').each(function(el) {
        el.tooltip('<div class="tooltiprelated"><h1>'+related['title']+'</h1><ul>'+related['data'][i]+'</ul></div>', { width: related['width'], style: 'related', sticky: 1, click: 1 });
        i++;
    });
});