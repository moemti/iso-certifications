<x-layouts.app>
    <div class="auth-page">
        <div class="auth-card auth-card--wide">
            <h1 class="auth-title">Edit Partner: {{ $partner->company_name }}</h1>
            <p class="auth-desc">Update partner information</p>

            <form action="{{ route('partners.update', $partner) }}" method="POST" class="mt-6">
                @csrf
                @method('PATCH')

                @include('partners.form-fields')

                <div class="mt-6 flex gap-4">
                    <button type="submit" class="button button-primary flex-1">
                        Update Partner
                    </button>
                    <a href="{{ route('partners.index') }}" class="button bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded flex-1 text-center">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
