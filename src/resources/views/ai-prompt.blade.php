 <!-- User prompt -->

 <div class="prompt-wrapper h-100" x-data>
     <div class="toolbar d-flex align-items-center justify-content-center py-2">
         <a title="Send Message" class="btn btn-primary rounded-pill" href="{{ $url }}" onclick="event.preventDefault(); document.getElementById('chat-form').submit();">

             <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-send" viewBox="0 0 16 16">
                 <path d="M15.854.146a.5.5 0 0 1 .11.54l-5.819 14.547a.75.75 0 0 1-1.329.124l-3.178-4.995L.643 7.184a.75.75 0 0 1 .124-1.33L15.314.037a.5.5 0 0 1 .54.11ZM6.636 10.07l2.761 4.338L14.13 2.576 6.636 10.07Zm6.787-8.201L1.591 6.602l4.339 2.76 7.494-7.493Z" />
             </svg>
         </a>
         <div class="audio-controls d-flex align-items-center">
             <button id="open-microphone" class="btn btn-secondary rounded-4">
                 <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-mic" viewBox="0 0 16 16">
                     <path d="M3.5 6.5A.5.5 0 0 1 4 7v1a4 4 0 0 0 8 0V7a.5.5 0 0 1 1 0v1a5 5 0 0 1-4.5 4.975V15h3a.5.5 0 0 1 0 1h-7a.5.5 0 0 1 0-1h3v-2.025A5 5 0 0 1 3 8V7a.5.5 0 0 1 .5-.5z" />
                     <path d="M10 8a2 2 0 1 1-4 0V3a2 2 0 1 1 4 0v5zM8 0a3 3 0 0 0-3 3v5a3 3 0 0 0 6 0V3a3 3 0 0 0-3-3z" />
                 </svg>
             </button>
             <button id="mute-microphone" class="btn text-warning d-none">
                 <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-mic-mute" viewBox="0 0 16 16">
                     <path d="M13 8c0 .564-.094 1.107-.266 1.613l-.814-.814A4.02 4.02 0 0 0 12 8V7a.5.5 0 0 1 1 0v1zm-5 4c.818 0 1.578-.245 2.212-.667l.718.719a4.973 4.973 0 0 1-2.43.923V15h3a.5.5 0 0 1 0 1h-7a.5.5 0 0 1 0-1h3v-2.025A5 5 0 0 1 3 8V7a.5.5 0 0 1 1 0v1a4 4 0 0 0 4 4zm3-9v4.879l-1-1V3a2 2 0 0 0-3.997-.118l-.845-.845A3.001 3.001 0 0 1 11 3z" />
                     <path d="m9.486 10.607-.748-.748A2 2 0 0 1 6 8v-.878l-1-1V8a3 3 0 0 0 4.486 2.607zm-7.84-9.253 12 12 .708-.708-12-12-.708.708z" />
                 </svg>
             </button>
         </div>
     </div>
     <textarea form="chat-form" class="form-control rounded-0 h-100" name="prompt" id="prompt" placeholder="Me: "></textarea>
     <script>

     </script>
 </div>
 <form id="chat-form" method="POST" action="{{ $url }}">
     @csrf
 </form>

 <!-- /Chat Form -->
 <style>
     #prompt {
         resize: none;
         height: 50px;
         max-height: 50vh;
     }
 </style>

 <script>
     /* TODO: refactoring */

     /* Select the dom elements */
     const microphoneOpen = document.querySelector('#open-microphone');
     const microphoneMute = document.querySelector('#mute-microphone');
     const outputDiv = document.querySelector('#prompt');
     /* Create a new instance of the SpeechRecognition class */
     const recognition = new window.webkitSpeechRecognition();
     //console.log(recognition)
     // Keeps the mic open and listen until muted
     recognition.continuous = true;
     recognition.interimResults = true;
     /* Start the voice recognition */
     microphoneOpen.addEventListener('click', () => {
         // inform the user that we are recording
         alert('Voice recognition will start after you press ok')
         // start the recognition
         recognition.start();
         // hide the open mic
         microphoneOpen.classList.toggle('d-none');
         // show the mute mic
         microphoneMute.classList.toggle('d-none')
     });

     /* Listen for incoming results */
     recognition.addEventListener('result', (event) => {
         const transcript = Array.from(event.results)
             .map(result => result[0].transcript)
             .join('');
         outputDiv.textContent = transcript;
     });

     /* Stop the Recognition */

     microphoneMute.addEventListener('click', () => {
         // Inform the user recognition will stop
         alert('Voice recognition will stop after you press ok')
         // stop recognition
         recognition.stop();
         // show the open mic
         microphoneOpen.classList.toggle('d-none');
         // hide the mute mic
         microphoneMute.classList.toggle('d-none')
     })

     /* TODO: refactoring */
     function autoResize(textarea) {
         const textAreaHeight = textarea.scrollHeight + 'px';
         textarea.style.height = 'auto';
         textarea.style.height = textarea.scrollHeight + 'px';
         document.querySelector('.conversation').style = {
             "height": "calc(100%" + " - " + textAreaHeight + ")",
             "min-height": "50vh"
         }
     }
 </script>
