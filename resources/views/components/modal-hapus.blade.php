<div class="modal fade" id="modalHapus" tabindex="-1"> {{-- ID diubah menjadi generik --}}
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header"><h1 class="modal-title fs-5">Konfirmasi Hapus</h1><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body"><p>Apakah Anda yakin ingin menghapus <strong x-text="deleteName"></strong>?</p></div>
            <div class="modal-footer">
                <form x-bind:action="deleteUrl" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>