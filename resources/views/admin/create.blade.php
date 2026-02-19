@extends('layouts.admin')

@section('title', 'Create Row - ' . ucfirst($table))

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white shadow rounded-lg">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold text-gray-800">
                    <i class="fas fa-plus-circle mr-2"></i>
                    Create New Row
                </h2>
                <a href="{{ route('admin.table', $table) }}" class="text-gray-600 hover:text-gray-800">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Table
                </a>
            </div>
            <p class="text-sm text-gray-500 mt-1">Table: <span class="font-semibold">{{ $table }}</span></p>
        </div>
        
        <!-- Form -->
        <form method="POST" action="{{ route('admin.store', $table) }}" class="p-6">
            @csrf
            
            <div class="space-y-6">
                @foreach($formColumns as $column)
                    <div>
                        <label for="{{ $column['name'] }}" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ ucfirst(str_replace('_', ' ', $column['name'])) }}
                            @if(!$column['nullable'] && $column['default'] === null)
                                <span class="text-red-500">*</span>
                            @endif
                            <span class="text-xs text-gray-500 font-normal ml-2">({{ $column['type'] }})</span>
                        </label>
                        
                        @php
                            $inputType = 'text';
                            $inputClass = 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500';
                            
                            // Determine input type based on column type
                            if (strpos($column['type'], 'int') !== false) {
                                $inputType = 'number';
                                $inputClass .= ' number-input';
                            } elseif (strpos($column['type'], 'decimal') !== false || strpos($column['type'], 'float') !== false || strpos($column['type'], 'double') !== false) {
                                $inputType = 'number';
                                $inputClass .= ' number-input';
                                $step = 'step="0.01"';
                            } elseif (strpos($column['type'], 'date') !== false && strpos($column['type'], 'time') === false) {
                                $inputType = 'date';
                            } elseif (strpos($column['type'], 'datetime') !== false || strpos($column['type'], 'timestamp') !== false) {
                                $inputType = 'datetime-local';
                            } elseif (strpos($column['type'], 'time') !== false) {
                                $inputType = 'time';
                            } elseif ($column['type'] === 'tinyint' && $column['max_length'] == 1) {
                                $inputType = 'checkbox';
                            } elseif (strpos($column['type'], 'text') !== false || ($column['max_length'] && $column['max_length'] > 255)) {
                                $inputType = 'textarea';
                            } elseif ($column['name'] === 'email' || strpos($column['name'], 'email') !== false) {
                                $inputType = 'email';
                            } elseif ($column['name'] === 'password' || strpos($column['name'], 'password') !== false) {
                                $inputType = 'password';
                            }
                        @endphp
                        
                        @if($inputType === 'textarea')
                            <textarea 
                                name="{{ $column['name'] }}" 
                                id="{{ $column['name'] }}" 
                                rows="4"
                                class="{{ $inputClass }} @error($column['name']) border-red-500 @enderror"
                                @if(!$column['nullable'] && $column['default'] === null) required @endif
                                @if($column['max_length']) maxlength="{{ $column['max_length'] }}" @endif
                            >{{ old($column['name'], $column['default']) }}</textarea>
                        @elseif($inputType === 'checkbox')
                            <div class="mt-2">
                                <label class="inline-flex items-center">
                                    <input 
                                        type="checkbox" 
                                        name="{{ $column['name'] }}" 
                                        id="{{ $column['name'] }}" 
                                        value="1"
                                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        {{ old($column['name'], $column['default']) ? 'checked' : '' }}
                                    >
                                    <span class="ml-2 text-sm text-gray-600">Yes</span>
                                </label>
                            </div>
                        @else
                            <input 
                                type="{{ $inputType }}" 
                                name="{{ $column['name'] }}" 
                                id="{{ $column['name'] }}" 
                                value="{{ old($column['name'], $column['default']) }}"
                                class="{{ $inputClass }} @error($column['name']) border-red-500 @enderror"
                                @if(!$column['nullable'] && $column['default'] === null) required @endif
                                @if($column['max_length'] && $inputType !== 'number') maxlength="{{ $column['max_length'] }}" @endif
                                @if(isset($step)) {!! $step !!} @endif
                                @if($inputType === 'number' && strpos($column['type'], 'unsigned') !== false) min="0" @endif
                            >
                        @endif
                        
                        @error($column['name'])
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        
                        @if($column['nullable'])
                            <p class="mt-1 text-xs text-gray-500">Optional</p>
                        @endif
                    </div>
                @endforeach
            </div>
            
            <!-- Form Actions -->
            <div class="mt-8 flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.table', $table) }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-save mr-2"></i>
                    Create Row
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
