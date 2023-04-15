 <!-- User prompt -->

 <div class="prompt-wrapper h-100" x-data="
        {
            processing: false,
            can_talk: true,
            recognition: null,
            selectedPreset: '',
            presets: [
            {
                label: 'Default',
                options: [
                    {text: 'Free Writing', value: ''}
                ]
            },
            {
                label: 'Tutorial',
                options: [
                    {text: 'Table Of Contents', value: 'Complete the table of contents for my new [topic] for [audience] crash course. \n - What is [topic] \n - First Steps \n - Data Types \n - Functions \n '},
                    {text: 'Example TOC', value: 'Complete the table of contents for my new PHP for Intermediate Developers crash course. \n - What is PHP today \n - First Steps \n - Data Types \n - Functions \n '},
                    {text: 'Complete Chapter', value: 'Complete chapter [copy chapter] in [language]: \n '},
                    {text: 'Example', value: 'Complete chapters in PHP. \nIntroduction:\n- What is PHP today\nFirst Steps\n- Installing PHP\n- Setting up a development environment\n- Basic syntax'}
                ]
            },
            {
                label: 'Code',
                options: [
                    {text: 'Translate', value: 'Translate to [language] code. \nThe software must ask to the user to insert a number and return the sum of all inserted numbers'},
                    {text: 'Explain', value: 'Explain [topic] in [language] with code blocks.'},
                    {text: 'Review', value: 'Review this code.'},
                    {text: 'Refactor Simple', value: 'Refactor this code. \n\`\`\`language \n\`\`\`' },
                    {text: 'Refactor', value: 'Refactor this code following [convention] conventions.'},
                    {text: 'Refactor & Improve', value: 'Refactor this code and improve it using [language] [version] features.'},
                    {text: 'Complete', value: 'Complete the following [language] code.\n \`\`\`language \n [your code here] \n\`\`\`'},
                ]
            },
            {
                label: 'Documents',
                options: [
                    {text: 'Generate', value: 'Generate a document draft about [topic]:\n'},
                    {text: 'Proposal', value: 'Write a proposal for [client] about [project]:\n '},
                ]
            },
            {
                label: 'Social',
                options: [
                    {text: 'Twitter', value: 'Write a tween [topic]'},
                    {text: 'Linkedin', value: 'Write a Linkedin Post about [topic] for [audience]: \n '},
                    {text: 'Istagram', value: 'Write a caption for Istagram: \n '},

                ]
            }],
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
               
                    this.recognition = 'SpeechRecognition' in window ? new SpeechRecognition() : new webkitSpeechRecognition() ;
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
            }

        }
    ">
     <div class="toolbar d-flex align-items-end justify-content-between justify-content-lg-center py-2 px-1 border-top border-secondary gap-2">
         @if (Route::has('admin.conversations.index'))
         <a class="btn text-white" href="{{url()->previous()}}" title="{{__('Return back')}}">
             @include('partials.icons.back')
         </a>
         <a href="{{route('admin.conversations.index')}}" class="btn text-white">
             @include('partials.icons.list')
             <div class="fs_sm text-uppercase">{{__('Chat')}}</div>
         </a>
         @endif
         <!-- Button trigger modal -->
         <button type="button" class="btn text-white" data-bs-toggle="modal" data-bs-target="#chat_settings_modal">
             <div class="icon d-flex flex-column align-items-center">
                 <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-three-dots-vertical" viewBox="0 0 16 16">
                     <path d="M9.5 13a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z" />
                 </svg>
                 <span class="text-uppercase fs_sm">Settings</span>
             </div>
         </button>

         <!-- Modal -->
         <div class="modal fade" id="chat_settings_modal" tabindex="-1" role="dialog" aria-labelledby="chatSettings" aria-hidden="true">
             <div class="modal-dialog modal-lg" role="document">
                 <div class="modal-content bg-secondary">
                     <div class="modal-header">
                         <h5 class="modal-title" id="chatSettings">Chat Settings</h5>
                         <button type="button" class="btn-close bg-white" data-bs-dismiss="modal" aria-label="Close"></button>

                     </div>
                     <div class="modal-body">
                         <div class="mb-3">
                             <label for="prompt-preset">Assistant Presets</label>
                             <select name="prompt-preset" id="prompt-preset" class="form-control" x-model='selectedPreset'>
                                 <template x-for="(preset, i) in presets" :key="i">
                                     <optgroup :label="preset.label">
                                         <template x-for="(option, i) in preset.options" :key="i">
                                             <option :value="option.value" x-text="option.text"></option>
                                         </template>
                                     </optgroup>
                                 </template>
                             </select>
                         </div>
                         @include('partials.chat.settings')
                     </div>

                 </div>
             </div>
         </div>

         <button form="chat-form" title="Send Message" class="btn text-white" type="submit" x-show="!processing" @click.prevent="submitChatPrompt($event)">
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
     </div>
     <textarea form="chat-form" class="form-control rounded-0 h-100" name="prompt" id="prompt" placeholder="Me: " x-model="selectedPreset"></textarea>

     <form id="chat-form" method="POST" action="{{ $url }}" x-show="!processing">
         @csrf
     </form>
 </div>

 <!-- /Chat Form -->
 <style>
     #prompt {
         resize: none;
         height: 50px;
         max-height: 50vh;
     }
 </style>

 <script>
     window.onload = function() {

         const conversationEl = document.querySelector('.conversation');
         console.log(conversationEl);
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