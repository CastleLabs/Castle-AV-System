/**
 * Combined JavaScript for AV Controls and Remote Control System
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize both systems
    initializeReceiverControls();
    loadTransmitters();
});

// Receiver Control Functions remain the same
function initializeReceiverControls() {
    $('.receiver form').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        $.ajax({
            url: '',
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(response) {
                showResponseMessage(response.message, response.success);
            },
            error: function() {
                showResponseMessage('Failed to update settings', false);
            }
        });
    });

    // Updated functionality for Power All On button
    $('#power-all-on').on('click', function() {
        // Send the power-on command immediately
        sendPowerCommandToAll('cec_tv_on.sh');

        // Set a timer to send the command again after 45 seconds
        setTimeout(function() {
            sendPowerCommandToAll('cec_tv_on.sh');
        }, 45000); // 45000 milliseconds = 45 seconds

        // Optional: Provide user feedback
        showResponseMessage('Powering on devices... The command will repeat in 45 seconds.', true);
    });

    // Power All Off functionality remains the same
    $('#power-all-off').on('click', function() {
        sendPowerCommandToAll('cec_tv_off.sh');
    });
}

// Volume and Power functions remain the same...
function updateVolumeLabel(slider) {
    const label = slider.parentElement.querySelector('.volume-label');
    if (label) {
        label.textContent = slider.value;
    }
}

function sendPowerCommand(deviceIp, command) {
    return $.ajax({
        url: '',
        type: 'POST',
        data: {
            receiver_ip: deviceIp,
            power_command: command
        },
        dataType: 'json'
    });
}

function sendPowerCommandToAll(command) {
    const receivers = $('.receiver');
    let promises = [];

    receivers.each(function() {
        const deviceIp = $(this).find('input[name="receiver_ip"]').val();
        promises.push(sendPowerCommand(deviceIp, command));
    });

    Promise.all(promises).then(() => {
        showResponseMessage('All devices successfully updated.', true);
    }).catch(() => {
        showResponseMessage('Failed to update one or more devices.', false);
    });
}

function showResponseMessage(message, success) {
    const responseElement = $('#response-message');
    responseElement
        .removeClass('success error')
        .addClass(success ? 'success' : 'error')
        .html(message)
        .fadeIn();

    setTimeout(() => responseElement.fadeOut(), 5000);
}

// Updated Remote Control Functions
function loadTransmitters() {
    fetch('transmitters.txt')
        .then(response => response.text())
        .then(data => {
            const transmitters = data.split('\n').filter(line => line.trim() !== '');
            
            const select = document.createElement('select');
            select.id = 'transmitter';
            
            transmitters.forEach(transmitter => {
                const [name, url] = transmitter.split(',').map(item => item.trim());
                const option = document.createElement('option');
                option.value = url;
                option.textContent = name;
                select.appendChild(option);
            });
            
            const container = document.getElementById('transmitter-select');
            container.innerHTML = 'Select Transmitter: ';
            container.appendChild(select);
        })
        .catch(error => {
            console.error('Error loading transmitters:', error);
            showError('Failed to load transmitters');
        });
}

function sendCommand(action) {
    const transmitter = document.getElementById('transmitter');
    if (!transmitter || !transmitter.value) {
        showError('Please select a transmitter');
        return;
    }

    $.ajax({
        url: 'api.php',
        type: 'POST',
        data: {
            device_url: transmitter.value,
            action: action
        },
        dataType: 'json'
    }).fail(function(error) {
        showError('Failed to send command');
    });
}

function showError(message) {
    const errorElement = document.getElementById('error-message');
    const errorTextElement = document.getElementById('error-text');
    errorTextElement.textContent = message;
    errorElement.style.display = 'block';
    
    setTimeout(() => {
        errorElement.style.display = 'none';
    }, 5000);
}
