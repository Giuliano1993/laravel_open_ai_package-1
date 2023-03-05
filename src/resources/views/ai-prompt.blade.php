 <!-- User prompt -->

 <div class="prompt-wrapper" style="display: flex;" x-data>
     <textarea oninput="autoResize(this)" form="chat-form" class="form-control rounded-0" name="prompt" id="prompt" placeholder="Me: "></textarea>
     <a class="p-3 text-light" href="{{ $url }}" onclick="event.preventDefault(); document.getElementById('chat-form').submit();">
         <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-send" viewBox="0 0 16 16">
             <path d="M15.854.146a.5.5 0 0 1 .11.54l-5.819 14.547a.75.75 0 0 1-1.329.124l-3.178-4.995L.643 7.184a.75.75 0 0 1 .124-1.33L15.314.037a.5.5 0 0 1 .54.11ZM6.636 10.07l2.761 4.338L14.13 2.576 6.636 10.07Zm6.787-8.201L1.591 6.602l4.339 2.76 7.494-7.493Z" />
         </svg>
     </a>
 </div>
 <form id="chat-form" method="POST" action="{{ $url }}">
     @csrf
 </form>

 <!-- /Chat Form -->
 <style>
     #prompt {
         resize: none;
         height: 70px;
         max-height: 50vh;
     }
 </style>

 <script>
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
