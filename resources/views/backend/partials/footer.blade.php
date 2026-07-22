<footer class="main-footer">
  <div class="pull-right hidden-xs">
    <strong>Copyright &copy; {{ date('Y') }} <a target="_blank" href="https://bphn.go.id">Bagian Hukum Setda Kota Kendari</a>.</strong> All
    rights reserved.
  </div>
  User : <span class="label label-default label-md">{{ ucfirst(auth()->user()->username) }}</span> | Hak Akses :
  <span class="label label-warning label-md">superadmin</span>&nbsp;
</footer>
