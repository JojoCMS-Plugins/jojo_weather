<?php
/**
 *                    Jojo CMS
 *                ================
 *
 * Copyright 2007 Mike Cochrane <code@gardyneholt.co.nz>
 * Copyright 2007 Tom
 *
 * See the enclosed file license.txt for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @author  Michael Cochrane <code@gardyneholt.co.nz>
 * @author  Tom
 * @license http://www.fsf.org/copyleft/lgpl.html GNU Lesser General Public License
 * @link    http://www.jojocms.org JojoCMS
 */

Jojo::addFilter('output', 'inpageweather', 'jojo_weather');

Jojo::addFilter('output', 'inpageweatherwithcode', 'jojo_weather');

$_options[] = array(
    'id' => 'weather_partnerid',
    'category' => 'Weather Widget',
    'label' => 'Weather.com Partner ID',
    'description' => 'Partner ID from weather.com recieved after <href="https://registration.weather.com/ursa/wow/step1?">applying here</a>',
    'type' => 'text',
    'default' => '',
    'options' => '',
    'plugin' => 'jojo_weather'
);

$_options[] = array(
    'id' => 'weather_licensekey',
    'category' => 'Weather Widget',
    'label' => 'Weather.com License Key',
    'description' => 'License to use xml weather feed from weather.com recieved after <href="https://registration.weather.com/ursa/wow/step1?">applying here</a>',
    'type' => 'text',
    'default' => '',
    'options' => '',
    'plugin' => 'jojo_weather'
);

$_options[] = array(
    'id' => 'weather_forecast_today',
    'category' => 'Weather Widget',
    'label' => 'Show Todays Forecast',
    'description' => 'Show forecast for today (when available, before 2pm)',
    'type' => 'radio',
    'default' => 'yes',
    'options' => 'yes,no',
    'plugin' => 'jojo_weather'
);

$_options[] = array(
    'id' => 'weather_forecast_tomorrow',
    'category' => 'Weather Widget',
    'label' => 'Show Tomorrows Forecast',
    'description' => 'Show forecast for tomorrow',
    'type' => 'radio',
    'default' => 'yes',
    'options' => 'yes,no',
    'plugin' => 'jojo_weather'
);

$_options[] = array(
    'id' => 'weather_forecast_night',
    'category' => 'Weather Widget',
    'label' => 'Show Nighttime Forecast',
    'description' => 'Show forecast for night as well as day',
    'type' => 'radio',
    'default' => 'no',
    'options' => 'yes,no',
    'plugin' => 'jojo_weather'
);

$_options[] = array(
    'id'          => 'weather_info',
    'category' => 'Weather Widget',
    'label'       => 'Weather info to Display',
    'description' => 'Information types to be displayed',
    'type'        => 'checkbox',
    'default'     => 'curr_temp,high,wind,sunrise',
    'options'     => 'curr_temp,high,wind,sunrise',
    'plugin' => 'jojo_weather'
);