document.addEventListener('DOMContentLoaded', function(){
    /*--------------------------------------------------------------
    # Viewers Page Functions
    --------------------------------------------------------------*/

    function updateDateTime() {
        // Get the current date and time
        var currentDate = new Date();
    
        // Format the date as "Month Day, Year"
        var optionsDate = { month: 'long', day: 'numeric', year: 'numeric' };
        var formattedDate = currentDate.toLocaleDateString(undefined, optionsDate);
    
        // Format the time as "Hours:Minutes AM/PM"
        var optionsTime = { hour: 'numeric', minute: '2-digit', hour12: true };
        var formattedTime = currentDate.toLocaleTimeString(undefined, optionsTime);
    
        // Update the content of the element with the formatted date and time
        document.getElementById('datetime').innerHTML = formattedDate + '<br>' + formattedTime;
    }
    
    // Call the function initially
    updateDateTime();
    
    // Update the date and time every second (1000 milliseconds)
    setInterval(updateDateTime, 1000);

});