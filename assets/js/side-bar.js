document.addEventListener('DOMContentLoaded', function() {
  const sidebar = document.getElementById("mySidebar");

  sidebar.addEventListener('mouseover', ()=>{
      console.log("opening sidebar");
      sidebar.style.width = "250px";
      sidebar.classList.remove("close");
      document.getElementById("main").style.marginLeft = "250px";
  });
  sidebar.addEventListener('mouseout',()=>{
      console.log("closing sidebar");
      sidebar.style.width = "75px";
      sidebar.classList.add("close");
      document.getElementById("main").style.marginLeft = "85px";
  });
  
  function updateDateTime() {
      // Get the current date and time
      var currentDateTime = new Date();
    
      // Format the date
      var formattedDateTime = currentDateTime.toLocaleString('en-US', {
        month: 'long',
        day: 'numeric',
        year: 'numeric',
        hour: 'numeric',
        minute: 'numeric',
        hour12: true
      });
    
      // Display the formatted date on the webpage
      document.getElementById('datetime').textContent = formattedDateTime;
    }
    
    // Update the date every second (1000 milliseconds)
    setInterval(updateDateTime, 1000);
    
    // Initial call to display the date immediately
    updateDateTime();
  
  });