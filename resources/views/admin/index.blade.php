@extends('layouts.admin')

@section('title', 'Database Tables')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold text-gray-800">
                    <i class="fas fa-database mr-2"></i>
                    Database Tables
                </h2>
            </div>
        </div>
        
        <div class="p-6">
            @if(count($tableNames) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($tableNames as $tableName)
                        <a href="{{ route('admin.table', $tableName) }}" class="block p-6 bg-white border border-gray-200 rounded-lg hover:shadow-lg hover:border-blue-500 transition-all duration-200">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-table text-blue-600 text-xl"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $tableName }}</h3>
                                    <p class="text-sm text-gray-500 mt-1">View and manage data</p>
                                </div>
                                <div class="ml-auto">
                                    <i class="fas fa-chevron-right text-gray-400"></i>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-inbox text-gray-400 text-6xl mb-4"></i>
                    <p class="text-gray-500 text-lg">No tables found in the database.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
