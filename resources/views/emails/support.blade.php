<x-mail::message>
# Support Message
    {{ auth()->user()->name }}

    {{ $subject }}

    {{ $message }}


Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
