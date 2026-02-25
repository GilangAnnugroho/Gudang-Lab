@php
    $oldDetails = old('details');
    if (is_array($oldDetails) && count($oldDetails)) {
        $rows = $oldDetails;
    } else {
        $rows = [
            ['item_master_id' => null, 'requested_quantity' => null, 'notes' => null],
        ];
    }
@endphp

<div class="table-responsive">
    <table class="table table-bordered table-sm mb-0 align-middle" id="details-table">
        <thead class="table-light text-center">
            <tr>
                <th style="width:40px;">No</th>
                <th style="width:320px;">Item (Kode — Nama)</th>
                <th style="width:110px;">Jumlah</th>
                <th>Catatan</th>
                <th style="width:70px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
        @foreach($rows as $i => $row)
            <tr>
                <td class="align-middle text-center nomor-urut">{{ $i+1 }}</td>

                {{-- ITEM --}}
                <td>
                    <select name="details[{{ $i }}][item_master_id]"
                            class="form-select form-select-sm">
                        <option value="">– pilih item –</option>
                        @foreach($items as $id => $label)
                            <option value="{{ $id }}"
                                {{ (string)($row['item_master_id'] ?? '') === (string)$id ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </td>

                {{-- JUMLAH --}}
                <td>
                    <input type="number"
                           name="details[{{ $i }}][requested_quantity]"
                           class="form-control form-control-sm text-end"
                           min="1"
                           value="{{ $row['requested_quantity'] ?? '' }}">
                </td>

                {{-- CATATAN --}}
                <td>
                    <input type="text"
                           name="details[{{ $i }}][notes]"
                           class="form-control form-control-sm"
                           value="{{ $row['notes'] ?? '' }}"
                           placeholder="Opsional">
                </td>

                {{-- HAPUS --}}
                <td class="text-center align-middle">
                    <button type="button"
                            class="btn btn-sm btn-outline-danger btn-remove-row">
                        <i class="ri-delete-bin-line"></i>
                    </button>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

<div class="mt-2">
    <button type="button" class="btn btn-sm btn-outline-primary" id="btn-add-row">
        <i class="ri-add-line me-1"></i> Tambah Baris
    </button>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const table = document.getElementById('details-table');
    const btnAdd = document.getElementById('btn-add-row');

    function renumberRows() {
        const rows = table.querySelectorAll('tbody tr');
        rows.forEach((tr, index) => {
            tr.querySelector('.nomor-urut').textContent = index + 1;
            tr.querySelectorAll('select, input').forEach(function (el) {
                if (!el.name) return;
                el.name = el.name.replace(/details\[\d+]/, 'details[' + index + ']');
            });
        });
    }

    table.addEventListener('click', function (e) {
        if (e.target.closest('.btn-remove-row')) {
            const rows = table.querySelectorAll('tbody tr');
            if (rows.length <= 1) {
                rows[0].querySelectorAll('select, input').forEach(el => el.value = '');
            } else {
                e.target.closest('tr').remove();
                renumberRows();
            }
        }
    });

    btnAdd.addEventListener('click', function () {
        const tbody = table.querySelector('tbody');
        const rows = tbody.querySelectorAll('tr');
        const lastRow = rows[rows.length - 1];
        const newRow = lastRow.cloneNode(true);

        newRow.querySelectorAll('select, input').forEach(function (el) {
            if (el.tagName === 'SELECT') {
                el.selectedIndex = 0;
            } else {
                el.value = '';
            }
        });

        tbody.appendChild(newRow);
        renumberRows();
    });
});
</script>
@endpush
