<x-layouts.app>
    <div class="auth-page">
        <div class="auth-card auth-card--wide">
            <h1 class="auth-title">Add New Partner</h1>
            <p class="auth-desc">Enter partner information</p>

            <form action="{{ route('partners.store') }}" method="POST" class="mt-6">
                @csrf

                @include('partners.form-fields')

                <div class="mt-6 flex gap-4">
                    <button type="submit" class="button button-primary flex-1">
                        Add Partner
                    </button>
                    <a href="{{ route('partners.index') }}" class="button bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded flex-1 text-center">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
