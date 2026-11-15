@include('dashboard.header')
<div class="content-body">
    <div class="container-fluid">
        @include('dashboard.partials.alerts')
        <div class="row justify-content-center">
            <div class="col-xl-8 col-lg-10">
                <div class="card">
                    <div class="card-body p-4 p-md-5">
                        <h4 class="text-center mb-4">Create a Support Ticket</h4>
                        <form action="{{ route('ticket.submit') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Name</label>
                                    <input type="text" class="form-control" value="{{ Auth::user()->name }}" readonly>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" value="{{ Auth::user()->email }}" readonly>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="subject">Subject</label>
                                <input type="text" id="subject" name="subject" class="form-control" maxlength="150"
                                    value="{{ old('subject') }}" placeholder="What do you need help with?" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="message">Message</label>
                                <textarea id="message" name="message" class="form-control" rows="6" maxlength="5000"
                                    placeholder="Describe the issue" required>{{ old('message') }}</textarea>
                            </div>
                            <button type="submit" class="btn btn-success">Send</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('dashboard.footer')
