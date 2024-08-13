document.addEventListener("DOMContentLoaded", function () {
  // Your code here
  let currentPlaylist = [];

    function filterByDate(event) {
        event.preventDefault(); // Prevent the default form submission behavior

        const inputDate = document.getElementById('inputDate').value.toLowerCase();
        const inputQuery = document.getElementsByName('query')[0].value.toLowerCase();

        const streamDivs = document.querySelectorAll('.strm-date');

        streamDivs.forEach(dateHeader => {
            const date = dateHeader.innerText.toLowerCase();
            const displayStyle = date.includes(inputDate) && date.includes(inputQuery) ? 'flex' : 'none';
            dateHeader.parentElement.style.display = displayStyle;
        });
    }

    // Event listener for the "Filter" button
    const filterButton = document.getElementById('filterButton');
    if (filterButton) {
        filterButton.addEventListener('click', filterByDate);
    }

    // Event listener for the form submit
    const searchForm = document.querySelector('.search-form');
    if (searchForm) {
        searchForm.addEventListener('submit', filterByDate);
    }

    function sortStreamDivs(order) {
        const histContainer = document.getElementById('hist-container');
        const streamDivs = Array.from(histContainer.getElementsByClassName('stream'));

        streamDivs.sort((a, b) => {
            const dateA = a.querySelector('.strm-date').innerText;
            const dateB = b.querySelector('.strm-date').innerText;

            return order === 'recent-to-oldest' ? dateB.localeCompare(dateA) : dateA.localeCompare(dateB);
        });

        histContainer.innerHTML = '';
        streamDivs.forEach(streamDiv => {
            histContainer.appendChild(streamDiv);
        });
    }

    const sortSelect = document.querySelector('.sel-hs-sort');
    if (sortSelect) {
        sortSelect.addEventListener('change', function () {
            const selectedOption = this.options[this.selectedIndex].id;
            const order = selectedOption === 'sort-r-o' ? 'recent-to-oldest' : 'oldest-to-recent';

            sortStreamDivs(order);
        });
    }

    function displayHistory(data) {
        const histContainer = document.getElementById('hist-container');
        histContainer.innerHTML = "";

        data.forEach(date => {
            const streamDiv = document.createElement('div');
            streamDiv.className = 'stream';

            const dateHeader = document.createElement('h4');
            dateHeader.className = 'strm-date';
            dateHeader.innerText = date;

            const playButton = document.createElement('button');
            playButton.innerText = 'Preview';
            playButton.addEventListener('click', () => playContent(date));

            streamDiv.appendChild(dateHeader);
            streamDiv.appendChild(playButton);

            histContainer.appendChild(streamDiv);
        });
    }

  function playContent(date) {
    fetch(`get_history_for_date.php?date=${date}`)
      .then(response => response.json())
      .then(files => {
        currentPlaylist = files;
        playNext(); 
      });
  }

function playNext() {
    const container = document.getElementById('media-container');

    if (!container) {
        console.error('Error: media-container element not found');
        return;
    }

    if (currentPlaylist.length > 0) {
        const mediaFile = currentPlaylist.shift();
        const mediaElement = mediaFile.endsWith('.mp3')
            ? createAudioPlayer(mediaFile)
            : createVideoPlayer(mediaFile);

        container.innerHTML = '';
        container.appendChild(mediaElement);
    }
}

  function createAudioPlayer(audioFile) {
    const audio = document.createElement('audio');
    audio.controls = true;
    audio.autoplay = true;
    audio.src = audioFile;
    audio.addEventListener('ended', playNext);
    
    return audio;
  }

  function createVideoPlayer(videoFile) {
    const video = document.createElement('video');
    video.controls = true;
    video.autoplay = true;
    video.src = videoFile;
    video.addEventListener('ended', playNext);
    video.style.width = '1000px';
    return video;
  }

  fetch(`get_history_data.php?date=`)
    .then(response => response.json())
    .then(data => displayHistory(data));
});
