<x-master-layout>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12 col-md-12">
                <div class="card">
                    <div class="card-body">
                        <center><h2>{{__('messages.service_category')}}</h2></center><br>

                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        <form action="{{ route('service_questions.save') }}" method="POST">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ $userId }}">

                            <p style="font-size: larger;">{{__('messages.service_ques')}}</p>

                            @foreach($categories as $category)
                                <div class="row mb-3 align-items-center">
                                    <label class="col-md-4"><strong>{{ $category->name }}</strong></label>
                                    <div class="col-md-8 d-flex">
                                        <div class="form-check me-3">
                                            <input type="radio" name="answers[{{ $category->id }}]" value="yes" class="form-check-input"
                                                {{ isset($userAnswers[$category->id]) && $userAnswers[$category->id] == 'yes' ? 'checked' : '' }} required>
                                            <label class="form-check-label">Yes</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="radio" name="answers[{{ $category->id }}]" value="no" class="form-check-input"
                                                {{ isset($userAnswers[$category->id]) && $userAnswers[$category->id] == 'no' ? 'checked' : '' }} required>
                                            <label class="form-check-label">No</label>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            <button type="submit" class="btn btn-primary mt-3">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-master-layout>
