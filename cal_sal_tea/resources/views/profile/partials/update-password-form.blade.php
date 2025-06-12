<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Update Password') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div class="form-group">
            <label for="update_password_current_password">{{__('Current Password')}}</label>
            <input id="update_password_current_password" name="current_password" type="password" class="form-control" autocomplete="current-password">
            @if($errors->updatePassword->get('current_password'))
                <div class="text-danger mt-2">
                     @foreach((array)$errors->updatePassword->get('current_password') as $message)
                        {{ $message }}
                    @endforeach
                </div>
            @endif
        </div>

        <div class="form-group">
            <label for="update_password_password">{{__('New Password')}}</label>
            <input id="update_password_password" name="password" type="password" class="form-control" autocomplete="new-password">
             @if($errors->updatePassword->get('password'))
                <div class="text-danger mt-2">
                     @foreach((array)$errors->updatePassword->get('password') as $message)
                        {{ $message }}
                    @endforeach
                </div>
            @endif
        </div>

        <div class="form-group">
            <label for="update_password_password_confirmation">{{__('Confirm Password')}}</label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="form-control" autocomplete="new-password">
             @if($errors->updatePassword->get('password_confirmation'))
                <div class="text-danger mt-2">
                     @foreach((array)$errors->updatePassword->get('password_confirmation') as $message)
                        {{ $message }}
                    @endforeach
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
