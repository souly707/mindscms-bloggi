@extends('layouts.app')

@section('content')

<div class="col-lg-12 col-12">
    <div class="blog-details content">
        <article class="blog-post-details">
            <div class="post-thumbnail">

                @if ($post->media->count() > 0)

                <div id="carouselIndicators" class="carousel slide" data-ride="carousel">
                    <ol class="carousel-indicators">

                        @foreach ($post->media as $media)

                        <li data-target="#carouselIndicators" data-slide-to="{{ $loop->index }}"
                            class="{{ $loop->index == 0 ? 'active' : ''}}"></li>

                        @endforeach

                    </ol>
                    <div class="carousel-inner">

                        @foreach ($post->media as $media)

                        <div class="carousel-item {{ $loop->index == 0 ? 'active' : ''}}">
                            <img src="{{ asset('assets/posts/' . $media->file_name) }}" class="d-block w-100"
                                alt="{{ $post->title }}">
                        </div>
                        @endforeach

                    </div>

                    @if ($post->media->count() > 1)

                    <a class="carousel-control-prev" href="#carouseIndicators" role="button" data-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="sr-only">Previous</span>
                    </a>
                    <a class="carousel-control-next" href="#carouselIndicators" role="button" data-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="sr-only">Next</span>
                    </a>

                    @endif

                </div>

                @endif
            </div>
            <div class="post_wrapper">
                <div class="post_header">
                    <h2>{{ $post->title }}</h2>
                    <div class="blog-date-categori">
                        <ul>
                            <li>{{ $post->created_at->format('M d Y') }}</li>
                            <li><a href="#" title="Posts by {{ $post->user->name }}"
                                    rel="author">{{ $post->user->name }}</a></li>
                        </ul>
                    </div>
                </div>

                <div class="post_content">
                    <p>{{ $post->description }}</p>
                </div>

            </div>
        </article>
    </div>
</div>

@endsection