<form method="get" class="mb-3">
    <div class="input-group">
        <input type="text"
               name="q"
               class="form-control"
               value="{{ request('q') }}"
               placeholder="Cari...">
        <button class="btn btn-outline-secondary" type="submit">
            <i class="ri-search-line"></i>
        </button>
    </div>
</form>
