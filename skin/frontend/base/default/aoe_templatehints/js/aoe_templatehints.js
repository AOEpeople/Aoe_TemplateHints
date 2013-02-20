Event.observe(window, "load", function() {
    $$(".tpl-hint").each(function(node) {
        var id = node.getAttribute('id');
        node.observe('mouseover', function(event) {
            Event.stop(event);
            new Tip(
                this,
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
        });
    });
});
