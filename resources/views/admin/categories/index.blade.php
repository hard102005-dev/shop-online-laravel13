@extends('layouts.admin')

@section('title', 'Categories Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold mb-0">Categories</h1>
        <p class="text-muted mb-0">Manage hierarchical product categories and navigation taxonomy.</p>
    </div>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary shadow-sm">
        <i class="bi bi-plus-lg me-1"></i>Add Category
    </a>
</div>

<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">#</th>
                        <th>Category Name</th>
                        <th>Image</th>
                        <th>Parent Category</th>
                        <th>Slug</th>
                        <th>Sort Order</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categories as $category)
                        <tr>
                            <td class="ps-4 text-muted">{{ $category->id }}</td>
                            <td class="fw-bold">
                                @if ($category->parent_id)
                                    <span class="text-muted ms-2">└─</span>
                                @endif
                                {{ $category->name }}
                            </td>
                            <td>
                                @if ($category->image_path)
                                    <img src="{{ asset('storage/'.$category->image_path) }}" alt="{{ $category->name }}" class="rounded-3" style="width: 60px; height: 40px; object-fit: cover;" loading="lazy">
                                @else
                                    <span class="text-muted fs-7">No image</span>
                                @endif
                            </td>
                            <td>
                                @if ($category->parent)
                                    <span class="badge bg-secondary-subtle text-secondary">{{ $category->parent->name }}</span>
                                @else
                                    <span class="text-muted fs-7">Root Category</span>
                                @endif
                            </td>
                            <td><code>{{ $category->slug }}</code></td>
                            <td>{{ $category->sort_order }}</td>
                            <td>
                                @if ($category->is_active)
                                    <span class="badge bg-success-subtle text-success">Active</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger">Inactive</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-sm btn-outline-primary me-1">
                                    <i class="bi bi-pencil me-1"></i>Edit
                                </a>
                                <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this category?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash me-1"></i>Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No categories found. Click 'Add Category' to create one.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if ($categories->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $categories->links() }}
        </div>
    @endif
</div>
@endsection
