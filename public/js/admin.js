jQuery(document).ready(function($) {
    $('.select-image-button').click(function(e) {
        e.preventDefault();

        var buttonId = $(this).attr('id');
        var targetFieldId = $(this).data('target'); // Verwenden Sie data-target, um das Ziel zu identifizieren
        var imageField = $(targetFieldId); // Das Bildfeld basierend auf data-target
        var hiddenFieldId = targetFieldId.replace('#image-', 'image-url-');
        var hiddenField = $('#' + hiddenFieldId); // Verstecktes Feld für die URL

        var mediaUploader = wp.media({
            title: 'Wählen Sie ein Bild aus',
            button: {
                text: 'Bild verwenden'
            },
            multiple: false
        });

        mediaUploader.on('select', function() {
            var attachment = mediaUploader.state().get('selection').first().toJSON();

            imageField.attr('src', attachment.url).show().css('max-height', '100px');
            hiddenField.val(attachment.url);
        });

        mediaUploader.open();
    });
});
