@if (($notif = session('notif')) && ($data = session('notif')['data']))
    <div class="form-group row">
        <div class="col-lg-12 col-md-12">
            <div id="notifikasi" class="alert alert-{{ $notif['status'] == 'error' ? 'danger' : 'success' }}" role="alert">
                {{ $notif['pesan'] }}
            </div>
        </div>
    </div>
@endif
