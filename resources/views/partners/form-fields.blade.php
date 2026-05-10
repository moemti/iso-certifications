<div class="grid grid-cols-1 gap-6 md:grid-cols-2">
    <!-- Company Name (Full Width) -->
    <div class="form-field md:col-span-2">
        <label class="form-label" for="company_name">Company Name *</label>
        <input 
            class="form-input" 
            type="text" 
            id="company_name" 
            name="company_name" 
            value="{{ old('company_name', $partner->company_name ?? '') }}" 
            required
        >
        @error('company_name')
            <span class="form-error">{{ $message }}</span>
        @enderror
    </div>

    <!-- CUI (Left) -->
    <div class="form-field">
        <label class="form-label" for="cui">CUI (Tax ID) *</label>
        <input 
            class="form-input" 
            type="text" 
            id="cui" 
            name="cui" 
            value="{{ old('cui', $partner->cui ?? '') }}" 
            required
            maxlength="20"
        >
        @error('cui')
            <span class="form-error">{{ $message }}</span>
        @enderror
    </div>

    <!-- Trade Register Number (Right) -->
    <div class="form-field">
        <label class="form-label" for="trade_register_number">Trade Register Number</label>
        <input 
            class="form-input" 
            type="text" 
            id="trade_register_number" 
            name="trade_register_number" 
            value="{{ old('trade_register_number', $partner->trade_register_number ?? '') }}" 
            maxlength="50"
        >
        @error('trade_register_number')
            <span class="form-error">{{ $message }}</span>
        @enderror
    </div>

    <!-- Address (Full Width) -->
    <div class="form-field md:col-span-2">
        <label class="form-label" for="address">Address *</label>
        <input 
            class="form-input" 
            type="text" 
            id="address" 
            name="address" 
            value="{{ old('address', $partner->address ?? '') }}" 
            required
        >
        @error('address')
            <span class="form-error">{{ $message }}</span>
        @enderror
    </div>

    <!-- City (Left) -->
    <div class="form-field">
        <label class="form-label" for="city">City *</label>
        <input 
            class="form-input" 
            type="text" 
            id="city" 
            name="city" 
            value="{{ old('city', $partner->city ?? '') }}" 
            required
            maxlength="100"
        >
        @error('city')
            <span class="form-error">{{ $message }}</span>
        @enderror
    </div>

    <!-- County (Right) -->
    <div class="form-field">
        <label class="form-label" for="county">County *</label>
        <input 
            class="form-input" 
            type="text" 
            id="county" 
            name="county" 
            value="{{ old('county', $partner->county ?? '') }}" 
            required
            maxlength="100"
        >
        @error('county')
            <span class="form-error">{{ $message }}</span>
        @enderror
    </div>

    <!-- Postal Code (Left) -->
    <div class="form-field">
        <label class="form-label" for="postal_code">Postal Code</label>
        <input 
            class="form-input" 
            type="text" 
            id="postal_code" 
            name="postal_code" 
            value="{{ old('postal_code', $partner->postal_code ?? '') }}" 
            maxlength="10"
        >
        @error('postal_code')
            <span class="form-error">{{ $message }}</span>
        @enderror
    </div>

    <!-- Contact Person (Right) -->
    <div class="form-field">
        <label class="form-label" for="contact_person">Contact Person *</label>
        <input 
            class="form-input" 
            type="text" 
            id="contact_person" 
            name="contact_person" 
            value="{{ old('contact_person', $partner->contact_person ?? '') }}" 
            required
            maxlength="255"
        >
        @error('contact_person')
            <span class="form-error">{{ $message }}</span>
        @enderror
    </div>

    <!-- Phone (Left) -->
    <div class="form-field">
        <label class="form-label" for="phone">Phone *</label>
        <input 
            class="form-input" 
            type="text" 
            id="phone" 
            name="phone" 
            value="{{ old('phone', $partner->phone ?? '') }}" 
            required
            maxlength="20"
        >
        @error('phone')
            <span class="form-error">{{ $message }}</span>
        @enderror
    </div>

    <!-- Email (Right) -->
    <div class="form-field">
        <label class="form-label" for="email">Email *</label>
        <input 
            class="form-input" 
            type="email" 
            id="email" 
            name="email" 
            value="{{ old('email', $partner->email ?? '') }}" 
            required
        >
        @error('email')
            <span class="form-error">{{ $message }}</span>
        @enderror
    </div>

    <!-- Bank Name (Left) -->
    <div class="form-field">
        <label class="form-label" for="bank_name">Bank Name</label>
        <input 
            class="form-input" 
            type="text" 
            id="bank_name" 
            name="bank_name" 
            value="{{ old('bank_name', $partner->bank_name ?? '') }}" 
            maxlength="255"
        >
        @error('bank_name')
            <span class="form-error">{{ $message }}</span>
        @enderror
    </div>

    <!-- Bank Account (Right) -->
    <div class="form-field">
        <label class="form-label" for="bank_account">Bank Account</label>
        <input 
            class="form-input" 
            type="text" 
            id="bank_account" 
            name="bank_account" 
            value="{{ old('bank_account', $partner->bank_account ?? '') }}" 
            maxlength="50"
        >
        @error('bank_account')
            <span class="form-error">{{ $message }}</span>
        @enderror
    </div>

    <!-- Notes (Full Width) -->
    <div class="form-field md:col-span-2">
        <label class="form-label" for="notes">Notes</label>
        <textarea 
            class="form-input" 
            id="notes" 
            name="notes" 
            rows="4"
            maxlength="1000"
        >{{ old('notes', $partner->notes ?? '') }}</textarea>
        @error('notes')
            <span class="form-error">{{ $message }}</span>
        @enderror
    </div>
</div>
