# Castle AV System

A comprehensive web application for centralized control of audio-visual equipment at The Castle entertainment center.

## Overview

Castle AV System provides a unified interface for staff to manage lighting, displays, audio sources, and other devices across different venue areas (bowling lanes, bars, rink). The goal is to simplify AV operations by integrating disparate systems – like LED lighting controllers, AV-over-IP transmitters/receivers, and IR-controlled devices – into one accessible dashboard.

### Key Benefits:

- **Simplified Operations**: One interface instead of multiple remotes or apps
- **User-Friendly**: Allows non-technical users to operate complex AV setups
- **Centralized Control**: Manage all venue areas from a single system
- **Browser-Based**: Access controls from any device on the local network

## System Architecture

Castle AV System is a PHP web application with a mix of front-end HTML/JavaScript and back-end scripts. It runs on a local server (Raspberry Pi or PC) on the venue's network and is accessed via a web browser.

### High-Level Components:

- **Main Web Server**: Hosts PHP files and serves web pages (running on 192.168.8.127)
- **Front-End**: HTML interfaces with forms, buttons, and status displays
- **Back-End**: PHP scripts handling logic and device communication
- **Configuration Files**: INI and TXT files for easy device management

### Communication with Devices:

- **WLED LED Controllers**: HTTP requests to each controller's API endpoint
- **IR-controlled Devices**: Network commands to IR transmitters (Global Caché iTach or similar)
- **Just Add Power AV-over-IP**: HTTP API calls to manage video/audio distribution

## Features

### Central Dashboard
- System logo and area selection buttons
- User login for secure access
- Quick navigation to all modules

### Area-Specific Control Panels
- **Bowling**: Neoverse displays and music
- **Bowling Bar**: TV control, music, decorative lighting
- **Jesters**: Pub/lounge AV management
- **Rink**: Roller rink audio and LED screen control

### Device Management
- Status monitoring for all AV-over-IP devices
- Volume adjustment for audio zones
- Global reboot functionality
- "Fix RockBot" music system recovery

### On-Screen Display (OSD)
- Send text overlays to any screen in the venue
- Identify devices by displaying their information
- Set custom messages with color, size, and timeout options

## Directory Structure

```
/
├── index.html          # Main login and dashboard
├── script.js           # Main JavaScript for login
├── logo.png            # System logo
├── bowling/            # Bowling lanes control
├── bowlingbar/         # Bowling bar control
├── jesters/            # Jesters pub control
├── rink/               # Roller rink control
├── devices/            # Device management
├── osd/                # On-Screen Display module
├── dashboard/          # Admin dashboard and configs
└── tools/              # Developer tools
```

### Module Structure (common to most areas)

```
/[area]/
├── index.php           # Main UI for the area
├── api.php             # Processes AJAX requests
├── utils.php           # Helper functions
├── config.php          # Area-specific configuration
├── script.js           # Frontend interactivity
├── styles.css          # Area styling
├── WLEDlist.ini        # LED controller IPs (if applicable)
├── transmitters.txt    # IR transmitter IPs
└── payloads.txt        # IR command codes
```

## Configuration Files

| File | Purpose |
|------|---------|
| `DBconfigs.ini` | Master list of network resources and IPs |
| `WLEDlist.ini` | Lists IPs of WLED LED controllers for an area |
| `transmitters.txt` | Maps names to IR transmitter IPs |
| `payloads.txt` | Library of IR command codes (hex or sendir strings) |
| `receivers.txt` | Lists video receiver names and IPs |
| `config.php` | PHP configuration variables and settings |

## Deployment

### Server Requirements
- PHP 7+ with cURL enabled
- Web server (Apache/Nginx)
- Local network access to all controlled devices
- Static IP for the server

### Setup Steps
1. Place repository files in web server document root
2. Edit `script.js` to set a custom password
3. Configure device files:
   - Update `DBconfigs.ini` with correct IPs and URLs
   - Edit `transmitters.txt` to point to your IR blasters
   - Verify IR codes in `payloads.txt`
   - Update `WLEDlist.ini` with your LED controller IPs
   - Ensure `receivers.txt` lists all display devices

### Testing
- Test each module individually
- Verify device connectivity
- Check logs (`av_controls.log`) for any errors

## Maintenance

- Regularly update configuration files when adding new equipment
- Back up the entire system folder periodically
- Check logs for errors or unusual activity
- Update IR codes if you change controlled devices

## Integration Overview

The Castle AV System integrates with the following technologies:

- **WLED** - For decorative LED lighting control
- **Global Caché** (or similar) - For IR control of TVs and cable boxes
- **Just Add Power** - For AV-over-IP distribution
- **RockBot** - For background music streaming
- **Various sensors** - For facility monitoring (via external dashboards)

## Security

The application uses a simple password protection on the main page. Since this is a local network application, it relies on network security for additional protection. For improved security, consider implementing server-side authentication.

---

*Developed by Castle Labs*
