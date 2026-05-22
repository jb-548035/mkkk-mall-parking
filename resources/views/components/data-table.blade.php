@props(['headers', 'rows', 'actions' => true])

<div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    @foreach($headers as $header)
                    <th>{{ $header }}</th>
                    @endforeach
                    @if($actions)
                    <th>Actions</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                {{ $slot }}
            </tbody>
        </table>
    </div>
</div>