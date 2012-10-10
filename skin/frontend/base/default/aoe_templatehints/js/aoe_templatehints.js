var tips = [];

Event.observe(window,"load",function() {
    $$(".tpl-hint").each(function(node) {

        var id = node.getAttribute('id');

        node.observe('mouseenter', function(event) {
            new Tip(
                this,
                event,
                $(node.getAttribute('id') + '-infobox').innerHTML,
                $(node.getAttribute('id') + '-title').innerHTML,
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


// $(node.getAttribute('id') + '-infobox')
/*


Event.observe(window,"load",function() {
    $$(".tpl-hint").each(function(node) {
        node.observe('mouseenter', function(element) { console.log(element)});
        node.observe('mouseleave', function(element) { console.log(element)});
    });
});*/
