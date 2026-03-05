@extends('layouts.app')

@section('title', 'Manage Categories')

@section('content')
    <h1>Manage Categories - {{ $company->name }}</h1>
    
    @if($company->pivot->role === 'owner')
        <form method="POST" action="/companies/{{ $company->id }}/categories" style="margin: 1rem 0;">
            @csrf
            <div style="display: flex; gap: 0.5rem;">
                <input type="text" name="name" placeholder="New category name" required style="flex: 1;">
                <button type="submit">Add Category</button>
            </div>
            @error('name')<span>{{ $message }}</span>@enderror
        </form>
    @endif

    @if($categories->isEmpty())
        <p>No categories yet.</p>
    @else
        <table style="width: 100%; border-collapse: collapse; margin-top: 1rem;">
            <thead>
                <tr style="background: #f5f5f5;">
                    <th style="padding: 0.75rem; text-align: left; border-bottom: 2px solid #ddd;">Name</th>
                    @if($company->pivot->role === 'owner')
                        <th style="padding: 0.75rem; text-align: left; border-bottom: 2px solid #ddd;">Actions</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach($categories as $category)
                    <tr id="row-{{ $category->id }}">
                        <td style="padding: 0.75rem; border-bottom: 1px solid #ddd;">
                            <span id="name-{{ $category->id }}">{{ $category->name }}</span>
                            <form id="edit-form-{{ $category->id }}" method="POST" action="/companies/{{ $company->id }}/categories/{{ $category->id }}" style="display: none;">
                                @csrf
                                @method('PUT')
                                <input type="text" name="name" value="{{ $category->name }}" required>
                            </form>
                        </td>
                        @if($company->pivot->role === 'owner')
                            <td style="padding: 0.75rem; border-bottom: 1px solid #ddd;">
                                <button type="button" onclick="editCategory({{ $category->id }})" id="edit-btn-{{ $category->id }}" style="padding: 0.25rem 0.5rem; font-size: 0.9rem; margin-right: 0.5rem;">Edit</button>
                                <button type="button" onclick="saveCategory({{ $category->id }})" id="save-btn-{{ $category->id }}" style="display: none; padding: 0.25rem 0.5rem; font-size: 0.9rem; margin-right: 0.5rem;">Save</button>
                                <button type="button" onclick="cancelEdit({{ $category->id }})" id="cancel-btn-{{ $category->id }}" style="display: none; padding: 0.25rem 0.5rem; font-size: 0.9rem; margin-right: 0.5rem;">Cancel</button>
                                <form method="POST" action="/companies/{{ $company->id }}/categories/{{ $category->id }}" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this category?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="padding: 0.25rem 0.5rem; font-size: 0.9rem; background: #c62828;">Delete</button>
                                </form>
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
    
    <a href="/companies/{{ $company->id }}" style="display: inline-block; margin-top: 1rem;">
        <button type="button">Back to Company</button>
    </a>

    <script>
        function editCategory(id) {
            document.getElementById('name-' + id).style.display = 'none';
            document.getElementById('edit-form-' + id).style.display = 'block';
            document.getElementById('edit-btn-' + id).style.display = 'none';
            document.getElementById('save-btn-' + id).style.display = 'inline';
            document.getElementById('cancel-btn-' + id).style.display = 'inline';
        }

        function saveCategory(id) {
            document.getElementById('edit-form-' + id).submit();
        }

        function cancelEdit(id) {
            document.getElementById('name-' + id).style.display = 'inline';
            document.getElementById('edit-form-' + id).style.display = 'none';
            document.getElementById('edit-btn-' + id).style.display = 'inline';
            document.getElementById('save-btn-' + id).style.display = 'none';
            document.getElementById('cancel-btn-' + id).style.display = 'none';
        }
    </script>
@endsection
