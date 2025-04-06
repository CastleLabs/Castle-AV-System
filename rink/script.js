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

    $('#power-all-on').on('click', function() {
        sendPowerCommandToAll('cec_tv_on.sh');
    });

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

    Promise.all(promises);
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
