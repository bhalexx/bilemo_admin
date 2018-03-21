var $ = require('jquery');

$(document).ready(function () {
    /**
     * Collections management
     */
    var featureCount = $('#mobile_features').find('.form-group').length,
        pictureCount = $('#mobile_pictures').find('.form-group').length;
    
    //Adds a field
    var addField = function (container, count) {
        if (!count) {
            count = container.children().length;
        }
        var prototype = container.attr('data-prototype');
        var item = prototype.replace(/__name__/g, count);
        
        container.append(item);
        
        if (container.attr('id') === 'mobile_features') {
            featureCount++;
        }
        if (container.attr('id') === 'mobile_pictures') {
            pictureCount++;
        }
    };

    //On "add" button click
    $(document).on('click', '.collection-add', function () {
        var $collectionContainer = $('#' + $(this).data('collection'));
        var count = $(this).data('collection') === 'mobile_features' ? featureCount : pictureCount;
        addField($collectionContainer, count);
    });

    //Add an empty feature field if none existing
    if ($('.collection-add').length > 0 && featureCount === 0) {
        addField($('#mobile_features'), featureCount);
    }

    //Add an empty picture field if none existing
    if ($('.collection-add').length > 0 && pictureCount === 0) {
        addField($('#mobile_pictures'), pictureCount);
    }

    // On "delete" button click
    $('#mobile_features, #mobile_pictures').on('click', '.collection-remove', function () {
        $(this).parent().parent().remove();
    });


});