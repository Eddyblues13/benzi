@if (session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <b>Error!</b> {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif
@if (session('status') || session('message'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <b>Success!</b> {{ session('status') ?? session('message') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif
@if ($errors->any())
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <b>Please fix the following:</b>
    <ul class="mb-0 mt-1">
        @foreach ($errors->all() as $message)
        <li>{{ $message }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif
