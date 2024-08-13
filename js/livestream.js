//chrome://flags/#unsafely-treat-insecure-origin-as-secure
document.addEventListener('DOMContentLoaded', function () {
    const videoElement = document.getElementById('video');
    const canvasElement = document.getElementById('canvas');
    const context = canvasElement.getContext('2d');
    const ws = new WebSocket('ws://localhost:8081'); // Change with your IP address
    let livestreamStarted = false;
    let livestreamInterval;
    let livestreamId; // To store the ID of the current livestream
   

    document.getElementById('startLiveBtn').addEventListener('click', function () {
        checkActiveLivestreamAndStart();
    });

    document.getElementById('endLiveBtn').addEventListener('click', function () {
        endLivestream();
    });

    function checkActiveLivestreamAndStart() {
        const xhr = new XMLHttpRequest();
        xhr.open('GET', 'checkActiveLivestream.php', true);

        xhr.onreadystatechange = function () {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                handleActiveLivestreamCheck(xhr);
            }
        };

        xhr.onerror = function (error) {
            console.error('XHR error:', error);
        };

        xhr.send();
    }

    function handleActiveLivestreamCheck(xhr) {
        if (xhr.status === 200) {
            try {
                const response = JSON.parse(xhr.responseText.trim());

                if (response.activeLivestream) {
                    // Display an error message or take other actions
                    console.error('Cannot start a new livestream. Another livestream is already active.');
                } else {
                    // No active livestream, proceed to start the livestream
                    startLivestream();
                }
            } catch (error) {
                console.error('Error parsing JSON response:', error);
            }
        } else {
            console.error('HTTP request error. Status:', xhr.status);
        }
    }

function startLivestream() {
    if (livestreamStarted) {
        console.log('Livestream is already active.');
        return;
    }

    const title = document.getElementById('titleStream').value;
    const description = document.getElementById('descStream').value;

    // Check if there is an active livestream
    axios.get('isActiveStream.php')
        .then(response => {
            if (response.data.activeLivestream) {
                alert('Another livestream is already active. Cannot start a new one.');
            } else {
                navigator.mediaDevices.getUserMedia({ video: true })
                    .then((stream) => {
                        videoElement.srcObject = stream;
                        videoElement.addEventListener('play', () => {
                            canvasElement.width = videoElement.videoWidth / 2;
                            canvasElement.height = videoElement.videoHeight / 2;
                            livestreamStarted = true;
                            livestreamInterval = setInterval(() => {
                                sendFrame();
                            }, 1000 / 5);

                            insertLivestreamToDatabase(title, description);
                        });
                    })
                    .catch((err) => console.error('Error accessing camera:', err));
            }
        })
        .catch(error => console.error('Error checking active livestream:', error));
}


    function endLivestream() {
        clearInterval(livestreamInterval);
        livestreamStarted = false;

        // Stop the video stream
        const streamTracks = videoElement.srcObject.getTracks();
        streamTracks.forEach(track => track.stop());
        
        // Update the livestream in the database
        updateLivestreamInDatabase();
        location.reload();
    }

    function sendFrame() {
        if (videoElement.paused || videoElement.ended) {
            return;
        }

        context.drawImage(videoElement, 0, 0, canvasElement.width, canvasElement.height);
        canvasElement.toBlob((blob) => {
            if (ws.readyState === WebSocket.OPEN) {
                ws.send(blob);
            } else {
                console.error('WebSocket is not open.');
            }
        }, 'image/jpeg');
    }

    function insertLivestreamToDatabase(title, description) {
        const xhr = new XMLHttpRequest();

        xhr.open('POST', 'handleLivestream.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function () {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                handleResponse(xhr);
            }
        };

        const data = 'insertLivestream=true&titleStream=' + encodeURIComponent(title) + '&descStream=' + encodeURIComponent(description);
        xhr.send(data);
    }

    function updateLivestreamToDatabase(title, description) {
        const xhr = new XMLHttpRequest();

        xhr.open('POST', 'handleLivestream.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function () {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                handleResponse(xhr);
            }
        };

        const data = 'updateLivestream=true&livestreamId=' + livestreamId + '&titleStream=' + encodeURIComponent(title) + '&descStream=' + encodeURIComponent(description);
        xhr.send(data);
    }

    function updateLivestreamInDatabase() {
        const xhr = new XMLHttpRequest();

        xhr.open('POST', 'handleLivestream.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function () {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                handleResponse(xhr);
            }
        };

        const data = 'endLivestream=true&livestreamId=' + livestreamId;
        xhr.send(data);
    }

    function handleResponse(xhr) {
        console.log('HTTP response status:', xhr.status);
        console.log('HTTP response text:', xhr.responseText);

        if (xhr.status === 200) {
            if (xhr.responseText.trim() !== '') {
                try {
                    const response = JSON.parse(xhr.responseText.trim());

                    if (!response.success) {
                        console.error('Error:', response.error);
                    } else {
                        // If the response contains a livestreamId, set it
                        if (response.livestreamId) {
                            livestreamId = response.livestreamId;
                        }
                    }
                } catch (error) {
                    console.error('Error parsing JSON response:', error);
                }
            } else {
                console.log('Empty response from the server.');
            }
        } else {
            console.error('HTTP request error. Status:', xhr.status);
        }
    }

    ws.onmessage = (event) => {
        if (event.data instanceof Blob) {
            const videoBlob = event.data;
            const videoURL = URL.createObjectURL(videoBlob);
            videoElement.src = videoURL;
        }
    };

    ws.onerror = (error) => {
        console.error('WebSocket error:', error);
    };
});
