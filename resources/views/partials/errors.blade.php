@if ($errors->any())
    <div class="alert alert-error" role="alert" tabindex="-1">
        <strong>Please correct the following:</strong>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
