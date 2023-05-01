<style>
    .aia-bg-dark {
        background-color: #2A292D;
    }

    .aia-text-white {
        color: white;
    }

    #ai-chat-component .panel {
        width: 100%;
        height: 100%;
        position: fixed;
        bottom: 0;
        right: 0;
        overflow: hidden;
    }


    #ai-chat-component .panel>.container {
        max-width: 98%;
        margin: auto;
        height: 100%;
    }

    #ai-chat-component .options {
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    #ai-chat-component .settings {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    #ai-chat-component .panel>.container .conversation {
        border-radius: 0.5rem;
        padding: 1rem;
        margin-top: 2rem;
        margin-bottom: 2rem;
        overflow-y: auto;
        height: calc(100% - 80px);
    }

    #ai-chat-component .panel .conversation pre {
        background-color: black;
        padding: 1rem;
        color: white;
        border-radius: 1rem;
        margin-top: 1rem;
    }

    #ai-chat-component .panel #prompt {
        width: 100%;
        background: #0000;
        color: white;
        border: none;
        border-bottom: 1px solid #808080;

    }

    #ai-chat-component .prompt-wrapper {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    #ai-chat-component .ai-assistant-icon {
        display: flex;
        position: fixed;
        bottom: 0;
        right: 0;
        margin: 1rem;
        background-color: #2A292D;
        color: white;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        justify-content: center;
        align-items: center;
    }
</style>
<!-- /ai-chat-component Styles -->
<div id="ai-chat-component" x-data="assistant">

    <div class="panel aia-bg-dark aia-text-white py-4">

        <div class="container">
            <div class="settings">


                <select form="chat-form" name="model" id="model">
                    <option value="text-davinci-003" selected disabled>Default (text/davinci)</option>
                    <optgroup label="text">
                        <option value="text-davinci-003">Davinci</option>
                        <option value="text-curie-001">Curie</option>
                        <option value="text-babbage-001">Babbage</option>
                        <option value="text-ada-001">Ada</option>
                    </optgroup>
                    <optgroup label="code">
                        <option value="code-davinci-002">Davinci</option>
                        <option value="code-cushman-001">Cushman</option>
                    </optgroup>
                </select>



                <div class="options">
                    <div class="minimize" @click="minimizeWindow()">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrows-collapse" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h13a.5.5 0 0 1 0 1h-13A.5.5 0 0 1 1 8Zm7-8a.5.5 0 0 1 .5.5v3.793l1.146-1.147a.5.5 0 0 1 .708.708l-2 2a.5.5 0 0 1-.708 0l-2-2a.5.5 0 1 1 .708-.708L7.5 4.293V.5A.5.5 0 0 1 8 0Zm-.5 11.707-1.146 1.147a.5.5 0 0 1-.708-.708l2-2a.5.5 0 0 1 .708 0l2 2a.5.5 0 0 1-.708.708L8.5 11.707V15.5a.5.5 0 0 1-1 0v-3.793Z" />
                        </svg>
                    </div>
                    <div class="resize" @click="resizeWindow()">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrows-angle-expand" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M5.828 10.172a.5.5 0 0 0-.707 0l-4.096 4.096V11.5a.5.5 0 0 0-1 0v3.975a.5.5 0 0 0 .5.5H4.5a.5.5 0 0 0 0-1H1.732l4.096-4.096a.5.5 0 0 0 0-.707zm4.344-4.344a.5.5 0 0 0 .707 0l4.096-4.096V4.5a.5.5 0 1 0 1 0V.525a.5.5 0 0 0-.5-.5H11.5a.5.5 0 0 0 0 1h2.768l-4.096 4.096a.5.5 0 0 0 0 .707z" />
                        </svg>
                    </div>

                </div>

            </div>
            <!-- Conversation -->
            <div class="conversation" :style="conversation_height"></div>
            <!-- /Conversation -->

            <!-- Chat Form -->

            <!-- Preset istructions -->
            <div class="mb-3">
                <textarea form="chat-form" type="text" class="form-control" name="istructions" id="istructions" hidden>
                    @if(session('chat'))
                    {{session('chat')}}
                    @else
                    {{ $text ?? $text }}
                    @endif
                </textarea>
            </div>
            <!-- User prompt -->
            <div class="prompt-wrapper">
                <textarea form="chat-form" class="form-control" name="prompt" id="prompt" :rows="rows" placeholder="Me: " @keyup.enter="rows++"></textarea>
                <a class="p-2" href="{{ url('/ai-completation')}}" onclick="event.preventDefault(); document.getElementById('chat-form').submit();">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-send" viewBox="0 0 16 16">
                        <path d="M15.854.146a.5.5 0 0 1 .11.54l-5.819 14.547a.75.75 0 0 1-1.329.124l-3.178-4.995L.643 7.184a.75.75 0 0 1 .124-1.33L15.314.037a.5.5 0 0 1 .54.11ZM6.636 10.07l2.761 4.338L14.13 2.576 6.636 10.07Zm6.787-8.201L1.591 6.602l4.339 2.76 7.494-7.493Z" />
                    </svg>
                </a>
            </div>
            <form id="chat-form" method="POST" action="{{ $url }}">
                @csrf
            </form>
            <!-- /Chat Form -->
        </div>
    </div>
    <div class="ai-assistant-icon" @click="minimizeWindow()" x-show="collapsed">
        <svg xmlns=" http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-robot" viewBox="0 0 16 16">
            <path d="M6 12.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5ZM3 8.062C3 6.76 4.235 5.765 5.53 5.886a26.58 26.58 0 0 0 4.94 0C11.765 5.765 13 6.76 13 8.062v1.157a.933.933 0 0 1-.765.935c-.845.147-2.34.346-4.235.346-1.895 0-3.39-.2-4.235-.346A.933.933 0 0 1 3 9.219V8.062Zm4.542-.827a.25.25 0 0 0-.217.068l-.92.9a24.767 24.767 0 0 1-1.871-.183.25.25 0 0 0-.068.495c.55.076 1.232.149 2.02.193a.25.25 0 0 0 .189-.071l.754-.736.847 1.71a.25.25 0 0 0 .404.062l.932-.97a25.286 25.286 0 0 0 1.922-.188.25.25 0 0 0-.068-.495c-.538.074-1.207.145-1.98.189a.25.25 0 0 0-.166.076l-.754.785-.842-1.7a.25.25 0 0 0-.182-.135Z" />
            <path d="M8.5 1.866a1 1 0 1 0-1 0V3h-2A4.5 4.5 0 0 0 1 7.5V8a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1v1a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-1a1 1 0 0 0 1-1V9a1 1 0 0 0-1-1v-.5A4.5 4.5 0 0 0 10.5 3h-2V1.866ZM14 7.5V13a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V7.5A3.5 3.5 0 0 1 5.5 4h5A3.5 3.5 0 0 1 14 7.5Z" />
        </svg>
    </div>
