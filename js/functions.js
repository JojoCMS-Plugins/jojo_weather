function jojo_weather_update(selectbox) {
    $('#jojo_weather_div').html('<img src="images/weather-loader.gif" alt="Loading"/>');
    $.getJSON('json/jojo_weather.php', { 'weatherloc': $(selectbox).val()},
            function(data) {
                $('#jojo_weather_div').html(data);
            });
}
