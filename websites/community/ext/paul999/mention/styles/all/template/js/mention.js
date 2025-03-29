$(document).ready(function () {
    function remoteSearch(query, callback) {
        if (query.length < MIN_MENTION_LENGTH) {
            callback([]);
            return;
        }
        $.getJSON(U_AJAX_MENTION_URL, {q: query, t: 'u'}, function (data) {
            callback(data)
        });
    }
    function remoteSearchSmileys(query, callback) {
        if (query.length < MIN_MENTION_LENGTH) {
            callback([]);
            return;
        }
        $.getJSON(U_AJAX_MENTION_URL, {q: query, t: 's'}, function (data) {
            callback(data)
        });
    }

    tribute = new Tribute({
        noMatchTemplate: function () {
            return false;
        },
        collection: [{
            trigger: '@',
            menuItemTemplate: function (item) {
            	if (item===undefined) {
            		return '';
    		}
                if (item.original.type === 'group') {
                    return item.original.value +  SIMPLE_MENTION_GROUP_NAME.replace('{CNT}', item.original.cnt) ;
                }
                return '<img class="avatar" src="'+item.original.avatar+'" alt=""> '+item.original.value;
            },
            selectTemplate: function (item) {
            	if (item===undefined) {
            		return '';
    		}
                if (item.original.type === 'user') {
                    return '[mention]' + item.original.value + '[/mention]';
                }
            },
            values: remoteSearch,
            spaceSelectsMatch: true,
            lookup: 'value',
        }, {
            trigger: ':',
            menuItemTemplate: function (item) {
            	if (item===undefined) {
            		return '';
    		}
                return '<img class="smiley" src="/images/smilies/'+item.original.value+'" alt=""> '+item.original.key;
            },

            selectTemplate: function (item) {
            	if (item===undefined) {
            		return '';
    		}
                if (item.original.type === 'smiley') {
                    return item.original.key;
                }
            },
            values: remoteSearchSmileys,
            spaceSelectsMatch: true,
            lookup: 'value',
        }]
    });
    tribute.attach($('[name="message"]'));
});
