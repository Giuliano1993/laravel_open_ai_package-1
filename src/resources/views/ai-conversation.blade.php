<div class="conversation p-2">
    <div class="row flex-column g-4">
        @forelse( $messages as $message)
        <div class="col">
            <div class="card {{$message->status == 'sent' ? 'bg-secondary' : 'bg-info'}}">
                <div class="card-header">
                    @if($message->status == 'sent')
                    <div class="avatar d-inline-block bg-dark test-white rounded-circle">
                        <i class="fas fa-user fa-sm fa-fw"></i>
                    </div>
                    @else
                    <div class="ai">
                        <i class="fas fa-robot fa-sm fa-fw"></i>
                    </div>
                    @endif
                </div>
                <div class="card-body">
                    {!! $message->body !!}
                </div>
            </div>

        </div>
        @empty
        <div class="col-12">
            <p>No messages yet.</p>
        </div>
        @endforelse
    </div>
</div>
