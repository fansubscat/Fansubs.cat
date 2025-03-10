$(document).ready(function () {
    function remoteSearch(query, callback) {
        if (query.length < MIN_MENTION_LENGTH) {
            callback([]);
            return;
        }
        $.getJSON(U_AJAX_MENTION_URL, {q: query}, function (data) {
            callback(data)
        });
    }

    tribute = new Tribute({
        collection: [{
            trigger: '@',
            menuItemTemplate: function (item) {
                if (item.original.type === 'group') {
                    return item.original.value +  SIMPLE_MENTION_GROUP_NAME.replace('{CNT}', item.original.cnt) ;
                }
                return item.original.value;
            },

            selectTemplate: function (item) {
                if (item.original.type === 'user') {
                    return '[smention u=' + item.original.user_id + ']' + item.original.value + '[/smention]';
                }
                else if (item.original.type === 'group') {
                    return '[smention g=' + item.original.group_id + ']' + item.original.value + '[/smention]';
                }
            },
            values: remoteSearch,
            spaceSelectsMatch: true,
            lookup: 'value',
        }]
    });
    tribute.attach($('[name="message"]'));
});
