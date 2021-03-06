@extends('layouts.app')

@section('content')

    {{-- main feed section --}}


    @if(count($feed))
        @foreach($feed as $feedItem)


            @include('partials.review_card_1',['review'=>$feedItem])

        @endforeach
        <div class="d-flex justify-content-center my-4">
            {{ $feed->links() }}
        </div>
    @else
        You don't have any reviews in your feed. Why don't you follow someone!
    @endif

    {{-- End of main feed section --}}
@endsection


@section('aside')

    {{-- aside section --}}
    @include('partials.side_bar_recommendation',compact('recommendation'))
    {{-- End of aside section --}}
@endsection






