 <div class="prompt-wrapper shadow " x-data="prompt_data">
     <div class="top_toolbar position-absolute top-0 w-100" x-show="promptFocus">
         <div class="d-flex justify-content-center align-items-center">
             <button @click="insertBackticks" class="btn">
                 <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-code-slash" viewBox="0 0 16 16">
                     <path d="M10.478 1.647a.5.5 0 1 0-.956-.294l-4 13a.5.5 0 0 0 .956.294l4-13zM4.854 4.146a.5.5 0 0 1 0 .708L1.707 8l3.147 3.146a.5.5 0 0 1-.708.708l-3.5-3.5a.5.5 0 0 1 0-.708l3.5-3.5a.5.5 0 0 1 .708 0zm6.292 0a.5.5 0 0 0 0 .708L14.293 8l-3.147 3.146a.5.5 0 0 0 .708.708l3.5-3.5a.5.5 0 0 0 0-.708l-3.5-3.5a.5.5 0 0 0-.708 0z" />
                 </svg>
             </button>
             <div class="resize_textarea" @click="promptFocus = false">
                 <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-bar-down" viewBox="0 0 16 16">
                     <path fill-rule="evenodd" d="M1 3.5a.5.5 0 0 1 .5-.5h13a.5.5 0 0 1 0 1h-13a.5.5 0 0 1-.5-.5zM8 6a.5.5 0 0 1 .5.5v5.793l2.146-2.147a.5.5 0 0 1 .708.708l-3 3a.5.5 0 0 1-.708 0l-3-3a.5.5 0 0 1 .708-.708L7.5 12.293V6.5A.5.5 0 0 1 8 6z" />
                 </svg>
             </div>
         </div>
     </div>
     <!-- Prompt -->
     <textarea form="chat-form" name="prompt" id="prompt" class="form-control bg-dark border-top border-secondary text-muted" x-bind:class="{'ps-5' : !promptFocus}" placeholder="Me: " x-model="selectedPreset" x-on:focus="promptFocus = true" :style="promptFocus && { height: '50vh' }"></textarea>

     <form id="chat-form" method="POST" action="{{ $url }}" x-show="!processing">
         @csrf
     </form>
     <!-- /Prompt -->


     <template x-if="!promptFocus">
         <!-- New Conversation copy -->

         <!-- /New Conversation -->

         <div class="dropdown">
             <button class="btn text-white position-absolute left-0 bottom-0 dropdown-toggle" type="button" id="newToggler" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                 @include('partials.icons.plus')
             </button>
             <div class="dropdown-menu" aria-labelledby="newToggler">

                 <a class="dropdown-item" href="{{ route('admin.conversations.new')}} ">
                     @include('partials.icons.plus')
                     <span class="fs_sm text-uppercase">{{__('New Conversation')}}</span>
                 </a>
                 <button class="dropdown-item" x-on:click="document.getElementById('offcanvasFileUpload').classList.toggle('show'); showOffCanvas = true; promptFocus = true;">
                     @include('partials.icons.plus')
                     <span class="fs_sm text-uppercase">{{__('Upload File')}}</span>
                 </button>

             </div>
         </div>
     </template>

     <div class="bg-dark toolbar align-items-end justify-content-between justify-content-lg-center py-2 px-1  gap-2" x-bind:class="{ 'd-flex': promptFocus, 'd-none': !promptFocus }">
         @if (Route::has('admin.conversations.index'))
         <!-- New Conversation -->
         <a class="btn text-white" href="{{ route('admin.conversations.new')}} ">
             @include('partials.icons.plus')
             <div class="fs_sm text-uppercase">{{__('new')}}</div>
         </a>
         <!-- /New Conversation -->

         <!-- Conversations Page -->
         <a href="{{route('admin.conversations.index')}}" class="btn text-white">
             @include('partials.icons.list')
             <div class="fs_sm text-uppercase">{{__('Chat')}}</div>
         </a>
         <!-- Conversations Page -->
         @endif


         <button form="chat-form" title="Send Message" class="btn text-white" type="submit" x-show="!processing" @click.prevent="submitChatPrompt($event)" tabindex="-1">
             <div class="icon d-flex flex-column align-items-center">
                 <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-send" viewBox="0 0 16 16">
                     <path d="M15.854.146a.5.5 0 0 1 .11.54l-5.819 14.547a.75.75 0 0 1-1.329.124l-3.178-4.995L.643 7.184a.75.75 0 0 1 .124-1.33L15.314.037a.5.5 0 0 1 .54.11ZM6.636 10.07l2.761 4.338L14.13 2.576 6.636 10.07Zm6.787-8.201L1.591 6.602l4.339 2.76 7.494-7.493Z" />
                 </svg>
                 <span class="text-uppercase fs_sm">{{__('Send')}}</span>
             </div>
         </button>

         <span class="icon loading d-none">
             <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-activity" viewBox="0 0 16 16">
                 <path fill-rule="evenodd" d="M6 2a.5.5 0 0 1 .47.33L10 12.036l1.53-4.208A.5.5 0 0 1 12 7.5h3.5a.5.5 0 0 1 0 1h-3.15l-1.88 5.17a.5.5 0 0 1-.94 0L6 3.964 4.47 8.171A.5.5 0 0 1 4 8.5H.5a.5.5 0 0 1 0-1h3.15l1.88-5.17A.5.5 0 0 1 6 2Z" />
             </svg>
             <span>
                 loading...
             </span>
         </span>

         <div class="audio-controls d-flex align-items-center">
             <button id="open-microphone" class="btn text-white" @click="start_talking()" x-show="can_talk && !processing">
                 <div class="icon d-flex flex-column align-items-center">
                     <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-mic" viewBox="0 0 16 16">
                         <path d="M3.5 6.5A.5.5 0 0 1 4 7v1a4 4 0 0 0 8 0V7a.5.5 0 0 1 1 0v1a5 5 0 0 1-4.5 4.975V15h3a.5.5 0 0 1 0 1h-7a.5.5 0 0 1 0-1h3v-2.025A5 5 0 0 1 3 8V7a.5.5 0 0 1 .5-.5z" />
                         <path d="M10 8a2 2 0 1 1-4 0V3a2 2 0 1 1 4 0v5zM8 0a3 3 0 0 0-3 3v5a3 3 0 0 0 6 0V3a3 3 0 0 0-3-3z" />
                     </svg>
                     <span class="text-uppercase fs_sm">{{__('Talk')}}</span>
                 </div>
             </button>
             <button id="mute-microphone" class="btn text-warning" @click="stop_talking()" x-show="!can_talk">
                 <div class="icon d-flex flex-column align-items-center">
                     <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-mic-mute" viewBox="0 0 16 16">
                         <path d="M13 8c0 .564-.094 1.107-.266 1.613l-.814-.814A4.02 4.02 0 0 0 12 8V7a.5.5 0 0 1 1 0v1zm-5 4c.818 0 1.578-.245 2.212-.667l.718.719a4.973 4.973 0 0 1-2.43.923V15h3a.5.5 0 0 1 0 1h-7a.5.5 0 0 1 0-1h3v-2.025A5 5 0 0 1 3 8V7a.5.5 0 0 1 1 0v1a4 4 0 0 0 4 4zm3-9v4.879l-1-1V3a2 2 0 0 0-3.997-.118l-.845-.845A3.001 3.001 0 0 1 11 3z" />
                         <path d="m9.486 10.607-.748-.748A2 2 0 0 1 6 8v-.878l-1-1V8a3 3 0 0 0 4.486 2.607zm-7.84-9.253 12 12 .708-.708-12-12-.708.708z" />
                     </svg>
                     <span class="text-uppercase fs_sm">{{__('Mute')}}</span>
                 </div>

             </button>
         </div>


         <button class="btn text-white" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasFileUpload" aria-controls="offcanvasFileUpload">
             <div class="icon d-flex flex-column align-items-center">
                 <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark-arrow-up" viewBox="0 0 16 16">
                     <path d="M8.5 11.5a.5.5 0 0 1-1 0V7.707L6.354 8.854a.5.5 0 1 1-.708-.708l2-2a.5.5 0 0 1 .708 0l2 2a.5.5 0 0 1-.708.708L8.5 7.707V11.5z" />
                     <path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5v2z" />
                 </svg>
                 <span class="text-uppercase fs_sm">{{__('File')}}</span>
             </div>
         </button>

         <div class="offcanvas offcanvas-bottom" x-bind:class="{ 'show': showOffCanvas }" data-bs-backdrop="static" tabindex="-1" id="offcanvasFileUpload" aria-labelledby="staticBackdropLabel">
             <div class="offcanvas-header">
                 <h5 class="offcanvas-title" id="staticBackdropLabel">File upload</h5>
                 <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close" x-on:click="showOffCanvas = false"></button>
             </div>
             <div class="offcanvas-body">
                 <div>
                     Click the button to upload the content of a file into the propmt and let gpt help you.
                     <input class="form-control" type="file" @change="handleFileUpload($event)">
                 </div>
             </div>
         </div>


         @include('partials.chat.offcanvas-settings')
     </div>


 </div>

 <!-- /Chat Form -->
 <style>
     .prompt-wrapper {
         position: fixed;
         bottom: 40px;
         left: 0px;
         width: 100%;
     }

     @media screen and (min-width: 1200px) {
         .prompt-wrapper {

             bottom: 0px;

         }
     }

     #prompt {
         resize: none;
         border: none;
     }

     .close_toolbar {
         position: absolute;
         bottom: 0;
         right: 0;
         z-index: 100;
     }
 </style>

 <script>
     document.addEventListener('alpine:init', () => {

         Alpine.data('prompt_data', () => ({
             processing: false,
             can_talk: true,
             recognition: null,
             selectedPreset: '',
             showOffCanvas: false,
             fileContent: '',
             promptFocus: false,
             presets: [{
                     label: 'Default',
                     options: [{
                         text: 'Free Writing',
                         value: ''
                     }]
                 },
                 {
                     label: 'Tutorial',
                     options: [{
                             text: 'Table Of Contents',
                             value: 'Complete the table of contents for my new [topic] for [audience] crash course. \n - What is [topic] \n - First Steps \n - Data Types \n - Functions \n '
                         },
                         {
                             text: 'Example TOC',
                             value: 'Complete the table of contents for my new PHP for Intermediate Developers crash course. \n - What is PHP today \n - First Steps \n - Data Types \n - Functions \n '
                         },
                         {
                             text: 'Complete Chapter',
                             value: 'Complete chapter [copy chapter] in [language]: \n '
                         },
                         {
                             text: 'Example',
                             value: 'Complete chapters in PHP. \nIntroduction:\n- What is PHP today\nFirst Steps\n- Installing PHP\n- Setting up a development environment\n- Basic syntax'
                         }
                     ]
                 },
                 {
                     label: 'Code',
                     options: [{
                             text: 'Translate',
                             value: 'Translate to [language] code. \nThe software must ask to the user to insert a number and return the sum of all inserted numbers'
                         },
                         {
                             text: 'Explain',
                             value: 'Explain [topic] in [language] with code blocks.'
                         },
                         {
                             text: 'Review',
                             value: 'Review this code.'
                         },
                         {
                             text: 'Refactor Simple',
                             value: 'Refactor this code. \n\`\`\`language \n\`\`\`'
                         },
                         {
                             text: 'Refactor',
                             value: 'Refactor this code following [convention] conventions.'
                         },
                         {
                             text: 'Refactor & Improve',
                             value: 'Refactor this code and improve it using [language] [version] features.'
                         },
                         {
                             text: 'Complete',
                             value: 'Complete the following [language] code.\n \`\`\`language \n [your code here] \n\`\`\`'
                         },
                     ]
                 },
                 {
                     label: 'Documents',
                     options: [{
                             text: 'Generate',
                             value: 'Generate a document draft about [topic]:\n'
                         },
                         {
                             text: 'Proposal',
                             value: 'Write a proposal for [client] about [project]:\n '
                         },
                     ]
                 },
                 {
                     label: 'Social',
                     options: [{
                             text: 'Twitter',
                             value: 'Write a tween [topic]'
                         },
                         {
                             text: 'Linkedin',
                             value: 'Write a Linkedin Post about [topic] for [audience]: \n '
                         },
                         {
                             text: 'Istagram',
                             value: 'Write a caption for Istagram: \n '
                         },

                     ]
                 }
             ],
             submitChatPrompt(e) {
                 e.preventDefault()
                 //console.log(e);
                 // Hide the processing form and button
                 this.processing = !this.processing
                 // Submit the form
                 document.getElementById('chat-form').submit()
                 // Select the loading icon
                 const loadingIcon = document.querySelector('.icon.loading')
                 // Toggle the d-none class
                 loadingIcon.classList.toggle('d-none')
                 // animate the icon
                 loadingIcon.animate([{
                         opacity: '0.5'
                     },
                     {
                         opacifiy: '1'
                     }
                 ], {
                     duration: 1000,
                     iterations: Infinity
                 })

             },
             start_talking() {
                 this.can_talk = !this.can_talk;
                 /* Create a new instance of the SpeechRecognition class */

                 if ('SpeechRecognition' in window || 'webkitSpeechRecognition' in window) {

                     this.recognition = 'SpeechRecognition' in window ? new SpeechRecognition() : new webkitSpeechRecognition();
                     console.log(this.recognition)
                     // Keeps the mic open and listen until muted
                     this.recognition.continuous = true;
                     this.recognition.interimResults = true;
                     this.recognition.lang = ['it-IT', 'en-US']
                     // start the this.recognition
                     console.info('Start voice recognition')
                     this.recognition.start();

                     // select the prompt area
                     const outputDiv = document.querySelector('#prompt');
                     //Listen for incoming results and insert them into the prompt
                     this.recognition.onresult = function(event) {
                         const transcript = Array.from(event.results)
                             .map(result => result[0].transcript)
                             .join('');
                         outputDiv.textContent = transcript;
                     }
                     /*    this.recognition.addEventListener('result', (event) => {
                            const transcript = Array.from(event.results)
                                .map(result => result[0].transcript)
                                .join('');
                            outputDiv.textContent = transcript;
                        }); */

                     this.recognition.onerror = (event) => {
                         console.error(event.error);
                     };
                 } else {
                     console.log('Speech Recognition not supported')
                 }

             },
             stop_talking() {
                 this.can_talk = !this.can_talk;
                 if ('SpeechRecognition' in window || 'webkitSpeechRecognition' in window) {
                     console.info('Voice recognition stopped')
                     // stop recognition
                     this.recognition.stop();
                 }
             },
             handleFileUpload(event) {
                 const file = event.target.files[0];
                 const reader = new FileReader();
                 const self = this;
                 reader.onload = () => {
                     const fileContent = reader.result;
                     console.log(fileContent);

                     this.selectedPreset = fileContent;
                 }

                 reader.readAsText(file);
             },
             insertBackticks() {
                 let textarea = document.getElementById('prompt');
                 let start = textarea.selectionStart;
                 let end = textarea.selectionEnd;
                 let text = textarea.value;
                 let newText = text.substring(0, start) + '\n```markdown\n\n' + text.substring(start, end) + '```' + text.substring(end);
                 textarea.value = newText;
                 textarea.selectionStart = start + 1;
                 textarea.selectionEnd = end + 1;
             }

         }))
     })
 </script>

 <script>
     /*
     TODO:
    Refactor this code, use nextTick
    https://alpinejs.dev/magics/nextTick
    */
     window.onload = function() {

         const conversationEl = document.querySelector('.conversation');
         //console.log(conversationEl);
         conversationEl.scrollBy(0, conversationEl.scrollHeight)
     }

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
