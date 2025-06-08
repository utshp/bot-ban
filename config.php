<?php

return [
    // Your Minecraft server name
    'SERVER_NAME' => 'Lava Steal',

    // Supported locales: 'en' - English, 'cs' - Czech
    'LOCALE' => 'en',

    // Keep at null for default web server timezone
    'TIMEZONE' => null,

    // Enables error display for database, set to false for production
    'DEBUG' => true,

    // Allows authorization feature. Set this to false if you want to have a public banlist.
    'ALLOW_AUTHORIZATION' => true,
    // Users authorized to access the ban list
    'USERS' => [
        [
            'username' => 'admin',
            'password' => '1234'
        ]
    ],

    // Supported punishment plugins: 'libertybans', 'advancedban', 'litebans'
    'PUNISHMENT_PLUGIN' => 'libertybans',

    // Custom table prefix set in punishment plugin config, keep at null for default value. Only apply when using litebans
    'CUSTOM_TABLE_PREFIX' => null,

    // Database port
    'DB_PORT' => '3306',
    // Database host
    'DB_HOST' => 'titan.ateex.cloud',
    // Database name
    'DB_NAME' => 's3702_databass_we',
    // Database user
    'DB_USER' => 'u3702_WELfikEvuf',
    // Database password
    'DB_PASSWORD' => 'rHl^gS9VRWwf+MmcLxXK7SxF',

    // Enables the server column in the punishments table, only supported by LibertyBans and LiteBans
    'ALLOW_SCOPES' => false,

    // Custom scope names
    'CUSTOM_SCOPE_NAMES' => [
        'survival' => 'Survival',
        'pvp' => 'PvP',
        'creative' => 'Creative',
    ],

    // Custom scope colors
    // Supported color names available at https://tailwindcss.com/docs/customizing-colors
    'CUSTOM_SCOPE_COLORS' => [
        'survival' => 'green',
        'pvp' => 'pink',
        'creative' => 'purple',
    ],
    // Default scope color
    'DEFAULT_SCOPE_COLOR' => 'blue',
    // Global scope color
    'GLOBAL_SCOPE_COLOR' => 'yellow',
    
    // Show home link
    'SHOW_HOME_LINK' => true,
    // Home URL, used to redirect to the main website of your server
    'HOME_URL' => '/',

    // Show server logo
    'SHOW_SERVER_LOGO' => false,
    // Server logo location
    // Upload your custom images to the 'public/custom' folder, the resulting link will be '/custom/<filename>'
    'SERVER_LOGO' => '',

    // Background image location
    'BACKGROUND_IMAGE' => '/img/background.webp',
    // Console image location
    'CONSOLE_IMAGE' => '/img/console.png',
    // IP address image location
    'IP_ADDRESS_IMAGE' => '/img/console.png',
    // Default avatar image location
    'DEFAULT_AVATAR_IMAGE' => '/img/steve.png',

    // Avatar source, use the {player} variable for the player name
    'AVATAR_SOURCE' => 'https://mc-heads.net/avatar/{player}/32',
];
