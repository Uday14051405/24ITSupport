@extends('landing-page.layouts.default')


@section('content')
<div class="section-padding">
    <div class="container">
<!-- <landing-category-section></landing-category-section> -->
    @if($submenuId)
        <category-page link="{{ route('category.data', ['submenu_id' => $submenuId]) }}"></category-page>
    @else
        <category-page link="{{ route('category.data') }}"></category-page>
    @endif
    </div>
</div>
@endsection
