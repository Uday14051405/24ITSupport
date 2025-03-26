<x-master-layout>
    <div class="container-fluid">
        @include('partials._handyman')

        <div class="row">
            <div class="col-lg-12">
                <div class="card card-block card-stretch">
                    <div class="card-body p-0">
                        <div class="d-flex justify-content-between align-items-center p-3 flex-wrap gap-3">
                            <h5 class="fw-bold">{{$handymandata->first_name .' '. $handymandata->last_name}} - {{$pageTitle}}</h5>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        <form action="{{ route('handyman.service-data.save', $handymandata->id) }}" method="POST">
                            @csrf
                            <p style="font-size: large;">Will <b>{{ $handymandata->first_name }}</b> able to provide this service?</p>

                            @foreach($categories as $category)
                                <div class="row mb-3 align-items-center">
                                    <label class="col-md-4"><strong>{{ $category->name }}</strong></label>
                                    <div class="col-md-8 d-flex">
                                        <div class="form-check me-3">
                                            <input type="radio" name="answers[{{ $category->id }}]" value="yes" class="form-check-input"
                                                {{ isset($userAnswers[$category->id]) && $userAnswers[$category->id] == 'yes' ? 'checked' : '' }}>
                                            <label class="form-check-label">Yes</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="radio" name="answers[{{ $category->id }}]" value="no" class="form-check-input"
                                                {{ isset($userAnswers[$category->id]) && $userAnswers[$category->id] == 'no' ? 'checked' : '' }}>
                                            <label class="form-check-label">No</label>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            <button type="submit" class="btn btn-primary mt-3">Save Changes</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-master-layout>
