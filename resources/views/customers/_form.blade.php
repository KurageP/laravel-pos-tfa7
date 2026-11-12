<div class="field">
    <label for="full_name">Full name <span aria-hidden="true">*</span></label>
    <input id="full_name" name="full_name" type="text" maxlength="255" required
           autocomplete="name" value="{{ old('full_name', $customer->full_name) }}"
           @error('full_name') aria-invalid="true" aria-describedby="full_name-error" @enderror>
    @error('full_name') <p class="field-error" id="full_name-error">{{ $message }}</p> @enderror
</div>
<div class="field">
    <label for="email">Email address <span aria-hidden="true">*</span></label>
    <input id="email" name="email" type="email" maxlength="255" required
           autocomplete="email" value="{{ old('email', $customer->email) }}"
           @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
    @error('email') <p class="field-error" id="email-error">{{ $message }}</p> @enderror
</div>
<div class="field">
    <label for="phone">Phone <span class="optional">(optional)</span></label>
    <input id="phone" name="phone" type="tel" maxlength="40" autocomplete="tel"
           value="{{ old('phone', $customer->phone) }}"
           @error('phone') aria-invalid="true" aria-describedby="phone-error" @enderror>
    @error('phone') <p class="field-error" id="phone-error">{{ $message }}</p> @enderror
</div>
<div class="form-actions">
    <button class="button" type="submit">{{ $submitLabel }}</button>
    <a class="button button-secondary" href="{{ route('customers.index') }}">Cancel</a>
</div>
