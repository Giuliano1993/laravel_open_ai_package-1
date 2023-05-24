<div class="conversation p-2" x-data="{
    onlyStarred: false,
    filterMessages(){

        this.onlyStarred = !this.onlyStarred;
        const messages = document.querySelectorAll('.message');
        messages.forEach(message => {

            if (message.getAttribute('data-message-starred') == 0 && this.onlyStarred){
                message.style.display = 'none'
            } else {
                message.style.display = 'block'
            }
        })

    }}">
    <!-- TODO: Copy this content back in the package files and refactor with multiple components or partials -->
    <div class="row flex-column g-4">
        @forelse( $messages as $message)
        <div class="message col" data-message-starred="{{$message->has_star}}">
            <!-- TODO: Make a component for the meta date element -->
            <div class="metadata my-2 d-flex justify-content-between align-items-center">

                <!-- TODO: Make a component for the message author details -->
                <div class="metadata_user message_data text-white">
                    @if($message->status == 'sent')
                    <div class="avatar">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-badge" viewBox="0 0 16 16">
                            <path d="M6.5 2a.5.5 0 0 0 0 1h3a.5.5 0 0 0 0-1h-3zM11 8a3 3 0 1 1-6 0 3 3 0 0 1 6 0z" />
                            <path d="M4.5 0A2.5 2.5 0 0 0 2 2.5V14a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2.5A2.5 2.5 0 0 0 11.5 0h-7zM3 2.5A1.5 1.5 0 0 1 4.5 1h7A1.5 1.5 0 0 1 13 2.5v10.795a4.2 4.2 0 0 0-.776-.492C11.392 12.387 10.063 12 8 12s-3.392.387-4.224.803a4.2 4.2 0 0 0-.776.492V2.5z" />
                        </svg>
                        <span class="username text-capitalize d-none d-sm-inline-block">{{$message->conversation->user->name}}</span>
                    </div>
                    @else
                    <div class="ai">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-robot" viewBox="0 0 16 16">
                            <path d="M6 12.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5ZM3 8.062C3 6.76 4.235 5.765 5.53 5.886a26.58 26.58 0 0 0 4.94 0C11.765 5.765 13 6.76 13 8.062v1.157a.933.933 0 0 1-.765.935c-.845.147-2.34.346-4.235.346-1.895 0-3.39-.2-4.235-.346A.933.933 0 0 1 3 9.219V8.062Zm4.542-.827a.25.25 0 0 0-.217.068l-.92.9a24.767 24.767 0 0 1-1.871-.183.25.25 0 0 0-.068.495c.55.076 1.232.149 2.02.193a.25.25 0 0 0 .189-.071l.754-.736.847 1.71a.25.25 0 0 0 .404.062l.932-.97a25.286 25.286 0 0 0 1.922-.188.25.25 0 0 0-.068-.495c-.538.074-1.207.145-1.98.189a.25.25 0 0 0-.166.076l-.754.785-.842-1.7a.25.25 0 0 0-.182-.135Z" />
                            <path d="M8.5 1.866a1 1 0 1 0-1 0V3h-2A4.5 4.5 0 0 0 1 7.5V8a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1v1a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-1a1 1 0 0 0 1-1V9a1 1 0 0 0-1-1v-.5A4.5 4.5 0 0 0 10.5 3h-2V1.866ZM14 7.5V13a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V7.5A3.5 3.5 0 0 1 5.5 4h5A3.5 3.5 0 0 1 14 7.5Z" />
                        </svg>
                        <span class="text-capitalize d-none d-sm-inline-block">Ai-assistant</span>
                    </div>
                    @endif
                </div>
                <!-- /.message_data -->
                <div class="metadata_date text-center mb-1">
                    <div class="date fs_sm text-muted">
                        {{ \Carbon\Carbon::parse($message->created_at)->diffForHumans() }}
                    </div>
                </div>
            </div>
            <!-- /.metadata -->
            <!-- TODO: Make a component for the entire message card -->
            <div id="card-message-{{ $message->id }}" class="card border-0 shadow {{$message->status == 'sent' ? 'bg-dark-subtle' : 'bg-light-subtle'}}">

                @if ($message->is_issue)
                <div class="card-header">

                    <span>
                        <strong>
                            <i class="bi bi-github"></i>
                            Issue Url:
                        </strong>
                        <a href="{{$message->issue_url}}" target="_blank" class=" text-bg-info-subtle">
                            {{$message->issue_url}}</a>
                    </span>
                </div>
                @endif

                <div class="card-body" contenteditable="false">
                    {!! Str::of($message->body)->markdown() !!}
                </div>

                <div class="card-footer text-muted d-flex justify-content-between align-items-center" x-data="star_message({{$message->has_star}}, {{$message->id}}, {{$message->conversation_id}})">
                    <!-- TODO: Make a component for the star  -->
                    <button type="submit" form="star-message-{{$message->id}}" class="star-button btn btn-outline-light border-0" data-message-id="{{ $message->id }}" @click.prevent="starMessage(conversationId, messageId)">

                        <template x-if="starred">

                            <div class="star text-warning">
                                <svg xmlns=" http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-star-fill" viewBox="0 0 16 16">
                                    <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z" />
                                </svg>
                            </div>
                        </template>

                        <template x-if="!starred">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-star" viewBox="0 0 16 16">
                                <path d="M2.866 14.85c-.078.444.36.791.746.593l4.39-2.256 4.389 2.256c.386.198.824-.149.746-.592l-.83-4.73 3.522-3.356c.33-.314.16-.888-.282-.95l-4.898-.696L8.465.792a.513.513 0 0 0-.927 0L5.354 5.12l-4.898.696c-.441.062-.612.636-.283.95l3.523 3.356-.83 4.73zm4.905-2.767-3.686 1.894.694-3.957a.565.565 0 0 0-.163-.505L1.71 6.745l4.052-.576a.525.525 0 0 0 .393-.288L8 2.223l1.847 3.658a.525.525 0 0 0 .393.288l4.052.575-2.906 2.77a.565.565 0 0 0-.163.506l.694 3.957-3.686-1.894a.503.503 0 0 0-.461 0z" />
                            </svg>
                        </template>

                    </button>
                    <!-- /.star -->


                    <!-- TODO: Make a component for the toolbar -->
                    <div class="btn-toolbar justify-content-end" role="toolbar" aria-label="Toolbar">
                        <div class="btn-group" role="group" aria-label="Button Group">

                            <!-- TODO: Make a component for the git-offcanvas -->
                            <button type="button" class="btn btn-sm text-muted align-self-end" data-bs-toggle="offcanvas" data-bs-target="#git-offcanvas-{{$message->id}}" aria-controls="git-offcanvas-{{$message->id}}">
                                <span class="d-none d-sm-inline-block">{{__('Git') }}</span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-git" viewBox="0 0 16 16">
                                    <path d="M15.698 7.287 8.712.302a1.03 1.03 0 0 0-1.457 0l-1.45 1.45 1.84 1.84a1.223 1.223 0 0 1 1.55 1.56l1.773 1.774a1.224 1.224 0 0 1 1.267 2.025 1.226 1.226 0 0 1-2.002-1.334L8.58 5.963v4.353a1.226 1.226 0 1 1-1.008-.036V5.887a1.226 1.226 0 0 1-.666-1.608L5.093 2.465l-4.79 4.79a1.03 1.03 0 0 0 0 1.457l6.986 6.986a1.03 1.03 0 0 0 1.457 0l6.953-6.953a1.031 1.031 0 0 0 0-1.457" />
                                </svg>
                            </button>

                            <div class="offcanvas offcanvas-end bg-secondary" tabindex="-1" id="git-offcanvas-{{$message->id}}" aria-labelledby="staticBackdropLabel">
                                <div class="offcanvas-header">
                                    <!-- <h5 class="offcanvas-title" id="staticBackdropLabel"></h5> -->
                                    <button type="button" class="btn-close bg-light" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                                </div>
                                {{-- TODO: update how data are retrived and passed to git_providers() --}}
                                <div class="offcanvas-body" x-data="git_providers({{$git ? $git : json_encode(session('git_providers'))}})">

                                    <!-- Plain html  -->

                                    <template x-if="providers">
                                        <div class="mb-3">
                                            <label class="text-white" for="provider">Available provider</label>


                                            <select class="form-control" x-model="selected_provider">
                                                <template x-for="provider in providers">
                                                    <option x-text="provider.name"></option>
                                                </template>
                                                <option value="" disabled x-show="providers.length === 0">Connect a provider</option>
                                            </select>

                                        </div>
                                    </template>
                                    <template x-if="providers && selected_provider=='bitbucket'">
                                        <div class="mb-3">
                                            <label class="text-white" for="workspace">Workspace</label>
                                            <select x-init="getWorkspaces()" x-model="selected_workspace" class="form-control">
                                                <template x-for="workspace in workspaces">
                                                    <option x-value="workspace" x-text="workspace"></option>
                                                </template>
                                            </select>
                                        </div>
                                    </template>
                                    <div class="mb-3">
                                        <label class="text-white d-flex justify-content-between mb-2" for="repository">Repositories

                                            <span class="btn btn-sm text-muted" @click="getRepositories(selected_provider)" :class="{ 'pulse text-white': !repositories.length > 0 }">
                                                <span class="text-uppercase fs_sm">Download</span>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cloud-download" viewBox="0 0 16 16">
                                                    <path d="M4.406 1.342A5.53 5.53 0 0 1 8 0c2.69 0 4.923 2 5.166 4.579C14.758 4.804 16 6.137 16 7.773 16 9.569 14.502 11 12.687 11H10a.5.5 0 0 1 0-1h2.688C13.979 10 15 8.988 15 7.773c0-1.216-1.02-2.228-2.313-2.228h-.5v-.5C12.188 2.825 10.328 1 8 1a4.53 4.53 0 0 0-2.941 1.1c-.757.652-1.153 1.438-1.153 2.055v.448l-.445.049C2.064 4.805 1 5.952 1 7.318 1 8.785 2.23 10 3.781 10H6a.5.5 0 0 1 0 1H3.781C1.708 11 0 9.366 0 7.318c0-1.763 1.266-3.223 2.942-3.593.143-.863.698-1.723 1.464-2.383z" />
                                                    <path d="M7.646 15.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 14.293V5.5a.5.5 0 0 0-1 0v8.793l-2.146-2.147a.5.5 0 0 0-.708.708l3 3z" />
                                                </svg>

                                            </span>
                                        </label>
                                        <select class="form-control" x-model="selected_repo" x-init="selected_repo = repositories[0].name">
                                            <option value="">Select a repository, or download</option>
                                            <template x-for="repo in repositories">
                                                <option x-bind:value="selected_provider == 'gitlab' ? repo.id : repo.name" x-text="repo.name"></option>
                                            </template>
                                        </select>
                                        <small class="text-muted" x-show="selected_repo">Selected repo:
                                            <span x-text="selected_repo"></span>
                                        </small>
                                        <small class="text-danger" x-show="!selected_repo">Select a repository from the list</small>
                                    </div>
                                    <div class="alert alert-info" role="alert" x-show="issue_error_message">
                                        <strong>Ops!</strong>
                                        <span x-text="issue_error_message"></span>
                                    </div>
                                    <div class="mb-3">
                                        <label class="text-white" for="issue">Issue description</label>
                                        <input name="issue" id="issue" type="text" class="form-control" placeholder="Issue name here" x-model="issue_title">
                                    </div>

                                    <button type="submit" class="btn btn-primary" @click="createIssue(selected_provider, selected_repo, issue_title, {{$message->id}})" :class="{'d-none': !selected_repo || !issue_title.length > 0}">Add as issue</button>

                                    <template x-if="is_issue_open">
                                        <div class="alert alert-success" role="alert">
                                            <strong>Issue Created</strong> <a x-bind:href="issue.html_url" target="_blank">See on <span x-text="selected_provider"></span></a>
                                        </div>
                                    </template>

                                </div>
                            </div>
                            <!-- /.git-offcanvas-{{$message->id}} -->

                            <!-- TODO: Make a component for the files offcanvas -->
                            <button type="button" class="btn btn-sm text-muted align-self-end" data-bs-toggle="offcanvas" data-bs-target="#files-offcanvas" aria-controls="files-offcanvas">
                                <span class="d-none d-sm-inline-block">{{__('Save as File') }}</span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-plus" viewBox="0 0 16 16">
                                    <path d="M8.5 6a.5.5 0 0 0-1 0v1.5H6a.5.5 0 0 0 0 1h1.5V10a.5.5 0 0 0 1 0V8.5H10a.5.5 0 0 0 0-1H8.5V6z" />
                                    <path d="M2 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2zm10-1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1z" />
                                </svg>
                            </button>
                            <div class="offcanvas offcanvas-end bg-secondary" tabindex="-1" id="files-offcanvas" aria-labelledby="staticBackdropLabel">
                                <div class="offcanvas-header">
                                    <!-- <h5 class="offcanvas-title" id="staticBackdropLabel"></h5> -->
                                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                                </div>
                                <div class="offcanvas-body">
                                    <div class="mb-3">
                                        <label class="text-white" for="file_name">File name</label>
                                        <input name="file_name" id="file_name" type="text" class="form-control" placeholder="mydocument.md">
                                        <small class="text-muted">Input the filename including the file extension</small>
                                    </div>

                                    <button type="submit" class="btn btn-primary">Save file</button>
                                </div>
                            </div>
                            <!-- /.file -->

                            <!-- TODO: Make a component for the voice-controls  -->
                            <div class="voice-controls" x-data="voice_controls('{{app()->getLocale()}}')">
                                <button type="button" class="read btn btn-sm text-muted align-self-end" @click="read( {{ $message->id }})" x-show="!window.speechSynthesis.speaking">
                                    <span class="d-none d-sm-inline-block" x-bind:class="{ 'text-warning': status.message === {{$message->id}} && status.reading }"> {{__('Read')}}</span>

                                    <svg xmlns=" http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-speaker" viewBox="0 0 16 16">
                                        <path d="M12 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h8zM4 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H4z" />
                                        <path d="M8 4.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5zM8 6a2 2 0 1 0 0-4 2 2 0 0 0 0 4zm0 3a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3zm-3.5 1.5a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z" />
                                    </svg>
                                </button>
                                <!-- /.read -->

                                <button class="mute btn btn-sm text-muted align-self-end" @click="stopReading()">
                                    <span class="d-none d-sm-inline-block">{{__('Mute') }}</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-volume-mute" viewBox="0 0 16 16">
                                        <path d="M6.717 3.55A.5.5 0 0 1 7 4v8a.5.5 0 0 1-.812.39L3.825 10.5H1.5A.5.5 0 0 1 1 10V6a.5.5 0 0 1 .5-.5h2.325l2.363-1.89a.5.5 0 0 1 .529-.06zM6 5.04 4.312 6.39A.5.5 0 0 1 4 6.5H2v3h2a.5.5 0 0 1 .312.11L6 10.96V5.04zm7.854.606a.5.5 0 0 1 0 .708L12.207 8l1.647 1.646a.5.5 0 0 1-.708.708L11.5 8.707l-1.646 1.647a.5.5 0 0 1-.708-.708L10.793 8 9.146 6.354a.5.5 0 1 1 .708-.708L11.5 7.293l1.646-1.647a.5.5 0 0 1 .708 0z" />
                                    </svg>
                                </button>
                                <!-- /.mute -->
                            </div>
                            <!-- /.voice-controls -->

                            <!-- TODO: Make a component for the copy content -->

                            <button type="button" class="btn btn-sm text-muted align-self-end" @click="copyContent({{$message->id}})" x-data='copy_message'>
                                <div class="clipboard_copy text-muted">
                                    <span class="d-none d-sm-inline-block">{{__('Copy')}}</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-clipboard" viewBox="0 0 16 16">
                                        <path d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1h1a1 1 0 0 1 1 1V14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3.5a1 1 0 0 1 1-1h1v-1z" />
                                        <path d="M9.5 1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5h3zm-3-1A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3z" />
                                    </svg>
                                </div>
                                <div class="clipboard_check d-none">
                                    <span>{{__('Copied')}}</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-clipboard-check-fill" viewBox="0 0 16 16">
                                        <path d="M6.5 0A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3Zm3 1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5h3Z" />
                                        <path d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1A2.5 2.5 0 0 1 9.5 5h-3A2.5 2.5 0 0 1 4 2.5v-1Zm6.854 7.354-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 0 1 .708-.708L7.5 10.793l2.646-2.647a.5.5 0 0 1 .708.708Z" />
                                    </svg>
                                </div>
                            </button>
                            <!-- /.copy -->
                        </div>

                    </div>
                    <!-- /.toolbar -->


                </div>

            </div>
            <!-- #card-message-id -->

        </div>
        @empty
        <div class="col-12">
            <p>No messages yet.</p>
        </div>
        @endforelse
    </div>
    <div class="py-4" x-on:click="filterMessages()">Only Starred messages</div>
</div>