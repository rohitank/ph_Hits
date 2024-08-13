// dashboard.js
document.addEventListener('DOMContentLoaded', function () {
    var video = document.getElementById('strm-vid');
    var titleElement = document.getElementById('prerecorded-title');
    var cmElement = document.getElementById('prerecorded-cm');

    function fetchCurrentContent() {
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function () {
            
            if (xhr.readyState === 4 && xhr.status === 200) {
                var response = JSON.parse(xhr.responseText);
                if (response.success) {
                    var content = response.content;

                    video.src = content.filePath;

                    video.currentTime = (content.timeDifference / 1000) * -1; 
                    console.log(video.currentTime);
                    titleElement.innerText = 'Now Playing: ' + content.contentTitle;
                    cmElement.innerText = 'Uploader: ' + content.uploaderName;

                    video.addEventListener('canplaythrough', function () {
                        video.play();
                    });

                    video.addEventListener('ended', function () {
                        fetchCurrentContent();
                        
                    });
                    
                } else {
                    console.log('here');
                    fetch('add_default_to_queue.php', {
                            method: 'POST',
                        })
                        .then(response => response.text())
                        .then(data => {
                            console.log(data);
                            fetchCurrentContent();
                            location.reload();
                        })
                        .catch(error => {
                            console.error('Error making POST request:', error);
                        });
                        location.reload();
                }
                
            }
           
        };

        xhr.open('GET', 'getCurrentContent.php', true);
        xhr.send();
        
    }


    // Start by fetching the current content
    fetchCurrentContent();
});