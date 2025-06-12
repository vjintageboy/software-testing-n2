<section class="space-y-6">
    <header>
        <p class="mt-1 text-sm text-gray-600">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
        </p>
    </header>

    {{-- AdminLTE không dùng modal của AlpineJS, nên ta sẽ dùng component modal có sẵn --}}
    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#confirm-user-deletion-modal">
        {{ __('Delete Account') }}
    </button>
    
    {{-- Modal Component --}}
    <div class="modal fade" id="confirm-user-deletion-modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="post" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('delete')

                    <div class="modal-header">
                        <h5 class="modal-title" id="modalLabel">{{ __('Are you sure you want to delete your account?') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                         <p>
                            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
                        </p>

                        <div class="form-group">
                             <label for="password" class="sr-only">{{ __('Password') }}</label>
                             <input id="password" name="password" type="password" class="form-control" placeholder="{{ __('Password') }}" required>
                             @if($errors->userDeletion->get('password'))
                                <div class="text-danger mt-2">
                                     @foreach((array)$errors->userDeletion->get('password') as $message)
                                        {{ $message }}
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-danger">{{ __('Delete Account') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
