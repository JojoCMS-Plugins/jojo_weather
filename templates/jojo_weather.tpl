<div id="jojo_weather_div">
{if $locations}
    <h5>Current Weather in</h5>
    <form action="{$SITEURL}/weather" method="post">
        <select id="jojo_weather_loc" name="jojo_weather_loc" onchange="jojo_weather_update(this); return false;">
{foreach from=$locations key=k item=v}
                <option value="{$k}"{if $k==$currentlocation}selected="selected"{/if}>{$v}</option>
{/foreach}
        </select>
{elseif $location}
    <h5>Weather in {$locationdata.dnam}</h5>
{/if}
    <p>As at local time: {$locationdata.tm}</p>
    <div id="weather-current">
            <div class="weather-conditions">
                <a href='http://www.weather.com/weather/local/{$currentlocation}'><img  class='icon-image' style="vertical-align:-12px;"  alt='{$currentconditions.t}' title='{$currentconditions.t}' src='images/weather/31x31/{$currentconditions.icon}.png' /></a>
            {if $curr_temp}Temperature: <b>{$currentconditions.tmp}</b>{$locationdata.tempunit}{if $currentconditions.tmp != $currentconditions.flik} <i>(feels like {$currentconditions.flik}{$locationdata.tempunit})</i>{/if}<br/>{/if}
            {if $wind}Wind: {if $currentconditions.wind.s == 'calm'}<b>Calm</b>{else}<b>{$currentconditions.wind.t}</b> @ <b>{$currentconditions.wind.s}</b>{$locationdata.speedunit}{/if}<br />{/if}
            {if $sunrise}Sunrise: {$locationdata.sunr} Sunset: {$locationdata.suns}{/if}
            </div>
            <div class="clear"></div>
    </div>
{if $OPTIONS.weather_forecast_today=='yes' || $OPTIONS.weather_forecast_tomorrow=='yes'}
    <div id="weather-forecasts">
{foreach from=$forecasts key=k item=fc}
    {if $OPTIONS.weather_forecast_today=='yes' && $fc.hi != 'N/A' && $k==0}
        <h5>Forecast for Today</h5>
    {elseif $OPTIONS.weather_forecast_tomorrow=='yes' && $k>0}
        <h5>Forecast for {$fc.fdateday} {$fc.fdate}</h5>
    {/if}
    {if ($OPTIONS.weather_forecast_today=='yes' && $fc.hi != 'N/A' && $k==0) || ($OPTIONS.weather_forecast_tomorrow=='yes' && $fc.hi != 'N/A' && $k==1)}
        <div class="weather-forecast">
                {if $high}<div class="weather-temp">High: <b>{$fc.hi}</b>{$locationdata.tempunit}&nbsp; Low: <b>{$fc.low}</b>{$locationdata.tempunit}</div>{/if}
    {foreach from=$fc.part key=key item=fcp}
        {if $OPTIONS.weather_forecast_night=='yes' || $fcp.p != 'n'}
            <div class="weather-conditions">
                    <a href='http://www.weather.com/weather/local/{$currentlocation}'><img class='icon-image' style="vertical-align:-12px;" alt='{$fcp.t}' title='{$fcp.t}. Get Detailed forecast for {$currentlocation} at weather.com' src='images/weather/31x31/{$fcp.icon}.png' /></a>
                {if $wind}
                    Wind: {if $fcp.wind.s == 'calm'}<b>Calm</b>{else}<b>{$fcp.wind.t}</b> @ <b>{$fcp.wind.s}</b>{$locationdata.speedunit}{/if}<br />
                {/if}
            </div>
        {/if}
    {/foreach}
        </div>
    {/if}
{/foreach}
    </div>
{/if}
    <div id="weather-credits">
        <p><a href='http://www.weather.com/?prod=xoap&amp;par=$partner'><img src='images/weather/logos/TWClogo_31px.png' class='icon-image' style="vertical-align:-14px;" alt='weather.com logo' /></a>Data provided by&nbsp;<a href='http://www.weather.com/?prod=xoap&amp;par=$partner'>weather.com</a><sup>&reg;</sup><br />
        {foreach from=$weatherlinks item=wl}
            <a href="{$wl.l}">{$wl.t}</a><br />
        {/foreach}
        </p>
    </div>
    {if $locations}</form>{/if}
</div>