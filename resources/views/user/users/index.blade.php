@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-3xl p-4">
    <h2 class="text-xl font-bold mb-4">Danh sách User</h2>

    <a href="{{ route('users.create') }}" class="px-4 py-2 bg-blue-500 text-white rounded-lg mb-4 inline-block">Tạo mới</a>

    <table class="table-auto w-full border">
        <thead class="bg-gray-100">
            <tr>
                <th class="border px-4 py-2">Name</th>
                <th class="border px-4 py-2">Mention</th>
                <th class="border px-4 py-2">Email</th>
                <th class="border px-4 py-2">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
            <tr>
                <td class="border px-4 py-2">{{ $user->name }}</td>
                <td class="border px-4 py-2">{{ $user->mention_with_at }}</td>
                <td class="border px-4 py-2">{{ $user->email }}</td>
                <td class="border px-4 py-2">
                    <a href="{{ route('users.show', $user->mention) }}" class="text-blue-600">View</a> |
                    <a href="{{ route('users.edit', $user->mention) }}" class="text-green-600">Edit</a> |
                    <form action="{{ route('users.destroy', $user->mention) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600" onclick="return confirm('Are you sure?')">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-4">
        {{ $users->links() }}
    </div>
</div>
@endsection