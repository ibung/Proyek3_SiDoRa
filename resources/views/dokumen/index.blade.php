<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Daftar Dokumen</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  {{-- Tailwind via CDN (tanpa build) --}}
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-900">
  <div class="max-w-6xl mx-auto p-6">
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-2xl font-bold">Daftar Dokumen</h1>
        <p class="text-sm text-gray-500">Sumber: Neon · tersinkron dengan Laravel</p>
      </div>
      <a href="/db-health" target="_blank" class="text-sm text-blue-600 hover:underline">Cek koneksi DB →</a>
    </div>

    <!-- Filter bar -->
    <form id="filters" class="grid grid-cols-1 sm:grid-cols-5 gap-3 bg-white p-4 rounded-xl shadow">
      <input id="q" name="q" placeholder="Cari judul / nomor..." class="col-span-2 rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500">
      <select id="status" name="status" class="rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500">
        <option value="">Status: semua</option>
        <option value="draft">draft</option>
        <option value="publik">publik</option>
        <option value="arsip">arsip</option>
      </select>
      <select id="kategori_id" name="kategori_id" class="rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500">
        <option value="">Kategori: semua</option>
        @foreach ($kategori as $k)
          <option value="{{ $k->kategori_id }}">{{ $k->nama_kategori }}</option>
        @endforeach
      </select>
      <select id="per_page" name="per_page" class="rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500">
        <option value="10">10 / halaman</option>
        <option value="25">25 / halaman</option>
        <option value="50">50 / halaman</option>
      </select>
      <div class="sm:col-span-5 flex gap-2">
        <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Terapkan</button>
        <button type="button" id="resetBtn" class="px-4 py-2 border rounded-lg hover:bg-gray-100">Reset</button>
      </div>
    </form>

    <!-- Table -->
    <div class="mt-4 bg-white rounded-xl shadow overflow-hidden">
      <div id="loading" class="p-6 text-sm text-gray-600 hidden">Memuat data…</div>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Judul</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Terbit</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dibuat oleh</th>
              <th class="px-4 py-3"></th>
            </tr>
          </thead>
          <tbody id="tbody" class="divide-y divide-gray-100 bg-white"></tbody>
        </table>
      </div>

      <!-- footer -->
      <div class="flex items-center justify-between p-4">
        <div class="text-sm text-gray-600" id="summary">—</div>
        <div class="flex items-center gap-2">
          <button id="prev" class="px-3 py-1.5 border rounded-lg disabled:opacity-40">‹ Prev</button>
          <span id="pageinfo" class="text-sm text-gray-600">—</span>
          <button id="next" class="px-3 py-1.5 border rounded-lg disabled:opacity-40">Next ›</button>
        </div>
      </div>
    </div>
  </div>

  <script>
  const qs = (o) => new URLSearchParams(Object.fromEntries(Object.entries(o).filter(([,v]) => v!=='' && v!=null))).toString();

  const state = { page: 1, q: '', status: '', kategori_id: '', per_page: 10, loading: false };

  async function load(){
    try {
      state.loading = true;
      document.getElementById('loading').classList.toggle('hidden', false);

      const url = '/dokumen-data?' + qs({ page: state.page, q: state.q, status: state.status, kategori_id: state.kategori_id, per_page: state.per_page });
      const res = await fetch(url, { headers: {'Accept':'application/json'} });
      const data = await res.json();

      const rows = data.data || [];
      const tb = document.getElementById('tbody');
      tb.innerHTML = rows.map(r => `
        <tr class="hover:bg-gray-50">
          <td class="px-4 py-2 text-sm text-gray-600">${r.dokumen_id}</td>
          <td class="px-4 py-2">
            <div class="font-medium">${r.judul ?? ''}</div>
            <div class="text-xs text-gray-500">${r.nomor_dokumen ?? ''}</div>
          </td>
          <td class="px-4 py-2 text-sm">${r.kategori?.nama_kategori ?? '-'}</td>
          <td class="px-4 py-2">
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs
              ${badge(r.status).cls}">${badge(r.status).txt}</span>
          </td>
          <td class="px-4 py-2 text-sm">${r.tanggal_terbit ?? '-'}</td>
          <td class="px-4 py-2 text-sm">${r.creator?.nama_lengkap ?? '-'}</td>
          <td class="px-4 py-2 text-right"><a class="text-blue-600 hover:underline" href="/dokumen/${r.dokumen_id}" target="_blank">Detail</a></td>
        </tr>
      `).join('');

      // footer
      document.getElementById('summary').textContent =
        `Menampilkan ${rows.length} item · ${data.from ?? 0}–${data.to ?? 0} dari ${data.total ?? rows.length}`;

      state.page = data.current_page || 1;
      const last = data.last_page || 1;
      document.getElementById('pageinfo').textContent = `Hal ${state.page} / ${last}`;
      document.getElementById('prev').disabled = state.page <= 1;
      document.getElementById('next').disabled = state.page >= last;

    } catch (e) {
      alert('Gagal memuat data: ' + e);
    } finally {
      state.loading = false;
      document.getElementById('loading').classList.toggle('hidden', true);
    }
  }

  function badge(s){
    const base = 'bg-gray-100 text-gray-700';
    const map = {
      draft:  {cls: 'bg-yellow-100 text-yellow-800', txt:'draft'},
      publik: {cls: 'bg-green-100 text-green-800',  txt:'publik'},
      arsip:  {cls: 'bg-gray-200 text-gray-700',   txt:'arsip'},
    };
    return map[s] || {cls: base, txt: (s ?? '-')};
  }

  // filters
  const form = document.getElementById('filters');
  form.addEventListener('submit', e => {
    e.preventDefault();
    state.page = 1;
    state.q = document.getElementById('q').value.trim();
    state.status = document.getElementById('status').value;
    state.kategori_id = document.getElementById('kategori_id').value;
    state.per_page = +document.getElementById('per_page').value || 10;
    load();
  });
  document.getElementById('resetBtn').addEventListener('click', () => {
    form.reset();
    state.page = 1; state.q=''; state.status=''; state.kategori_id=''; state.per_page=10;
    load();
  });

  document.getElementById('prev').addEventListener('click', () => { if(state.page>1){ state.page--; load(); } });
  document.getElementById('next').addEventListener('click', () => { state.page++; load(); });

  // pertama kali
  load();
  </script>
</body>
</html>
