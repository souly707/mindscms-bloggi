<div class="wn__sidebar mt-4">
    <!-- Start Single Widget -->
    <aside class="widget recent_widget">
        <ul>
            <li class="list-group-item">
                <img src="{{ asset('assets/users/defualt.jpg') }}" alt="{{ auth()->user()->name }}">
            </li>

            <li class="list-group-item"><a href="{{ route('frontend.dashboard') }}">My Posts</a></li>
            <li class="list-group-item"><a href="{{ route('user.post.create') }}">Create Post</a></li>
            <li class="list-group-item"><a href="{{ route('user.comments') }}">Manige Comments</a></li>
            <li class="list-group-item"><a href="{{ route('user.edit_info') }}">Update Informations</a></li>

            <li class="list-group-item"><a href="{{ route('frontend.logout') }}" onclick="event.preventDefault();
                document.getElementById('logout-form').submit();">LogOute</a></li>
        </ul>
    </aside>
    <!-- End Single Widget -->
</div>