</div>
<!-- /#ai-chat-component Markup -->
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('assistant', () => ({
            rows: 4,
            status: 'normal',
            collapsed: false,
            get conversation_height() {
                return `height: calc(100% - ${this.rows * 20}px);`;
            },
            /**
             * Function to resize the chat assistant window
             *
             */
            resizeWindow() {
                const options = document.querySelector('#ai-chat-component .resize')
                if (this.status !== 'full') {
                    document.querySelector('.panel').style.width = '100%'
                    document.querySelector('.panel').style.height = '100vh'
                    document.querySelector('.panel').style.margin = '0'
                    this.status = 'full'


                } else {
                    document.querySelector('.panel').style.width = '100%'
                    document.querySelector('.panel').style.height = 'fit-content'
                    this.status = 'normal'
                }
            },
            minimizeWindow() {
                const options = document.querySelector('#ai-chat-component .minimize')
                if (this.collapsed) {
                    // show the panel
                    document.querySelector('.panel').style.display = 'block';
                    // hide the chat icon
                    document.querySelector('.ai-assistant-icon').style.display = 'none'
                    // resize the panel
                    document.querySelector('.panel').style.width = '100%'
                    document.querySelector('.panel').style.height = 'fit-content'
                    document.querySelector('.panel').style.margin = '0'
                    this.collapsed = false


                } else {
                    // hide the panel
                    document.querySelector('.panel').style.display = 'none';
                    // show the icon
                    document.querySelector('.ai-assistant-icon').style.display = 'flex'
                    this.collapsed = true
                }
            }
        }))
    })

    /* Chat

    TODO: remove when added db persistency*/
    const textarea = document.querySelector('#ai-chat-component textarea#istructions')

    const text = textarea.value
    let newText = text.replaceAll("\n", '<br>')
    let newText_rev = newText.replaceAll("Me:", '<strong>Me: </strong>')
    let newText_rev_2 = newText_rev.replaceAll("AI:", '<strong>AI: </strong>')

    document.querySelector('.conversation').innerHTML = newText_rev_2
</script>
<!-- #ai-chat-component Script -->