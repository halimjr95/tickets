<x-app-layout>
    <x-slot name="header">
        {{ $ticket->title }}
    </x-slot>

    @hasanyrole('admin|agent')
    <div class="mb-4 flex justify-end">
        @if($ticket->isOpen())
        <form action="{{ route('tickets.close', $ticket) }}" method="POST" style="display: inline-block;">
            @csrf
            @method('PATCH')
            <x-primary-button>
                Close
            </x-primary-button>
        </form>
        @elseif(!$ticket->isArchived())
        <form action="{{ route('tickets.reopen', $ticket) }}" method="POST" class="mr-2" style="display: inline-block;">
            @csrf
            @method('PATCH')
            <x-primary-button>
                Reopen
            </x-primary-button>
        </form>

        <form action="{{ route('tickets.archive', $ticket) }}" method="POST" style="display: inline-block;">
            @csrf
            @method('PATCH')
            <x-primary-button>
                Archive
            </x-primary-button>
        </form>
        @endif
    </div>
    @endhasanyrole

    <div class="space-y-4">
        <div class="min-w-0 rounded-lg bg-white p-4 shadow-xs">
            <p class="text-gray-600">
                {{ $ticket->message }}
            </p>
        </div>

        @if($ticket->getMedia('tickets_attachments')->count())
        <div class="min-w-0 rounded-lg bg-white p-4 shadow-xs">
            <h4 class="mb-4 font-semibold text-gray-600">
                Attachments
            </h4>
            <div class="flex space-x-2">
                @foreach($ticket->getMedia('tickets_attachments') as $media)
                <div class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-200">
                    <a href="{{ route('attachment-download', $media) }}" class="hover:underline">
                        <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                            style="width: 20px">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m.75 12 3 3m0 0 3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                    </a>
                </div>
                @endforeach
            </div>

            {{-- @foreach($ticket->getMedia('tickets_attachments') as $media)
            <p>
                <a href="{{ route('attachment-download', $media) }}" class="hover:underline">{{ $media->file_name }}</a>
            </p>
            <img src="{{ $media->file_name }}" alt="">
            @endforeach --}}
        </div>
        @endif

        <div class="min-w-0 rounded-lg bg-white p-4 shadow-xs space-y-4">
            <h4 class="mb-4 font-semibold text-gray-600">
                Messages
            </h4>

            @if(!$ticket->isArchived())
            <form action="{{ route('message.store', $ticket) }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div>
                    <textarea id="message" name="message"
                        class="mt-1 block h-32 w-full rounded-md border-gray-300 shadow-sm focus-within:text-primary-600 focus:border-primary-300 focus:ring-primary-200 focus:ring focus:ring-opacity-50"
                        required>{{ old('message') }}</textarea>
                    <x-input-error :messages="$errors->get('message')" class="mt-2" />
                </div>
                <div class="mt-4">
                    <input type="file" name="attachments[]" multiple>
                </div>

                <x-primary-button class="mt-4">
                    Submit
                </x-primary-button>
            </form>
            @endif

            @forelse($ticket->messages as $message)
            <div class="mb-2 w-full">
                <div
                    class="flex {{ auth()->user()->id === $message->user_id ? 'justify-end' : 'justify-start' }} w-full">
                    <div
                        class="w-full max-w-[50%] rounded-lg p-4 {{ auth()->user()->id === $message->user_id ? 'bg-blue-100' : 'bg-gray-100' }} shadow">
                        <p class="text-gray-800 mb-2">{{ $message->message }}</p>

                        <div class="flex justify-between items-center mt-2">
                            <div class="flex items-center">
                                <span class="font-semibold text-gray-800 text-sm mr-2">{{ $message->user->name }}</span>
                                <span class="text-gray-500 text-xs">{{ $message->created_at->format('Y-m-d H:i')
                                    }}</span>
                            </div>

                            <div class="flex space-x-2">
                                @foreach($message->getMedia('messages_attachments') as $media)
                                <div class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-200">
                                    <a href="{{ route('attachment-download', $media) }}" class="hover:underline">
                                        <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                            style="width: 20px">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m.75 12 3 3m0 0 3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                        </svg>
                                    </a>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <p class="text-gray-600">No messages found.</p>
            @endforelse


        </div>
    </div>
</x-app-layout>