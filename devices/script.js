$(document).ready(function() {
    const rebootModal = $('#rebootModal');

    // Setup image refresh functionality
    setupImageHandlers();
    
    // Refresh images periodically (every 30 seconds)
    setInterval(refreshAllImages, 30000);

    // Existing reboot modal handlers
    $('#reboot-all').on('click', function() {
        rebootModal.show();
    });

    $('#cancelReboot').on('click', function() {
        rebootModal.hide();
    });

    $('#confirmReboot').on('click', function() {
        rebootModal.hide();
        executeRebootAll();
    });

    // Close modal if clicking outside
    $(window).on('click', function(event) {
        if (event.target == rebootModal[0]) {
            rebootModal.hide();
        }
    });

    $('#fix-rockbot').on('click', function() {
        executeFixRockBot();
    });
});

function setupImageHandlers() {
    // Handle initial image load errors
    $('.status-image').on('error', function() {
        const $img = $(this);
        const $container = $img.parent();
        
        if (!$container.find('.image-error').length) {
            $container.append('<div class="image-error">Device Offline</div>');
        }
        $img.addClass('error');
    });

    // Handle refresh button clicks
    $('.refresh-button').on('click', function(e) {
        e.preventDefault();
        const ip = $(this).data('ip');
        refreshImage(ip);
    });

    // Initial load of all images
    $('.status-image').each(function() {
        const $img = $(this);
        $img.attr('src', $img.attr('src') + '?t=' + Date.now());
    });
}

function refreshImage(ip) {
    const $img = $(`.status-image[data-ip="${ip}"]`);
    const $container = $img.parent();
    
    // Remove any existing error message
    $container.find('.image-error').remove();
    $img.removeClass('error');
    
    // Add timestamp to force browser to reload the image
    $img.attr('src', `http://${ip}/pull.bmp?t=${Date.now()}`);
}

function refreshAllImages() {
    $('.status-image').each(function() {
        const ip = $(this).data('ip');
        refreshImage(ip);
    });
}

function executeRebootAll() {
    $.ajax({
        url: 'reboot_all.php',
        type: 'POST',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert(response.message);
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error("AJAX Error:", textStatus, errorThrown);
            console.log("Response Text:", jqXHR.responseText);
            alert('An error occurred while trying to reboot devices. Check the console for details.');
        }
    });
}

function executeFixRockBot() {
    const $button = $('#fix-rockbot');
    $button.prop('disabled', true).text('Fixing...');
    
    $.ajax({
        url: 'fix_rockbot.php',
        type: 'POST',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert('RockBot fixed successfully: ' + response.message);
            } else {
                alert('Error fixing RockBot: ' + response.message);
            }
            console.log('Detailed results:', response.details);
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error("AJAX Error:", textStatus, errorThrown);
            console.log("Response Text:", jqXHR.responseText);
            alert('An error occurred while trying to fix RockBot. Check the console for details.');
        },
        complete: function() {
            $button.prop('disabled', false).text('Fix RockBot');
        }
    });
}
