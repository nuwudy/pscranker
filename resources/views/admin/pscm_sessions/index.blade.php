@extends('layouts.admin')
@section('title', 'MicroLearning Sessions')
@section('content')

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold">Sessions</h3>
                    <a href="{{ route('admin.pscm.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded">Create New Session</a>
                </div>
                
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr>
                            <th class="border-b p-2">Title</th>
                            <th class="border-b p-2">Status</th>
                            <th class="border-b p-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sessions as $session)
                        <tr>
                            <td class="border-b p-2">{{ $session->title }}</td>
                            <td class="border-b p-2">{{ ucfirst($session->status) }}</td>
                            <td class="border-b p-2">
                                <a href="{{ route('admin.pscm.edit', $session->id) }}" class="text-blue-500">Edit</a>
                                |
                                <a href="{{ route('learner.pscm.show', $session->slug) }}" class="text-green-500" target="_blank">View</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
