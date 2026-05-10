<x-layouts.app>
    <div class="auth-page">
        <div class="auth-card auth-card--wide">
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
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-400">
                        <thead class="text-xs uppercase bg-gray-700 text-gray-300">
                            <tr>
                                <th class="px-4 py-3">Company Name</th>
                                <th class="px-4 py-3">CUI</th>
                                <th class="px-4 py-3">City</th>
                                <th class="px-4 py-3">Phone</th>
                                <th class="px-4 py-3">Email</th>
                                <th class="px-4 py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($partners as $partner)
                                <tr class="border-b border-gray-700 hover:bg-gray-800">
                                    <td class="px-4 py-3 font-medium">{{ $partner->company_name }}</td>
                                    <td class="px-4 py-3">{{ $partner->cui }}</td>
                                    <td class="px-4 py-3">{{ $partner->city }}</td>
                                    <td class="px-4 py-3">{{ $partner->phone }}</td>
                                    <td class="px-4 py-3">{{ $partner->email }}</td>
                                    <td class="px-4 py-3">
                                        <a href="{{ route('partners.edit', $partner) }}" class="text-blue-400 hover:text-blue-300 mr-3">
                                            Edit
                                        </a>
                                        <form action="{{ route('partners.destroy', $partner) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this partner?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-400 hover:text-red-300">
                                                Delete
                                            </button>
                                        </form>
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
</x-layouts.app>
