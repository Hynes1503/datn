@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Đăng bài mới</h2>
        <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label>Tiêu đề</label>
                <input type="text" name="title" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Nội dung</label>
                <textarea name="content" class="form-control" rows="5" required></textarea>
            </div>
            <div class="mb-3">
                <label>Hashtag</label>
                <input type="text" name="hashtag" class="form-control">
            </div>
            <div class="mb-3">
                <label>Ảnh (có thể chọn nhiều)</label>
                <input type="file" name="images[]" class="form-control" multiple>
            </div>

            <button type="submit" class="btn btn-success">Đăng bài</button>
        </form>
    </div>
@endsection
