<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="editForm" method="POST">
      @csrf
      @method('PUT')
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editModalLabel">Edit Data</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" id="edit-id">
          <div class="mb-3">
            <label>Tanggal</label>
            <input type="date" name="date" id="edit-date" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>ALB</label>
            <input type="number" step="any" name="avg_alb" id="edit-alb" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Air</label>
            <input type="number" step="any" name="avg_air" id="edit-air" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Kotoran</label>
            <input type="number" step="any" name="avg_kotoran" id="edit-kotoran" class="form-control" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </div>
    </form>
  </div>
</div> 