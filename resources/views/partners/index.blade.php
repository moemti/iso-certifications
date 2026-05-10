<x-layouts.app>
    <div class="list-page">
        <div class="list-shell">
            <div class="auth-card auth-card--wide list-card">
                <h1 class="auth-title">Partners</h1>
                <p class="auth-desc">Manage your business partners</p>

            @if (session('success'))
                <div class="bg-green-900 border border-green-700 text-green-100 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-900 border border-red-700 text-red-100 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

                <div class="mb-6 flex justify-end">
                    <a href="{{ route('partners.create') }}" class="button button-primary">
                        Add Partner
                    </a>
                </div>

                @if ($partners->count() > 0)
                    <div class="list-table-wrap">
                        <table class="list-table">
                            <thead>
                                <tr>
                                    <th>Company Name</th>
                                    <th>CUI</th>
                                    <th>City</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($partners as $partner)
                                    <tr>
                                        <td data-label="Company">{{ $partner->company_name }}</td>
                                        <td data-label="CUI">{{ $partner->cui }}</td>
                                        <td data-label="City">{{ $partner->city }}</td>
                                        <td data-label="Phone">{{ $partner->phone }}</td>
                                        <td data-label="Email">{{ $partner->email }}</td>
                                        <td data-label="Actions">
                                            <div class="list-actions">
                                                <a href="{{ route('partners.edit', $partner) }}" class="text-blue-400 hover:text-blue-300">
                                                    Edit
                                                </a>
                                                <form action="{{ route('partners.destroy', $partner) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this partner?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-400 hover:text-red-300">
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $partners->links() }}
                    </div>
                @else
                    <div class="text-center py-8 text-gray-400">
                        <p>No partners found. <a href="{{ route('partners.create') }}" class="text-blue-400 hover:text-blue-300">Create one</a></p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>
