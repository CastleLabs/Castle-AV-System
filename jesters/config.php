<?php
/**
 * Generated Configuration File
 * Last Updated: 2024-11-22 19:08:36
 */

const RECEIVERS = [
    'Bar TV Left' => [
        'ip' => '192.168.8.20',
        'show_power' => true
    ],
    'Bar TV Right' => [
        'ip' => '192.168.8.12',
        'show_power' => true
    ],
    'Dining Area TV' => [
        'ip' => '192.168.8.70',
        'show_power' => true
    ],
];

const TRANSMITTERS = [
    'Apple TV' => 2,
    'Cable Box 1' => 7,
    'Cable Box 2' => 3,
    'Cable Box 3' => 4,
    'Unifi Signage' => 5,
    'RockBot Audio' => 1,
    'Rink Spare Audio' => 6,
    'Rink Spare Video' => 8,
    'Mobile Video TX' => 9,
    'Mobile Audio TX' => 10
];

const MAX_VOLUME = 11;
const MIN_VOLUME = 1;
const VOLUME_STEP = 1;
const HOME_URL = 'http://192.168.8.127';
const LOG_LEVEL = 'info';
const API_TIMEOUT = 1;

// Remote control configuration
const REMOTE_CONTROL_COMMANDS = array (
  0 => 'power',
  1 => 'guide',
  2 => 'up',
  3 => 'down',
  4 => 'left',
  5 => 'right',
  6 => 'select',
  7 => 'channel_up',
  8 => 'channel_down',
  9 => '0',
  10 => '1',
  11 => '2',
  12 => '3',
  13 => '4',
  14 => '5',
  15 => '6',
  16 => '7',
  17 => '8',
  18 => '9',
  19 => 'last',
  20 => 'exit',
);

const VOLUME_CONTROL_MODELS = array (
  0 => '3G+4+ TX',
  1 => '3G+AVP RX',
  2 => '3G+AVP TX',
  3 => '3G+WP4 TX',
  4 => '2G/3G SX',
);

const ERROR_MESSAGES = array (
  'connection' => 'Unable to connect to %s (%s). Please check the connection and try again.',
  'global' => 'Unable to connect to any receivers. Please check your network connection and try again.',
  'remote' => 'Unable to send remote command. Please try again.',
);

const LOG_FILE = __DIR__ . '/av_controls.log';
