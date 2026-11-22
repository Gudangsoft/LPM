{{-- Nama Pengusul otomatis = Kaprodi prodi yang dipilih (bisa diubah manual). --}}
@push('scripts')
<script>
(() => {
    const prodi = document.getElementById('prodi_id');
    const pengusul = document.getElementById('nama_pengusul');
    if (!prodi || !pengusul) return;
    let otomatis = '';
    const isi = () => {
        const nama = prodi.selectedOptions[0]?.dataset.pengusul || '';
        // Hanya timpa bila kosong atau masih berisi isian otomatis sebelumnya (bukan ketikan pengguna).
        if (nama && (pengusul.value.trim() === '' || pengusul.value === otomatis)) {
            pengusul.value = nama;
            otomatis = nama;
        }
    };
    prodi.addEventListener('change', isi);
    isi();
})();
</script>
@endpush
