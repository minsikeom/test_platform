<form method="post" action="/test"  enctype="multipart/form-data">
    @csrf
    <input type="file" name="files">
    <input type="submit" onclick="onsubmit(this.form)">
</form>
