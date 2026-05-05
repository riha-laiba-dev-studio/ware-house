@extends('layouts.app')
@section('title','Backup & Restore')
@section('page-title','Backup & Restore')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

  {{-- Create Backup --}}
  <div class="card">
    <div class="card-header"><h3 class="font-semibold text-gray-700"><i class="fas fa-download text-blue-500 mr-2"></i>Create Backup</h3></div>
    <div class="card-body">
      <p class="text-sm text-gray-500 mb-4">Create a full database backup. The file will be saved to server storage and can be downloaded.</p>
      <form method="POST" action="{{ route('backup.create') }}">
        @csrf
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4 text-sm text-blue-800">
          <i class="fas fa-info-circle mr-1"></i>
          This will export the complete <strong>WMS</strong> database including all transactions, inventory, and user data.
        </div>
        <button type="submit" class="btn-primary w-full justify-center py-3">
          <i class="fas fa-database mr-2"></i>Create Database Backup Now
        </button>
      </form>
    </div>
  </div>

  {{-- Restore --}}
  <div class="card">
    <div class="card-header"><h3 class="font-semibold text-gray-700"><i class="fas fa-upload text-orange-500 mr-2"></i>Restore Database</h3></div>
    <div class="card-body">
      <p class="text-sm text-gray-500 mb-4">Upload a previously created <code>.sql</code> backup file to restore the database.</p>
      <form method="POST" action="{{ route('backup.restore') }}" enctype="multipart/form-data" onsubmit="return confirm('WARNING: This will OVERWRITE your current database. Are you absolutely sure?');">
        @csrf
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4 text-sm text-red-800">
          <i class="fas fa-triangle-exclamation mr-1"></i>
          <strong>Danger!</strong> Restoring will overwrite all current data. Make a backup first!
        </div>
        <label class="form-label">Select .sql backup file</label>
        <input type="file" name="backup_file" accept=".sql,.txt" class="form-input mb-3" required>
        <button type="submit" class="btn-warning w-full justify-center py-3">
          <i class="fas fa-upload mr-2"></i>Restore From File
        </button>
      </form>
    </div>
  </div>
</div>

{{-- Backup Files List --}}
<div class="card">
  <div class="card-header">
    <h3 class="font-semibold text-gray-700">Saved Backups</h3>
    <span class="badge badge-gray">{{ $backups->count() }} files</span>
  </div>
  <div class="overflow-x-auto">
    <table class="table">
      <thead><tr><th>Filename</th><th>Size</th><th>Created</th><th>Actions</th></tr></thead>
      <tbody>
        @forelse($backups as $b)
        <tr>
          <td class="font-mono text-sm text-blue-700"><i class="fas fa-file-code mr-2 text-gray-400"></i>{{ $b['name'] }}</td>
          <td class="text-gray-500">{{ $b['size'] }}</td>
          <td class="text-gray-500">{{ $b['created'] }}</td>
          <td class="flex gap-2">
            <a href="{{ route('backup.download', $b['name']) }}" class="btn-outline btn-sm text-blue-600"><i class="fas fa-download"></i> Download</a>
            <form method="POST" action="{{ route('backup.destroy', $b['name']) }}" onsubmit="return confirm('Delete this backup file?');">
              @csrf @method('DELETE')
              <button class="btn-outline btn-sm text-red-500"><i class="fas fa-trash"></i></button>
            </form>
          </td>
        </tr>
        @empty
        <tr><td colspan="4" class="text-center py-8 text-gray-400"><i class="fas fa-folder-open text-3xl mb-2 block opacity-30"></i>No backups yet. Create one above.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
