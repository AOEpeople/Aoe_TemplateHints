Event.observe(window, "load", function() {
    document.on(
        'mouseover',
        '.tpl-hint',
        function(event, element) {
            Event.stop(event);
            id = element.getAttribute('id');
            new Tip(
                element,
                event,
                $(id + '-infobox').innerHTML,
                $(id + '-title').innerHTML,
                {
                    style: 'slick',
                    hideOn: 'click',
                    fixed: true,
                    group: 'ath'
                }
            );
        }.bind(this)
    );
});
