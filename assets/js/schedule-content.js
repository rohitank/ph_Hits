document.addEventListener("DOMContentLoaded", function () {
    // Initialize Sortable
    new Sortable(document.getElementById("tablebody"), {
        animation: 150, // Optional animation duration (milliseconds)
    });
});

function handleFileSelect() {
    var fileInput = document.getElementById('contentFile');
    var durationDisplay = document.getElementById('durationDisplay');

    // Check if a file is selected
    if (fileInput.files.length > 0) {
        var selectedFile = fileInput.files[0];

        // Check if the selected file is a video or audio file
        if (selectedFile.type.startsWith('audio/') || selectedFile.type.startsWith('video/')) {
            var fileReader = new FileReader();

            fileReader.onload = function (e) {
                var mediaElement = document.createElement(selectedFile.type.startsWith('audio/') ? 'audio' : 'video');
                mediaElement.src = e.target.result;

                mediaElement.addEventListener('loadedmetadata', function () {
                    var durationInSeconds = Math.floor(mediaElement.duration);
                    var durationDisplayText = 'Duration: ' + formatTime(durationInSeconds);
                    durationDisplay.textContent = durationDisplayText;
                });
            };

            fileReader.readAsDataURL(selectedFile);
        } else {
            durationDisplay.textContent = 'Duration: Not available';
        }
    } else {
        durationDisplay.textContent = 'Duration: Not available';
    }
}

// Duration Count2
function handleFileSelect() {
    var fileInput = document.getElementById('ContentFile');
    var DurationDisplay = document.getElementById('DurationDisplay');

    // Check if a file is selected
    if (fileInput.files.length > 0) {
        var selectedFile = fileInput.files[0];

        // Check if the selected file is a video or audio file
        if (selectedFile.type.startsWith('audio/') || selectedFile.type.startsWith('video/')) {
            var fileReader = new FileReader();

            fileReader.onload = function (e) {
                var mediaElement = document.createElement(selectedFile.type.startsWith('audio/') ? 'audio' : 'video');
                mediaElement.src = e.target.result;

                mediaElement.addEventListener('loadedmetadata', function () {
                    var DurationInSeconds = Math.floor(mediaElement.duration);
                    var DurationDisplayText = 'Duration: ' + formatTime(DurationInSeconds);
                    DurationDisplay.textContent = DurationDisplayText;
                });
            };

            fileReader.readAsDataURL(selectedFile);
        } else {
            DurationDisplay.textContent = 'Duration: Not available';
        }
    } else {
        DurationDisplay.textContent = 'Duration: Not available';
    }
}

// Helper function to format time in HH:MM:SS
function formatTime(seconds) {
    var hours = Math.floor(seconds / 3600);
    var minutes = Math.floor((seconds % 3600) / 60);
    var remainingSeconds = seconds % 60;

    return (
        String(hours).padStart(2, '0') +
        ':' +
        String(minutes).padStart(2, '0') +
        ':' +
        String(remainingSeconds).padStart(2, '0')
    );
}


function validateForm() {
    // Validate current date
    var currentDate = document.getElementById('currentDate').value;
    if (!currentDate) {
        alert('Please select the current date.');
        return false;
    }

    // Validate title
    var title = document.getElementById('title').value;
    if (!title) {
        alert('Please enter a title.');
        return false;
    }

    // Validate description
    var description = document.getElementById('description').value;
    if (!description) {
        alert('Please enter a short description.');
        return false;
    }

    return true; // Form is valid
}

function scheduleContent() {
    if (validateForm()) {

        var formData = new FormData(document.getElementById('scheduleForm'));

        fetch('upload.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
            } else {
                alert('Video upload failed: ' + data.message);
            }
        })
        .catch(error => console.error('Error uploading video:', error));
    }
}


function addToQueue() {
    if (validateForm()) {

        var formData = new FormData(document.getElementById('submissionForm'));

        fetch('add_to_queue.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
            } else {
                alert('Video upload failed: ' + data.message);
            }
        })
        .catch(error => console.error('Error uploading video:', error));
    }
}

function deleteFromQueue(queueID) {
    if (confirm('Are you sure you want to delete this record?')) {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'delete_queue.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    var result = xhr.responseText;
                    if (result === 'success') {
                        alert('Record deleted successfully');

                        var row = document.getElementById('row_' + queueID);
                        if (row) {
                            row.parentNode.removeChild(row);
                        }
                        window.location.reload();
                    } else {
                        alert('Error deleting record: ' + result);
                    }
                } else {
                    console.error('Error deleting record:', xhr.statusText);
                }
            }
        };
        xhr.send('queueID=' + queueID); // Send queueID instead of queueNum
    }
}

document.addEventListener('DOMContentLoaded', function () {
    var deleteButtons = document.querySelectorAll('.delete-btn');
    deleteButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            var queueID = button.getAttribute('data-id');
            deleteFromQueue(queueID);
        });
    });
});

function updateQueue(newQueueNum, contentID, currentQueueNum) {
    if (newQueueNum != currentQueueNum) {
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4) {
                if (this.status == 200) {
                    var response = JSON.parse(this.responseText);
                    alert(response.message);

                    if (response.success) {
                        window.location.reload();
                    }
                } else {
                    console.error('Error updating queue:', this.statusText);
                }
            }
        };
        xhttp.open("POST", "update_queue_order.php", true);
        xhttp.setRequestHeader("Content-type", "application/json");

        // Convert data to JSON format
        var requestData = {
            newQueueNum: newQueueNum,
            contentID: contentID,
            currentQueueNum: currentQueueNum
        };
        xhttp.send(JSON.stringify(requestData));
    }
}

function swapQueueItems(queueItemA) {
    var selectedSchedTime = $("#schedTimeDropdown" + queueItemA).val();

    // Perform AJAX request to get the other queue item
    $.ajax({
        type: "POST",
        url: "getOtherQueueItem.php", // Create a new PHP file to handle this request
        data: {
            action: "getOtherQueueItem",
            queueItemA: queueItemA,
            selectedSchedTime: selectedSchedTime
        },
        success: function (response) {
            var otherQueueItem = JSON.parse(response);

            // Check if the response is valid
            if (otherQueueItem && otherQueueItem.queueItemB) {
                // Swap the queue items
                swapQueueItemsWithID(queueItemA, otherQueueItem.queueItemB);
            } else {
                alert("Error: Unable to determine the other queue item.");
            }
        },
        error: function (xhr, status, error) {
            alert("Error: " + error);
        }
    });
}

function swapQueueItemsWithID(queueItemA, queueItemB) {
    // Perform AJAX request to update the queue
    $.ajax({
        type: "POST",
        url: "updateQueue.php",
        data: {
            action: "swapQueueItems",
            queueItemA: queueItemA,
            queueItemB: queueItemB
        },
        success: function (response) {
            if (response === "Success") {
                // Refresh the page or update the queue table using JavaScript
                location.reload(); // Refresh the page
                // You can also update the queue table without refreshing the page using JavaScript
                // Example: Update the table using DOM manipulation
                // $("#tablebody").html(updatedTableHtml);
            } else {
                alert("Error: " + response);
            }
        },
        error: function (xhr, status, error) {
            alert("Error: " + error);
        }
    });
}