@extends('layouts.app')
@section('title', 'Admin Portal')
@section('head')
<script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script>
<style>
    :root {
      --bg: #f5f7fb;
      --card: #ffffff;
      --text: #172033;
      --muted: #667085;
      --line: #e5e9f0;
      --accent: #244a7c;
      --danger: #a33;
      --shadow: 0 8px 28px rgba(22, 34, 51, .08);
    }

    * { box-sizing: border-box; }

    body {
      margin: 0;
      font: 14px/1.45 system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
      background: linear-gradient(180deg, #f7f9fc 0, #f3f6fa 100%);
      color: var(--text);
    }

    button, input, select, textarea { font: inherit; }
    .hidden { display: none !important; }
    .muted { color: var(--muted); }

    .login {
      min-height: 100vh;
      display: grid;
      place-items: center;
      padding: 24px;
      background:
        radial-gradient(circle at top left, #eef4fb 0, transparent 38%),
        linear-gradient(135deg, #f7f9fc, #eef3f8);
    }

    .login-card {
      width: min(420px, 100%);
      background: var(--card);
      padding: 38px 36px;
      border: 1px solid #e2e8f0;
      border-radius: 22px;
      box-shadow: 0 18px 55px rgba(30, 50, 80, .12);
    }

    .brand-badge {
      display: inline-flex;
      padding: 5px 9px;
      border-radius: 999px;
      background: #eaf1f9;
      color: #244a7c;
      font-size: 11px;
      font-weight: 800;
      letter-spacing: .08em;
    }

    h1, h2, h3 { margin-top: 0; }

    .login-card h1 {
      font-size: 30px;
      letter-spacing: -.5px;
      margin: 8px 0 4px;
    }

    .login-subtitle {
      margin: 0 0 18px;
      color: #36577f;
      font-weight: 600;
    }

    .field { margin: 14px 0; }

    .field label {
      display: block;
      font-weight: 650;
      margin-bottom: 6px;
    }

    .field input,
    .field textarea,
    .field select,
    .toolbar input,
    .toolbar select {
      width: 100%;
      padding: 10px 12px;
      border: 1px solid #ccd3dd;
      border-radius: 9px;
      background: #fff;
    }

    .field input:focus,
    .field textarea:focus,
    .field select:focus,
    .toolbar input:focus,
    .toolbar select:focus {
      outline: none;
      border-color: #6f8fb5;
      box-shadow: 0 0 0 3px rgba(36, 74, 124, .10);
    }

    .btn {
      border: 0;
      border-radius: 9px;
      padding: 10px 14px;
      cursor: pointer;
      background: var(--accent);
      color: #fff;
      font-weight: 650;
      transition: transform .12s ease, box-shadow .12s ease, background .12s ease;
    }

    .btn:hover {
      transform: translateY(-1px);
      box-shadow: 0 4px 10px rgba(36, 74, 124, .12);
    }

    .btn.secondary {
      background: #eef2f7;
      color: #253858;
    }

    .btn.danger {
      background: #fff1f1;
      color: var(--danger);
      border: 1px solid #efcaca;
    }

    .btn.small {
      padding: 6px 9px;
      font-size: 12px;
    }

    .login-card .btn {
      width: 100%;
      padding: 12px 16px;
      margin-top: 4px;
    }

    .msg {
      min-height: 20px;
      margin-top: 10px;
      color: var(--danger);
    }

    header {
      background: #fff;
      border-bottom: 1px solid var(--line);
      padding: 18px 28px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 16px;
      box-shadow: 0 2px 10px rgba(22, 34, 51, .035);
    }

    .header-brand {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .brand-mark {
      width: 38px;
      height: 38px;
      border-radius: 11px;
      display: grid;
      place-items: center;
      background: #244a7c;
      color: white;
      font-weight: 800;
      font-size: 18px;
    }

    .header-brand strong { font-size: 17px; }
    .header-subtitle { font-size: 12px; color: #667085; margin-top: 1px; }

    .wrap {
      max-width: 1400px;
      margin: auto;
      padding: 26px 22px 22px;
    }

    .stats {
      display: grid;
      grid-template-columns: repeat(4, minmax(0, 1fr));
      gap: 16px;
    }

    .stat,
    .panel {
      background: #fff;
      border: 1px solid var(--line);
      border-radius: 16px;
      box-shadow: 0 3px 14px rgba(22, 34, 51, .04);
    }

    .stat { padding: 20px; }
    .stat b { font-size: 29px; display: block; letter-spacing: -.5px; margin-bottom: 3px; }
    .stat span { color: var(--muted); }

    .tabs {
      display: flex;
      gap: 8px;
      flex-wrap: wrap;
      margin: 18px 0;
      padding: 5px;
      background: #e9eef5;
      border-radius: 12px;
      width: max-content;
      max-width: 100%;
    }

    .tab { background: #e9eef5; color: #344054; border-radius: 9px; }
    .tab.active { background: var(--accent); color: #fff; box-shadow: 0 3px 8px rgba(36, 74, 124, .18); }

    .panel { padding: 20px; margin-top: 16px; }

    .topline,
    .admin-actions-head {
      display: flex;
      align-items: center;
      gap: 12px;
      flex-wrap: wrap;
    }

    .spacer { flex: 1; }

    .toolbar {
      display: grid;
      grid-template-columns: 2fr 1fr auto;
      gap: 10px;
      margin-bottom: 14px;
    }

    .table-wrap {
      overflow: auto;
      border: 1px solid var(--line);
      border-radius: 10px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      min-width: 900px;
    }

    th, td {
      text-align: left;
      padding: 10px 12px;
      border-bottom: 1px solid var(--line);
      vertical-align: top;
    }

    th {
      background: #f8fafc;
      position: sticky;
      top: 0;
      font-size: 12px;
      text-transform: uppercase;
      letter-spacing: .035em;
      color: #526174;
    }

    tbody tr:hover { background: #fafcff; }
    .actions { display: flex; gap: 6px; flex-wrap: wrap; }

    .admin-status,
    .admin-role {
      display: inline-flex;
      align-items: center;
      padding: 4px 8px;
      border-radius: 999px;
      font-size: 12px;
      font-weight: 700;
    }

    .admin-status.active { background: #eaf7ef; color: #217a46; }
    .admin-status.inactive { background: #f2f4f7; color: #667085; }
    .admin-role { background: #eef3f9; color: #36577f; }

    .admin-note {
      padding: 12px 14px;
      background: #f8fafc;
      border: 1px solid var(--line);
      border-radius: 10px;
      margin: 12px 0 16px;
    }

    .modal {
      position: fixed;
      inset: 0;
      background: rgba(15, 23, 42, .46);
      display: grid;
      place-items: center;
      padding: 20px;
      z-index: 10;
    }

    .modal-card {
      background: #fff;
      width: min(820px, 100%);
      max-height: 90vh;
      overflow: auto;
      border-radius: 16px;
      padding: 22px;
    }

    .grid2 {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 12px;
    }

    .full { grid-column: 1 / -1; }

    .modal-actions {
      display: flex;
      gap: 8px;
      justify-content: flex-start;
      margin-top: 16px;
    }

    .activity { padding: 10px 0; border-bottom: 1px solid var(--line); }

    @media (max-width: 800px) {
      .stats { grid-template-columns: 1fr 1fr; }
      .toolbar, .grid2 { grid-template-columns: 1fr; }
      .full { grid-column: auto; }
      header { padding: 14px 16px; }
      .wrap { padding: 16px; }
      .tabs { width: 100%; }
      .login-card { padding: 28px 22px; }
    }
  </style>
@endsection
@section('content')

  <section id="loginView" class="login">
    <div class="login-card">
      <div class="brand-badge">ADMIN PORTAL</div>
      <h1>Admin Users</h1>
      <p class="login-subtitle">Administration</p>
      <p class="muted">Sign in with your User ID and Password.</p>

      <form id="loginForm">
        <div class="field">
          <label for="loginUsername">User ID</label>
          <input id="loginUsername" type="text" required autocomplete="username" placeholder="admin">
        </div>

        <div class="field">
          <label for="loginPassword">Password</label>
          <input id="loginPassword" type="password" required autocomplete="current-password">
        </div>

        <button class="btn" type="submit">Login</button>
        <div id="loginMsg" class="msg"></div>
      </form>
    </div>
  </section>

  <section id="adminView" class="hidden">
    <header>
      <div class="header-brand">
        <span class="brand-mark">A</span>
        <div>
          <strong>Admin Users</strong>
          <div class="header-subtitle">Administration</div>
          <div id="adminIdentity" class="muted"></div>
        </div>
      </div>
      <button id="logoutBtn" class="btn secondary">Logout</button>
    </header>

    <main class="wrap">
      <div class="stats">
        <div class="stat"><b id="totalCount">0</b><span>Active faculty</span></div>
        <div class="stat"><b id="deletedCount">0</b><span>Deleted records</span></div>
        <div class="stat"><b id="deptCount">0</b><span>Departments</span></div>
        <div class="stat"><b id="updatedAt">—</b><span>Latest update</span></div>
      </div>

      <div class="tabs">
        <button class="btn tab active" data-tab="records">Faculty Records</button>
        <button class="btn tab" data-tab="deleted">Deleted Records</button>
        <button class="btn tab" data-tab="admins">Admin Users</button>
        <button class="btn tab" data-tab="activity">Activity Log</button>
      </div>

      <section id="recordsTab" class="panel tabpane">
        <div class="topline">
          <h2>Faculty Records</h2>
          <span class="spacer"></span>
          <button id="uploadExcelBtn" class="btn secondary">Upload Excel</button>
          <button id="exportBtn" class="btn">Download Excel</button>
        </div>

        <div class="toolbar">
          <input id="searchBox" placeholder="Search name, email, department...">
          <select id="deptFilter"><option value="">All departments</option></select>
          <button id="clearFilter" class="btn secondary">Clear</button>
        </div>

        <div class="table-wrap">
          <table>
            <thead>
              <tr><th>Name</th><th>Email</th><th>Department</th><th>Updated</th><th>Actions</th></tr>
            </thead>
            <tbody id="recordsBody"></tbody>
          </table>
        </div>
      </section>

      <section id="deletedTab" class="panel tabpane hidden">
        <h2>Deleted Records</h2>
        <div class="table-wrap">
          <table>
            <thead>
              <tr><th>Name</th><th>Email</th><th>Department</th><th>Deleted</th><th>Actions</th></tr>
            </thead>
            <tbody id="deletedBody"></tbody>
          </table>
        </div>
      </section>

      <section id="adminsTab" class="panel tabpane hidden">
        <div class="admin-actions-head">
          <div>
            <h2 style="margin-bottom:4px">Admin Users</h2>
            <p class="muted" style="margin:0">Manage database login users.</p>
          </div>
          <span class="spacer"></span>
          <button id="openAddAdmin" class="btn">+ Add Admin</button>
        </div>

        <div class="admin-note">
          User IDs are stored in <strong>admin_login_users</strong>. Passwords are stored only as secure hashes, not plain text.
        </div>

        <div class="table-wrap">
          <table>
            <thead>
              <tr><th>User ID</th><th>Role</th><th>Status</th><th>Created</th><th>Actions</th></tr>
            </thead>
            <tbody id="adminsBody"></tbody>
          </table>
        </div>
      </section>

      <section id="activityTab" class="panel tabpane hidden">
        <h2>Activity Log</h2>
        <div id="activityList"></div>
      </section>
    </main>
  </section>

  <div id="recordModal" class="modal hidden">
    <div class="modal-card">
      <h2 id="modalTitle">Faculty record</h2>
      <form id="recordForm" class="grid2"></form>
      <div class="modal-actions">
        <button id="saveRecord" class="btn hidden" type="button">Save Changes</button>
        <button id="closeModal" class="btn secondary" type="button">Close</button>
      </div>
      <div id="modalMsg" class="msg"></div>
    </div>
  </div>

  <div id="excelUploadModal" class="modal hidden">
    <div class="modal-card" style="width:min(980px,100%)">
      <h2>Upload Faculty Excel</h2>
      <p class="muted">Select the Microsoft Forms Excel file. Existing records are matched by email and updated; new emails are inserted. Rows without the required consent are skipped.</p>

      <div class="field">
        <label for="excelFileInput">Excel file (.xlsx / .xls)</label>
        <input id="excelFileInput" type="file" accept=".xlsx,.xls,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel">
      </div>

      <div id="excelSummary" class="admin-note hidden"></div>
      <div id="excelPreviewWrap" class="table-wrap hidden" style="max-height:360px;overflow:auto">
        <table>
          <thead><tr><th>Row</th><th>Name</th><th>Email</th><th>Department</th><th>Status</th></tr></thead>
          <tbody id="excelPreviewBody"></tbody>
        </table>
      </div>

      <div class="modal-actions">
        <button id="importExcelBtn" class="btn hidden" type="button">Import to Database</button>
        <button id="closeExcelUpload" class="btn secondary" type="button">Cancel</button>
      </div>
      <div id="excelUploadMsg" class="msg"></div>
    </div>
  </div>

  <div id="addAdminModal" class="modal hidden">
    <div class="modal-card" style="width:min(560px,100%)">
      <h2>Add Admin User</h2>

      <div class="field">
        <label for="newAdminUsername">User ID</label>
        <input id="newAdminUsername" type="text" placeholder="newadmin">
      </div>

      <div class="field">
        <label for="newAdminPassword">Password</label>
        <input id="newAdminPassword" type="password" placeholder="Password">
      </div>

      <div class="field">
        <label for="newAdminRole">Role</label>
        <select id="newAdminRole">
          <option value="admin">Admin</option>
          <option value="super_admin">Super Admin</option>
        </select>
      </div>

      <div class="modal-actions">
        <button id="saveAdminUser" class="btn" type="button">Add Admin</button>
        <button id="closeAddAdmin" class="btn secondary" type="button">Cancel</button>
      </div>
      <div id="addAdminMsg" class="msg"></div>
    </div>
  </div>

  

@endsection
@section('scripts')
<script src="{{ asset('js/admin.js') }}"></script>
@endsection
