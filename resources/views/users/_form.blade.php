<div class="field">
    <label for="username">Username <span aria-hidden="true">*</span></label>
    <input id="username" name="username" type="text" maxlength="255" required
           autocomplete="username" value="{{ old('username', $user->username) }}"
           @error('username') aria-invalid="true" aria-describedby="username-error" @enderror>
    @error('username') <p class="field-error" id="username-error">{{ $message }}</p> @enderror
</div>
<div class="field">
    <label for="full_name">Full name <span aria-hidden="true">*</span></label>
    <input id="full_name" name="full_name" type="text" maxlength="255" required
           autocomplete="name" value="{{ old('full_name', $user->full_name) }}"
           @error('full_name') aria-invalid="true" aria-describedby="full_name-error" @enderror>
    @error('full_name') <p class="field-error" id="full_name-error">{{ $message }}</p> @enderror
</div>
@if ($user->exists)
    <div class="field">
        <label for="avatar">Profile picture <span class="optional">(optional)</span></label>
        <div class="avatar-preview">
            <img class="avatar avatar-large" src="{{ $user->avatarUrl() }}"
                 alt="Current profile picture for {{ $user->full_name }}" width="80" height="80">
            <p class="hint">Current profile picture</p>
        </div>
        <input id="avatar" name="avatar" type="file" accept="image/jpeg,image/png,image/gif,image/webp"
               aria-describedby="avatar-help{{ $errors->has('avatar') ? ' avatar-error' : '' }}"
               @error('avatar') aria-invalid="true" @enderror>
        <p class="hint" id="avatar-help">JPG, PNG, GIF, or WebP. Maximum 2 MB and 4096 × 4096 pixels.
            Saved as a 256 × 256 PNG thumbnail. Leave blank to keep the current picture.
            After a validation error, please select the file again.</p>
        @error('avatar') <p class="field-error" id="avatar-error">{{ $message }}</p> @enderror
    </div>
@endif
<div class="form-actions">
    <button class="button" type="submit">{{ $submitLabel }}</button>
    <a class="button button-secondary" href="{{ route('users.index') }}">Cancel</a>
</div>
